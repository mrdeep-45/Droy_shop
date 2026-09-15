<?php

namespace App\Http\Controllers;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\DealLog;
use App\Services\DealLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\NoteTitleModel;
class DealDetailsController extends Controller
{
    //
    private DealLogService $logService;

    public function __construct(DealLogService $logService)
    {
        $this->logService = $logService;
    }

    public function details($id)
    {
        $page_name = 'Deal Details';
        $realId = $this->decryptId($id);
        $deal = Deal::with(['dealStage', 'lead.country', 'lead.state', 'lead.city', 'lead.sourceInfo'])->findOrFail($realId);

        $products = DB::table('tbl_enquiry_items')
            ->join('tbl_product', 'tbl_product.product_id', '=', 'tbl_enquiry_items.product_id')
            ->where('tbl_enquiry_items.enquiry_id', $deal->lead_id)
            ->select('tbl_product.prod_name', 'tbl_enquiry_items.quantity', 'tbl_enquiry_items.price')
            ->get();

        $stages = DealStage::where('status', 0)
            ->where(function ($query) use ($deal) {
                $query->whereNotIn('stage_name', ['Close Client', 'Lose', 'Loss'])
                    ->orWhere('id', $deal->deal_stage);
            })
            ->orderBy('order_no', 'asc')
            ->get();

        $staffs = DB::table('mst_staff')
            ->select('staff_id', 'staff_name', 'status', 'staff_email', 'staff_type')
            ->where('status', 0)
            ->orderBy('staff_name', 'asc')
            ->get();

        $sources = DB::table('mst_lead_source')
            ->where('status', 0)
            ->orderBy('title', 'asc')
            ->get();

        $countries = DB::table('mst_country')
            ->where('status', 0)
            ->orderBy('country_name', 'asc')
            ->get();

        $states = DB::table('mst_state')
            ->where('status', 0)
            ->where('c_id', $deal->lead->countries_id ?? 0)
            ->orderBy('state_name', 'asc')
            ->get();

        $cities = DB::table('mst_city')
            ->where('status', 0)
            ->where('state_id', $deal->lead->state_id ?? 0)
            ->orderBy('city_name', 'asc')
            ->get();

        $all_products = DB::table('tbl_product')
            ->where('status', 0)
            ->orderBy('prod_name', 'asc')
            ->get();

        $selected_product_ids = DB::table('tbl_enquiry_items')
            ->where('enquiry_id', $deal->lead_id)
            ->pluck('product_id')
            ->toArray();

        $noteTitles = NoteTitleModel::active()->get();

        $industries = DB::table('mst_industry_type')
            ->where('status', 0)
            ->orderBy('industry_type_name', 'asc')
            ->get();

        return view('company.crm.deal.details', compact('deal', 'products', 'stages', 'staffs', 'sources', 'countries', 'states', 'cities', 'page_name', 'all_products', 'selected_product_ids', 'noteTitles', 'industries'));
    }

    public function updateDetails(Request $request, $id)
    {
        if ($request->has('closing_date') && $request->closing_date) {
            try {
                $formattedDate = Carbon::createFromFormat('d-m-Y', $request->closing_date)->format('Y-m-d');
                $request->merge(['closing_date' => $formattedDate]);
            } catch (\Exception $e) {
                try {
                    $formattedDate = Carbon::parse($request->closing_date)->format('Y-m-d');
                    $request->merge(['closing_date' => $formattedDate]);
                } catch (\Exception $ex) {
                    // Do nothing, validator will catch it
                }
            }
        }

        if ($request->has('dispatch_date') && $request->dispatch_date) {
            try {
                $formattedDate = Carbon::createFromFormat('d-m-Y', $request->dispatch_date)->format('Y-m-d');
                $request->merge(['dispatch_date' => $formattedDate]);
            } catch (\Exception $e) {
                try {
                    $formattedDate = Carbon::parse($request->dispatch_date)->format('Y-m-d');
                    $request->merge(['dispatch_date' => $formattedDate]);
                } catch (\Exception $ex) {
                    // Do nothing, validator will catch it
                }
            }
        }

        // Convert product_ids to comma-separated deal_name if provided
        $productNames = '';
        if ($request->has('product_ids')) {
            $productNames = DB::table('tbl_product')
                ->whereIn('product_id', $request->product_ids)
                ->pluck('prod_name')
                ->implode(', ');
            $request->merge(['deal_name' => $productNames]);
        }

        // If the new stage is won, make iwo_no and dispatch_date required
        $newStageObj = DealStage::find($request->deal_stage);
        $isWon = $newStageObj && $newStageObj->is_won == 1;

        $validator = Validator::make($request->all(), [
            'deal_name' => 'required|string|max:255',
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:tbl_product,product_id',
            'contact_person_name' => 'nullable|string|max:255',
            'closing_date' => 'required|date',
            'email' => 'nullable|email|max:255',
            'phone' => ['nullable', 'string', 'max:15', 'regex:/^[0-9()+\- ]+$/'],
            'company_name' => 'nullable|string|max:255',
            'source' => 'nullable|integer',
            'deal_stage' => 'required|integer|exists:mst_deal_stage,id',
            'amount' => 'nullable|numeric|min:0',
            'countries_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'industry_id' => 'nullable|integer',
            'iwo_no' => $isWon ? 'required|string|max:255' : 'nullable|string|max:255',
            'dispatch_date' => $isWon ? 'required|date' : 'nullable|date',
            'deal_type' => 'nullable|string',
            'assigned_to' => 'nullable|integer',
            'description' => 'nullable|string',
        ], [], [
            'iwo_no' => 'IWO No',
            'dispatch_date' => 'Dispatch Date',
            'closing_date' => 'Closing Date',
            'product_ids' => 'Products',
            'deal_stage' => 'Deal Stage',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $deal = Deal::findOrFail($id);

            // Capture old Deal values
            $oldDealName = $deal->deal_name;
            $oldClosingDate = $deal->closing_date;
            $oldAmount = $deal->amount;
            $oldDealStageId = $deal->deal_stage;
            $oldDescription = $deal->description;

            // Capture old Lead values
            $oldContactPerson = '';
            $oldEmail = '';
            $oldPhone = '';
            $oldCompanyName = '';
            $oldSourceId = null;
            $oldCountryId = null;
            $oldStateId = null;
            $oldCityId = null;

            $lead = null;
            if ($deal->lead_id) {
                $lead = DB::table('enquiry_manage')->where('enquiry_id', $deal->lead_id)->first();
                if ($lead) {
                    $oldContactPerson = $lead->contact_person_name;
                    $oldEmail = $lead->email;
                    $oldPhone = $lead->phone;
                    $oldCompanyName = $lead->company_name;
                    $oldSourceId = $lead->source;
                    $oldCountryId = $lead->countries_id;
                    $oldStateId = $lead->state_id;
                    $oldCityId = $lead->city_id;
                }
            }

            // Sync products if passed and calculate amount
            if ($deal->lead_id && $request->has('product_ids')) {
                $existingItems = DB::table('tbl_enquiry_items')
                    ->where('enquiry_id', $deal->lead_id)
                    ->get()
                    ->keyBy('product_id')
                    ->toArray();

                $newProductIds = $request->product_ids;

                // Delete products not in the new list
                $productIdsToDelete = array_diff(array_keys($existingItems), $newProductIds);
                if (!empty($productIdsToDelete)) {
                    DB::table('tbl_enquiry_items')
                        ->where('enquiry_id', $deal->lead_id)
                        ->whereIn('product_id', $productIdsToDelete)
                        ->delete();
                }

                $totalAmount = 0;
                foreach ($newProductIds as $productId) {
                    $productInfo = DB::table('tbl_product')->where('product_id', $productId)->first();
                    $price = $productInfo ? $productInfo->prod_amount : 0;
                    $totalAmount += $price;

                    // Fetch GST value
                    $gstVal = 0;
                    if ($productInfo && $productInfo->gst) {
                        $gstVal = DB::table('mst_gst')->where('gst_id', $productInfo->gst)->value('gst_no') ?? 0;
                    }

                    if (isset($existingItems[$productId])) {
                        // Update existing (preserve ID!)
                        DB::table('tbl_enquiry_items')
                            ->where('id', $existingItems[$productId]->id)
                            ->update([
                                'quantity' => 1,
                                'price' => $price,
                                'gst' => $gstVal,
                            ]);
                    } else {
                        // Insert new
                        DB::table('tbl_enquiry_items')->insert([
                            'enquiry_id' => $deal->lead_id,
                            'product_id' => $productId,
                            'quantity' => 1,
                            'price' => $price,
                            'gst' => $gstVal,
                        ]);
                    }
                }
                $request->merge(['amount' => $totalAmount]);
            }

            $newStageObj = DealStage::find($request->deal_stage);
            $dealStatusVal = 0;
            if ($newStageObj) {
                if ($newStageObj->is_won == 1) {
                    $dealStatusVal = 1;
                } elseif ($newStageObj->is_lost == 1) {
                    $dealStatusVal = 2;
                }
            }

            $dealDataToUpdate = [
                'deal_name' => $request->deal_name,
                'closing_date' => $request->closing_date,
                'deal_stage' => $request->deal_stage,
                'deal_status' => $dealStatusVal,
                'amount' => $request->amount,
                'description' => $request->description,
                'updated_by' => getCreatedBy(),
                'updated_at' => now(),
            ];

            if ($request->has('iwo_no')) $dealDataToUpdate['iwo_no'] = $request->iwo_no;
            if ($request->has('dispatch_date')) $dealDataToUpdate['dispatch_date'] = $request->dispatch_date;
            if ($request->has('deal_type')) $dealDataToUpdate['deal_type'] = $request->deal_type;
            if ($request->has('assigned_to')) $dealDataToUpdate['assigned_to'] = $request->assigned_to;

            // Update Deal record
            $deal->update($dealDataToUpdate);

            // Update associated Lead/Enquiry record
            if ($deal->lead_id) {
                $leadUpdateData = [
                    'enquiry_name' => $request->deal_name
                ];
                if ($request->has('contact_person_name')) $leadUpdateData['contact_person_name'] = $request->contact_person_name;
                if ($request->has('email')) $leadUpdateData['email'] = $request->email;
                if ($request->has('phone')) $leadUpdateData['phone'] = $request->phone;
                if ($request->has('company_name')) $leadUpdateData['company_name'] = $request->company_name;
                if ($request->has('source')) $leadUpdateData['source'] = $request->source;
                if ($request->has('countries_id')) $leadUpdateData['countries_id'] = $request->countries_id;
                if ($request->has('state_id')) $leadUpdateData['state_id'] = $request->state_id;
                if ($request->has('city_id')) $leadUpdateData['city_id'] = $request->city_id;
                if ($request->has('industry_id')) $leadUpdateData['industry_id'] = $request->industry_id;

                if (!empty($leadUpdateData)) {
                    $leadUpdateData['updated_by'] = getCreatedBy();
                    $leadUpdateData['updated_at'] = now();
                    DB::table('enquiry_manage')
                        ->where('enquiry_id', $deal->lead_id)
                        ->update($leadUpdateData);
                }
            }

            // Construct detail-oriented log message
            $changes = [];

            if ((string) $oldDealName !== (string) $request->deal_name) {
                $changes[] = "Deal Name from \"{$oldDealName}\" to \"{$request->deal_name}\"";
            }
            // Normalize dates to Y-m-d format for comparison
            $oldClosingDateFormatted = $oldClosingDate ? Carbon::parse($oldClosingDate)->format('Y-m-d') : '';
            $newClosingDateFormatted = $request->closing_date ? Carbon::parse($request->closing_date)->format('Y-m-d') : '';
            if ($oldClosingDateFormatted !== $newClosingDateFormatted) {
                $oldClosingDateDisplay = $oldClosingDate ? Carbon::parse($oldClosingDate)->format('d-m-Y') : '';
                $newClosingDateDisplay = $request->closing_date ? Carbon::parse($request->closing_date)->format('d-m-Y') : '';
                $changes[] = "Closing Date from \"{$oldClosingDateDisplay}\" to \"{$newClosingDateDisplay}\"";
            }
            if ((float) $oldAmount !== (float) $request->amount) {
                $changes[] = "Amount from \"{$oldAmount}\" to \"{$request->amount}\"";
            }
            if ((int) $oldDealStageId !== (int) $request->deal_stage) {
                $oldStageName = DB::table('mst_deal_stage')->where('id', $oldDealStageId)->value('stage_name') ?: 'Unknown';
                $newStageName = DB::table('mst_deal_stage')->where('id', $request->deal_stage)->value('stage_name') ?: 'Unknown';
                $changes[] = "Deal Stage from \"{$oldStageName}\" to \"{$newStageName}\"";
            }
            if ((string) $oldDescription !== (string) $request->description) {
                $changes[] = "Description updated";
            }

            if ($lead) {
                if ($request->has('contact_person_name') && (string) $oldContactPerson !== (string) $request->contact_person_name) {
                    $changes[] = "Contact Person from \"{$oldContactPerson}\" to \"{$request->contact_person_name}\"";
                }
                if ($request->has('email') && (string) $oldEmail !== (string) $request->email) {
                    $changes[] = "Email from \"{$oldEmail}\" to \"{$request->email}\"";
                }
                if ($request->has('phone') && (string) $oldPhone !== (string) $request->phone) {
                    $changes[] = "Phone from \"{$oldPhone}\" to \"{$request->phone}\"";
                }
                if ($request->has('company_name') && (string) $oldCompanyName !== (string) $request->company_name) {
                    $changes[] = "Company Name from \"{$oldCompanyName}\" to \"{$request->company_name}\"";
                }
                if ($request->has('source') && (int) $oldSourceId !== (int) $request->source) {
                    $oldSourceName = DB::table('mst_lead_source')->where('source_id', $oldSourceId)->value('title') ?: 'None';
                    $newSourceName = DB::table('mst_lead_source')->where('source_id', $request->source)->value('title') ?: 'None';
                    $changes[] = "Source from \"{$oldSourceName}\" to \"{$newSourceName}\"";
                }
                if ($request->has('countries_id') && (int) $oldCountryId !== (int) $request->countries_id) {
                    $oldCountry = DB::table('mst_country')->where('c_id', $oldCountryId)->value('country_name') ?: 'None';
                    $newCountry = DB::table('mst_country')->where('c_id', $request->countries_id)->value('country_name') ?: 'None';
                    $changes[] = "Country from \"{$oldCountry}\" to \"{$newCountry}\"";
                }
                if ($request->has('state_id') && (int) $oldStateId !== (int) $request->state_id) {
                    $oldState = DB::table('mst_state')->where('state_id', $oldStateId)->value('state_name') ?: 'None';
                    $newState = DB::table('mst_state')->where('state_id', $request->state_id)->value('state_name') ?: 'None';
                    $changes[] = "State from \"{$oldState}\" to \"{$newState}\"";
                }
                if ($request->has('city_id') && (int) $oldCityId !== (int) $request->city_id) {
                    $oldCity = DB::table('mst_city')->where('city_id', $oldCityId)->value('city_name') ?: 'None';
                    $newCity = DB::table('mst_city')->where('city_id', $request->city_id)->value('city_name') ?: 'None';
                    $changes[] = "City from \"{$oldCity}\" to \"{$newCity}\"";
                }
            }

            if (count($changes) > 0) {
                $logDescription = "Deal details updated: " . implode(', ', $changes);
                $this->logService->log($deal, 'deal_updated', $logDescription);

                if ($deal->lead_id) {
                    DB::table('enquiry_history')->insert([
                        'enquiry_id'  => $deal->lead_id,
                        'change_type' => 'Enquiry Updated',
                        'reason'      => $logDescription,
                        'table_name'  => 'enquiry_manage',
                        'changed_by'  => getCreatedBy(),
                        'changed_at'  => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Deal updated successfully.',
                'amount' => number_format($deal->amount, 2, '.', ''),
                'deal_name' => $deal->deal_name
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('updateDetails error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function decryptId($payload)
    {
        try {
            $decrypted = str_replace(['-', '_'], ['+', '/'], $payload);
            $remainder = strlen($decrypted) % 4;
            if ($remainder) {
                $decrypted .= str_repeat('=', 4 - $remainder);
            }
            return decrypt($decrypted);
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
