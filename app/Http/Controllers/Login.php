<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\ResetPasswordMail;

class Login extends Controller
{
    /**
     * Handle the AJAX login request.
     */
    public function login_process(Request $request)
    {
        // dd($request->all());
        // 1. Validation
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        // 2. Define your Guard Logic
        $guards = [
            'superadmin' => [
                'table' => 'super_admin',
                'pk' => 'admin_id',
                'email_col' => 'email',
                'redirect' => 'main.dashboard',
                'session_fn' => 'createSuperAdminSession',
            ],
            'company' => [
                'table' => 'companies',
                'pk' => 'company_id',
                'email_col' => 'email',
                'redirect' => 'dashboard',
                'session_fn' => 'createCompanySession',
            ],
            'staff' => [
                'table' => 'mst_staff',
                'pk' => 'staff_id',
                'email_col' => 'staff_email',
                'redirect' => 'dashboard',
                'session_fn' => 'createStaffSession',
            ],
        ];

        // 3. Iterate through guards to find the user
        foreach ($guards as $type => $config) {
            // Query the specific table
            $user = DB::table($config['table'])
                ->where($config['email_col'], $request->email)
                ->first();

            // dd($user);
            if ($user && md5($request->password) === $user->password) {
                Session::regenerate();
                $this->{$config['session_fn']}($user);
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful! Redirecting...',
                    'redirect_url' => route($config['redirect']),
                ]);
            }
        }
        // dd($guards['staff']);
        // 4. If loop finishes with no match
        return response()->json([
            'success' => false,
            'message' => 'These credentials do not match our records.',
        ]);
    }
    private function createSuperAdminSession($user)
    {
        session([
            'admin_id' => $user->admin_id,
            'name' => $user->name,
            'user_name' => $user->user_name,
            'email' => $user->email,
            'login_type' => 'Super Admin',
        ]);
    }
    private function createCompanySession($user)
    {
        session([
            'company_id' => $user->company_id,
            'name' => $user->name,
            'email' => $user->email,
            'login_type' => 'Company',
        ]);
    }
    private function createStaffSession($user)
    {
        // Check for parent company efficiently
        $companyId = 1; // Default
        // if (isset($user->created_by) && $user->created_by) {
        //     $companyId = DB::table('companies')->where('company_id', $user->created_by)->value('company_id') ?? 1;
        // }
        session([
            'staff_id' => $user->staff_id,
            'staff_name' => $user->staff_name,
            'staff_email' => $user->staff_email,
            'role_id' => $user->role_id,
            'company_id' => get_company_details(1)->company_id,
            'login_type' => 'Staff',
        ]);
    }

    /**
     * Logout Method
     */
    public function logout()
    {
        Session::flush();
        Session::regenerateToken();
        return redirect()->route('login');
    }

    public function quick_search(Request $request)
    {
        $searchTerm = $request->input('query');
        $results = [
            'menus' => [],
            'submenus' => [],
            'leads' => [],
            'notes' => [],
            'calls' => [],
            'attachments' => [],
            'deals' => [],
            'customers' => [],
            'quotations' => [],
            'invoices' => [],
            'products' => [],
            'staff' => [],
            'campaigns' => [],
            'masters' => []
        ];
        $userType = session()->get('login_type');

        if (!$searchTerm) {
            return response()->json($results);
        }

        try {
            if ($userType == 'Company') {
                $companyId = session()->get('company_id');

                $menuIds = DB::table('company_permission')
                    ->where('company_id', $companyId)
                    ->pluck('c_menu_id')
                    ->unique()
                    ->toArray();

                $submenuIds = DB::table('company_permission')
                    ->where('company_id', $companyId)
                    ->whereNotNull('c_sub_menu_id')
                    ->pluck('c_sub_menu_id')
                    ->unique()
                    ->toArray();
            } elseif ($userType == 'Staff') {
                $roleId = session()->get('role_id');

                $menuIds = DB::table('role_menu_permissions')
                    ->where('role_id', $roleId)
                    ->pluck('menu_id')
                    ->unique()
                    ->toArray();

                $submenuIds = DB::table('role_menu_permissions')
                    ->where('role_id', $roleId)
                    ->whereNotNull('sub_menu_id')
                    ->pluck('sub_menu_id')
                    ->unique()
                    ->toArray();
            } else {
                return response()->json($results);
            }

            // 1. Menus & Submenus
            $menus = DB::table('company_menus')
                ->whereIn('c_menu_id', $menuIds)
                ->where('menu_title', 'like', '%' . $searchTerm . '%')
                ->orderBy('menu_order')
                ->where('is_submenu', 0)
                ->limit(5)
                ->get();

            $submenus = DB::table('company_submenus as s')
                ->join('company_menus as m', 's.c_menu_id', '=', 'm.c_menu_id')
                ->whereIn('s.c_sub_menu_id', $submenuIds)
                ->where('s.sub_menu_title', 'like', '%' . $searchTerm . '%')
                ->orderBy('s.submenu_order')
                ->limit(10)
                ->select('s.*', 'm.menu_title as parent_title')
                ->get();

            $excludedLocationTitles = ['country', 'state', 'city', 'countries', 'states', 'cities', 'location', 'locations'];

            foreach ($menus as $menu) {
                if (in_array(strtolower(trim($menu->menu_title)), $excludedLocationTitles)) {
                    continue;
                }
                $results['menus'][] = [
                    'title' => $menu->menu_title,
                    'icon' => $menu->menu_icon ?? 'bx bx-cube',
                    'url' => ($menu->menu_route && Route::has($menu->menu_route)) ? route($menu->menu_route) : '#',
                    'type' => 'menu',
                ];
            }

            foreach ($submenus as $submenu) {
                if (in_array(strtolower(trim($submenu->sub_menu_title)), $excludedLocationTitles)) {
                    continue;
                }
                $results['submenus'][] = [
                    'title' => $submenu->sub_menu_title,
                    'icon' => $submenu->sub_menu_icon ?? 'bx bx-cube',
                    'url' => ($submenu->sub_menu_route && Route::has($submenu->sub_menu_route)) ? route($submenu->sub_menu_route) : '#',
                    'parent' => $submenu->parent_title ?? 'Unknown',
                    'type' => 'submenu',
                ];
            }

            // 2. CRM Notes & Activities
            $notesQuery = DB::table('tbl_crm_notes as n')
                ->where('n.status', 0)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('n.note', 'like', '%' . $searchTerm . '%')
                      ->orWhere('n.title', 'like', '%' . $searchTerm . '%');
                });

            if ($userType === 'Staff' && session('staff_id')) {
                $staffId = session('staff_id');
                $notesQuery->where(function ($w) use ($staffId) {
                    $w->where('n.assigned_to', $staffId)
                      ->orWhere('n.created_by', $staffId);
                });
            }

            $notes = $notesQuery->orderBy('n.id', 'desc')->limit(8)->get();

            foreach ($notes as $note) {
                $parentName = 'Unknown Reference';
                $url = '#';

                if ($note->reference_type === 'lead') {
                    $lead = DB::table('enquiry_manage')
                        ->where('enquiry_id', $note->reference_id)
                        ->select('enquiry_name', 'company_name', 'enquiry_no')
                        ->first();

                    if ($lead) {
                        $leadName = $lead->enquiry_name ?: ($lead->company_name ?: ('Lead #' . $lead->enquiry_no));
                        $parentName = 'Lead: ' . $leadName;
                        if (Route::has('lead.details')) {
                            $url = route('lead.details', encrypt($note->reference_id));
                        }
                    }
                } elseif ($note->reference_type === 'deal') {
                    $deal = DB::table('tbl_deal')
                        ->where('deal_id', $note->reference_id)
                        ->select('deal_name', 'iwo_no')
                        ->first();

                    if ($deal) {
                        $dealName = $deal->deal_name ?: ('Deal #' . $deal->iwo_no);
                        $parentName = 'Deal: ' . $dealName;
                        if (Route::has('deal.details')) {
                            $url = route('deal.details', encrypt($note->reference_id));
                        }
                    }
                }

                $snippetText = trim(strip_tags($note->note ?? ''));
                if (mb_strlen($snippetText) > 90) {
                    $snippetText = mb_substr($snippetText, 0, 90) . '...';
                }

                $results['notes'][] = [
                    'title' => $parentName,
                    'snippet' => $snippetText,
                    'note_title' => $note->title ?? 'Note',
                    'url' => $url,
                    'type' => 'note',
                    'icon' => 'fa-regular fa-note-sticky',
                ];
            }

            // 2b. CRM Calls & Meetings
            $callsQuery = DB::table('tbl_crm_calls as c')
                ->where('c.status', 0)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('c.subject', 'like', '%' . $searchTerm . '%')
                      ->orWhere('c.call_agenda', 'like', '%' . $searchTerm . '%')
                      ->orWhere('c.call_purpose', 'like', '%' . $searchTerm . '%')
                      ->orWhere('c.call_result', 'like', '%' . $searchTerm . '%')
                      ->orWhere('c.lead_name', 'like', '%' . $searchTerm . '%');
                });

            if ($userType === 'Staff' && session('staff_id')) {
                $staffId = session('staff_id');
                $callsQuery->where(function ($w) use ($staffId) {
                    $w->where('c.assigned_to', $staffId)
                      ->orWhere('c.created_by', $staffId);
                });
            }

            $calls = $callsQuery->orderBy('c.id', 'desc')->limit(5)->get();

            foreach ($calls as $call) {
                $url = '#';
                if ($call->reference_type === 'lead' && Route::has('lead.details')) {
                    $url = route('lead.details', encrypt($call->reference_id));
                } elseif ($call->reference_type === 'deal' && Route::has('deal.details')) {
                    $url = route('deal.details', encrypt($call->reference_id));
                }

                $parentText = $call->lead_name ? ('Lead: ' . $call->lead_name) : 'CRM Call';
                $snippet = trim(strip_tags($call->call_agenda ?: ($call->call_purpose ?: ($call->call_result ?: ''))));
                if (mb_strlen($snippet) > 80) {
                    $snippet = mb_substr($snippet, 0, 80) . '...';
                }

                $results['calls'][] = [
                    'title' => $parentText,
                    'snippet' => $snippet ?: 'No agenda details',
                    'note_title' => $call->subject ?: 'Call',
                    'url' => $url,
                    'type' => 'call',
                    'icon' => 'fa-solid fa-phone-volume',
                ];
            }

            // 2c. CRM Attachments
            $attachmentsQuery = DB::table('tbl_crm_attachments as a')
                ->where(function ($w) {
                    $w->where('a.status', 0)->orWhereNull('a.status');
                })
                ->where(function ($q) use ($searchTerm) {
                    $q->where('a.file_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('a.file_path', 'like', '%' . $searchTerm . '%');
                });

            if ($userType === 'Staff' && session('staff_id')) {
                $staffId = session('staff_id');
                $attachmentsQuery->where(function ($w) use ($staffId) {
                    $w->where('a.created_by', $staffId);
                });
            }

            $attachments = $attachmentsQuery->orderBy('a.id', 'desc')->limit(5)->get();

            foreach ($attachments as $att) {
                $parentName = 'Unknown Attachment';
                $url = '#';

                if ($att->reference_type === 'lead') {
                    $lead = DB::table('enquiry_manage')
                        ->where('enquiry_id', $att->reference_id)
                        ->select('enquiry_name', 'company_name', 'enquiry_no')
                        ->first();

                    if ($lead) {
                        $leadName = $lead->enquiry_name ?: ($lead->company_name ?: ('Lead #' . $lead->enquiry_no));
                        $parentName = 'Lead: ' . $leadName;
                        if (Route::has('lead.details')) {
                            $url = route('lead.details', encrypt($att->reference_id));
                        }
                    }
                } elseif ($att->reference_type === 'deal') {
                    $deal = DB::table('tbl_deal')
                        ->where('deal_id', $att->reference_id)
                        ->select('deal_name', 'iwo_no')
                        ->first();

                    if ($deal) {
                        $dealName = $deal->deal_name ?: ('Deal #' . $deal->iwo_no);
                        $parentName = 'Deal: ' . $dealName;
                        if (Route::has('deal.details')) {
                            $url = route('deal.details', encrypt($att->reference_id));
                        }
                    }
                }

                $results['attachments'][] = [
                    'title' => $parentName,
                    'snippet' => $att->file_name ?? '',
                    'note_title' => 'Attachment',
                    'url' => $url,
                    'type' => 'attachment',
                    'icon' => 'fa-solid fa-paperclip',
                ];
            }

            // 3. Leads Search
            $leadsQuery = DB::table('enquiry_manage')
                ->where('status', 0)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('enquiry_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('company_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('contact_person_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%')
                      ->orWhere('enquiry_no', 'like', '%' . $searchTerm . '%')
                      ->orWhere('notes', 'like', '%' . $searchTerm . '%');
                });

            if ($userType === 'Staff' && session('staff_id')) {
                $staffId = session('staff_id');
                $leadsQuery->where(function ($q) use ($staffId) {
                    $q->where('assigned_to', $staffId)
                      ->orWhere('created_by', $staffId)
                      ->orWhereIn('enquiry_id', function ($sub) use ($staffId) {
                          $sub->select('reference_id')
                              ->from('tbl_crm_notes')
                              ->where('reference_type', 'lead')
                              ->where('status', 0)
                              ->where(function ($w) use ($staffId) {
                                  $w->where('assigned_to', $staffId)
                                    ->orWhere('created_by', $staffId);
                              });
                      });
                });
            }

            $leads = $leadsQuery->orderBy('enquiry_id', 'desc')->limit(6)->get();

            foreach ($leads as $lead) {
                $displayName = $lead->enquiry_name ?: ($lead->company_name ?: ('Lead #' . $lead->enquiry_no));
                $subInfo = [];
                if (!empty($lead->company_name) && $lead->company_name !== $displayName) {
                    $subInfo[] = $lead->company_name;
                }
                if (!empty($lead->phone)) {
                    $subInfo[] = $lead->phone;
                }
                if (!empty($lead->email)) {
                    $subInfo[] = $lead->email;
                }
                $subText = !empty($subInfo) ? implode(' | ', $subInfo) : ('Enquiry #' . $lead->enquiry_no);

                $url = Route::has('lead.details') ? route('lead.details', encrypt($lead->enquiry_id)) : '#';

                $results['leads'][] = [
                    'title' => $displayName,
                    'subtitle' => $subText,
                    'url' => $url,
                    'type' => 'lead',
                    'icon' => 'fa-solid fa-user-tie',
                ];
            }

            // 4. Deals Search
            $dealsQuery = DB::table('tbl_deal')
                ->where('status', 0)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('deal_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('iwo_no', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%');
                });

            if ($userType === 'Staff' && session('staff_id')) {
                $staffId = session('staff_id');
                $dealsQuery->where(function ($w) use ($staffId) {
                    $w->where('assigned_to', $staffId)
                      ->orWhere('created_by', $staffId);
                });
            }

            $deals = $dealsQuery->orderBy('deal_id', 'desc')->limit(5)->get();

            foreach ($deals as $deal) {
                $displayName = $deal->deal_name ?: ('Deal #' . $deal->iwo_no);
                $subInfo = [];
                if (!empty($deal->iwo_no)) {
                    $subInfo[] = 'IWO: ' . $deal->iwo_no;
                }
                if (!empty($deal->amount)) {
                    $subInfo[] = '₹' . number_format($deal->amount, 2);
                }
                $subText = !empty($subInfo) ? implode(' | ', $subInfo) : '';

                $url = Route::has('deal.details') ? route('deal.details', encrypt($deal->deal_id)) : '#';

                $results['deals'][] = [
                    'title' => $displayName,
                    'subtitle' => $subText,
                    'url' => $url,
                    'type' => 'deal',
                    'icon' => 'fa-solid fa-briefcase',
                ];
            }

            // 5. Customers Search
            $customers = DB::table('tbl_customer')
                ->where('status', 0)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('company_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('contact_person', 'like', '%' . $searchTerm . '%')
                      ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%')
                      ->orWhere('address', 'like', '%' . $searchTerm . '%');
                })
                ->orderBy('customer_id', 'desc')
                ->limit(5)
                ->get();

            foreach ($customers as $customer) {
                $displayName = $customer->company_name ?: ($customer->contact_person ?: ('Customer #' . $customer->customer_id));
                $subInfo = [];
                if (!empty($customer->contact_person) && $customer->contact_person !== $displayName) {
                    $subInfo[] = $customer->contact_person;
                }
                if (!empty($customer->phone)) {
                    $subInfo[] = $customer->phone;
                }
                if (!empty($customer->email)) {
                    $subInfo[] = $customer->email;
                }
                $subText = !empty($subInfo) ? implode(' | ', $subInfo) : '';

                $url = Route::has('customer.index') ? route('customer.index') : '#';

                $results['customers'][] = [
                    'title' => $displayName,
                    'subtitle' => $subText,
                    'url' => $url,
                    'type' => 'customer',
                    'icon' => 'fa-solid fa-building',
                ];
            }

            // 6. Quotations Search
            $quotationsQuery = DB::table('tbl_quotation')
                ->where('status', '!=', 2)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('quotation_no', 'like', '%' . $searchTerm . '%')
                      ->orWhere('customer_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('contact_person_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('subject', 'like', '%' . $searchTerm . '%')
                      ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%');
                });

            if ($userType === 'Staff' && session('staff_id')) {
                $staffId = session('staff_id');
                $quotationsQuery->where(function ($w) use ($staffId) {
                    $w->where('assigned_to', $staffId)
                      ->orWhere('created_by', $staffId);
                });
            }

            $quotations = $quotationsQuery->orderBy('quotation_id', 'desc')->limit(5)->get();

            foreach ($quotations as $quotation) {
                $displayName = 'Quotation #' . $quotation->quotation_no;
                $subInfo = [];
                if (!empty($quotation->customer_name)) {
                    $subInfo[] = $quotation->customer_name;
                }
                if (!empty($quotation->grand_total)) {
                    $subInfo[] = '₹' . number_format($quotation->grand_total, 2);
                }
                $subText = !empty($subInfo) ? implode(' | ', $subInfo) : '';

                $url = Route::has('quotation.index') ? route('quotation.index') : '#';

                $results['quotations'][] = [
                    'title' => $displayName,
                    'subtitle' => $subText,
                    'url' => $url,
                    'type' => 'quotation',
                    'icon' => 'fa-solid fa-file-signature',
                ];
            }

            // 7. Invoices (Main & Proforma)
            $mainInvoices = DB::table('tbl_main_invoice')
                ->where(function ($q) use ($searchTerm) {
                    $q->where('invoice_no', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%')
                      ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                })
                ->orderBy('main_invoice_id', 'desc')
                ->limit(3)
                ->get();

            foreach ($mainInvoices as $inv) {
                $subInfo = ['Main Invoice'];
                if (!empty($inv->grand_total)) {
                    $subInfo[] = '₹' . number_format($inv->grand_total, 2);
                }
                $url = Route::has('main_invoice.edit') ? route('main_invoice.edit', $inv->main_invoice_id) : (Route::has('main_invoice.index') ? route('main_invoice.index') : '#');

                $results['invoices'][] = [
                    'title' => 'Invoice #' . $inv->invoice_no,
                    'subtitle' => implode(' | ', $subInfo),
                    'url' => $url,
                    'type' => 'invoice',
                    'icon' => 'fa-solid fa-file-invoice-dollar',
                ];
            }

            $piInvoices = DB::table('tbl_performa_invoice')
                ->where(function ($q) use ($searchTerm) {
                    $q->where('invoice_no', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%')
                      ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                      ->orWhere('buyer_order_no', 'like', '%' . $searchTerm . '%');
                })
                ->orderBy('invoice_id', 'desc')
                ->limit(3)
                ->get();

            foreach ($piInvoices as $pi) {
                $subInfo = ['Proforma Invoice'];
                if (!empty($pi->grand_total)) {
                    $subInfo[] = '₹' . number_format($pi->grand_total, 2);
                }
                $url = Route::has('performa_invoice.edit') ? route('performa_invoice.edit', $pi->invoice_id) : (Route::has('performa_invoice.index') ? route('performa_invoice.index') : '#');

                $results['invoices'][] = [
                    'title' => 'PI #' . $pi->invoice_no,
                    'subtitle' => implode(' | ', $subInfo),
                    'url' => $url,
                    'type' => 'invoice',
                    'icon' => 'fa-solid fa-file-invoice',
                ];
            }

            // 8. Products Search
            $products = DB::table('tbl_product')
                ->where('status', 0)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('prod_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('prod_code', 'like', '%' . $searchTerm . '%')
                      ->orWhere('prod_desc', 'like', '%' . $searchTerm . '%');
                })
                ->orderBy('product_id', 'desc')
                ->limit(5)
                ->get();

            foreach ($products as $prod) {
                $displayName = $prod->prod_name ?: ('Product #' . $prod->prod_code);
                $subInfo = [];
                if (!empty($prod->prod_code)) {
                    $subInfo[] = 'Code: ' . $prod->prod_code;
                }
                if (!empty($prod->prod_amount)) {
                    $subInfo[] = '₹' . number_format($prod->prod_amount, 2);
                }
                $subText = !empty($subInfo) ? implode(' | ', $subInfo) : '';

                $url = Route::has('products.edit') ? route('products.edit', $prod->product_id) : (Route::has('products.index') ? route('products.index') : '#');

                $results['products'][] = [
                    'title' => $displayName,
                    'subtitle' => $subText,
                    'url' => $url,
                    'type' => 'product',
                    'icon' => 'fa-solid fa-box-open',
                ];
            }

            // 9. Staff Search
            $staffMembers = DB::table('mst_staff')
                ->where('status', 0)
                ->where(function ($q) use ($searchTerm) {
                    $q->where('staff_name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('staff_code', 'like', '%' . $searchTerm . '%')
                      ->orWhere('staff_email', 'like', '%' . $searchTerm . '%')
                      ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                      ->orWhere('position', 'like', '%' . $searchTerm . '%')
                      ->orWhere('department', 'like', '%' . $searchTerm . '%');
                })
                ->orderBy('staff_id', 'desc')
                ->limit(4)
                ->get();

            foreach ($staffMembers as $stf) {
                $displayName = $stf->staff_name ?: ('Staff #' . $stf->staff_code);
                $subInfo = [];
                if (!empty($stf->position)) {
                    $subInfo[] = $stf->position;
                }
                if (!empty($stf->phone)) {
                    $subInfo[] = $stf->phone;
                }
                if (!empty($stf->staff_email)) {
                    $subInfo[] = $stf->staff_email;
                }
                $subText = !empty($subInfo) ? implode(' | ', $subInfo) : '';

                $url = Route::has('staff.index') ? route('staff.index') : '#';

                $results['staff'][] = [
                    'title' => $displayName,
                    'subtitle' => $subText,
                    'url' => $url,
                    'type' => 'staff',
                    'icon' => 'fa-solid fa-id-badge',
                ];
            }

            $dynamicResults = $this->dynamicMasterAndModuleSearch($searchTerm, $userType, session()->get('company_id') ?: 1);
            $results['campaigns'] = $dynamicResults['campaigns'];
            $results['masters'] = $dynamicResults['masters'];

            foreach ($results as $category => &$items) {
                // Filter out items without valid redirect URL
                $items = array_values(array_filter($items, function ($item) {
                    return isset($item['url']) && $item['url'] !== '#' && !empty($item['url']);
                }));
                foreach ($items as &$item) {
                    $separator = str_contains($item['url'], '?') ? '&' : '?';
                    $item['url'] .= $separator . 'search=' . urlencode($searchTerm);
                }
            }
            unset($items, $item);

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Search failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    protected function dynamicMasterAndModuleSearch($searchTerm, $userType, $companyId)
    {
        $masterResults = [];
        $campaignResults = [];

        $tables = Cache::remember('crm_searchable_tables_list_v2', 3600, function () {
            $allTables = array_map(function ($t) {
                return array_values((array) $t)[0];
            }, DB::select('SHOW TABLES'));

            $ignore = [
                'cache', 'cache_locks', 'migrations', 'password_reset_tokens',
                'failed_jobs', 'sessions', 'wa_config', 'mail_config', 'pusher_config',
                'company_permission', 'role_menu_permissions', 'super_admin', 'users', 'companies',
                'enquiry_manage', 'tbl_crm_notes', 'tbl_crm_calls', 'tbl_crm_attachments', 'tbl_deal', 'tbl_customer',
                'tbl_quotation', 'tbl_main_invoice', 'tbl_performa_invoice', 'tbl_product',
                'mst_staff', 'company_menus', 'company_submenus',
                'mst_country', 'mst_state', 'mst_city'
            ];

            return array_values(array_filter($allTables, function ($t) use ($ignore) {
                return !in_array($t, $ignore) &&
                    !str_contains($t, '_log') &&
                    !str_contains($t, '_history') &&
                    !str_contains($t, '_item') &&
                    !str_contains($t, '_mapping') &&
                    !str_contains($t, '_map');
            }));
        });

        foreach ($tables as $table) {
            try {
                $columns = Cache::remember('crm_table_cols_' . $table, 3600, function () use ($table) {
                    return DB::getSchemaBuilder()->getColumnListing($table);
                });

                $searchCols = [];
                foreach ($columns as $col) {
                    if (
                        str_ends_with($col, '_id') ||
                        str_ends_with($col, '_at') ||
                        str_ends_with($col, '_by') ||
                        in_array($col, [
                            'id', 'status', 'deleted_at', 'sort_order', 'order_no',
                            'menu_order', 'is_won', 'is_lost', 'color', 'password',
                            'token', 'remember_token', 'company_id', 'created_type'
                        ])
                    ) {
                        continue;
                    }
                    if (
                        str_contains($col, 'name') || str_contains($col, 'title') ||
                        str_contains($col, 'no') || str_contains($col, 'code') ||
                        str_contains($col, 'type') || str_contains($col, 'terms') ||
                        str_contains($col, 'unit') || str_contains($col, 'subject') ||
                        str_contains($col, 'desc') || str_contains($col, 'gst') ||
                        str_contains($col, 'vat') || str_contains($col, 'stage') ||
                        str_contains($col, 'source')
                    ) {
                        $searchCols[] = $col;
                    }
                }

                if (empty($searchCols)) {
                    foreach ($columns as $col) {
                        if (!str_ends_with($col, '_id') && $col !== 'id' && $col !== 'status') {
                            $searchCols[] = $col;
                            break;
                        }
                    }
                }

                if (empty($searchCols)) {
                    continue;
                }

                $humanName = $this->formatHumanTableName($table);
                $matchByTable = stripos($humanName, $searchTerm) !== false;

                $query = DB::table($table);

                if (!$matchByTable) {
                    $query->where(function ($q) use ($searchCols, $searchTerm) {
                        foreach ($searchCols as $col) {
                            $q->orWhere($col, 'like', '%' . $searchTerm . '%');
                        }
                    });
                }

                if (in_array('status', $columns)) {
                    $query->where('status', 0);
                }

                if ($userType === 'Company' && in_array('company_id', $columns) && $companyId) {
                    $query->where(function ($w) use ($companyId) {
                        $w->where('company_id', $companyId)
                          ->orWhereNull('company_id')
                          ->orWhere('company_id', 0);
                    });
                }

                $rows = $query->limit(3)->get();

                if ($rows->isEmpty()) {
                    continue;
                }

                $url = $this->getDynamicTableRoute($table);

                foreach ($rows as $row) {
                    $titleVal = '';
                    foreach ($searchCols as $col) {
                        if (isset($row->$col) && $row->$col !== '') {
                            $titleVal = $row->$col;
                            break;
                        }
                    }
                    if ($titleVal === '') {
                        $titleVal = $humanName;
                    }
                    if ($table === 'mst_gst' && is_numeric($titleVal)) {
                        $titleVal = 'GST ' . $titleVal . '%';
                    }
                    if ($table === 'mst_vat' && is_numeric($titleVal)) {
                        $titleVal = 'VAT ' . $titleVal . '%';
                    }

                    $cleanTitle = Str::limit(trim(preg_replace('/\s+/', ' ', (string) $titleVal)), 85);

                    $masterIcon = 'fa fa-cogs';
                    if (in_array($table, ['mst_gst', 'mst_vat'])) {
                        $masterIcon = 'fa fa-percent';
                    } elseif ($table === 'tbl_campaign') {
                        $masterIcon = 'fa fa-bullhorn';
                    } elseif (in_array($table, ['tbl_terms', 'tbl_unit', 'document_category'])) {
                        $masterIcon = 'fa fa-file-lines';
                    }

                    $item = [
                        'title' => $cleanTitle,
                        'subtitle' => $humanName,
                        'url' => $url,
                        'type' => 'master',
                        'icon' => $masterIcon,
                    ];

                    if ($table === 'tbl_campaign') {
                        $campaignResults[] = $item;
                    } else {
                        $masterResults[] = $item;
                    }
                }
            } catch (\Exception $ex) {
                continue;
            }
        }

        return [
            'campaigns' => $campaignResults,
            'masters' => $masterResults,
        ];
    }

    protected function formatHumanTableName($table)
    {
        $clean = preg_replace('/^(mst_|tbl_)/', '', $table);
        $clean = str_replace('_', ' ', $clean);
        if (strtolower($clean) === 'gst') return 'GST Master';
        if (strtolower($clean) === 'vat') return 'VAT Master';
        if (strtolower($clean) === 'hsn gst mapping') return 'HSN-GST Mapping';
        if (strtolower($clean) === 'crm calls') return 'CRM Calls';
        if (strtolower($clean) === 'crm notes') return 'CRM Notes';
        return ucwords($clean) . (str_starts_with($table, 'mst_') ? ' Master' : '');
    }

    protected function getDynamicTableRoute($table)
    {
        $map = [
            'mst_gst' => 'gst.index',
            'mst_vat' => 'vat.index',
            'lead_status' => 'leadstatus.index',
            'mst_lead_source' => 'leadsource.index',
            'mst_deal_stage' => 'dealstage.index',
            'tbl_product_category' => 'productcategory.index',
            'mst_industry_type' => 'industry_type.index',
            'mst_campaign_type' => 'campaign_type.index',
            'tbl_campaign' => 'campaign.index',
            'tbl_terms' => 'terms.index',
            'tbl_unit' => 'unit.index',
            'mst_city' => 'city.index',
            'mst_state' => 'state.index',
            'mst_country' => 'country.index',
            'document_category' => 'documents.index',
            'tbl_raw_material' => 'rawmaterial.index',
        ];

        if (isset($map[$table]) && Route::has($map[$table])) {
            return route($map[$table]);
        }

        $base = preg_replace('/^(mst_|tbl_)/', '', $table);
        $baseNoUnderscore = str_replace('_', '', $base);

        if (Route::has($base . '.index')) {
            return route($base . '.index');
        }
        if (Route::has($baseNoUnderscore . '.index')) {
            return route($baseNoUnderscore . '.index');
        }
        if (Route::has($base)) {
            return route($base);
        }

        $submenu = DB::table('company_submenus')
            ->where('sub_menu_route', 'like', '%' . $base . '%')
            ->orWhere('sub_menu_route', 'like', '%' . $baseNoUnderscore . '%')
            ->first();

        if ($submenu && !empty($submenu->sub_menu_route) && Route::has($submenu->sub_menu_route)) {
            return route($submenu->sub_menu_route);
        }

        return '#';
    }

    public function change_password_view()
    {
        $page_title = 'Change Password';
        $page_name = 'Change Password';
        return view('company.change_password', compact('page_title', 'page_name'));
    }

    public function update_password(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $loginType = session('login_type');
        $tableMap = [
            'Super Admin' => ['table' => 'super_admin', 'pk' => 'admin_id', 'session_key' => 'admin_id'],
            'Company' => ['table' => 'companies', 'pk' => 'company_id', 'session_key' => 'company_id'],
            'Staff' => ['table' => 'mst_staff', 'pk' => 'staff_id', 'session_key' => 'staff_id'],
        ];

        if (!isset($tableMap[$loginType])) {
            // We still return 200 to avoid console errors, but with success false
            return response()->json(['success' => false, 'message' => 'Unauthorized action.']);
        }

        $config = $tableMap[$loginType];
        $userId = session($config['session_key']);

        $user = DB::table($config['table'])->where($config['pk'], $userId)->first();

        // Check current password
        if (md5($request->current_password) !== $user->password) {
            // Changed status code to 200 so it doesn't show red in Network/Console
            return response()->json(['success' => false, 'message' => 'Current password does not match.']);
        }

        // Update password
        DB::table($config['table'])
            ->where($config['pk'], $userId)
            ->update(['password' => md5($request->new_password)]);

        return response()->json(['success' => true, 'message' => 'Password updated successfully!']);
    }

    public function forgot_password_process(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $guards = [
            'superadmin' => ['table' => 'super_admin', 'email_col' => 'email'],
            'company' => ['table' => 'companies', 'email_col' => 'email'],
            'staff' => ['table' => 'mst_staff', 'email_col' => 'staff_email'],
        ];

        $userFound = false;
        $userEmail = null;

        foreach ($guards as $type => $config) {
            $user = DB::table($config['table'])
                ->where($config['email_col'], $request->email)
                ->first();

            if ($user) {
                $userFound = true;
                $userEmail = $request->email;
                break;
            }
        }

        if (!$userFound) {
            return response()->json([
                'success' => false,
                'message' => 'No user found with this email address.',
            ]);
        }

        // Generate token and save to password_reset_tokens
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $userEmail],
            [
                'email' => $userEmail,
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Send Email
        $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($userEmail));
        
        try {
            Mail::to($userEmail)->send(new ResetPasswordMail($resetLink));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please check your mail configuration.',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset link has been sent to your email.',
        ]);
    }

    public function show_reset_password(Request $request, $token)
    {
        $email = $request->query('email');
        return view('reset-password', compact('token', 'email'));
    }

    public function reset_password_process(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetToken) {
            return back()->withErrors(['email' => 'Invalid or expired password reset token.']);
        }

        $guards = [
            'superadmin' => ['table' => 'super_admin', 'email_col' => 'email'],
            'company' => ['table' => 'companies', 'email_col' => 'email'],
            'staff' => ['table' => 'mst_staff', 'email_col' => 'staff_email'],
        ];

        foreach ($guards as $type => $config) {
            $user = DB::table($config['table'])
                ->where($config['email_col'], $request->email)
                ->first();

            if ($user) {
                DB::table($config['table'])
                    ->where($config['email_col'], $request->email)
                    ->update(['password' => md5($request->password)]);
                
                DB::table('password_reset_tokens')->where('email', $request->email)->delete();
                
                return redirect()->route('login')->with('success', 'Password has been successfully reset. You can now login.');
            }
        }

        return back()->withErrors(['email' => 'User not found.']);
    }

}
