<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Penetapan Mitra Pangan</title>
    <style>
        @page {
            margin: 2.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.3;
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
            margin-bottom: 30px;
        }
        .data-table {
            width: 100%;
            margin: 20px 0;
        }
        .data-table td:first-child {
            width: 200px;
        }
        .data-table td:nth-child(2) {
            width: 20px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
        }
        .signature-space {
            height: 60px;
        }
        .right-align {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SURAT PENETAPAN</h1>
        <h1>SEBAGAI MITRA PANGAN</h1>
        <h1>PENGADAAN GABAH/BERAS DALAM NEGERI</h1>
        <p>Nomor: {{ $nomor_surat }}</p>
        <p>TAHUN {{ date('Y') }}</p>
    </div>

    <div class="content">
        <p>Pada hari ini, {{ $hari }}, tanggal {{ $tanggal }}, berdasarkan Berita Acara Hasil Seleksi Nomor: {{ $nomor_BA }}, maka kepada:</p>

        <table class="data-table">
            <tr>
                <td>1. Nama Perusahaan</td>
                <td>:</td>
                <td>{{ $mitra->nama_perusahaan }}</td>
            </tr>
            <tr>
                <td>2. Badan Hukum/Usaha</td>
                <td>:</td>
                <td>{{ $mitra->badan_hukum_usaha }}</td>
            </tr>
            <tr>
                <td>3. Alamat Perusahaan</td>
                <td>:</td>
                <td>{{ $mitra->alamat_perusahaan }}</td>
            </tr>
            <tr>
                <td>4. Status</td>
                <td>:</td>
                <td>{{ $mitra->status ?? 'Penggilingan' }}</td>
            </tr>
            <tr>
                <td>5. Nomor Urut Seleksi</td>
                <td>:</td>
                <td>{{ sprintf("%03d", $seleksi->id_seleksi_mitra) }}</td>
            </tr>
            <tr>
                <td>6. Hasil Seleksi</td>
                <td>:</td>
                <td>{{ strtoupper($seleksi->status_seleksi) }}</td>
            </tr>
        </table>
    </div>

    <div class="content" style="font-size: 13pt; font-weight: bold; text-align: center;">
        Ditetapkan sebagai Mitra Pangan ADA Gabah/Beras DN Perum BULOG Kantor Cabang Surakarta
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p>Mitra Pangan,</p>
            <div class="signature-space"></div>
            <p>{{ $mitra->nama_cp }}<br>{{ $mitra->nama_perusahaan }}</p>
        </div>
        <div class="signature-box right-align">
            <p>Perum BULOG Kantor Cabang Surakarta</p>
            <div class="signature-space"></div>
            <p>{{ $karyawan->nama_karyawan }}<br>{{ $karyawan->jabatan }}</p>
        </div>
    </div>
</body>
</html>