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
        width: 100%;
        position: relative;
        
        }
        .signature-left {
            position: absolute;
            left: 0;
            width: 45%;
            text-align: center;
        }
        .signature-right {
            position: absolute;
            right: 0;
            width: 45%;
            text-align: center;
        }
        .signature-space {
            height: 80px; /* Space for signature */
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
        <h1>Nomor: {{ $nomor_surat }}</h1>
        <h1>TAHUN {{ date('Y') }}</h1>
    </div>

    <div class="content">
        <p>Pada hari ini, {{ $hari }} tanggal {{ $tanggal }}, berdasarkan Berita Acara Hasil Seleksi Nomor : {{ $nomor_BA }}, maka kepada :</p>

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
                <td>{{ $nomor_urut_seleksi }}</td>
            </tr>
            <tr>
                <td>6. Hasil Seleksi</td>
                <td>:</td>
                <td>{{ strtoupper($seleksi->status_seleksi) }}</td>
            </tr>
        </table>
    </div>

    <div class="content" style="font-size: 12pt; text-align: start;">
        Ditetapkan sebagai Mitra Pangan ADA Gabah/Beras DN Perum BULOG Kantor Cabang
    </div>

    <div class="signature-section">
        <div class="signature-left">
            <p>Mitra Pangan,</p>
            <div class="signature-space"></div>
            <p style="margin: 0;">{{ $mitra->nama_cp }}</p>
            <p style="margin: 0;">{{ $mitra->nama_perusahaan }}</p>
        </div>
        <div class="signature-right">
            <p>Perum BULOG Kantor Cabang Surakarta</p>
            <div class="signature-space"></div>
            <p style="margin: 0;">{{ $karyawan->nama_karyawan }}</p>
            <p style="margin: 0;">{{ $karyawan->jabatan }}</p>
        </div>
    </div>
</body>
</html>