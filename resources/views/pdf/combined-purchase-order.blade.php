<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order - {{ $purchaseOrder->no_surat }}</title>
    <style>
        @page { 
            margin: 1.5cm 2cm; 
            size: A4; 
        }
        
        body {
            font-family: Calibri, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        /* ========== SURAT PERMOHONAN STYLES ========== */
        .header { 
            text-align: center; 
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 3px double #000;
        }
        
        .company-name { 
            font-size: 11pt; 
            font-weight: bold; 
            margin-bottom: 3px;
        }
        
        .address { 
            font-size: 10pt; 
            margin-bottom: 5px;
        }
        
        .document-number { 
            font-size: 10pt; 
            font-weight: bold;
        }

        .box-container { 
            display: table;
            width: 100%; 
            margin: 8px 0;
            border-collapse: separate;
            border-spacing: 10px 0;
        }
        
        .box { 
            display: table-cell;
            width: 42%; 
            border: 1px solid #000; 
            padding: 6px 8px;
            vertical-align: top;
        }
        
        .box-item { 
            margin-bottom: 6px; 
            min-height: 22px;
            font-size: 9pt;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }
        
        .box-item:last-child {
            border-bottom: none;
        }
        
        .box-item strong {
            display: inline-block;
            width: 100%;
            margin-bottom: 2px;
        }

        .title { 
            text-align: center; 
            font-weight: bold; 
            margin: 12px 0 10px 0;
            font-size: 10pt;
            line-height: 1.4;
        }
        
        .content { 
            margin: 8px 0; 
            line-height: 1.4; 
            text-align: justify;
            font-size: 10pt;
        }

        .surat-permohonan table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 8px 0;
            font-size: 9pt;
        }
        
        .surat-permohonan table th, .surat-permohonan table td { 
            border: 1px solid #000; 
            padding: 3px 4px; 
            text-align: center;
        }
        
        .surat-permohonan table th { 
            background: #f0f0f0;
            font-weight: bold;
        }

        .footer-text {
            margin: 8px 0;
            font-size: 9pt;
            line-height: 1.3;
        }

        .signature { 
            margin-top: 10px; 
            text-align: right;
            font-size: 9pt;
            line-height: 1.5;
            display: inline-block;
            float: right;
            text-align: center;
        }
        
        .signature-space {
            margin-top: 40px;
            margin-bottom: 0;
        }

        .approval-box {
            margin-top: 140px;
            border: 1px solid #000;
            padding: 8px;
            page-break-inside: avoid;
            width: 50%;
            float: left;
            clear: both;
        }
        
        .approval-title {
            text-align: center; 
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 9pt;
        }
        
        .approval-content {
            font-size: 9pt;
            line-height: 1.4;
        }
        
        .checkbox-item {
            margin: 3px 0;
            position: relative;
            padding-left: 20px;
        }
        
        .checkbox-box {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #000;
            position: absolute;
            left: 0;
            top: 2px;
        }
        
        /* ========== FORM PENAWARAN STYLES ========== */
        .fp-header {
            text-align: left;
            margin-bottom: 15px;
        }
        
        .fp-company-name {
            font-size: 9pt;
            font-weight: normal;
        }
        
        .fp-address {
            font-size: 9pt;
        }
        
        .fp-title {
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
            font-size: 11pt;
        }
        
        .fp-info-section {
            margin: 15px 0;
            font-size: 9pt;
        }
        
        .fp-info-title {
            font-weight: bold;
            margin-bottom: 8px;
            text-decoration: underline;
        }
        
        .fp-info-grid {
            display: table;
            width: 100%;
        }
        
        .fp-info-row {
            display: table-row;
        }
        
        .fp-info-label {
            display: table-cell;
            width: 18%;
            padding: 1px 0;
            white-space: nowrap;
        }
        
        .fp-info-separator {
            display: table-cell;
            width: 1%;
        }
        
        .fp-info-value {
            display: table-cell;
            width: 31%;
            padding: 1px 0;
        }
        
        .fp-statement-section {
            margin: 2px 0;
            text-align: justify;
            line-height: 1.3;
        }
        
        .fp-statement-section ol {
            margin: 2px 0;
            padding-left: 20px;
        }
        
        .fp-statement-section li {
            margin: 2px 0;
        }
        
        .fp-signature-container {
            margin-top: 20px;
            display: table;
            width: 100%;
        }
        
        .fp-signature-box {
            display: table-cell;
            width: 2%;
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: top;
            font-size: 8pt;
        }
        
        .fp-signature-box-right {
            display: table-cell;
            width: 2%;
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: top;
            font-size: 8pt;
        }
    </style>
</head>
<body>

@php
    $namaPerusahaanLengkap = isset($mitra) && $mitra ? $mitra->nama_perusahaan_lengkap : ($purchaseOrder->nama_perusahaan ?? '');
@endphp

<!-- Page 1: Surat Permohonan -->
<div class="surat-permohonan" style="font-size: 10pt; line-height: 1.3;">
    <div class="header">
        <div class="company-name">{{ $namaPerusahaanLengkap }}</div>
        <div class="address">{{ $mitra->alamat_perusahaan ?? '' }}</div>
        <div class="document-number">NO {{ $purchaseOrder->no_surat }}</div>
    </div>

    <div class="box-container">
        <div class="box">
            <div class="box-item"><strong>Agenda No.</strong>{{ $purchaseOrder->agenda_no ?? '' }}</div>
            <div class="box-item"><strong>Tgl. Terima</strong>{{ $purchaseOrder->tanggal_terima ? $purchaseOrder->tanggal_terima->format('d/m/Y') : '' }}</div>
            <div class="box-item"><strong>Paraf</strong>{{ $purchaseOrder->paraf ?? '' }}</div>
        </div>
        <div class="box">
            <div class="box-item"><strong>KONTRAK YLL</strong>{{ $purchaseOrder->kontrak_yll ?? '' }}</div>
            <div class="box-item"><strong>REALISASI S/D</strong></div>
            <div class="box-item"><strong>DISETUJUI/TIDAK</strong></div>
        </div>
    </div>

    <div class="title">
        SURAT PERMOHONAN ORDER PEMBELIAN (OP)<br>
        PENGADAAN <strong>{{ strtoupper($purchaseOrder->jenis_komoditas_lengkap) }}</strong> DALAM NEGERI TAHUN {{ date('Y') }}
    </div>

    <div class="content">
        Kepada yth,<br>
        Pemimpin/Wakil Pemimpin Cabang Surakarta<br>
        Di Tempat
    </div>

    <div class="content">
        Bersama ini kami <strong>{{ $namaPerusahaanLengkap }}</strong> bermohon untuk ikut serta dalam rangka pengadaan <strong>{{ strtoupper($purchaseOrder->jenis_komoditas_lengkap) }}</strong> dalam negeri tahun {{ date('Y') }} dengan mengajukan penawaran untuk menyediakan komoditas sebagai berikut :
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Jenis Komoditas</th>
                <th style="width: 15%;">Harga (Rp/Kg)</th>
                <th style="width: 15%;">Kuantum (Kg)</th>
                <th style="width: 20%;">Nilai (Rp)</th>
                <th style="width: 20%;">Komp. Pergud.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchaseOrder->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ strtoupper($purchaseOrder->jenis_komoditas_lengkap) }} - {{ $item->kualitas_lengkap }}</td>
                <td>{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td>{{ number_format($item->kuantum, 0, ',', '.') }}</td>
                <td>{{ number_format($item->nilai, 0, ',', '.') }}</td>
                <td>{{ $item->komplek_pergudangan_lengkap }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer-text">
        Kami bersedia tunduk dan patuh terhadap ketentuan yang tercantum dalam order pembelian <strong>{{ strtoupper($purchaseOrder->jenis_komoditas_lengkap) }}</strong> Dalam Negeri dan peraturan yang berlaku di Perusahaan Umum (Perum) BULOG.
    </div>

    <div class="footer-text">
        Demikian disampaikan dan atas persetujuan dan kerja samanya diucapkan terima kasih.
    </div>

    <div class="signature">
        <div>Surakarta, {{ $tanggal }}</div>
        <div style="margin-top: 5px;">Pemohon</div>
        <div class="signature-space"></div>
        <div style="margin-top: 5px;">({{ strtoupper($mitra->nama_cp ?? $purchaseOrder->created_by ?? '') }})</div>
        <div style="margin-top: 2px;">{{ $namaPerusahaanLengkap }}</div>
    </div>

    <div class="approval-box">
        <div class="approval-title">ASSMAN PENGADAAN</div>
        <div class="approval-content">
            <strong>PERSETUJUAN</strong><br>
            <strong>GUDANG</strong>
        </div>
        <div class="approval-content" style="margin-top: 5px;">
            <strong>Disposisi:</strong><br>
            <div class="checkbox-item"><span class="checkbox-box"></span> Cek dan Koordinasikan</div>
            <div class="checkbox-item"><span class="checkbox-box"></span> Tindak Lanjut Sesuai Ketentuan</div>
            <div class="checkbox-item"><span class="checkbox-box"></span> Tertib Administrasi dan GCG</div>
            <div class="checkbox-item"><span class="checkbox-box"></span> .......................................</div>
        </div>
    </div>
</div>

<div class="page-break"></div>

<!-- Page 2: Form Penawaran -->
<div class="form-penawaran" style="font-size: 9pt; line-height: 1.4;">
    <div class="fp-header">
        <div class="fp-company-name">Perum BULOG</div>
        <div class="fp-address">Kantor Cabang Surakarta</div>
    </div>

    <div class="fp-title">
        FORM PENAWARAN {{ strtoupper($purchaseOrder->jenis_komoditas_lengkap) }} DALAM NEGERI
    </div>

    <div class="fp-info-section">
        <div class="fp-info-title">DATA INFORMASI PEMASOK</div>
        <div class="fp-info-grid">
            <div class="fp-info-row">
                <div class="fp-info-label">Nama Pemasok</div>
                <div class="fp-info-separator">:</div>
                <div class="fp-info-value">{{ $namaPerusahaanLengkap }}</div>
                <div class="fp-info-label" style="padding-left: 50px;">Nama Bank</div>
                <div class="fp-info-separator" style="padding-left: 25px;">:</div>
                <div class="fp-info-value" style="padding-left: 15px;">{{ $mitra->bank_korespondensi ?? '' }}</div>
            </div>
            <div class="fp-info-row">
                <div class="fp-info-label">Alamat Pemasok</div>
                <div class="fp-info-separator">:</div>
                <div class="fp-info-value">{{ $mitra->alamat_perusahaan ?? '' }}</div>
                <div class="fp-info-label" style="padding-left: 50px;">Nomor Rekening</div>
                <div class="fp-info-separator" style="padding-left: 25px;">:</div>
                <div class="fp-info-value" style="padding-left: 15px;">{{ $mitra->no_rekening ?? '' }}</div>
            </div>
            <div class="fp-info-row">
                <div class="fp-info-label">Nomor KTP</div>
                <div class="fp-info-separator">:</div>
                <div class="fp-info-value">{{ $mitra->nik ?? '' }}</div>
                <div class="fp-info-label" style="padding-left: 50px;">Nama Pemilik</div>
                <div class="fp-info-separator" style="padding-left: 25px;">:</div>
                <div class="fp-info-value" style="padding-left: 15px;">{{ $mitra->nama_cp ?? '' }}</div>
            </div>
            <div class="fp-info-row">
                <div class="fp-info-label">Nomor Telepon/HP</div>
                <div class="fp-info-separator">:</div>
                <div class="fp-info-value">{{ $mitra->no_telp_cp ?? $mitra->no_telp_perusahaan ?? '' }}</div>
                <div class="fp-info-label" style="padding-left: 50px;">NPWP</div>
                <div class="fp-info-separator" style="padding-left: 25px;">:</div>
                <div class="fp-info-value" style="padding-left: 15px;">{{ $mitra->npwp ?? '' }}</div>
            </div>
            <div class="fp-info-row">
                <div class="fp-info-label">Kota</div>
                <div class="fp-info-separator">:</div>
                <div class="fp-info-value">{{ $mitra->kota_kabupaten ?? '' }}</div>
                <div class="fp-info-label" style="padding-left: 50px;">NPPK</div>
                <div class="fp-info-separator" style="padding-left: 25px;">:</div>
                <div class="fp-info-value" style="padding-left: 15px;">{{ strtoupper($mitra->pkp ?? '') }}</div>
            </div>
            <div class="fp-info-row">
                <div class="fp-info-label">Nama Pejabat</div>
                <div class="fp-info-separator">:</div>
                <div class="fp-info-value">{{ $mitra->nama_cp ?? '' }}</div>
                <div class="fp-info-label"></div>
                <div class="fp-info-separator"></div>
                <div class="fp-info-value"></div>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 60px;"></div>

    <div class="fp-statement-section">
        <div class="fp-info-title">PERNYATAAN PEMASOK</div>
        <ol>
            <li>Bersedia tunduk dan patuh terhadap seluruh pernyataan, ketentuan, prosedur maupun instruksi yang berlaku dalam Pengadaan Gabah/Beras/Beras Negeri Perum BULOG maupun instruksi yang berlaku dalam Pengadaan Gabah/Beras/Beras Negeri Perum BULOG secara mutlak dan/atau tidak akan menggugat secara hukum.</li>
            <li>Menyampaikan penawaran komoditi sebagai berikut:</li>
        </ol>
    </div>

    @php
        $kualitasList = $purchaseOrder->items->pluck('kualitas_lengkap')->unique()->filter()->implode(' / ');
        $kuantumList = $purchaseOrder->items->pluck('kuantum')->map(function($k) {
            return number_format($k, 0, ',', '.') . ' Kg';
        })->implode(' / ');
    @endphp

    <div style="margin: 5px 0; padding-left: 40px;">
        <div style="margin-bottom: 3px; display: table; width: 100%;">
            <div style="display: table-row;">
                <div style="display: table-cell; width: 120px;">a. Jenis Komoditi</div>
                <div style="display: table-cell; width: 10px;">:</div>
                @php
                    $commodityUpper = strtoupper($purchaseOrder->jenis_komoditas_lengkap);
                    $kualitasCollection = $purchaseOrder->items->pluck('kualitas_lengkap')->unique()->filter();
                    $jenisKomoditiDenganKualitasList = $kualitasCollection->map(function($k) use ($commodityUpper) {
                        return $commodityUpper.' - '.$k;
                    })->implode(' / ');
                    if (empty($jenisKomoditiDenganKualitasList)) { $jenisKomoditiDenganKualitasList = $commodityUpper; }
                @endphp
                <div style="display: table-cell;">{{ $jenisKomoditiDenganKualitasList }}</div>
            </div>
        </div>
        <div style="margin-bottom: 5px; display: table; width: 100%;">
            <div style="display: table-row;">
                <div style="display: table-cell; width: 120px;">b. Kualitas</div>
                <div style="display: table-cell; width: 10px;">:</div>
                <div style="display: table-cell;">{{ $kualitasList }}</div>
            </div>
        </div>
        <div style="margin-bottom: 5px; display: table; width: 100%;">
            <div style="display: table-row;">
                <div style="display: table-cell; width: 120px;">c. Kuantum</div>
                <div style="display: table-cell; width: 10px;">:</div>
                <div style="display: table-cell;">{{ $kuantumList }}</div>
            </div>
        </div>
    </div>

    <div class="fp-statement-section" style="margin-top: 10px;">
        <ol start="3">
            <li>Bersedia menerima hasil pemeriksaan kualitas yang dilakukan oleh pemeriksa yang ditunjuk Perum BULOG secara mutlak dan/atau tidak akan menggugat secara hukum.</li>
        </ol>
    </div>

    <div class="fp-signature-container" style="margin-top: 100px;">
        <div class="fp-signature-box" style="height: 110px; position: relative;">
            <div style="padding-bottom: 2px; margin: 0 -1px; font-size: 8pt; font-weight: normal;">Tempat :</div>
            <div style="position: absolute; top: 18px; left: 0; right: 0; border-bottom: 1px solid #000; margin: 0 -1px;"></div>
            <div style="padding-bottom: 2px; margin: 0 -1px; font-size: 8pt; font-weight: normal;">Tanggal :</div>
            <div style="position: absolute; top: 36px; left: 0; right: 0; border-bottom: 1px solid #000; margin: 0 -1px;"></div>
            <div style="flex-grow: 1;"></div>
            <div style="position: absolute; bottom: 20px; left: 0; right: 0; border-bottom: 1px solid #000; margin: 0 -1px;"></div>
            <div style="position: absolute; bottom: 4px; left: 0; right: 0; text-align: center; font-size: 7pt;">Tanda tangan dan nama lengkap</div>
        </div>
        <div style="display: table-cell; width: 4%;"></div>
        <div class="fp-signature-box-right" style="height: 105px; position: relative;">
            <div style="padding-bottom: 2px; margin: 0 -1px; font-size: 8pt; text-align: center; font-weight: normal;">Pengadaan</div>
            <div style="position: absolute; top: 25px; left: 0; right: 0; border-bottom: 1px solid #000; margin: 0 -1px;"></div>
            <div style="flex-grow: 1;"></div>
            <div style="position: absolute; bottom: 15px; left: 0; right: 0; border-bottom: 1px solid #000; margin: 0 -1px;"></div>
            <div style="position: absolute; bottom: 1px; left: 0; right: 0; text-align: center; font-size: 7pt;">Tanda tangan dan nama lengkap</div>
        </div>
    </div>
</div>

</body>
</html>