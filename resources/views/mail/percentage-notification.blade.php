<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Daily Device Progress Report</title>
    </head>

    <body style="margin:0; padding:20px; background:#f3f4f6; font-family:Arial,Helvetica,sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="center">
                    <table width="800" cellpadding="0" cellspacing="0" border="0"style="max-width:800px;background:#ffffff;border-radius:10px;overflow:hidden;">
                        <!-- HEADER -->
                        <tr>
                            <td style="background:#172554; padding:30px; text-align:center;">
                                <h2 style="color:#ffffff; margin:0; letter-spacing:1px;">
                                    DEVICE MONITORING SYSTEM
                                </h2>

                                <p style="color:#bfdbfe; font-size:12px; margin-bottom:0;">
                                    DAILY AUTOMATED PROGRESS REPORT
                                </p>

                            </td>
                        </tr>

                        <!-- BODY -->
                        <tr>
                            <td style="padding:30px;">
                                <h2 style="color:#172554; margin-top:0;">
                                    {{ $emailTitle }}
                                </h2>

                                <p style="color:#475569;">
                                    Good morning!
                                </p>

                                <p style="color:#475569; line-height:1.6;">
                                    Please see below the list of devices that currently
                                    belong to the following progress category:
                                </p>

                                <!-- SUMMARY -->
                                <table width="100%" cellpadding="15" cellspacing="0"
                                    style="background:#f8fafc; border-left:5px solid {{ $progressColor }};">
                                    <tr>
                                        <td>
                                            <div style="font-size:13px; color:#64748b;">
                                                {{ $progressLabel }}
                                            </div>

                                            <h1 style="color:{{ $progressColor }}; margin:5px 0; font-size:32px;">
                                                {{ $devices->count() }}
                                            </h1>

                                            <div style="font-size:13px; color:#64748b;">
                                                Total Devices
                                            </div>
                                        </td>

                                        <td align="right">
                                            <div style="color:#64748b; font-size:12px;">
                                                Report Date
                                            </div>

                                            <strong style="color:#172554;">
                                                {{ now()->timezone('Asia/Manila')->format('F d, Y') }}
                                            </strong>
                                        </td>
                                    </tr>

                                </table>

                                <br>

                                <!-- DEVICE TABLE -->
                                <h3 style="color:#172554;">
                                    Device Progress Details
                                </h3>

                                <table width="100%" cellpadding="9" cellspacing="0" style="border-collapse:collapse; font-size:12px;">
                                    <thead>
                                        <tr style="background:#172554; color:#ffffff;">
                                            <th align="left">#</th>
                                            <th align="left">Device Code</th>
                                            <th align="left">Device Code</th>
                                            <th align="right">Actual SO</th>
                                            <th align="right">Target SO</th>
                                            <th align="right">Progress</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @php
                                            $sortedDevices = $devices->sortByDesc(function ($device) {
                                                return (float) $device->percentage;
                                            })->values();
                                        @endphp

                                        @foreach($sortedDevices as $index => $device)
                                            <tr style="background:{{ $index % 2 == 0 ? '#f8fafc' : '#ffffff' }};">
                                                <td style="border-bottom:1px solid #e5e7eb;">
                                                    {{ $index + 1 }}
                                                </td>

                                                <td style="border-bottom:1px solid #e5e7eb;">
                                                    <strong>
                                                        {{ $device->device_code }}
                                                    </strong>
                                                </td>

                                                <td style="border-bottom:1px solid #e5e7eb;">
                                                    <strong>
                                                        {{ $device->device_name }}
                                                    </strong>
                                                </td>

                                                <td align="right" style="border-bottom:1px solid #e5e7eb;">
                                                    {{ number_format($device->total_actual_so) }}
                                                </td>

                                                <td align="right" style="border-bottom:1px solid #e5e7eb;">
                                                    {{ number_format($device->target_so_value) }}
                                                </td>

                                                <td align="right" style="border-bottom:1px solid #e5e7eb; color:{{ $progressColor }}; font-weight:bold;">
                                                    {{ number_format($device->percentage, 2) }}%
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <br>
                                <p style="color:#475569; line-height:1.6; font-size:13px;">
                                    Please review the devices listed above and take
                                    the necessary action based on their current progress.
                                </p>
                            </td>
                        </tr>

                        <!-- FOOTER -->
                        <tr>
                            <td style="background:#f8fafc; padding:20px; text-align:center;">
                                <p style="color:#64748b; font-size:11px; margin:0;">
                                    This is an automatically generated email.
                                    Please do not reply.
                                </p>

                                <p style="color:#64748b; font-size:11px;">
                                    Device Monitoring System
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
