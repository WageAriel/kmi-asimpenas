<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Permohonan Order Pembelian</title>
    <style>
        @page {
            margin: 2cm;
            size: A4;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .company-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .address {
            font-size: 10pt;
            margin-bottom: 10px;
        }
        
        .document-number {
            font-size: 10pt;
            margin-bottom: 20px;
        }
        
        .header-boxes {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .left-box, .right-box {
            display: table-cell;
            width: 48%;
            border: 1px solid #000;
            padding: 10px;
            vertical-align: top;
        }
        
        .left-box {
            margin-right: 4%;
        }
        
        .box-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .box-item {
            margin-bottom: 8px;
            min-height: 20px;
            border-bottom: 1px solid #ccc;
        }
        
        .title {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
            text-decoration: underline;
        }
        
        .content {
            text-align: justify;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .commodity-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .commodity-table th,
        .commodity-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        
        .commodity-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .signature {
            text-align: right;
            margin-top: 40px;
        }
        
        .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #000;
            width: 200px;
            margin-left: auto;
        }
        
        .note {
            margin-top: 30px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="form-value">{{ $purchaseOrder->nama_perusahaan }}</div>
        <div class="address">Kalitengah RT 002 RW 001 Karangdowo, Kab. Klaten</div>
        <div class="document-number">{{ $no_surat }}</div>
    </div>

    <div class="header-boxes">
        <div style="display: flex; justify-content: space-between;">
            <div class="left-box" style="width: 45%; display: inline-block; vertical-align: top;">
                <div class="box-item">
                    <strong>Agenda No.</strong><br>
                    {{ $purchaseOrder->agenda_no ?? '' }}
                </div>
                <div class="box-item">
                    <strong>Tgl. Terima</strong><br>
                    {{ $purchaseOrder->tanggal_terima ? $purchaseOrder->tanggal_terima->format('d/m/Y') : '' }}
                </div>
                <div class="box-item">
                    <strong>Paraf</strong><br>
                    {{ $purchaseOrder->paraf ?? '' }}
                </div>
            </div>
            
            <div class="right-box" style="width: 45%; display: inline-block; vertical-align: top; margin-left: 10%;">
                <div class="box-item">
                    <strong>KONTRAK YLL</strong><br>
                    {{ $purchaseOrder->kontrak_yll ?? '' }}
                </div>
                <div class="box-item">
                    <strong>REALISASI S/D</strong><br>
                </div>
                <div class="box-item">
                    <strong>DISETUJUI/TIDAK</strong><br>
                </div>
            </div>
        </div>
    </div>

    <div class="title">
        SURAT PERMOHONAN ORDER PEMBELIAN (OP)<br>
        PENGADAAN {{ $purchaseOrder->jenis_komoditas }} DALAM NEGERI TAHUN 2025
    </div>

    <div class="content">
        <p>Kepada yth,<br>
        Pemimpin/Wakil Pemimpin Cabang Surakarta<br>
        Di Tempat</p>

        <p>Bersama ini kami <strong>{{ $purchaseOrder->nama_perusahaan }}</strong> bermohon untuk ikut serta dalam rangka pengadaan <strong>{{ $purchaseOrder->jenis_komoditas_lengkap }}</strong>*) dalam negeri tahun 2025 dengan mengajukan penawaran untuk menyediakan komoditas sebagai berikut :</p>
    </div>

    <table class="commodity-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Jenis Komoditas</th>
                <th>Jenis Pengadaan</th>
                <th>Harga (Rp)</th>
                <th>Nilai (Rp)</th>
                <th>Komp. Pergud.</th>
                <th>Kualitas</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $purchaseOrder->jenis_komoditas_lengkap }}</td>
                <td>{{ $purchaseOrder->jenis_pengadaan }}</td>
                <td>{{ number_format($purchaseOrder->harga, 0, ',', '.') }}</td>
                <td>{{ number_format($purchaseOrder->nilai, 0, ',', '.') }}</td>
                <td>{{ $purchaseOrder->komplek_pergudangan_lengkap }}</td>
                <td>{{ $purchaseOrder->kualitas_lengkap }}</td>
            </tr>
        </tbody>
    </table>

    <div class="content">
        <p>Kami bersedia tunduk dan patuh terhadap ketentuan yang tercantum dalam order pembelian gabah / beras<br>
        *) Dalam Negeri dan peraturan yang berlaku di Perusahaan Umum (Perum) BULOG.</p>
        
        <p>Demikian disampaikan dan atas persetujuan dan kerja samanya diucapkan terima kasih.</p>
    </div>

    <div class="signature">
        <p>Surakarta, {{ $tanggal }}<br>
        Pemohon</p>
        
        <div class="signature-line"></div>
        <p><strong>{{ strtoupper($purchaseOrder->created_by ?? 'BAGYO SUPRAPTO') }}</strong><br>
        {{ $purchaseOrder->nama_perusahaan }}</p>
    </div>

    <div style="margin-top: 50px; border: 2px solid #000; padding: 15px;">
        <div style="text-align: center; font-weight: bold; margin-bottom: 10px;">ASSMAN PENGADAAN</div>
        <div style="display: flex; justify-content: space-between;">
            <div style="width: 30%;">
                <div style="border: 1px solid #000; padding: 10px; margin-bottom: 10px;">
                    <strong>PERSETUJUAN</strong>
                </div>
                <div style="border: 1px solid #000; padding: 10px;">
                    <strong>GUDANG</strong>
                </div>
            </div>
            <div style="width: 65%;">
                <div style="margin-bottom: 10px;"><strong>Disposisi:</strong></div>
                <div style="margin-bottom: 5px;">
                    <input type="checkbox"> Cek dan Koordinasikan
                </div>
                <div style="margin-bottom: 5px;">
                    <input type="checkbox"> Tindak Lanjut Sesuai Ketentuan
                </div>
                <div style="margin-bottom: 5px;">
                    <input type="checkbox"> Tertib Administrasi dan GCG
                </div>
                <div>
                    <input type="checkbox"> .....................................
                </div>
            </div>
        </div>
    </div>
</body>
</html>