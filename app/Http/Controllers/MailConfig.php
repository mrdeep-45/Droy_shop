<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MailConfig extends Controller
{
    public function index()
    {
        $page_title = 'Mail Config';
        $page_name = 'Mail Config';
        $data = DB::table('mail_config')->where('id', 1)->first();
        if ($data && !empty($data->MAIL_PASSWORD)) {
            try {
                $data->MAIL_PASSWORD = ($data->MAIL_PASSWORD);
            } catch (\Throwable $th) {
                $data->MAIL_PASSWORD = '';
            }
        }

        if ($data && !empty($data->indiamart_expire_date)) {
            try {
                $data->indiamart_expire_date = \Carbon\Carbon::parse($data->indiamart_expire_date)->format('d-m-Y h:i A');
            } catch (\Throwable $th) {
                $data->indiamart_expire_date = '';
            }
        }

        $pusher = DB::table('pusher_config')->where('id', 1)->first();
        if ($pusher && !empty($pusher->PUSHER_SECRET)) {
            try {
                $pusher->PUSHER_SECRET = ($pusher->PUSHER_SECRET);

            } catch (\Throwable $th) {
                $pusher->PUSHER_SECRET = '';

            }
        }

        return view('company.setting.mail_config', compact('page_title', 'page_name', 'data', 'pusher'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'MAIL_MAILER' => 'nullable|string',
            'MAIL_HOST' => 'nullable|string',
            'MAIL_PORT' => 'nullable|integer',
            'MAIL_USERNAME' => 'nullable|string',
            'MAIL_PASSWORD' => 'nullable|string',
            'MAIL_ENCRYPTION' => 'nullable|string',
            'MAIL_FROM_ADDRESS' => 'nullable|string',
            'MAIL_FROM_NAME' => 'nullable|string',
            'indiamart_expire_date' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    $formats = ['d-m-Y h:i A', 'd-m-Y h:i a', 'd-m-Y g:i A', 'd-m-Y g:i a', 'd-m-Y H:i:s', 'd-m-Y H:i', 'Y-m-d H:i:s', 'Y-m-d H:i'];
                    foreach ($formats as $format) {
                        try {
                            if (\Carbon\Carbon::createFromFormat($format, $value) !== false) {
                                return;
                            }
                        } catch (\Throwable $e) {
                        }
                    }
                    $fail('The ' . str_replace('_', ' ', $attribute) . ' must be a valid date format (e.g. d-m-Y h:i A).');
                }
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'MAIL_MAILER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_ENCRYPTION',
            'MAIL_FROM_ADDRESS',
            'MAIL_FROM_NAME',
            'indiamart_expire_date',
        ]);

        if (!empty($data['MAIL_PASSWORD'])) {
            $data['MAIL_PASSWORD'] = ($data['MAIL_PASSWORD']);
        }

        if (!empty($data['indiamart_expire_date'])) {
            $data['indiamart_expire_date'] = \Carbon\Carbon::parse($data['indiamart_expire_date'])->format('Y-m-d H:i:s');
        }


        $existing = DB::table('mail_config')->where('id', 1)->first();

        if ($existing) {
            DB::table('mail_config')->where('id', 1)->update($data);
            $message = 'Mail Config updated successfully';
        } else {
            $data['id'] = 1;
            DB::table('mail_config')->insert($data);
            $message = 'Mail Config inserted successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function pusherstore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'PUSHER_KEY' => 'nullable|string',
            'PUSHER_SECRET' => 'nullable|string',
            'PUSHER_APP_ID' => 'nullable|string',
            'PUSHER_CLUSTER' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'PUSHER_KEY',
            'PUSHER_SECRET',
            'PUSHER_APP_ID',
            'PUSHER_CLUSTER',
        ]);

        if (!empty($data['PUSHER_SECRET'])) {
            $data['PUSHER_SECRET'] = ($data['PUSHER_SECRET']);
        }


        $existing = DB::table('pusher_config')->where('id', 1)->first();

        if ($existing) {
            DB::table('pusher_config')->where('id', 1)->update($data);
            $message = 'Pusher Config updated successfully';
        } else {
            $data['id'] = 1;
            DB::table('pusher_config')->insert($data);
            $message = 'Pusher Config inserted successfully';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
}
