<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Penawaran</title>
    <style>
        @page {
            margin: 1.5cm 2cm;
            size: A4;
        }
        
        body {
            font-family: Calibri, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: left;
            margin-bottom: 15px;
        }
        
        .company-name {
            font-size: 9pt;
            font-weight: normal;
        }
        
        .address {
            font-size: 9pt;
        }
        
        .title {
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
            font-size: 11pt;
        }
        
        .info-section {
            margin: 15px 0;
            font-size: 9pt;
        }
        
        .info-title {
            font-weight: bold;
            margin-bottom: 8px;
            text-decoration: underline;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 18%;
            padding: 1px 0;
            white-space: nowrap;
        }
        
        .info-separator {
            display: table-cell;
            width: 1%;
        }
        
        .info-value {
            display: table-cell;
            width: 31%;
            padding: 1px 0;
        }
        
        .statement-section {
            margin: 2px 0;
            text-align: justify;
            line-height: 1.3;
        }
        
        .statement-section ol {
            margin: 2px 0;
            padding-left: 20px;
        }
        
        .statement-section li {
            margin: 2px 0;
        }
        
        .signature-container {
            margin-top: 20px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 2%;
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: top;
            font-size: 8pt;
        }
        
        .signature-box-right {
            display: table-cell;
            width: 2%;
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: top;
            font-size: 8pt;
        }
        
        .signature-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 8pt;
        }
        
        .signature-label {
            margin-top: 2px;
            font-size: 8pt;
        }
        
        .signature-space {
            height: 50px;
        }
        
        .signature-name {
            text-align: center;
            margin-top: 2px;
            font-size: 7pt;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Perum BULOG</div>
        <div class="address">Kantor Cabang Surakarta</div>
    </div>

    <div class="title">
        FORM PENAWARAN <strong>{{ strtoupper($purchaseOrder->jenis_komoditas_lengkap) }}</strong> DALAM NEGERI
    </div>

    <div class="info-section">
        <div class="info-title">DATA INFORMASI PEMASOK</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nama Pemasok</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $purchaseOrder->nama_perusahaan }}</div>
                <div class="info-label" style="padding-left: 50px;">Nama Bank</div>
                <div class="info-separator" style="padding-left: 25px;">:</div>
                <div class="info-value" style="padding-left: 15px;">{{ $mitra->bank_korespondensi ?? '' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Alamat Pemasok</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $mitra->alamat_perusahaan ?? '' }}</div>
                <div class="info-label" style="padding-left: 50px;">Nomor Rekening</div>
                <div class="info-separator" style="padding-left: 25px;">:</div>
                <div class="info-value" style="padding-left: 15px;">{{ $mitra->no_rekening ?? '' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nomor KTP</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $mitra->nik ?? '' }}</div>
                <div class="info-label" style="padding-left: 50px;">Nama Pemilik</div>
                <div class="info-separator" style="padding-left: 25px;">:</div>
                <div class="info-value" style="padding-left: 15px;">{{ $mitra->nama_pemilik_rekening ?? '' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nomor Telepon/HP</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $mitra->no_telp_cp ?? $mitra->no_telp_perusahaan ?? '' }}</div>
                <div class="info-label" style="padding-left: 50px;">NPWP</div>
                <div class="info-separator" style="padding-left: 25px;">:</div>
                <div class="info-value" style="padding-left: 15px;">{{ $mitra->npwp ?? '' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kota</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $mitra->kota_kabupaten ?? '' }}</div>
                <div class="info-label" style="padding-left: 50px;">NPPK</div>
                <div class="info-separator" style="padding-left: 25px;">:</div>
                <div class="info-value" style="padding-left: 15px;">{{ strtoupper($mitra->pkp ?? '') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nama Pejabat</div>
                <div class="info-separator">:</div>
                <div class="info-value">{{ $mitra->nama_cp ?? '' }}</div>
                <div class="info-label"></div>
                <div class="info-separator"></div>
                <div class="info-value"></div>
            </div>
            
        </div>
    </div>

    <div style="margin-bottom: 60px;"></div>

    <div class="statement-section">
        <div class="info-title">PERNYATAAN PEMASOK</div>
        <ol>
            <li>Bersedia tunduk dan patuh terhadap seluruh pernyataan, ketentuan, prosedur maupun instruksi yang berlaku dalam Pengadaan <strong>{{ strtoupper($purchaseOrder->jenis_komoditas_lengkap) }}</strong> Dalam Negeri Perum BULOG.</li>
            <li>Menyampaikan penawaran komoditi sebagai berikut:</li>
        </ol>
    </div>

    @php
        // Gabungkan semua kualitas dengan pemisah /
        $kualitasCollection = $purchaseOrder->items->pluck('kualitas_lengkap')->unique()->filter();
        $kualitasList = $kualitasCollection->implode(' / ');
        $commodityUpper = strtoupper($purchaseOrder->jenis_komoditas_lengkap);
        $jenisKomoditiDenganKualitasList = $kualitasCollection->map(function($k) use ($commodityUpper) {
            return $commodityUpper.' - '.$k;
        })->implode(' / ');
        if (empty($jenisKomoditiDenganKualitasList)) {
            $jenisKomoditiDenganKualitasList = $commodityUpper;
        }
        
        // Gabungkan semua kuantum dengan pemisah /
        $kuantumList = $purchaseOrder->items->pluck('kuantum')->map(function($k) {
            return number_format($k, 0, ',', '.') . ' Kg';
        })->implode(' / ');
    @endphp

    <div style="margin: 5px 0; padding-left: 40px;">
        <div style="margin-bottom: 3px; display: table; width: 100%;">
            <div style="display: table-row;">
                <div style="display: table-cell; width: 120px;">a. Jenis Komoditi</div>
                <div style="display: table-cell; width: 10px;">:</div>
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

    <div class="statement-section" style="margin-top: 10px;">
        <ol start="3">
            <li>Bersedia menerima hasil pemeriksaan kualitas yang dilakukan oleh pemeriksa yang ditunjuk Perum BULOG secara mutlak dan/atau tidak akan menggugat secara hukum.</li>
        </ol>
    </div>

    <div class="signature-container" style="margin-top: 100px;">
        <div class="signature-box" style="height: 125px; position: relative;">
            <div style="padding-bottom: 2px; margin: 0 -1px; font-size: 8pt; font-weight: normal;">Tempat : Surakarta</div>
            <div style="position: absolute; top: 18px; left: 0; right: 0; border-bottom: 1px solid #000; margin: 0 -1px;"></div>
            <div style="padding-bottom: 2px; margin: 0 -1px; font-size: 8pt; font-weight: normal;">Tanggal : {{ $tanggal }}</div>
            <div style="position: absolute; top: 36px; left: 0; right: 0; border-bottom: 1px solid #000; margin: 0 -1px;"></div>
            <div style="flex-grow: 1;"></div>
            <div style="position: absolute; bottom: 30px; left: 0; right: 0; border-bottom: 1px solid #000; margin: 0 -1px;"></div>
            <div style="position: absolute; bottom: 15px; left: 0; right: 0; text-align: center; font-size: 7pt;"><strong>{{ strtoupper($nama_penandatangan ?? '') }}</strong></div>
            <div style="position: absolute; bottom: 1px; left: 0; right: 0; text-align: center; font-size: 7pt;">{{ $purchaseOrder->nama_perusahaan }}</div>
        </div>
        <div style="display: table-cell; width: 4%;"></div>
        <div class="signature-box-right" style="height: 105px; position: relative;">
            <div style="padding-bottom: 2px; margin: 0 -1px; font-size: 8pt; text-align: center; font-weight: normal;">Pengadaan</div>
            <div style="position: absolute; top: 30px; left: 0; right: 0; border-bottom: 1px solid #000; margin: 0 -1px;"></div>
            <div style="flex-grow: 1;"></div>
            <div style="position: absolute; bottom: 12px; left: 0; right: 0; border-bottom: 1px solid #000; margin: 0 -1px;"></div>
            <div style="position: absolute; bottom: -18px; left: 0; right: 0; text-align: center; font-size: 7pt;">Tanda tangan dan nama lengkap</div>
        </div>
    </div>

</body>
</html>