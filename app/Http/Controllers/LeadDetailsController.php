<?php

namespace App\Http\Controllers;

use App\Models\LeadDetails;
use App\Models\Deal;
use App\Models\DealLog;
use App\Models\Quotation;
use App\Models\QuotationLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\NoteTitleModel;

class LeadDetailsController extends Controller
{
    public function details($id)
    {
        $perms = checkPermissions('lead.index');
        if (!($perms['hasAnyPermission'] ?? false)) {
            abort(403, 'You do not have permission to view lead details.');
        }

        $page_name  = 'Lead Details';
        $page_title = 'Lead Details';
        $realId     = $this->decryptId($id);

        $lead = LeadDetails::with([
            'customer',
            'city',
            'state',
            'country',
            'sourceInfo',
            'leadStatus',
            'products.product',
            'assignedStaff'
        ])->findOrFail($realId);

        $products = DB::table('tbl_product')
            ->leftJoin('mst_gst', 'tbl_product.gst', '=', 'mst_gst.gst_id')
            ->select('tbl_product.*', 'mst_gst.gst_no')
            ->where('tbl_product.status', 0)
            ->orderBy('tbl_product.prod_name', 'asc')
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

        $statuses = DB::table('lead_status')
            ->where('status', 0)
            ->orderBy('sort_order', 'asc')
            ->get();

        $countries = DB::table('mst_country')
            ->where('status', 0)
            ->orderBy('country_name', 'asc')
            ->get();

        $states = DB::table('mst_state')
            ->where('status', 0)
            ->where('c_id', $lead->countries_id ?? 0)
            ->orderBy('state_name', 'asc')
            ->get();

        $cities = DB::table('mst_city')
            ->where('status', 0)
            ->where('state_id', $lead->state_id ?? 0)
            ->orderBy('city_name', 'asc')
            ->get();

        $stages = DB::table('mst_deal_stage')
            ->select('stage_name', 'id')
             ->whereNotIn('id', [6, 12, 13, 16])
            ->where('status', 0)
            ->orderBy('order_no', 'asc')
            ->get();

        $categories = DB::table('tbl_product_category')
            ->where('status', 0)
            ->orderBy('name', 'asc')
            ->get();

        $companyStateId = DB::table('companies')
            ->where('company_id', session('company_id') ?? 1)
            ->value('state');

        $noteTitles = NoteTitleModel::active()->get();

        return view('company.crm.lead.details', compact(
            'lead',
            'products',
            'stages',
            'staffs',
            'sources',
            'statuses',
            'countries',
            'states',
            'cities',
            'categories',
            'page_name',
            'page_title',
            'companyStateId',
            'noteTitles'
        ));
    }

    public function updateDetails(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'enquiry_name'        => 'required|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'phone'               => 'nullable|string|max:20',
            'alternate_phone_1'   => 'nullable|string|max:20',
            'email'               => 'required|email|max:255',
            'company_name'        => 'nullable|string|max:255',
            'source'              => 'nullable|integer',
            'status_id'           => 'required|integer',
            'notes'               => 'nullable|string',
            'countries_id'        => 'nullable|integer',
            'state_id'            => 'nullable|integer',
            'city_id'             => 'nullable|integer',
            'assigned_to'         => 'nullable|integer',
            'lead_type'           => 'nullable|string|in:Domestic,Export',
            'product_ids'         => 'nullable|array',
            'product_ids.*'       => 'integer|exists:tbl_product,product_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $lead = LeadDetails::findOrFail($id);

            // Capture old values for history logging
            $oldEnquiryName  = $lead->enquiry_name;
            $oldContactName  = $lead->contact_person_name;
            $oldPhone        = $lead->phone;
            $oldAltPhone     = $lead->alternate_phone_1;
            $oldEmail        = $lead->email;
            $oldCompanyName  = $lead->company_name;
            $oldSourceId     = $lead->source;
            $oldStatusId     = $lead->status_id;
            $oldNotes        = $lead->notes;
            $oldCountryId    = $lead->countries_id;
            $oldStateId      = $lead->state_id;
            $oldCityId       = $lead->city_id;
            $oldAssignedTo   = $lead->assigned_to;
            $oldLeadType     = $lead->lead_type;

            // Update LeadDetails record
            $lead->update([
                'enquiry_name'        => $request->enquiry_name,
                'contact_person_name' => $request->contact_person_name,
                'phone'               => $request->phone,
                'alternate_phone_1'   => $request->alternate_phone_1,
                'email'               => $request->email,
                'company_name'        => $request->company_name,
                'source'              => $request->source ?: null,
                'status_id'           => $request->status_id,
                'notes'               => $request->notes,
                'countries_id'        => $request->countries_id ?: null,
                'state_id'            => $request->state_id ?: null,
                'city_id'             => $request->city_id ?: null,
                'assigned_to'         => $request->assigned_to ?: null,
                'lead_type'           => $request->lead_type ?: null,
                'updated_by'          => getUpdatedBy(),
            ]);

            // Sync products if passed
            if ($request->has('product_ids')) {
                // Get old products list
                $oldProductIds = DB::table('tbl_enquiry_items')
                    ->where('enquiry_id', $lead->enquiry_id)
                    ->pluck('product_id')
                    ->toArray();
                
                $oldProductNames = DB::table('tbl_product')
                    ->whereIn('product_id', $oldProductIds)
                    ->pluck('prod_name')
                    ->toArray();
                $oldProductsStr = !empty($oldProductNames) ? implode(', ', $oldProductNames) : 'None';

                // Delete and sync products
                DB::table('tbl_enquiry_items')->where('enquiry_id', $lead->enquiry_id)->delete();
                $newProductIds = $request->input('product_ids', []);
                if (!empty($newProductIds)) {
                    $items = [];
                    foreach ($newProductIds as $pId) {
                        $prodInfo = DB::table('tbl_product')->where('product_id', $pId)->first();
                        $price = $prodInfo ? floatval($prodInfo->prod_amount) : 0;
                        $items[] = [
                            'enquiry_id' => $lead->enquiry_id,
                            'product_id' => $pId,
                            'quantity' => 1,
                            'price' => $price,
                        ];
                    }
                    DB::table('tbl_enquiry_items')->insert($items);
                }

                // Get new products names list
                $newProductNames = DB::table('tbl_product')
                    ->whereIn('product_id', $newProductIds)
                    ->pluck('prod_name')
                    ->toArray();
                $newProductsStr = !empty($newProductNames) ? implode(', ', $newProductNames) : 'None';

                $addedIds = array_diff($newProductIds, $oldProductIds);
                $removedIds = array_diff($oldProductIds, $newProductIds);

                $dealExists = DB::table('tbl_deal')->where('lead_id', $lead->enquiry_id)->exists();

                // Log individual added products
                foreach ($addedIds as $pId) {
                    $pName = DB::table('tbl_product')->where('product_id', $pId)->value('prod_name') ?: 'Unknown';
                    $reason = $dealExists
                        ? 'Product "' . $pName . '" added to the Deal.'
                        : 'Product "' . $pName . '" added.';
                    $this->logActivity($lead->enquiry_id, 'Product Added', $reason);
                }

                // Log individual removed products
                foreach ($removedIds as $pId) {
                    $pName = DB::table('tbl_product')->where('product_id', $pId)->value('prod_name') ?: 'Unknown';
                    $reason = $dealExists
                        ? 'Product "' . $pName . '" removed from the Deal.'
                        : 'Product "' . $pName . '" removed.';
                    $this->logActivity($lead->enquiry_id, 'Product Removed', $reason);
                }

                // Log product list update summary
                if (!empty($addedIds) || !empty($removedIds)) {
                    $this->logActivity($lead->enquiry_id, 'Product List Updated', 'Product list updated from ' . $oldProductsStr . ' to ' . $newProductsStr . '.');
                }

                // Log Deal updated summary (if deal exists and items were added)
                if ($dealExists && !empty($addedIds)) {
                    $this->logActivity($lead->enquiry_id, 'Deal Updated', 'Deal updated with additional products.');
                }
            }

            // Construct detail-oriented log message
            $changes = [];

            if ((string)$oldEnquiryName !== (string)$request->enquiry_name) {
                $changes[] = "Lead Name from \"{$oldEnquiryName}\" to \"{$request->enquiry_name}\"";
            }
            if ((string)$oldContactName !== (string)$request->contact_person_name) {
                $changes[] = "Contact Person from \"{$oldContactName}\" to \"{$request->contact_person_name}\"";
            }
            if ((string)$oldPhone !== (string)$request->phone) {
                $changes[] = "Phone from \"{$oldPhone}\" to \"{$request->phone}\"";
            }
            if ((string)$oldAltPhone !== (string)$request->alternate_phone_1) {
                $changes[] = "Alt Phone from \"{$oldAltPhone}\" to \"{$request->alternate_phone_1}\"";
            }
            if ((string)$oldEmail !== (string)$request->email) {
                $changes[] = "Email from \"{$oldEmail}\" to \"{$request->email}\"";
            }
            if ((string)$oldCompanyName !== (string)$request->company_name) {
                $changes[] = "Company Name from \"{$oldCompanyName}\" to \"{$request->company_name}\"";
            }
            if ((int)$oldSourceId !== (int)$request->source) {
                $oldSourceName = DB::table('mst_lead_source')->where('source_id', $oldSourceId)->value('title') ?: 'None';
                $newSourceName = DB::table('mst_lead_source')->where('source_id', $request->source)->value('title') ?: 'None';
                $changes[] = "Source from \"{$oldSourceName}\" to \"{$newSourceName}\"";
            }
            if ((int)$oldStatusId !== (int)$request->status_id) {
                $oldStatusName = DB::table('lead_status')->where('status_id', $oldStatusId)->value('title') ?: 'None';
                $newStatusName = DB::table('lead_status')->where('status_id', $request->status_id)->value('title') ?: 'None';
                $changes[] = "Status from \"{$oldStatusName}\" to \"{$newStatusName}\"";
            }
            if ((string)$oldNotes !== (string)$request->notes) {
                $changes[] = "Remarks from \"{$oldNotes}\" to \"{$request->notes}\"";
            }
            if ((int)$oldCountryId !== (int)$request->countries_id) {
                $oldCountry = DB::table('mst_country')->where('c_id', $oldCountryId)->value('country_name') ?: 'None';
                $newCountry = DB::table('mst_country')->where('c_id', $request->countries_id)->value('country_name') ?: 'None';
                $changes[] = "Country from \"{$oldCountry}\" to \"{$newCountry}\"";
            }
            if ((int)$oldStateId !== (int)$request->state_id) {
                $oldState = DB::table('mst_state')->where('state_id', $oldStateId)->value('state_name') ?: 'None';
                $newState = DB::table('mst_state')->where('state_id', $request->state_id)->value('state_name') ?: 'None';
                $changes[] = "State from \"{$oldState}\" to \"{$newState}\"";
            }
            if ((int)$oldCityId !== (int)$request->city_id) {
                $oldCity = DB::table('mst_city')->where('city_id', $oldCityId)->value('city_name') ?: 'None';
                $newCity = DB::table('mst_city')->where('city_id', $request->city_id)->value('city_name') ?: 'None';
                $changes[] = "City from \"{$oldCity}\" to \"{$newCity}\"";
            }
            if ((int)$oldAssignedTo !== (int)$request->assigned_to) {
                $oldStaff = DB::table('mst_staff')->where('staff_id', $oldAssignedTo)->value('staff_name') ?: 'None';
                $newStaff = DB::table('mst_staff')->where('staff_id', $request->assigned_to)->value('staff_name') ?: 'None';
                $changes[] = "Assigned To from \"{$oldStaff}\" to \"{$newStaff}\"";
            }
            if ((string)$oldLeadType !== (string)$request->lead_type) {
                $changes[] = "Lead Type from \"{$oldLeadType}\" to \"{$request->lead_type}\"";
            }

            if (count($changes) > 0) {
                $logDescription = "Lead details updated: " . implode(', ', $changes);
                $this->logActivity($lead->enquiry_id, 'Enquiry Updated', $logDescription);

                // Add specific logs for status changes and sharing/assignment changes
                if ((int)$oldStatusId !== (int)$request->status_id) {
                    $oldStatusName = DB::table('lead_status')->where('status_id', $oldStatusId)->value('title') ?: 'None';
                    $newStatusName = DB::table('lead_status')->where('status_id', $request->status_id)->value('title') ?: 'None';
                    $this->logActivity($lead->enquiry_id, 'Lead Status Changed', 'Lead status changed from "' . $oldStatusName . '" to "' . $newStatusName . '".');
                }

                if ((int)$oldAssignedTo !== (int)$request->assigned_to) {
                    $oldStaff = DB::table('mst_staff')->where('staff_id', $oldAssignedTo)->value('staff_name') ?: 'None';
                    $newStaff = DB::table('mst_staff')->where('staff_id', $request->assigned_to)->value('staff_name') ?: 'None';
                    $this->logActivity($lead->enquiry_id, 'Lead Shared', 'Lead assignment changed from "' . $oldStaff . '" to "' . $newStaff . '".');
                }
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Lead updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lead updateDetails error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getLogs($id)
    {
        $realId = $this->decryptId($id);
        $rawLogs = [];

        // 1. enquiry_history
        $historyLogs = DB::table('enquiry_history')
            ->where('enquiry_id', $realId)
            ->get();

        foreach ($historyLogs as $log) {
            $fileUrl = null;
            if (strcasecmp($log->change_type, 'Attachment Uploaded') === 0 || $log->change_type === 'attachment_uploaded') {
                if (preg_match('/(https?:\/\/[^\s\']+)/', $log->reason, $urlMatches)) {
                    $fileUrl = $urlMatches[1];
                } else {
                    $fileName = null;
                    if (preg_match("/Uploaded file: '(.*)'/", $log->reason, $matches)) {
                        $fileName = $matches[1];
                    } elseif (preg_match('/Uploaded attachment: (.*)/', $log->reason, $matches)) {
                        $fileName = $matches[1];
                    }
                    if ($fileName) {
                        $attachment = DB::table('tbl_crm_attachments')
                            ->where('reference_type', 'lead')
                            ->where('reference_id', $realId)
                            ->where('file_name', $fileName)
                            ->where('status', 0)
                            ->orderBy('id', 'desc')
                            ->first();
                        if ($attachment) {
                            $fileUrl = config('app.img_path') . '/' . ltrim($attachment->file_path, '/');
                        }
                    }
                }
            } elseif (preg_match('/(https?:\/\/[^\s\']+)/', $log->reason, $urlMatches)) {
                $fileUrl = $urlMatches[1];
            }

            $rawLogs[] = [
                'action' => $log->change_type,
                'description' => $log->reason,
                'changed_by_name' => get_createdby_name($log->changed_by) ?: 'System',
                'date' => Carbon::parse($log->changed_at),
                'file_url' => $fileUrl,
            ];
        }

        // 3. associated deal/quotation logs
        $deal = Deal::where('lead_id', $realId)->first();
        if ($deal) {
            $dealLogs = DealLog::where('deal_id', $deal->deal_id)->get();
            $quotationIds = Quotation::where('deal_id', $deal->deal_id)->pluck('quotation_id');
            $quotationLogs = QuotationLog::whereIn('quotation_id', $quotationIds)->get();

            $allDealLogs = $dealLogs->concat($quotationLogs);
            foreach ($allDealLogs as $log) {
                    $dealFileUrl = null;
                    $dealDesc = $log->description;
                    if (strcasecmp($log->action, 'attachment_uploaded') === 0) {
                        if (preg_match('/(https?:\/\/[^\s\']+)/', $dealDesc, $um)) {
                            $dealFileUrl = $um[1];
                        } elseif (preg_match('/Uploaded attachment: (.*)/', $dealDesc, $fm)) {
                            $att = DB::table('tbl_crm_attachments')
                                ->where('reference_type', 'deal')
                                ->where('reference_id', $deal->deal_id)
                                ->where('file_name', $fm[1])
                                ->where('status', 0)
                                ->orderBy('id', 'desc')
                                ->first();
                            if ($att) {
                                // $dealFileUrl = asset($att->file_path);
                                $dealFileUrl = config('app.img_path') . '/' . ltrim($att->file_path, '/');
                            }
                        }
                    }
                    $rawLogs[] = [
                        'action' => $log->action,
                        'description' => '(Deal Phase) ' . $log->description,
                        'changed_by_name' => get_createdby_name($log->performed_by) ?: 'System',
                        'date' => Carbon::parse($log->performed_at),
                        'file_url' => $dealFileUrl,
                    ];
            }
        }

        // Sort descending by date
        usort($rawLogs, function ($a, $b) {
            return $b['date']->timestamp - $a['date']->timestamp;
        });

        // Format for frontend
        $formattedLogs = [];
        foreach ($rawLogs as $log) {
            $formattedLogs[] = [
                'id' => uniqid(),
                'action' => $log['action'],
                'description' => $log['description'],
                'changed_by_name' => $log['changed_by_name'],
                'formatted_date' => $log['date']->format('d M Y h:i A'),
                'file_url' => $log['file_url'],
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $formattedLogs
        ]);
    }    public function saveProducts(Request $request, $id)
    {
        if (!$request->has('products') || empty($request->products)) {
            return response()->json([
                'success' => false,
                'message' => 'Atleast add one product'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'products' => 'nullable|array',
            'products.*.product_id' => 'required|integer|exists:tbl_product,product_id',
            'products.*.quantity' => 'required|numeric|min:0.01',
            'products.*.price' => 'required|numeric|min:0',
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
            // Get existing products
            $existingItems = DB::table('tbl_enquiry_items')
                ->where('enquiry_id', $id)
                ->get()
                ->toArray();

            $existingMap = [];
            foreach ($existingItems as $item) {
                $existingMap[$item->product_id] = [
                    'quantity' => floatval($item->quantity),
                    'price' => floatval($item->price),
                ];
            }

            // Submitted products
            $newProducts = $request->input('products', []);
            $newMap = [];
            foreach ($newProducts as $prod) {
                $pId = intval($prod['product_id']);
                $newMap[$pId] = [
                    'quantity' => floatval($prod['quantity']),
                    'price' => floatval($prod['price']),
                ];
            }

            $added = [];
            $deleted = [];
            $updated = [];
            $addedIds = [];
            $removedIds = [];
            $updatedIds = [];

            // Check for added and updated
            foreach ($newMap as $pId => $newInfo) {
                $prodName = DB::table('tbl_product')->where('product_id', $pId)->value('prod_name') ?: 'Unknown';
                if (!isset($existingMap[$pId])) {
                    $added[] = "{$prodName} (Qty: {$newInfo['quantity']}, Price: ₹{$newInfo['price']})";
                    $addedIds[] = $pId;
                } else {
                    $oldInfo = $existingMap[$pId];
                    if ($oldInfo['quantity'] != $newInfo['quantity'] || $oldInfo['price'] != $newInfo['price']) {
                        $updated[] = "{$prodName} (Qty changed from {$oldInfo['quantity']} to {$newInfo['quantity']}, Price from ₹{$oldInfo['price']} to ₹{$newInfo['price']})";
                        $updatedIds[] = $pId;
                    }
                }
            }

            // Check for deleted
            foreach ($existingMap as $pId => $oldInfo) {
                if (!isset($newMap[$pId])) {
                    $prodName = DB::table('tbl_product')->where('product_id', $pId)->value('prod_name') ?: 'Unknown';
                    $deleted[] = "{$prodName} (Qty: {$oldInfo['quantity']})";
                    $removedIds[] = $pId;
                }
            }

            // If absolutely nothing changed, we do not need to rewrite or log anything.
            $hasChanges = (!empty($added) || !empty($deleted) || !empty($updated));

            if ($hasChanges) {
                // Delete existing products
                DB::table('tbl_enquiry_items')->where('enquiry_id', $id)->delete();

                // Insert new products
                if (!empty($newProducts)) {
                    $items = [];
                    foreach ($newProducts as $prod) {
                        $price = floatval($prod['price'] ?? 0);
                        $items[] = [
                            'enquiry_id' => $id,
                            'product_id' => $prod['product_id'],
                            'quantity'   => $prod['quantity'],
                            'price'      => $price,
                            'gst'        => floatval($prod['gst'] ?? 0),
                        ];
                    }
                    DB::table('tbl_enquiry_items')->insert($items);
                }

                // Log chronological activities
                $dealExists = DB::table('tbl_deal')->where('lead_id', $id)->exists();

                // Log individual added products
                foreach ($addedIds as $pId) {
                    $pName = DB::table('tbl_product')->where('product_id', $pId)->value('prod_name') ?: 'Unknown';
                    $reason = $dealExists
                        ? 'Product "' . $pName . '" added to the Deal.'
                        : 'Product "' . $pName . '" added.';
                    $this->logActivity($id, 'Product Added', $reason);
                }

                // Log individual removed products
                foreach ($removedIds as $pId) {
                    $pName = DB::table('tbl_product')->where('product_id', $pId)->value('prod_name') ?: 'Unknown';
                    $reason = $dealExists
                        ? 'Product "' . $pName . '" removed from the Deal.'
                        : 'Product "' . $pName . '" removed.';
                    $this->logActivity($id, 'Product Removed', $reason);
                }

                // Log product list update summary
                if (!empty($addedIds) || !empty($removedIds)) {
                    $oldProductNamesList = DB::table('tbl_product')
                        ->whereIn('product_id', array_keys($existingMap))
                        ->pluck('prod_name')
                        ->toArray();
                    $oldProductsStr = !empty($oldProductNamesList) ? implode(', ', $oldProductNamesList) : 'None';

                    $newProductNamesList = DB::table('tbl_product')
                        ->whereIn('product_id', array_keys($newMap))
                        ->pluck('prod_name')
                        ->toArray();
                    $newProductsStr = !empty($newProductNamesList) ? implode(', ', $newProductNamesList) : 'None';

                    $this->logActivity($id, 'Product List Updated', 'Product list updated from ' . $oldProductsStr . ' to ' . $newProductsStr . '.');
                }

                // Log Deal updated summary (if deal exists and items were added/updated)
                if ($dealExists && (!empty($addedIds) || !empty($updatedIds))) {
                    $this->logActivity($id, 'Deal Updated', 'Deal updated with additional products.');
                }

                // Log individual product quantity updates
                foreach ($newMap as $pId => $newInfo) {
                    if (isset($existingMap[$pId])) {
                        $oldInfo = $existingMap[$pId];
                        $oldQty = $oldInfo['quantity'];
                        $newQty = $newInfo['quantity'];
                        if (floatval($oldQty) !== floatval($newQty)) {
                            $oldQtyFormatted = (floatval($oldQty) == intval($oldQty)) ? intval($oldQty) : floatval($oldQty);
                            $newQtyFormatted = (floatval($newQty) == intval($newQty)) ? intval($newQty) : floatval($newQty);

                            $prodName = DB::table('tbl_product')->where('product_id', $pId)->value('prod_name') ?: 'Unknown';
                            $this->logActivity($id, 'Product Quantity Updated', 'Product "' . $prodName . '" quantity updated from ' . $oldQtyFormatted . ' → ' . $newQtyFormatted . '.');
                        }
                    }
                }

                DB::commit();
                return response()->json(['success' => true, 'message' => 'Products saved successfully.']);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'No changes were made.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lead saveProducts error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to save products.'], 500);
        }
    }

    private function logActivity(int $enquiryId, string $changeType, string $reason): void
    {
        DB::table('enquiry_history')->insert([
            'enquiry_id'  => $enquiryId,
            'change_type' => $changeType,
            'reason'      => $reason,
            'table_name'  => 'enquiry_manage',
            'changed_by'  => getCreatedBy(),
            'changed_at'  => now(),
        ]);
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
