<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Tax Invoice {{ $document->invoice_no }}</title>
</head>

<body style="margin:0;padding:0;background:#f5f7fb;font-family:Arial,Helvetica,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f5f7fb;padding:30px 0;">
        <tr>
            <td align="center">

                <table width="620" cellpadding="0" cellspacing="0"
                    style="background:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 4px 12px rgba(0,0,0,0.05);">

                    <tr>
                        <td style="background:#00454f;color:#ffffff;padding:30px;text-align:center;">
                            <h2 style="margin:0;font-size:24px;font-weight:bold;">{{ $company->name }}</h2>
                            <div style="margin-top:10px;font-size:14px;line-height:22px;opacity:0.9;">
                                {{ $company->address }}<br>
                                Phone: {{ $company->contact_no }} | Email: {{ $company->email }}
                            </div>
                        </td>
                    </tr>

                    @if(!empty($document->message))
                        <tr>
                            <td style="padding:25px 35px 10px 35px;">
                                <table width="100%" cellpadding="0" cellspacing="0"
                                    style="background:#f8faff;border-left:5px solid #00454f;border-radius:6px;">
                                    <tr>
                                        <td style="padding:18px 20px;color:#444444;font-size:15px;line-height:24px;">
                                            <div style="font-size:16px;font-weight:bold;color:#00454f;margin-bottom:10px;">
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

                            <h2 style="margin:0 0 20px;color:#00454f;font-size:20px;">
                                Tax Invoice
                            </h2>

                            <table width="100%" cellpadding="10" cellspacing="0"
                                style="border-collapse:collapse;border:1px solid #eeeeee;font-size:14px;">

                                <tr style="background:#fcfcfc;">
                                    <td style="font-weight:bold;width:180px;border-bottom:1px solid #eeeeee;">Invoice No.</td>
                                    <td style="border-bottom:1px solid #eeeeee;font-weight:bold;color:#00454f;">{{ $document->invoice_no }}</td>
                                </tr>

                                <tr>
                                    <td style="font-weight:bold;border-bottom:1px solid #eeeeee;">Customer Name</td>
                                    <td style="border-bottom:1px solid #eeeeee;">{{ $document->customer_name }}</td>
                                </tr>

                                <tr style="background:#fcfcfc;">
                                    <td style="font-weight:bold;border-bottom:1px solid #eeeeee;">Invoice Date</td>
                                    <td style="border-bottom:1px solid #eeeeee;">{{ $document->invoice_date }}</td>
                                </tr>

                                <tr>
                                    <td style="font-weight:bold;border-bottom:1px solid #eeeeee;">Grand Total</td>
                                    <td style="border-bottom:1px solid #eeeeee;font-weight:bold;color:#28a745;">
                                        {{ $document->currency !== 'INR' ? $document->currency . ' ' . number_format($document->grand_total, 2) : '₹' . number_format($document->grand_total, 2) }}
                                    </td>
                                </tr>

                                <tr style="background:#fcfcfc;">
                                    <td style="font-weight:bold;border-bottom:1px solid #eeeeee;">Balance Remaining</td>
                                    <td style="border-bottom:1px solid #eeeeee;font-weight:bold;color:#dc3545;">
                                        {{ $document->currency !== 'INR' ? $document->currency . ' ' . number_format($document->balance_payment, 2) : '₹' . number_format($document->balance_payment, 2) }}
                                    </td>
                                </tr>

                            </table>

                            <div style="text-align:center;margin-top:35px;">
                                <a href="{{ $pdfUrl }}"
                                    style="background:#00454f;color:#ffffff;text-decoration:none;padding:14px 34px;border-radius:6px;font-weight:bold;display:inline-block;font-size:15px;">
                                    View / Download Tax Invoice PDF
                                </a>
                            </div>

                        </td>
                    </tr>

                    <tr>
                        <td style="background:#f8f8f8;padding:20px;text-align:center;font-size:13px;color:#666666;">
                            <strong>{{ $company->name }}</strong><br>
                            {{ $company->email }} | {{ $company->contact_no }}
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
