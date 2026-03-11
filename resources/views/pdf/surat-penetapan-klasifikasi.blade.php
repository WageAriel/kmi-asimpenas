<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Penetapan Klasifikasi Mitra Pangan</title>
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
            vertical-align: top;
        }
        .data-table td:nth-child(2) {
            width: 20px;
            vertical-align: top;
        }
        .data-table td:nth-child(3) {
            vertical-align: top;
            text-align: justify;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
            display: table;
        }
        .signature-left {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding-right: 10px;
        }
        .signature-right {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding-left: 10px;
        }
        .signature-space {
            height: 80px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SURAT PENETAPAN</h1>
        <h1>KLASIFIKASI MITRA PANGAN</h1>
        <h1>PENGADAAN GABAH/BERAS DALAM NEGERI</h1>
        <h1>Nomor: {{ $nomor_surat }}</h1>
        <h1>TAHUN {{ $tahun }}</h1>
    </div>

    <div class="content">
        <p>Pada hari ini {{ $hari }} tanggal {{ $tanggal }}, berdasarkan Berita Acara Hasil Klasifikasi Nomor : {{ $nomor_BA }}, maka kepada :</p>

        <table class="data-table">
            <tr>
                <td>1. Nama Perusahaan</td>
                <td>:</td>
                <td>{{ strtoupper($nama_perusahaan) }}</td>
            </tr>
            <tr>
                <td>2. Badan Hukum/Usaha</td>
                <td>:</td>
                <td>{{ strtoupper($badan_usaha) }}</td>
            </tr>
            <tr>
                <td>3. Alamat Perusahaan</td>
                <td>:</td>
                <td>{{ strtoupper($alamat_perusahaan) }}</td>
            </tr>
            <tr>
                <td>4. Status</td>
                <td>:</td>
                <td>{{ strtoupper($status_mitra) }}</td>
            </tr>
            <tr>
                <td>5. Nomor Urut Klasifikasi</td>
                <td>:</td>
                <td>{{ $nomor_urut_klasifikasi }}</td>
            </tr>
            <tr>
                <td>6. Hasil Klasifikasi</td>
                <td>:</td>
                <td>{{ $hasil_klasifikasi }}</td>
            </tr>
        </table>
    </div>

    <div class="content" style="font-size: 12pt;">
        <p>Ditetapkan sebagai Mitra Kerja ADA Gabah/Beras DN Perum BULOG Kantor Cabang {{ $kantor_cabang }} dengan <strong>Klasifikasi {{ $hasil_klasifikasi }}</strong>.</p>
    </div>

    <div class="signature-section">
        <div class="signature-left">
            <p>Mitra Pangan,</p>
            <div class="signature-space"></div>
            <p style="margin: 0; font-weight: bold;"><u>{{ strtoupper($nama_mitra) }}</u></p>
            <p style="margin: 0;">{{ strtoupper($nama_perusahaan) }}</p>
        </div>
        <div class="signature-right">
            <p>Perum BULOG Kantor Cabang {{ $kantor_cabang }}</p>
            <div class="signature-space"></div>
            <p style="margin: 0; font-weight: bold;"><u>{{ strtoupper($nama_pejabat) }}</u></p>
            <p style="margin: 0;">{{ strtoupper($jabatan_pejabat) }}</p>
        </div>
    </div>
</body>
</html>