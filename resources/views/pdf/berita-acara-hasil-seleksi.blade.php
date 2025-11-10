<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Hasil Seleksi</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            line-height: 1.35;
        }
        .header {
            text-align: center;
            margin-bottom: 18px;
        }
        .header h1 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }
        .content {
            text-align: justify;
            margin-bottom: 12px;
            font-size: 10pt;
        }
        .data-table {
            width: 100%;
            margin-bottom: 10px;
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
            margin: 12px 0;
        }
        .result-table th,
        .result-table td {
            border: 1px solid black;
            padding: 6px;
            vertical-align: top;
        }
        .result-table th {
            background-color: #f5f5f5;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }
        .result-table td {
            font-size: 9pt;
        }
        .conclusion {
            margin: 12px 0;
            text-align: justify;
            font-size: 10pt;
        }
        .signatures {
            margin-top: 25px;
            width: 100%;
        }
        .signature-row {
            display: table;
            width: 100%;
            margin-bottom: 25px;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        .signature-center {
            width: 50%;
            margin: 0 auto;
            text-align: center;
            margin-top: 20px;
        }
        .signature-space {
            height: 60px;
        }
        .signature-text {
            margin: 0;
            line-height: 1.35;
            font-size: 10pt;
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
            padding: 1.5px 0;
            font-size: 10pt;
        }
        
        .data-table {
            border-spacing: 0;
            margin-top: 5px;
        }
        
        .data-table td {
            padding: 1.5px 0;
            font-size: 10pt;
        }
        
        .section-title {
            font-weight: bold;
            margin: 10px 0 5px 0;
            font-size: 10pt;
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
                <th width="35%">Persyaratan</th>
                <th width="55%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>Dokumen Perijinan : {{ $hasil_seleksi->kesimpulan_dokumen }}</td>
                <td style="white-space: pre-line;">{{ $keterangan_dokumen }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Sarana Pengeringan : {{ $hasil_seleksi->kesimpulan_sarana_pengeringan }}</td>
                <td style="white-space: pre-line;">{{ $keterangan_pengeringan }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>Sarana Penggilingan : {{ $hasil_seleksi->kesimpulan_sarana_penggilingan }}</td>
                <td style="white-space: pre-line;">{{ $keterangan_penggilingan }}</td>
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
                <br>
                <p class="signature-text">Mitra Pangan,</p>
                <div class="signature-space"></div>
                <p class="signature-text"><u>{{ $nama_pj_mitra }}</u></p>
                <p class="signature-text">{{ $nama_perusahaan }}</p>
            </div>

            <!-- Right signature -->
            <div class="signature-box">
                <p class="signature-text">Perum BULOG Kantor Cabang {{ $unit_pelaksana }}</p>
                <p class="signature-text">Pelaksana Seleksi,</p>
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