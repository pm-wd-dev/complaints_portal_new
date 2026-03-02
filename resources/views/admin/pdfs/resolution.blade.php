<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resolution Document - Case #{{ $complaint->case_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 30px;
            color: #2c3e50;
            font-size: 10pt;
            background: white;
        }

        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60pt;
            opacity: 0.03;
            z-index: -1;
            color: #000;
            white-space: nowrap;
        }

        .letterhead {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #2c3e50;
            padding-bottom: 15px;
            position: relative;
        }

        .letterhead::after {
            content: '';
            position: absolute;
            bottom: 3px;
            left: 0;
            right: 0;
            border-bottom: 1px solid #2c3e50;
        }

        .letterhead h1 {
            color: #2c3e50;
            font-size: 20pt;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: bold;
        }

        .letterhead p {
            color: #34495e;
            margin: 3px 0;
            font-size: 9pt;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .document-info {
            margin: 15px 0;
            display: flex;
            justify-content: space-between;
            border: 1px solid #bdc3c7;
            padding: 10px;
            background: #f8f9fa;
        }

        .document-info p {
            margin: 0;
            font-size: 9pt;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            color: #2c3e50;
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 8px;
            padding: 5px 0;
            border-bottom: 2px solid #3498db;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section p {
            text-align: justify;
            margin: 5px 0;
            line-height: 1.4;
        }

        .signature-section {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .signature-grid {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-top: 15px;
            gap: 10px;
        }

        .signature-box {
            text-align: center;
            flex: 0 1 200px;
        }

        .signature-line {
            border-bottom: 1px solid #2c3e50;
            width: 200px;
            margin-bottom: 5px;
        }

        .signature-name {
            font-size: 9pt;
            color: #2c3e50;
            margin-bottom: 3px;
        }

        .signature-title {
            font-size: 8pt;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .signature-date {
            font-size: 8pt;
            color: #7f8c8d;
            border-top: 1px dotted #bdc3c7;
            padding-top: 3px;
            margin-top: 5px;
            width: 100%;
            max-width: 200px;
            margin: 5px auto 0;
        }

        .footer {
            position: fixed;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8pt;
            color: #7f8c8d;
            border-top: 1px solid #bdc3c7;
            padding-top: 10px;
            margin: 0 30px;
        }

        .qr-code {
            position: fixed;
            bottom: 20px;
            right: 30px;
            font-size: 8pt;
            text-align: center;
        }

        .confidential {
            position: fixed;
            top: 20px;
            right: 30px;
            color: #e74c3c;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            transform: rotate(90deg);
            transform-origin: bottom right;
        }

        .parties-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 10px 0;
            background: #f8f9fa;
            padding: 15px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
        }

        .party-info {
            padding: 10px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 3px;
        }

        .party-info strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
            font-size: 10pt;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }

        .party-info .email {
            color: #666;
            font-size: 9pt;
            font-style: italic;
        }

        .document-meta {
            text-align: right;
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 8pt;
            color: #666;
        }

        .document-meta .confidential {
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    <div class="watermark">RESOLUTION DOCUMENT</div>
    <div class="document-meta">
        <div>Generated: {{ date('Y-m-d H:i') }}</div>
        <div>Case #{{ $complaint->case_number }}</div>
    </div>

    <div class="container">
        <div class="letterhead">
            <h1>Resolution Document</h1>
            <p>Internal Complaint Resolution Department</p>
        </div>

        <div class="document-info">
            <div>
                <p><strong>Case Reference:</strong> {{ $complaint->case_number }}</p>
                <p><strong>Status:</strong> {{ ucfirst($complaint->status) }}</p>
            </div>
            <div>
                <p><strong>Date Issued:</strong> {{ $generated_at }}</p>
                <p><strong>Document Type:</strong> {{ ucfirst($template_type) }} Resolution</p>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Parties Involved</div>
            <div class="parties-grid">
                <div class="party-info">
                    <strong>Complainant</strong>
                    {{ $complaint->name }}
                    <div class="email">{{ $complaint->email }}</div>
                </div>
                <div class="party-info">
                    <strong>Respondent(s)</strong>
                    @foreach($complaint->respondents as $respondent)
                        {{ $respondent->user->name }}
                        <div class="email">{{ $respondent->user->email }}</div>
                        @if(!$loop->last)<div style="margin: 10px 0; border-bottom: 1px dashed #eee;"></div>@endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Complaint Summary</div>
            <p>{{ $complaint->description }}</p>
        </div>

        @if($complaint->investigationLogs->isNotEmpty())
        <div class="section">
            <div class="section-title">Investigation Findings</div>
            <p>{{ $complaint->investigationLogs->last()->note }}</p>
        </div>
        @endif

        <div class="section">
            <div class="section-title">Resolution Details</div>
            <p>{{ $resolution_text }}</p>
        </div>

        <div class="signature-section">
            <div class="section-title">Acknowledgment & Signatures</div>
            <p style="font-size: 9pt; margin-bottom:50px; color: #666;">
                By signing below, all parties acknowledge that they have read, understood, and agree to the terms outlined in this resolution document.
            </p>
            <div style="direction: rtl;">
            <table>
        <tr>
        @foreach($signers as $signer)
            <td style="padding: 0 15px;">
                @php
                    $signature = null;
                    if ($complaint->latestResolution) {
                        if ($signer === 'complainant') {
                            $signature = $complaint->latestResolution->signatures()->whereNull('user_id')->first();
                        } elseif ($signer === 'respondent' && $complaint->respondents->isNotEmpty()) {
                            $signature = $complaint->latestResolution->signatures()
                                ->where('user_id', $complaint->respondents->first()->user_id)
                                ->first();
                        } elseif ($signer === 'leadership') {
                            if(isset($admin_name)) {
                                $signature = $complaint->latestResolution->signatures()
                                    ->where('signer_name', $admin_name)
                                    ->first();
                            } else {
                                $signature = $complaint->latestResolution->signatures()
                                    ->whereHas('user', function($query) {
                                        $query->where('role', 'admin');
                                    })
                                    ->first();
                            }
                        }
                    }
                @endphp
                @php
                     \Log::info('Rendering Blade view', ['signer' => $signature]);
                @endphp
                @if($signature && $signature->signature_path)
                    <div style="min-height: 60px; margin-bottom: 10px;">
                        <img src="{{ public_path($signature->signature_path) }}" 
                             alt="Signature" 
                             style="max-height: 60px; max-width: 150px; object-fit: contain;">
                    </div>
                @else
                    <div class="signature-line" style="margin-bottom: 10px;"></div>
                @endif

                @if($signer === 'complainant')
                    <div class="signature-name">{{ $complaint->name }}</div>
                    <div class="signature-title">Complainant</div>
                @elseif($signer === 'respondent')
                    <div class="signature-name">{{ $complaint->respondents->first()->user->name }}</div>
                    <div class="signature-title">Respondent</div>
                @elseif($signer === 'leadership')
                <div class="signature-name">{{ Auth::user()->name ?? $admin_name }}</div>
                <div class="signature-title">Leadership</div>
                @endif

                <div class="signature-date">
                    Date: {{ $signature && $signature->signed_at ? $signature->signed_at->format('Y-m-d') : '________________' }}
                </div>
            </td>
        @endforeach
        </tr>
        </table>
            </div>
            </div>
            </div>
    </div>

    <div class="footer">
        This document is electronically generated and contains sensitive information.
        Any unauthorized use, disclosure, or reproduction is strictly prohibited.
    </div>

    <div class="qr-code">
        Case #{{ $complaint->case_number }}<br>
        {{ date('Y-m-d') }}
    </div>
</body>
</html>
