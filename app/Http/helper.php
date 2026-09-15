<?php

use App\Models\CompanyModel;
use App\Models\EnquiryManageModel;
use App\Models\RoleModel;
use Illuminate\Support\Facades\Config;
use App\Models\Staffmodel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\WhatsappController;

if (!function_exists('get_html_data')) {
    function get_html_data()
    {
        return 'lang="en" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-menu-styles="light" loader="enable" data-default-header-styles="color" style="--primary-rgb: 20, 33, 54;" data-vertical-style="default"';
    }
}

if (!function_exists('get_session_name')) {
    function get_session_name()
    {
        $name = null;
        $role = Session::get('login_type');

        if ($role == 'Staff') {
            $staffId = Session::get('staff_id');
            if ($staffId) {
                $staff = DB::table('mst_staff')->where('staff_id', $staffId)->value('staff_name');
                if ($staff) {
                    $name = $staff;
                }
            }
        } elseif ($role == 'Company') {
            $companyId = Session::get('company_id');
            if ($companyId) {
                $company = DB::table('companies')->where('company_id', $companyId)->value('name');
                if ($company) {
                    $name = $company;
                }
            }
        } elseif ($role == 'Manager') {
            $adminId = Session::get('manager_id');
            if ($adminId) {
                $admin = DB::table('mst_manager')->where('manager_id', $adminId)->value('manager_name');
                if ($admin) {
                    $name = $admin;
                }
            }
        } elseif ($role == 'Super Admin') {
            $adminId = Session::get('admin_id');
            if ($adminId) {
                $admin = DB::table('super_admin')->where('admin_id', $adminId)->value('name');
                if ($admin) {
                    $name = $admin;
                }
            }
        }

        return $name;
    }
}

if (!function_exists('get_createdby_name')) {
    function get_createdby_name($created_by)
    {
        $name = null;

        $staff = DB::table('mst_staff')->where('staff_id', $created_by)->value('staff_name');
        if ($staff) {
            $name = $staff;
        } else {
            $company_id = $created_by < 0 ? abs($created_by) : $created_by;
            $company = DB::table('companies')->where('company_id', $company_id)->value('name');
            if ($company) {
                // $name = $company . '<br>' . 'SuperAdmin';
                $name = 'SuperAdmin';
            } else {
                $admin = DB::table('super_admin')->where('admin_id', $created_by)->value('name');
                if ($admin) {
                    $name = $admin;
                }
            }
        }

        return $name;
    }
}

if (!function_exists('get_role_name')) {
    function get_role_name($role_id)
    {
        $role = RoleModel::where('role_id', $role_id)->where('status', 0)->first();

        return $role->role_name;
    }
}

if (!function_exists('get_role_type')) {
    function get_role_type($role_id)
    {
        $role = RoleModel::where('role_id', $role_id)->where('status', 0)->first();

        return $role->type;
    }
}

if (!function_exists('getRoles')) {
    function getRoles($type)
    {
        $role = RoleModel::getByType($type);

        return $role;
    }
}

if (!function_exists('getCreatedBy')) {
    function getCreatedBy()
    {
        $created_by = null;
        $role = Session::get('login_type');
        if ($role == 'Staff') {
            $created_by = Session::get('staff_id');
        } elseif ($role == 'Company') {
            $created_by = '-' . Session::get('company_id');
        } elseif ($role == 'Manager') {
            $created_by = Session::get('manager_id');
        } elseif ($role == 'SuperAdmin') {
            $created_by = Session::get('admin_id');
        }

        return $created_by;
    }
}

if (!function_exists('getUpdatedBy')) {
    function getUpdatedBy()
    {
        $updated_by = null;
        $role = Session::get('login_type');
        if ($role == 'Staff') {
            $updated_by = Session::get('staff_id');
        } elseif ($role == 'Company') {
            $updated_by = '-' . Session::get('company_id');
        } elseif ($role == 'SuperAdmin') {
            $updated_by = Session::get('admin_id');
        }

        return $updated_by;
    }
}

if (!function_exists('get_company_id')) {
    function get_company_id()
    {
        $type = session('login_type');
        $pk = abs(getCreatedBy());

        if ($type === 'Super Admin') {
            return 0;
        }

        $map = [
            'Company' => [
                'table' => 'companies',
                'pk' => 'company_id',
                'column' => 'company_id',
            ],
            'Staff' => [
                'table' => 'mst_staff',
                'pk' => 'staff_id',
                'column' => 'company_id',
            ],
            'Manager' => [
                'table' => 'mst_manager',
                'pk' => 'manager_id',
                'column' => 'company_id',
            ],
        ];

        if (!isset($map[$type])) {
            return null;
        }

        $row = DB::table($map[$type]['table'])
            ->where($map[$type]['pk'], $pk)
            ->first();

        return $row->{$map[$type]['column']} ?? null;
    }
}

if (!function_exists('get_created_name')) {
    function get_created_name($pk, $type)
    {
        if ($type === 'Super Admin') {
            return 'Super Admin';
        }

        $map = [
            'Company' => [
                'table' => 'companies',
                'pk' => 'company_id',
                'column' => 'name',
            ],
            'Staff' => [
                'table' => 'mst_staff',
                'pk' => 'staff_id',
                'column' => 'staff_name',
            ],
            'Manager' => [
                'table' => 'mst_manager',
                'pk' => 'manager_id',
                'column' => 'manager_name',
            ],
        ];

        if (!isset($map[$type]) || empty($pk)) {
            return '-';
        }

        return DB::table($map[$type]['table'])
            ->where($map[$type]['pk'], abs($pk))
            ->value($map[$type]['column']) ?? '-';
    }
}

if (!function_exists('get_company_details')) {
    function get_company_details($company_id)
    {
        return CompanyModel::where('company_id', $company_id)->where('status', 0)->first();
    }
}

if (!function_exists('get_company_details_one')) {
    function get_company_details_one()
    {
        if (session('login_type') === 'Company') {
            return CompanyModel::find(session('company_id'));
        }

        if (session('login_type') === 'Staff') {
            $companyId = session('company_id');

            if ($companyId) {
                return CompanyModel::find($companyId);
            }

            // fallback if not in session
            $staff = Staffmodel::find(session('staff_id'));
            if ($staff && $staff->created_by) {
                return CompanyModel::find($staff->created_by);
            }
        }

        return CompanyModel::find(1);
    }
}
if (!function_exists('isStaffLogin')) {
    function isStaffLogin()
    {

        return strtolower(Session::get('login_type')) === 'staff'
            && Session::get('role_id') != 2;
    }
}

if (!function_exists('get_staff_details')) {
    function get_staff_details($staff_id)
    {
        return DB::table('mst_staff')
            ->where('staff_id', $staff_id)
            ->where('status', 0)
            ->first();
    }
}

if (!function_exists('format_inr')) {
    function format_inr($number)
    {
        if (!is_numeric($number)) {
            return $number;
        }
        return number_format($number, 2);
    }
}

if (!function_exists('formatIndianCurrency')) {
    function formatIndianCurrency($number)
    {
        if (!is_numeric($number)) {
            return $number;
        }
        $decimal = '';
        if (strpos($number, '.') !== false) {
            list($number, $decimal) = explode('.', sprintf('%.2f', $number));
            $decimal = '.' . $decimal;
        } else {
            $decimal = '.00';
        }

        $number = (int) $number;
        $len = strlen($number);
        if ($len <= 3) {
            return '₹' . $number . $decimal;
        }
        $last_three = substr($number, -3);
        $remaining = substr($number, 0, -3);
        $remaining = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $remaining);
        return '₹' . $remaining . ',' . $last_three . $decimal;
    }
}

if (!function_exists('get_lead_name')) {
    function get_lead_name($lead_id)
    {
        $lead = EnquiryManageModel::where('enquiry_id', $lead_id)->first();

        if (!$lead) {
            return [
                'enquiry_name' => '',
                'enquiry_no' => '',
                'company_name' => '',
                'contact_person_name' => '',
                'email' => '',
                'phone' => '',
                'source' => '',
            ];
        }

        return [
            'enquiry_name' => $lead->enquiry_name,
            'enquiry_no' => $lead->enquiry_no,
            'company_name' => $lead->company_name,
            'contact_person_name' => $lead->contact_person_name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'source' => $lead->source,
        ];
    }
}


if (!function_exists('getGstById')) {
    function getGstById($gst_id)
    {
        return DB::table('mst_gst')
            ->where('gst_id', $gst_id)
            ->where('status', 0)
            ->first()
            ->gst_no ?? 0;
    }
}

if (!function_exists('getAmountInWords')) {
    function getAmountInWords(float $number)
    {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(
            0 => '',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',
            6 => 'Six',
            7 => 'Seven',
            8 => 'Eight',
            9 => 'Nine',
            10 => 'Ten',
            11 => 'Eleven',
            12 => 'Twelve',
            13 => 'Thirteen',
            14 => 'Fourteen',
            15 => 'Fifteen',
            16 => 'Sixteen',
            17 => 'Seventeen',
            18 => 'Eighteen',
            19 => 'Nineteen',
            20 => 'Twenty',
            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety'
        );
        $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
        while ($i < $digits_length) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $counter = count($str);
                $suffix = $digits[$counter];

                $temp = '';
                if ($number < 21) {
                    $temp = $words[$number];
                } else {
                    $tens = (int) (floor($number / 10) * 10);
                    $units = (int) ($number % 10);
                    $temp = $words[$tens] . ($units ? ' ' . $words[$units] : '');
                }

                if ($suffix) {
                    $temp .= ' ' . $suffix;
                }

                $str[] = $temp;
            } else {
                $str[] = null;
            }
        }

        $str = array_filter(array_reverse($str));

        $Rupees = '';
        if (count($str) > 0) {
            $keys = array_keys($str);
            $last_key = end($keys);
            $parts = [];
            foreach ($str as $key => $part) {
                if ($key === $last_key && count($str) > 1) {
                    $is_unit = false;
                    foreach (['Hundred', 'Thousand', 'Lakh', 'Crore'] as $u) {
                        if (str_contains($part, $u)) {
                            $is_unit = true;
                            break;
                        }
                    }
                    if (!$is_unit) {
                        $parts[] = 'and ' . $part;
                    } else {
                        $parts[] = $part;
                    }
                } else {
                    $parts[] = $part;
                }
            }
            $Rupees = implode(' ', $parts);
        }

        $paise = '';
        if ($decimal > 0) {
            $dec_val = (int) $decimal;
            if ($dec_val < 21) {
                $paise = "and " . $words[$dec_val] . ' Paise';
            } else {
                $tens = (int) (floor($dec_val / 10) * 10);
                $units = (int) ($dec_val % 10);
                $paise = "and " . $words[$tens] . ($units ? ' ' . $words[$units] : '') . ' Paise';
            }
        }

        $result = '';
        if ($Rupees) {
            $result .= 'Rupees ' . $Rupees;
        }
        if ($paise) {
            $result .= ' ' . $paise;
        }
        if ($result) {
            return preg_replace('/\s+/', ' ', trim($result)) . ' Only';
        }
        return 'Zero Rupees Only';
    }
}

if (!function_exists('get_email_config')) {
    function get_email_config()
    {
        $row = DB::table('mail_config')->where('id', 1)->first();
        //   \Log::info('Mail config row:', (array) $row);


        if (!$row) {
            return null;
        }

        Config::set('mail.default', $row->MAIL_MAILER ?? 'smtp');

        Config::set('mail.mailers.smtp.transport', $row->MAIL_MAILER ?? 'smtp');
        Config::set('mail.mailers.smtp.host', $row->MAIL_HOST ?? '127.0.0.1');
        Config::set('mail.mailers.smtp.port', $row->MAIL_PORT ?? 2525);
        Config::set('mail.mailers.smtp.username', $row->MAIL_USERNAME ?? null);
        Config::set('mail.mailers.smtp.password', $row->MAIL_PASSWORD ?? null);
        Config::set('mail.mailers.smtp.encryption', $row->MAIL_ENCRYPTION ?? null);

        Config::set('mail.from.address', $row->MAIL_FROM_ADDRESS ?? 'hello@example.com');
        Config::set('mail.from.name', $row->MAIL_FROM_NAME ?? config('app.name'));

    }
}
if (!function_exists('set_email_config')) {
    function set_email_config(array $data)
    {

        $allowed = [
            'MAIL_MAILER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_ENCRYPTION',
            'MAIL_FROM_ADDRESS',
            'MAIL_FROM_NAME',
        ];

        $filtered = array_intersect_key($data, array_flip($allowed));

        if (empty($filtered)) {
            return false;
        }

        $exists = DB::table('mail_config')->where('id', 1)->exists();

        if ($exists) {
            DB::table('mail_config')->where('id', 1)->update($filtered);
        } else {
            $filtered['id'] = 1;
            DB::table('mail_config')->insert($filtered);
        }

        return true;
    }
}



if (!function_exists('canAdd')) {
    /**
     * Check if the currently logged-in user can add manual attendance.
     *
     * @return bool
     */
    function canAdd()
    {
        // 1. Get the login type from the session
        $loginType = Session::get('login_type');
        // 2. Company login can always add manual attendance
        if ($loginType === 'Company') {
            return true;
        }
        // 3. Staff login can only add manual attendance if they are marked as HRMS admin
        if ($loginType === 'Staff') {
            $staffId = Session::get('staff_id');
            $staff = Staffmodel::find($staffId); // Ensure the correct namespace for your model

            return $staff && (int) $staff->is_hrms_admin === 1;
        }
        // 4. Manager / others -> not allowed
        return false;
    }
}

if (!function_exists('sendWaTextMessage')) {
    function sendWaTextMessage(string $number, string $message)
    {
        $wa = new WhatsappController();
        return $wa->sendText($number, $message);
    };
};

if (!function_exists('sendWaMediaMessage')) {
    function sendWaMediaMessage(string $number, string $message, string $mediaUrl, string $filename)
    {
        $wa = new WhatsappController();
        return $wa->sendMedia($number, $message, $mediaUrl, $filename);
    };
};

if (!function_exists('getTooltip')) {
    /**
     * Get tooltip text for a given field_key from mst_tooltip.
     * Uses a static cache to avoid repeated DB hits within the same request.
     *
     * @param string $fieldKey
     * @return string|null
     */
    function getTooltip(string $fieldKey): ?string
    {
        static $cache = [];

        if (array_key_exists($fieldKey, $cache)) {
            return $cache[$fieldKey];
        }

        $tooltip = DB::table('mst_tooltip')
            ->where('field_key', $fieldKey)
            ->where('status', 0)
            ->value('tooltip_text');

        $cache[$fieldKey] = $tooltip;

        return $tooltip;
    }
}

if (!function_exists('applyRoleBasedCityFilter')) {
    function applyRoleBasedCityFilter($query, $tableName = 'mst_city')
    {
        $loginType = session('login_type');
        
        if ($loginType === 'Staff' && session('staff_id')) {
            $staff = \App\Models\Staffmodel::find(session('staff_id'));
            
            if ($staff && !empty($staff->staff_type)) {
                $indiaId = \Illuminate\Support\Facades\DB::table('mst_country')->where('country_name', 'India')->value('c_id') ?? 99;
                
                $cIdColumn = $tableName ? "{$tableName}.c_id" : 'c_id';

                if (strtolower($staff->staff_type) === 'domestic') {
                    $query->where($cIdColumn, $indiaId);
                } elseif (strtolower($staff->staff_type) === 'export' || strtolower($staff->staff_type) === 'international') {
                    $query->where($cIdColumn, '!=', $indiaId);
                }
            }
        }
        
        return $query;
    }
}
