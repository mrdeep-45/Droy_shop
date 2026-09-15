<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\LeadSource;
class EnquirySource extends Controller
{
    //
    public function index()
    {
        $data = [
            'page_title' => 'Lead Source',
            'page_name' => 'Lead Source',
        ];
        return view('company.master.source', $data);
    }

    public function getList()
    {
        try {
            $sources = LeadSource::select(['source_id', 'title', 'source_type', 'created_by', 'created_at'])
                ->where('status', 0)
                ->orderBy('source_id', 'desc')
                ->get();

            $grouped = $sources->groupBy('source_type');

            $data = [];
            foreach ($grouped as $type => $rows) {
                $sourceItems = [];
                foreach ($rows as $row) {
                    $isUsed = false;
                    try {
                        $isUsed = \App\Models\EnquiryManageModel::whereRaw('FIND_IN_SET(?, source)', [$row->source_id])->where('status', 0)->exists();
                    } catch (\Exception $e) {
                        $isUsed = false;
                    }

                    $sourceItems[] = [
                        'source_id' => $row->source_id,
                        'title' => $row->title,
                        'created_by' => get_createdby_name($row->created_by),
                        'created_at' => \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i:s'),
                        'is_used' => $isUsed
                    ];
                }

                $data[] = [
                    'source_type' => $type,
                    'sources' => $sourceItems
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function storeOrUpdate(Request $request)
    {
        $company_id = get_company_id();
        $created_type = Session::get('login_type');

        try {
            $ids = $request->input('source_id');   // could be array or single value or null
            $names = $request->input('source_name'); // array of names
            $types = $request->input('source_type'); // array of source types

            if (is_string($names)) {
                $names = [$names];
            }
            if (is_string($types)) {
                $types = [$types];
            }

            if (empty($names) || !is_array($names)) {
                return response()->json([
                    'errors' => [
                        'source_name.0' => ['At least one Source name is required.'],
                    ],
                ], 422);
            }

            if (empty($types) || !is_array($types) || empty($types[0])) {
                return response()->json([
                    'errors' => [
                        'source_type' => ['Source type is required.'],
                        'source_type.0' => ['Source type is required.'],
                    ],
                ], 422);
            }

            if (count($types) === 1) {
                $types = array_fill(0, count($names), $types[0]);
            }

            if (!is_array($ids)) {
                $ids = $ids ? [$ids] : [];
            }

            $updated = false;
            $created = false;
            $newSource = null;

            DB::beginTransaction();

            $submittedIds = array_filter($ids);
            if (!empty($submittedIds)) {
                $selectedType = $types[0] ?? null;
                if ($selectedType) {
                    $dbIds = LeadSource::where('source_type', $selectedType)
                        ->where('status', 0)
                        ->pluck('source_id')
                        ->toArray();

                    $idsToDelete = array_diff($dbIds, $submittedIds);

                    foreach ($idsToDelete as $deleteId) {
                        $itemToDelete = LeadSource::find($deleteId);
                        if ($itemToDelete) {
                            $isUsed = false;
                            try {
                                $isUsed = \App\Models\EnquiryManageModel::whereRaw('FIND_IN_SET(?, source)', [$deleteId])->where('status', 0)->exists();
                            } catch (\Exception $e) {
                                $isUsed = false;
                            }

                            if ($isUsed) {
                                return response()->json([
                                    'errors' => [
                                        'source_name.0' => ["Cannot delete '{$itemToDelete->title}'! It is already used in Enquiry."],
                                    ],
                                ], 422);
                            }

                            $itemToDelete->update([
                                'status' => 1,
                                'updated_at' => now(),
                                'updated_by' => getUpdatedBy(),
                            ]);
                        }
                    }
                }
            }

            foreach ($names as $index => $name) {
                if (empty(trim($name))) {
                    return response()->json([
                        'errors' => [
                            "source_name.$index" => ["Source name at position $index is required."],
                        ],
                    ], 422);
                }

                $type = $types[$index] ?? null;
                if (empty($type) || !in_array($type, ['Online', 'Offline'])) {
                    return response()->json([
                        'errors' => [
                            "source_type.$index" => ["Source type (Online or Offline) is required."],
                        ],
                    ], 422);
                }

                $id = $ids[$index] ?? null;

                $exists = LeadSource::where('title', $name)->where('status', 0)
                    ->when($id, fn($q) => $q->where('source_id', '!=', $id))
                    ->exists();

                if ($exists) {
                    return response()->json([
                        'errors' => [
                            "source_name.$index" => ["This Source '$name' already exists."],
                        ],
                    ], 422);
                }

                if ($id) {
                    $item = LeadSource::find($id);
                    if ($item) {
                        $item->update([
                            'title' => $name,
                            'source_type' => $type,
                            'updated_by' => getUpdatedBy(),
                            'created_type' => $created_type ?? null,
                            'company_id' => $company_id ?? null,
                            'updated_at' => now(),
                        ]);
                        $updated = true;
                        $newSource = $item;
                    }
                } else {
                    $newSource = LeadSource::create([
                        'title' => $name,
                        'source_type' => $type,
                        'created_type' => $created_type ?? null,
                        'company_id' => $company_id ?? null,
                        'status' => 0,
                        'created_by' => getCreatedBy(),
                        'created_at' => now(),
                    ]);
                    $created = true;
                }
            }
            DB::commit();
            if ($updated && $created) {
                $message = 'Sources updated and added successfully.';
            } elseif ($updated) {
                $message = 'Sources updated successfully.';
            } elseif ($created) {
                $message = 'New Sources added successfully.';
            } else {
                $message = 'No changes were made.';
            }

            return response()->json([
                'status' => 'success', 
                'message' => $message, 
                'id' => $newSource ? $newSource->source_id : null, 
                'source_name' => $newSource ? $newSource->title : ''
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        try {
            if (in_array($id, ['Online', 'Offline'])) {
                $sources = LeadSource::where('source_type', $id)
                    ->where('status', 0)
                    ->get();
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'source_type' => $id,
                        'sources' => $sources
                    ],
                ]);
            }

            $group = LeadSource::where('source_id', $id)
                ->first();

            if (!$group) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sources not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'source_type' => $group->source_type,
                    'sources' => [$group]
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error fetching Sources: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        $source_id = $request->source_id;

        try {
            DB::beginTransaction();

            if (in_array($source_id, ['Online', 'Offline'])) {
                $sources = LeadSource::where('source_type', $source_id)->where('status', 0)->get();
                if ($sources->isEmpty()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Sources not found.',
                    ]);
                }

                foreach ($sources as $source) {
                    $isUsed = false;
                    try {
                        $isUsed = \App\Models\EnquiryManageModel::whereRaw('FIND_IN_SET(?, source)', [$source->source_id])->where('status', 0)->exists();
                    } catch (\Exception $e) {
                        $isUsed = false;
                    }

                    if ($isUsed) {
                        return response()->json([
                            'status' => 'error',
                            'message' => "Cannot delete! The Source '{$source->title}' in this group is already used in Enquiry.",
                        ]);
                    }
                }

                foreach ($sources as $source) {
                    $source->update([
                        'status' => 1,
                        'updated_at' => now(),
                        'updated_by' => getUpdatedBy(),
                    ]);
                }

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Source group marked as inactive.',
                ]);
            }

            $stockGroup = LeadSource::find($source_id);
            if (!$stockGroup) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Sources not found.',
                ]);
            }

            // Production Safe checking
            $isUsed = false;
            try {
                $isUsed = \App\Models\EnquiryManageModel::whereRaw('FIND_IN_SET(?, source)', [$source_id])->where('status', 0)->exists();
            } catch (\Exception $e) {
                $isUsed = false;
            }

            if ($isUsed) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete! This Lead Source is already used in Enquiry.',
                ]);
            }

            // Just update status to 1
            $stockGroup->update([
                'status' => 1,
                'updated_at' => now(),
                'updated_by' => getUpdatedBy(),
            ]);

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Sources marked as inactive.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update status: ' . $e->getMessage(),
            ]);
        }
    }
}
