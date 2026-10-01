<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>PPD Die-set Renewal Notification</title>

        <style>
            /* =====================================================
            RESET
            ===================================================== */
            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                background-color: #f1f5f9;
                font-family: Arial, Helvetica, sans-serif;
            }

            * {
                box-sizing: border-box;
            }

            table {
                border-spacing: 0;
                border-collapse: collapse;
            }

            td {
                padding: 0;
            }

            img {
                border: 0;
                display: block;
                max-width: 100%;
            }

            a {
                text-decoration: none;
            }

            p {
                margin: 0;
            }

            /* =====================================================
            OUTER WRAPPER
            ===================================================== */
            .email-wrapper {
                width: 100%;
                padding: 40px 15px;
                background-color: #f1f5f9;
            }

            .email-container {
                width: 680px;
                max-width: 100%;
                margin: 0 auto;
                background-color: #ffffff;
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 10px 35px rgba(15, 23, 42, 0.08);
            }

            /* =====================================================
            HEADER
            ===================================================== */
            .header {
                padding: 26px 36px;
                background-color: #0f172a;
            }

            .brand-name {
                color: #ffffff;
                font-size: 15px;
                font-weight: 700;
                letter-spacing: 1px;
                text-transform: uppercase;
            }

            .brand-subtitle {
                margin-top: 5px;
                color: #94a3b8;
                font-size: 11px;
            }

            .header-status {
                text-align: right;
                vertical-align: middle;
            }

            .status-pill {
                display: inline-block;
                padding: 7px 12px;
                border-radius: 20px;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: .5px;
                text-transform: uppercase;
            }

            /* =====================================================
            HERO
            ===================================================== */
            .hero {
                padding: 38px 40px 32px;
                background-color: #ffffff;
            }

            .hero-badge {
                display: inline-block;
                padding: 7px 12px;
                border-radius: 20px;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: .7px;
                text-transform: uppercase;
            }

            .hero-title {
                margin: 15px 0 10px;
                color: #0f172a;
                font-size: 30px;
                line-height: 1.2;
                font-weight: 700;
            }

            .hero-text {
                max-width: 550px;
                color: #64748b;
                font-size: 14px;
                line-height: 1.7;
            }

            /* =====================================================
            STATUS NOTICE
            ===================================================== */
            .notice-wrapper {
                padding: 0 40px 28px;
            }

            .notice {
                width: 100%;
                border-radius: 10px;
                overflow: hidden;
            }

            .notice-icon-cell {
                width: 60px;
                padding: 18px 0 18px 18px;
                vertical-align: middle;
            }

            .notice-icon {
                width: 34px;
                height: 34px;
                border-radius: 50%;
                font-size: 16px;
                font-weight: 700;
                line-height: 34px;
                text-align: center;
            }

            .notice-content {
                padding: 17px 18px 17px 12px;
                vertical-align: middle;
            }

            .notice-title {
                margin-bottom: 5px;
                font-size: 13px;
                font-weight: 700;
            }

            .notice-text {
                color: #64748b;
                font-size: 12px;
                line-height: 1.6;
            }

            /* =====================================================
            SECTION
            ===================================================== */
            .section {
                padding: 0 40px 30px;
            }

            .section-header {
                padding-bottom: 12px;
                border-bottom: 1px solid #e2e8f0;
            }

            .section-title {
                color: #0f172a;
                font-size: 12px;
                font-weight: 700;
                letter-spacing: .7px;
                text-transform: uppercase;
            }

            /* =====================================================
            INFO CARD
            ===================================================== */
            .info-card {
                margin-top: 14px;
                width: 100%;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                overflow: hidden;
            }

            .info-row {
                border-bottom: 1px solid #e2e8f0;
            }

            .info-row:last-child {
                border-bottom: none;
            }

            .info-label {
                width: 38%;
                padding: 15px 17px;
                background-color: #f8fafc;
                color: #64748b;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: .4px;
                text-transform: uppercase;
            }

            .info-value {
                width: 62%;
                padding: 15px 17px;
                color: #0f172a;
                background-color: #ffffff;
                font-size: 13px;
            }

            .device-code {
                display: inline-block;
                padding: 5px 8px;
                border-radius: 5px;
                background-color: #f1f5f9;
                color: #0f172a;
                font-family: Consolas, Monaco, monospace;
                font-size: 12px;
                font-weight: 700;
            }

            /* =====================================================
            METRICS
            ===================================================== */
            .metrics {
                margin-top: 14px;
                width: 100%;
            }

            .metric {
                width: 33.333%;
                padding: 18px;
                background-color: #f8fafc;
                border: 1px solid #e2e8f0;
                vertical-align: top;
            }

            .metric + .metric {
                border-left: none;
            }

            .metric:first-child {
                border-radius: 10px 0 0 10px;
            }

            .metric:last-child {
                border-radius: 0 10px 10px 0;
            }

            .metric-label {
                margin-bottom: 10px;
                color: #64748b;
                font-size: 9px;
                line-height: 1.4;
                font-weight: 700;
                letter-spacing: .5px;
                text-transform: uppercase;
            }

            .metric-value {
                color: #0f172a;
                font-size: 23px;
                line-height: 1.2;
                font-weight: 700;
            }

            /* =====================================================
            RESULT CARD
            ===================================================== */
            .result-card {
                width: 100%;
                margin-top: 14px;
                border: 1px solid #e2e8f0;
                border-radius: 10px;
                overflow: hidden;
            }

            .result-header {
                padding: 15px 17px;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: .6px;
                text-transform: uppercase;
            }

            .result-row {
                border-bottom: 1px solid #e2e8f0;
            }

            .result-row:last-child {
                border-bottom: none;
            }

            .result-label {
                width: 38%;
                padding: 14px 17px;
                background-color: #f8fafc;
                color: #64748b;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: .4px;
                text-transform: uppercase;
                vertical-align: top;
            }

            .result-value {
                width: 62%;
                padding: 14px 17px;
                background-color: #ffffff;
                color: #0f172a;
                font-size: 13px;
                line-height: 1.6;
                vertical-align: top;
            }

            .remark-box {
                white-space: pre-line;
            }

            /* =====================================================
            ACTION REQUIRED
            ===================================================== */
            .action-wrapper {
                padding: 2px 40px 32px;
            }

            .action-card {
                width: 100%;
                padding: 22px 24px;
                border-radius: 12px;
            }

            .action-label {
                margin-bottom: 8px;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: .8px;
                text-transform: uppercase;
            }

            .action-title {
                margin-bottom: 8px;
                color: #0f172a;
                font-size: 14px;
                font-weight: 700;
            }

            .action-text {
                color: #475569;
                font-size: 12px;
                line-height: 1.7;
            }

            .action-link {
                display: inline-block;
                margin-top: 14px;
                padding: 10px 16px;
                border-radius: 7px;
                color: #ffffff !important;
                background-color: #2563eb;
                font-size: 11px;
                font-weight: 700;
            }

            /* =====================================================
            REFERENCE / SUPPORT
            ===================================================== */
            .reference-wrapper {
                padding: 0 40px 35px;
            }

            .reference-card {
                padding: 20px 22px;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                background-color: #f8fafc;
            }

            .reference-label {
                margin-bottom: 8px;
                color: #64748b;
                font-size: 9px;
                font-weight: 700;
                letter-spacing: .8px;
                text-transform: uppercase;
            }

            .reference-text {
                color: #475569;
                font-size: 12px;
                line-height: 1.7;
            }

            .reference-link {
                color: #2563eb;
                font-weight: 700;
            }

            /* =====================================================
            FOOTER
            ===================================================== */
            .footer {
                padding: 26px 40px 30px;
                background-color: #0f172a;
                text-align: center;
            }

            .footer-title {
                margin-bottom: 7px;
                color: #ffffff;
                font-size: 11px;
                font-weight: 700;
            }

            .footer-text {
                color: #94a3b8;
                font-size: 10px;
                line-height: 1.7;
            }

            /* =====================================================
            MOBILE
            ===================================================== */
            @media only screen and (max-width: 600px) {

                .email-wrapper {
                    padding: 15px 0;
                }

                .email-container {
                    width: 100%;
                    border-radius: 0;
                }

                .header,
                .hero,
                .notice-wrapper,
                .section,
                .action-wrapper,
                .reference-wrapper,
                .footer {
                    padding-left: 20px;
                    padding-right: 20px;
                }

                .hero {
                    padding-top: 30px;
                }

                .hero-title {
                    font-size: 25px;
                }

                .metric {
                    display: block;
                    width: 100%;
                    margin-bottom: 8px;
                    border: 1px solid #e2e8f0 !important;
                    border-radius: 10px !important;
                }

                .metric + .metric {
                    border-left: 1px solid #e2e8f0;
                }

                .info-label,
                .result-label {
                    width: 40%;
                    padding: 13px;
                    font-size: 9px;
                }

                .info-value,
                .result-value {
                    width: 60%;
                    padding: 13px;
                    font-size: 12px;
                }

                .action-card {
                    padding: 20px;
                }

                .action-link {
                    display: block;
                    text-align: center;
                }

                .reference-card {
                    padding: 18px;
                }
            }
        </style>
    </head>

    <body>
        @php
            /*
            |--------------------------------------------------------------------------
            | STATUS CONFIGURATION
            |--------------------------------------------------------------------------
            |
            | 0 = Pending Approval
            | 1 = Approved
            | 2 = Disapproved
            |
            */

            $status = (int) ($approval_status ?? 0);
            if ($status === 0) {
                $emailStatus        = 'pending';
                $statusTitle        = 'Pending Approval';
                $statusText         = 'A new die-set renewal request has been submitted and is waiting for your review and approval.';
                $statusColor        = '#2563eb';
                $statusLight        = '#eff6ff';
                $statusTextColor    = '#1e3a8a';
                $statusIcon         = '!';
                $approvalTitle          = 'Approval Required';
                $approvalText           = 'A new die-set renewal record has been submitted and requires your review and approval.';
            } elseif ($status === 1) {
                $emailStatus        = 'approved';
                $statusTitle        = 'Approved';
                $statusText         = 'The die-set renewal request has been successfully reviewed and approved.';
                $statusColor        = '#16a34a';
                $statusLight        = '#f0fdf4';
                $statusTextColor    = '#166534';
                $statusIcon         = '✓';
                $approvalTitle          = 'Renewal Approved';
                $approvalText           = 'The die-set renewal request has been successfully reviewed and approved.';
            } elseif ($status === 2) {
                $emailStatus        = 'disapproved';
                $statusTitle        = 'Disapproved';
                $statusText         = 'The die-set renewal request has been reviewed and disapproved.';
                $statusColor        = '#dc2626';
                $statusLight        = '#fef2f2';
                $statusTextColor    = '#991b1b';
                $statusIcon         = '×';
                $approvalTitle          = 'Renewal Disapproved';
                $approvalText           = 'The die-set renewal request has been reviewed and was not approved.';
            } else {
                $emailStatus        = 'unknown';
                $statusTitle        = 'Status Update';
                $statusText         = 'There has been an update to the die-set renewal request.';
                $statusColor        = '#64748b';
                $statusLight        = '#f8fafc';
                $statusTextColor    = '#334155';
                $statusIcon         = 'i';
                $approvalTitle          = 'Die-set Renewal Update';
                $approvalText           = 'There has been an update to this die-set renewal record.';
            }
        @endphp

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;">
            <tr>
                <td>
                    <div class="email-wrapper">
                        <table role="presentation" class="email-container" width="680" cellpadding="0" cellspacing="0" border="0" align="center">
                            <tr>
                                <td class="header">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td>
                                                <p class="brand-name">
                                                    PPD System
                                                </p>

                                                <p class="brand-subtitle">
                                                    Die-set Management
                                                </p>
                                            </td>

                                            <td class="header-status">
                                                <span class="status-pill" style=" background-color: {{ $statusLight }}; color: {{ $statusTextColor }};">
                                                    {{ $statusTitle }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td class="hero">
                                    <span class="hero-badge" style="background-color: {{ $statusLight }}; color: {{ $statusTextColor }};">
                                        Die-set Renewal
                                    </span>

                                    <h1 class="hero-title">
                                        {{ $approvalTitle }}
                                    </h1>

                                    <p class="hero-text">
                                        {{ $approvalText }}
                                    </p>
                                </td>
                            </tr>

                            <tr>
                                <td class="notice-wrapper">
                                    <table role="presentation" class="notice" cellpadding="0" cellspacing="0" border="0" style=" background-color: {{ $statusLight }}; border-left: 4px solid {{ $statusColor }};">
                                        <tr>
                                            <td class="notice-icon-cell">
                                                <div class="notice-icon" style=" background-color: #ffffff; color: {{ $statusColor }}; border: 1px solid {{ $statusColor }};">
                                                    {{ $statusIcon }}
                                                </div>
                                            </td>

                                            <td class="notice-content">
                                                <p class="notice-title" style=" color: {{ $statusTextColor }};">
                                                    {{ $statusTitle }}
                                                </p>

                                                <p class="notice-text">
                                                    {{ $statusText }}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>


                            @if(in_array($status, [1, 2]))
                            <tr>
                                <td class="section">
                                    <table role="presentation" class="result-card" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td class="result-header" colspan="2" style=" background-color: {{ $statusLight }}; color: {{ $statusTextColor }}; border-bottom: 1px solid {{ $statusColor }};">
                                                {{ $statusTitle }} Details
                                            </td>
                                        </tr>

                                        <tr class="result-row">
                                            <td class="result-label">
                                                Date &amp; Time
                                            </td>

                                            <td class="result-value">
                                                @if(!empty($action_date_time))
                                                    {{ \Carbon\Carbon::parse($action_date_time)->format('F d, Y h:i A') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>

                                        <tr class="result-row">
                                            <td class="result-label">
                                                Remarks
                                            </td>

                                            <td class="result-value">
                                                <div class="remark-box">
                                                    {{ $approval_remark ?: 'No remarks provided.' }}
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            @endif

                            <tr>
                                <td class="section">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td class="section-header">
                                                <span class="section-title">
                                                    Device Information
                                                </span>
                                            </td>
                                        </tr>
                                    </table>

                                    <table role="presentation" class="info-card" cellpadding="0" cellspacing="0" border="0">
                                        <tr class="info-row">
                                            <td class="info-label">
                                                Device Code
                                            </td>

                                            <td class="info-value">
                                                <span class="device-code">
                                                    {{ $device_code ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>

                                        <tr class="info-row">
                                            <td class="info-label">
                                                Device Name
                                            </td>

                                            <td class="info-value">
                                                {{ $device_name ?? '-' }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <td class="section">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td class="section-header">
                                                <span class="section-title">
                                                    Die-set Longevity
                                                </span>
                                            </td>
                                        </tr>
                                    </table>

                                    <table role="presentation" class="metrics" cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td class="metric">
                                                <p class="metric-label">
                                                    Tool Life
                                                </p>

                                                <p class="metric-value">
                                                    {{ number_format($tool_life ?? 0) }}
                                                </p>
                                            </td>

                                            <td class="metric">
                                                <p class="metric-label">
                                                    YEC Sales Quantity
                                                </p>

                                                <p class="metric-value">
                                                    {{ number_format($yec_sales_qty ?? 0) }}
                                                </p>
                                            </td>

                                            <td class="metric">
                                                <p class="metric-label">
                                                    PMI Sales Quantity
                                                </p>

                                                <p class="metric-value">
                                                    {{ number_format($pmi_sales_qty ?? 0) }}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            @if($status === 0)
                                <tr>
                                    <td class="action-wrapper">
                                        <div class="action-card" style=" background-color:#eff6ff; border:1px solid #bfdbfe;">
                                            <p
                                                class="action-label"
                                                style="color:#2563eb;"
                                            >
                                                Action Required
                                            </p>

                                            <p class="action-title">
                                                Review Die-set Renewal Request
                                            </p>

                                            <p class="action-text">
                                                Please log in to your Rapidx account to review
                                                the die-set renewal request and complete the
                                                required approval process.
                                                <a href="http://rapidx/PPD_DSRLM/" class="action-link">
                                                    Open Rapidx
                                                </a>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @elseif($status === 1)
                                <tr>
                                    <td class="action-wrapper">
                                        <div class="action-card" style=" background-color:#f0fdf4; border:1px solid #bbf7d0;">
                                            <p class="action-label" style="color:#16a34a;">
                                                Approval Completed
                                            </p>

                                            <p class="action-title">
                                                Renewal Request Approved
                                            </p>

                                            <p class="action-text">
                                                No further action is required.
                                                This die-set renewal request has already
                                                been reviewed and approved.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @elseif($status === 2)
                                <tr>
                                    <td class="action-wrapper">
                                        <div class="action-card" style=" background-color:#fef2f2; border:1px solid #fecaca;">
                                            <p
                                                class="action-label"
                                                style="color:#dc2626;"
                                            >
                                                Review Required
                                            </p>

                                            <p class="action-title">
                                                Corrective Action Required
                                            </p>

                                            <p class="action-text">
                                                The die-set renewal request was disapproved.
                                                Please review the remarks above and take
                                                the necessary corrective action.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td class="reference-wrapper">
                                    <div class="reference-card">
                                        <p class="reference-label">
                                            Need Assistance?
                                        </p>

                                        <p class="reference-text">
                                            For concerns regarding the system,
                                            please contact ISS at local numbers
                                            <strong>205, 206, or 208</strong>,
                                            or submit a ticket through the

                                            <a href="http://rapidx/iss_service_request/" class="reference-link">
                                                ISS Service Request
                                            </a>.
                                        </p>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td class="footer">
                                    <p class="footer-title">
                                        PPD Die-set Renewal and Longevity System
                                    </p>

                                    <p class="footer-text">
                                        This is an automated notification.
                                        Please do not reply directly to this email.
                                        <br>
                                        © {{ date('Y') }} PPD System
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

    </body>
</html>
