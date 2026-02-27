<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pumRequest->code }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }


        body {
            font-family: 'Calibri', sans-serif;
            font-size: 11pt;
            color: #111;
            background: #fff;
            padding: 0;
            margin: 0;
        }

        /* ── Header ── */
        .header-title {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .header-code {
            font-size: 11pt;
            color: #444;
            font-weight: normal;
        }
        .header-line {
            border: none;
            border-top: 2px solid #000;
            margin: 10px 0 25px;
        }

        /* ── Info table ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        .info-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 11pt;
        }
        .info-table td.lbl { width: 30%; font-weight: bold; }
        .info-table td.sep { width: 2%; }
        .info-table td.val { width: 68%; }

        /* ── Signature Section ── */
        .signature-section {
            /* Flex layout for centered items */
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px 20px; /* Row gap 40px, Col gap 20px */
            margin-top: 50px;
            width: 100%;
            /* Allow breaking inside container so rows can split across pages */
            page-break-inside: auto; 
        }

        .sig-box {
            text-align: center;
            /* Prevent individual box from splitting */
            page-break-inside: avoid;
            break-inside: avoid;
            /* Width calculation for 3 items per row with gap */
            /* 100% / 3 = 33.33%, minus gap safety */
            flex: 0 0 30%; 
            max-width: 30%;
            min-width: 0;
        }

        .sig-role {
            font-size: 10pt;
            margin-bottom: 10px;
            font-weight: bold;
            min-height: 2em; /* Ensure height alignment */
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        /* QR image */
        .qr-img {
            width: 80px;
            height: 80px;
            display: block;
            margin: 0 auto 8px;
            border: 1px solid #eee; /* Subtle frame */
            padding: 2px;
        }
        /* Empty QR placeholder */
        .qr-empty {
            width: 80px;
            height: 80px;
            display: block;
            margin: 0 auto 8px;
        }

        .sig-name {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 5px;
            text-decoration: underline; /* Professional look often uses underline */
            text-underline-offset: 3px;
        }
        .sig-nip {
            font-size: 9pt;
            font-weight: normal;
            color: #555;
            margin-top: 2px;
        }



        /* Print & screen */
        @media print {
            @page { margin: 0; }
            body { padding: 1cm; }
            .no-print { display: none !important; }
        }
        .no-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        .btn-print { background: #1d4ed8; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; }
        .btn-back { background: #6b7280; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; text-decoration: none; }
    </style>
</head>
<body>

    {{-- ── Header ── --}}
    <div class="header-title">Detail Pengajuan</div>
    <div class="header-code">{{ $pumRequest->code }}</div>
    <hr class="header-line">

    {{-- ── Info Table ── --}}
    <table class="info-table">
        <tr>
            <td class="lbl">Tanggal Pengajuan</td>
            <td class="sep">:</td>
            <td class="val">{{ $pumRequest->request_date->format('d M Y') }}</td>
        </tr>
        <tr>
            <td class="lbl">Nama Pengaju</td>
            <td class="sep">:</td>
            <td class="val">{{ $pumRequest->requester->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Jumlah Pengajuan</td>
            <td class="sep">:</td>
            <td class="val">Rp {{ number_format($pumRequest->amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="lbl">Keterangan</td>
            <td class="sep">:</td>
            <td class="val" style="white-space:pre-line">{{ $pumRequest->description ?: '-' }}</td>
        </tr>
        <tr>
            <td class="lbl">Oleh Petugas</td>
            <td class="sep">:</td>
            <td class="val">{{ $pumRequest->creator->name ?? '-' }}</td>
        </tr>
    </table>

    {{-- ══════════════════════════════
         TANDA TANGAN (FLEX LAYOUT)
    ══════════════════════════════ --}}
    <div class="signature-section">
        
        {{-- 1. Pemohon / Dibuat --}}
        @php $req = $pumRequest->requester; @endphp
        <div class="sig-box">
            <div class="sig-role">Pemohon,<br>Dibuat</div>

            @if($req && isset($qrCodes[$req->id]))
                <img class="qr-img" src="{{ $qrCodes[$req->id] }}" alt="QR">
            @else
                <div class="qr-empty"></div>
            @endif

            <div class="sig-name">{{ $req->name ?? '' }}</div>
            @if($req && $req->nik)
                <div class="sig-nip">NIP. {{ $req->nik }}</div>
            @endif
        </div>

        {{-- 2. Approvals --}}
        @foreach($signedApprovals as $approval)
            @php 
                $approver = $approval->approver;
                $isReleaseStep = $approval->step && $approval->step->type === \App\Models\PumApprovalStep::TYPE_RELEASE;
                $roleLabel = $isReleaseStep ? 'Dirilis Oleh,' : 'Disetujui Oleh,';
            @endphp
            <div class="sig-box">
                <div class="sig-role">{{ $roleLabel }}<br>{{ $approval->step->name ?? 'Approver' }}</div>

                @if(!$isReleaseStep && $approver && isset($qrCodes[$approver->id]))
                    <img class="qr-img" src="{{ $qrCodes[$approver->id] }}" alt="QR">
                @else
                    <div class="qr-empty"></div>
                @endif

                <div class="sig-name">{{ $approver->name ?? '' }}</div>
                @if($approver && $approver->nik)
                    <div class="sig-nip">NIP. {{ $approver->nik }}</div>
                @endif
            </div>
        @endforeach

        {{-- Spacer to ensure left alignment if flex-grow used, or use justify-content: start/space-between --}}
    </div>

    {{-- Tombol layar --}}
    <div class="no-print">
        <a href="{{ route('pum-requests.show', $pumRequest) }}" class="btn-back">← Kembali</a>
        <button onclick="window.print()" class="btn-print">🖨 Cetak</button>
    </div>

</body>
</html>
