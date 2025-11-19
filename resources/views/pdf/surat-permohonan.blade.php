<?php

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Permohonan OP</title>
    <style>
        @page { 
            margin: 1.5cm 2cm; 
            size: A4; 
        }
        
        body {
            font-family: Calibri, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        
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

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 8px 0;
            font-size: 9pt;
        }
        
        table th, table td { 
            border: 1px solid #000; 
            padding: 3px 4px; 
            text-align: center;
        }
        
        table th { 
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
        
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }
        
        .signature-company {
            margin-top: 2px;
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
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="company-name">{{ $mitra->nama_perusahaan ?? $purchaseOrder->nama_perusahaan }}</div>
        <div class="address">{{ $mitra->alamat_perusahaan ?? '' }}</div>
        <div class="document-number">NO {{ $purchaseOrder->no_surat }}</div>
    </div>

    <!-- Box Container -->
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

    <!-- Title -->
    <div class="title">
        SURAT PERMOHONAN ORDER PEMBELIAN (OP)<br>
        PENGADAAN {{ strtoupper($purchaseOrder->jenis_komoditas_lengkap) }} DALAM NEGERI TAHUN {{ date('Y') }}
    </div>

    <!-- Content -->
    <div class="content">
        Kepada yth,<br>
        Pemimpin/Wakil Pemimpin Cabang Surakarta<br>
        Di Tempat
    </div>

    <div class="content">
        Bersama ini kami <strong>{{ $mitra->nama_perusahaan ?? $purchaseOrder->nama_perusahaan }}</strong> bermohon untuk ikut serta dalam rangka pengadaan <strong>{{ strtoupper($purchaseOrder->jenis_komoditas_lengkap) }}</strong> dalam negeri tahun {{ date('Y') }} dengan mengajukan penawaran untuk menyediakan komoditas sebagai berikut :
    </div>

    <!-- Table -->
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

    <!-- Footer Text -->
    <div class="footer-text">
        Kami bersedia tunduk dan patuh terhadap ketentuan yang tercantum dalam order pembelian {{ strtoupper($purchaseOrder->jenis_komoditas_lengkap) }} Dalam Negeri dan peraturan yang berlaku di Perusahaan Umum (Perum) BULOG.
    </div>

    <div class="footer-text">
        Demikian disampaikan dan atas persetujuan dan kerja samanya diucapkan terima kasih.
    </div>

    <!-- Signature -->
    <div class="signature">
        <div>Surakarta, {{ $tanggal }}</div>
        <div style="margin-top: 5px;">Pemohon</div>
        <div class="signature-space"></div>
        <div style="margin-top: 5px;">({{ strtoupper($mitra->nama_cp ?? $purchaseOrder->created_by ?? '') }})</div>
        <div style="margin-top: 2px;">{{ $mitra->nama_perusahaan ?? $purchaseOrder->nama_perusahaan }}</div>
    </div>

    <!-- Approval Box -->
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

</body>
</html>
