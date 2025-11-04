<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $purchaseOrder->nama_perusahaan }}</title>
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
        
        .page-break {
            page-break-before: always;
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
            font-size: 14pt;
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
            font-size: 10pt;
        }
        
        .commodity-table td {
            font-size: 10pt;
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
        
        /* Form Penawaran specific styles */
        .form-section {
            margin-bottom: 20px;
        }
        
        .form-row {
            display: flex;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .form-label {
            width: 30%;
            font-weight: bold;
        }
        
        .form-value {
            width: 70%;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            min-height: 20px;
        }
        
        .terms {
            margin: 20px 0;
            text-align: justify;
            line-height: 1.5;
        }
        
        .signature-both {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-section {
            width: 45%;
            text-align: center;
        }
        
        .signature-section .signature-line {
            margin-top: 60px;
            border-bottom: 1px solid #000;
            margin-bottom: 10px;
            width: 100%;
            margin-left: 0;
        }
        
        .checkbox-item {
            margin: 5px 0;
        }
        
        .checkbox-item input[type="checkbox"] {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <!-- HALAMAN 1: SURAT PERMOHONAN -->
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
                <th>Harga (Rp/Kg)</th>
                <th>Kuantum (Kg)</th>
                <th>Nilai (Rp)</th>
                <th>Komp. Pergud.</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchaseOrder->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->jenis_komoditas_lengkap }} - {{ $item->kualitas_lengkap }}</td>
                <td>{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td>{{ number_format($item->kuantum, 0, ',', '.') }}</td>
                <td>{{ number_format($item->nilai, 0, ',', '.') }}</td>
                <td>{{ $item->komplek_pergudangan_lengkap }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #999;">Tidak ada data item</td>
            </tr>
            @endforelse
            @if($purchaseOrder->items->count() > 0)
            <tr style="font-weight: bold; background-color: #f5f5f5;">
                <td colspan="4" style="text-align: right;">TOTAL:</td>
                <td>{{ number_format($purchaseOrder->total_nilai, 0, ',', '.') }}</td>
                <td>-</td>
            </tr>
            @endif
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

    <!-- HALAMAN 2: FORM PENAWARAN -->
    <div class="page-break">
        <div class="header">
            <div class="form-value">{{ $purchaseOrder->nama_perusahaan }}</div>
            <div class="form-value">Kalitengah RT 002 RW 001 Karangdowo, Kab. Klaten</div>
        </div>

        <div class="title">
            FORM PENAWARAN<br>
            PENGADAAN {{ strtoupper($purchaseOrder->jenis_komoditas_lengkap) }}
        </div>

        <div class="form-section">
            <div class="form-row">
                <div class="form-label">Nama Perusahaan:</div>
                <div class="form-value">{{ $purchaseOrder->nama_perusahaan }}</div>
            </div>
            
            <div class="form-row">
                <div class="form-label">Jenis Komoditas:</div>
                <div class="form-value">{{ $purchaseOrder->jenis_komoditas_lengkap }}</div>
            </div>
            
            <div class="form-row">
                <div class="form-label">Jenis Pengadaan:</div>
                <div class="form-value">{{ $purchaseOrder->jenis_pengadaan }}</div>
            </div>
            
            <div class="form-row">
                <div class="form-label">Jumlah Item:</div>
                <div class="form-value">{{ $purchaseOrder->items->count() }} item</div>
            </div>
            
            <div class="form-row">
                <div class="form-label">Total Nilai:</div>
                <div class="form-value">Rp {{ number_format($purchaseOrder->total_nilai, 0, ',', '.') }}</div>
            </div>
            
            <div class="form-row">
                <div class="form-label">Tanggal Penawaran:</div>
                <div class="form-value">{{ $tanggal }}</div>
            </div>
        </div>

        <table class="commodity-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Jenis Komoditas</th>
                    <th>Harga (Rp/Kg)</th>
                    <th>Kuantum (Kg)</th>
                    <th>Nilai (Rp)</th>
                    <th>Komp. Pergud.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchaseOrder->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->jenis_komoditas_lengkap }} - {{ $item->kualitas_lengkap }}</td>
                    <td>{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->kuantum, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->nilai, 0, ',', '.') }}</td>
                    <td>{{ $item->komplek_pergudangan_lengkap }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">Tidak ada data item</td>
                </tr>
                @endforelse
                @if($purchaseOrder->items->count() > 0)
                <tr style="font-weight: bold; background-color: #f5f5f5;">
                    <td colspan="4" style="text-align: right;">TOTAL:</td>
                    <td>{{ number_format($purchaseOrder->total_nilai, 0, ',', '.') }}</td>
                    <td>-</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="terms">
            <p><strong>SYARAT DAN KETENTUAN:</strong></p>
            <ol>
                <li>Harga yang ditawarkan sudah termasuk semua biaya sampai dengan gudang BULOG.</li>
                <li>Pembayaran dilakukan setelah barang diterima dan sesuai dengan spesifikasi yang diminta.</li>
                <li>Waktu pengiriman maksimal 7 (tujuh) hari setelah kontrak ditandatangani.</li>
                <li>Barang yang tidak sesuai spesifikasi akan ditolak dan menjadi tanggung jawab pemasok.</li>
                <li>Penawaran ini berlaku selama 30 (tiga puluh) hari sejak tanggal penawaran.</li>
            </ol>
        </div>

        <div class="signature-both">
            <div class="signature-section">
                <p><strong>PENAWAR</strong></p>
                <div class="signature-line"></div>
                <p><strong>{{ strtoupper($purchaseOrder->created_by ?? 'BAGYO SUPRAPTO') }}</strong><br>
                {{ $purchaseOrder->nama_perusahaan }}</p>
            </div>
            
            <div class="signature-section">
                <p><strong>PERUSAHAAN UMUM BULOG</strong></p>
                <div class="signature-line"></div>
                <p><strong>(.............................)</strong><br>
                Pejabat yang Berwenang</p>
            </div>
        </div>

        <div style="margin-top: 30px; border: 1px solid #000; padding: 10px;">
            <p><strong>PERSETUJUAN / DISPOSISI:</strong></p>
            <div class="checkbox-item">
                <input type="checkbox"> Disetujui untuk dilanjutkan ke proses kontrak
            </div>
            <div class="checkbox-item">
                <input type="checkbox"> Perlu klarifikasi teknis
            </div>
            <div class="checkbox-item">
                <input type="checkbox"> Perlu negosiasi harga
            </div>
            <div class="checkbox-item">
                <input type="checkbox"> Ditolak dengan alasan: _________________________
            </div>
            
            <div style="margin-top: 20px;">
                <p>Tanggal: _______________</p>
                <p>Paraf: _______________</p>
            </div>
        </div>
    </div>
</body>
</html>