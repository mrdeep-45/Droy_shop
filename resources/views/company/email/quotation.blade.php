<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Quotation {{ $document->quotation_no }}</title>
</head>

<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fb;padding:30px 0;">
        <tr>
            <td align="center">

                <table width="620" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:8px;overflow:hidden;">

                    <tr>
                        <td style="background:#1a237e;color:#ffffff;padding:30px;text-align:center;">
                            <h2 style="margin:0;">{{ $company->name }}</h2>

                            <div style="margin-top:12px;font-size:14px;line-height:24px;">

                                {{ $company->quotation_contact_no ?? $company->contact_no }}<br>
                                {{ $company->address }}
                            </div>
                        </td>
                    </tr>
                    @if(!empty($document->message))
                        <tr>
                            <td style="padding:25px 35px 10px 35px;">
                                <table width="100%" cellpadding="0" cellspacing="0"
                                    style="background:#f8faff;border-left:5px solid #1a237e;border-radius:6px;">
                                    <tr>
                                        <td style="padding:18px 20px;color:#444444;font-size:15px;line-height:24px;">
                                            <div style="font-size:16px;font-weight:bold;color:#1a237e;margin-bottom:10px;">
                                                Message
                                            </div>

                                            {!! nl2br(e($document->message)) !!}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="padding:35px;">

                            <h2 style="margin:0 0 20px;color:#1a237e;">
                                Quotation
                            </h2>

                            <table width="100%" cellpadding="8" cellspacing="0"
                                style="border-collapse:collapse;border:1px solid #eeeeee;">

                                <tr>
                                    <td style="font-weight:bold;width:180px;">Quotation No.</td>
                                    <td>{{ $document->quotation_no }}</td>
                                </tr>

                                <tr>
                                    <td style="font-weight:bold;">Customer Name</td>
                                    <td>{{ $document->customer_name }}</td>
                                </tr>

                                <tr>
                                    <td style="font-weight:bold;">Date</td>
                                    <td>{{ $document->quotation_date }}</td>
                                </tr>

                            </table>

                            <div style="text-align:center;margin-top:35px;">
                                <a href="{{ $pdfUrl }}"
                                    style="background:#1a237e;color:#ffffff;text-decoration:none;padding:14px 34px;border-radius:5px;font-weight:bold;display:inline-block;">
                                    View / Download Quotation PDF
                                </a>
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f8f8f8;padding:20px;text-align:center;font-size:13px;color:#666666;">
                            {{ $company->name }}<br>
                            {{ $company->contact_email ?? $company->email }} |
                            {{ $company->contact_phone ?? $company->contact_no }}
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
