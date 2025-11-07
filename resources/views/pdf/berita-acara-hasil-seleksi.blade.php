<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Hasil Seleksi</title>
    <style>
        @page {
            margin: 2.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
        }
        .content {
            text-align: justify;
            margin-bottom: 20px;
        }
        .data-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .data-table td:first-child {
            width: 200px;
        }
        .data-table td:nth-child(2) {
            width: 20px;
        }
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .result-table th,
        .result-table td {
            border: 1px solid black;
            padding: 8px;
        }
        .result-table th {
            background-color: #f5f5f5;
            text-align: center;
            font-weight: bold;
        }
        .conclusion {
            margin: 20px 0;
            text-align: justify;
        }
        .signatures {
            margin-top: 60px;
            width: 100%;
            position: relative;
            min-height: 250px;
        }
        .signature-row {
            position: relative;
            width: 100%;
            margin-bottom: 100px;
        }
        .signature-box {
            position: absolute;
            width: 45%;
        }
        .signature-box:first-child {
            left: 0;
        }
        .signature-box:last-child {
            right: 0;
        }
        .signature-center {
            width: 45%;
            margin: 0 auto;
            text-align: center;
            clear: both;
        }
        .signature-space {
            height: 80px;
        }
        .signature-text {
            margin: 0;
            line-height: 1.5;
        }
        .data-info {
        margin-top: 10px;
        margin-bottom: 10px;
        }
        .info-table {
            border-spacing: 0;
            margin-bottom: 10px;
        }
        
        .info-table td {
            padding: 2px 0;
        }
        
        .data-table {
            border-spacing: 0;
            margin-top: 5px;
        }
        
        .data-table td {
            padding: 2px 0;
        }
        
        .section-title {
            font-weight: bold;
            margin: 10px 0 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BERITA ACARA</h1>
        <h1>HASIL SELEKSI MITRA PANGAN</h1>
        <h1>PENGADAAN GABAH/BERAS DALAM NEGERI</h1>
        <h1>Nomor: {{ $nomor_surat }}</h1>
        <h1>TAHUN {{ $tahun }}</h1>
    </div>

    <div class="content">
        <p>Pada hari ini {{ $hari }} tanggal {{ $tanggal }}, telah diselesaikan proses seleksi terhadap Calon Mitra Pangan ADA Gabah/Beras DN sebagai berikut :</p>
    </div>

    <div class="data-info">
        <table class="info-table">
            <tr>
                <td style="width: 200px">NOMOR URUT SELEKSI</td>
                <td style="width: 20px">:</td>
                <td>{{ $nomor_urut_seleksi }}</td>
            </tr>
            <tr>
                <td>TANGGAL SELEKSI</td>
                <td>:</td>
                <td>{{ $tanggal_seleksi }}</td>
            </tr>
            <tr>
                <td>NOMOR ENTITAS BULOG</td>
                <td>:</td>
                <td>{{ $nomor_entitas_bulog }}</td>
            </tr>
            <tr>
                <td>UNIT PELAKSANA SELEKSI</td>
                <td>:</td>
                <td>{{ $unit_pelaksana }}</td>
            </tr>
        </table>

        <p class="section-title">DATA MITRA KERJA,</p>

        <table class="data-table">
            <tr>
                <td style="width: 200px">1. Nama Perusahaan</td>
                <td style="width: 20px">:</td>
                <td>{{ $nama_perusahaan }}</td>
            </tr>
            <tr>
                <td>2. Badan Hukum/Usaha</td>
                <td>:</td>
                <td>{{ $badan_usaha }}</td>
            </tr>
            <tr>
                <td>3. Alamat Perusahaan</td>
                <td>:</td>
                <td>{{ $alamat }}</td>
            </tr>
            <tr>
                <td>4. Status</td>
                <td>:</td>
                <td>{{ $status_mitra }}</td>
            </tr>
        </table>
    </div>

    <table class="result-table">
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="45%">Persyaratan</th>
                <th width="45%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>Dokumen Perijinan</td>
                <td>{{ $hasil_seleksi->kesimpulan_dokumen }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Sarana Pengeringan</td>
                <td>{{ $hasil_seleksi->kesimpulan_sarana_pengeringan }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>Sarana Penggilingan</td>
                <td>{{ $hasil_seleksi->kesimpulan_sarana_penggilingan }}</td>
            </tr>
        </tbody>
    </table>

    <div class="conclusion">
        <p>Berdasarkan hasil tersebut, maka {{ $nama_perusahaan }} dinyatakan {{ $hasil_akhir }}</p>
    </div>

    <div class="signatures">
        <div class="signature-row">
            <!-- Left signature -->
            <div class="signature-box">
                <p class="signature-text">Mitra Pangan,</p>
                <div class="signature-space"></div>
                <p class="signature-text"><u>{{ $nama_pj_mitra }}</u></p>
                <p class="signature-text">{{ $nama_perusahaan }}</p>
            </div>

            <!-- Right signature -->
            <div class="signature-box">
                <p class="signature-text">Perum BULOG Kantor Cabang {{ $unit_pelaksana }}</p>
                <div class="signature-space"></div>
                <p class="signature-text"><u>{{ $pelaksana->nama_karyawan }}</u></p>
                <p class="signature-text">{{ $pelaksana->jabatan }}</p>
            </div>
        </div>

        <!-- Bottom center signature -->
        <div class="signature-center">
            <p class="signature-text">Mengetahui,</p>
            <div class="signature-space"></div>
            <p class="signature-text"><u>{{ $pengetahui->nama_karyawan }}</u></p>
            <p class="signature-text">{{ $pengetahui->jabatan }}</p>
        </div>
    </div>
</body>
</html>