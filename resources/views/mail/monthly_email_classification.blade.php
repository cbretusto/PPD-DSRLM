
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daily Device Progress Report</title>
    </head>

    <body style="margin:0; padding:20px; background-color:#f3f4f6; font-family:Arial, Helvetica, sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" role="presentation">
            <tr>
                <td align="center">

                    <!-- MAIN CONTAINER -->
                    <table width="800" cellpadding="0" cellspacing="0" border="0"
                        role="presentation"
                        style="width:100%; max-width:800px; background-color:#ffffff; border-radius:10px; overflow:hidden;">

                        <!-- HEADER -->
                        <tr>
                            <td style="background-color:#172554; padding:30px; text-align:center;">
                                <h2 style="color:#ffffff; margin:0; letter-spacing:1px;">
                                    DEVICE MONITORING SYSTEM
                                </h2>

                                <p style="color:#bfdbfe; font-size:12px; margin-bottom:0;">
                                    MONTHLY AUTOMATED DEVICE REPORT
                                </p>
                            </td>
                        </tr>

                        <!-- BODY -->
                        <tr>
                            <td style="padding:30px;">
                                <h2 style="color:#172554; margin-top:0;">
                                    Device Progress Monthly Report
                                </h2>

                                <p style="color:#475569;">
                                    Good morning!
                                </p>

                                <p style="color:#475569; line-height:1.6;">
                                    Please see below the list of devices currently
                                    classified as <strong>Waiting For Longevity Parts</strong>.
                                </p>

                                <!-- SUMMARY -->
                                <table width="100%" cellpadding="15" cellspacing="0"
                                    border="0" role="presentation"
                                    style="background-color:#f8fafc; border-left:5px solid #2563eb;">
                                    <tr>
                                        <td>
                                            <div style="font-size:13px; color:#64748b;">
                                                Monthly Progress
                                            </div>

                                            <h1 style="color:#2563eb; margin:5px 0; font-size:32px;">
                                                {{ $devices->count() }}
                                            </h1>

                                            <div style="font-size:13px; color:#64748b;">
                                                Total Devices
                                            </div>
                                        </td>

                                        <td align="right">
                                            <div style="color:#64748b; font-size:12px;">
                                                Report Month
                                            </div>

                                            <strong style="color:#172554;">
                                                {{ now()->timezone('Asia/Manila')->format('F Y') }}
                                            </strong>
                                        </td>
                                    </tr>
                                </table>

                                <br>

                                <!-- DEVICE TABLE -->
                                <h3 style="color:#172554;">
                                    Device Details
                                </h3>

                                <table width="100%" cellpadding="9" cellspacing="0"
                                    border="0" role="presentation"
                                    style="border-collapse:collapse; font-size:12px;">

                                    <thead>
                                        <tr style="background-color:#172554; color:#ffffff;">
                                            <th align="left">#</th>
                                            <th align="left">Device Code</th>
                                            <th align="left">Device Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($devices as $index => $device)
                                            <tr style="background-color:{{ $index % 2 == 0 ? '#f8fafc' : '#ffffff' }};">
                                                <td style="border-bottom:1px solid #e5e7eb;">
                                                    {{ $index + 1 }}
                                                </td>

                                                <td style="border-bottom:1px solid #e5e7eb;">
                                                    <strong>
                                                        {{ $device->device_code }}
                                                    </strong>
                                                </td>

                                                <td style="border-bottom:1px solid #e5e7eb;">
                                                    {{ $device->device_name }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" align="center"
                                                    style="padding:20px; color:#64748b; border-bottom:1px solid #e5e7eb;">
                                                    No devices found for this classification.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <br>
                                <p style="color:#475569; line-height:1.6; font-size:13px;">
                                    Please review the devices listed above and take
                                    the necessary action regarding their longevity parts.
                                </p>
                            </td>
                        </tr>

                        <!-- FOOTER -->
                        <tr>
                            <td style="background-color:#f8fafc; padding:20px; text-align:center;">
                                <p style="color:#64748b; font-size:11px; margin:0;">
                                    This is an automatically generated email.
                                    Please do not reply.
                                </p>

                                <p style="color:#64748b; font-size:11px; margin-bottom:0;">
                                    Device Monitoring System
                                </p>
                            </td>
                        </tr>
                    </table>
                    <!-- END MAIN CONTAINER -->
                </td>
            </tr>
        </table>
    </body>
</html>
