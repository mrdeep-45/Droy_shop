<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'Email Message' }}</title>
</head>

<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fb;padding:30px 0;">
        <tr>
            <td align="center">

                <table width="620" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow: 0 4px 12px rgba(0,0,0,0.05);">

                    <!-- Header -->
                    <tr>
                        <td style="background:#1a237e;color:#ffffff;padding:25px 30px;text-align:center;">
                            <h2 style="margin:0;font-size:20px;">{{ $company->name ?? config('app.name') }}</h2>
                            @if(!empty($company->address))
                                <div style="margin-top:8px;font-size:13px;line-height:20px;opacity:0.9;">
                                    {{ $company->address }}
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Subject / Title Bar -->
                    @if(!empty($subject))
                        <tr>
                            <td style="padding:20px 35px 0 35px;">
                                <h3 style="margin:0;color:#1a237e;font-size:18px;border-bottom:2px solid #e8eaf6;padding-bottom:10px;">
                                    {{ $subject }}
                                </h3>
                            </td>
                        </tr>
                    @endif

                    <!-- Message Body -->
                    <tr>
                        <td style="padding:25px 35px;color:#333333;font-size:15px;line-height:24px;">
                           {!! $bodyMessage !!}
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f8f9fa;padding:20px;text-align:center;font-size:13px;color:#666666;border-top:1px solid #eeeeee;">
                            <strong>{{ $company->name ?? config('app.name') }}</strong><br>
                            @if(!empty($company->contact_email ?? $company->email))
                                {{ $company->contact_email ?? $company->email }}
                            @endif
                            @if(!empty($company->contact_phone ?? $company->contact_no))
                                | {{ $company->contact_phone ?? $company->contact_no }}
                            @endif
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
