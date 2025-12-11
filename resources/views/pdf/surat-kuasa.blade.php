<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Kuasa</title>
    <style>
        @page {
            margin: 2cm 2.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            color: #000;
        }
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 14pt;
            margin-bottom: 30px;
            margin-top: 0;
        }
        .content {
            text-align: justify;
            margin-bottom: 20px;
        }
        .data-list {
            margin-left: 40px;
            margin-bottom: 15px;
        }
        .data-list table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-list td {
            padding: 3px 0;
            vertical-align: top;
        }
        .data-list td:first-child {
            width: 30px;
        }
        .data-list td:nth-child(2) {
            width: 100px;
        }
        .data-list td:nth-child(3) {
            width: 10px;
            text-align: center;
        }
        .location-date {
            text-align: right;
            margin-top: 40px;
            margin-bottom: 40px;
        }
        .signature-section {
            margin-top: 20px;
            width: 100%;
        }
        .signature-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-section td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        .signature-space {
            height: 80px;
        }
        .signature-name {
            text-decoration: underline;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1 class="title">SURAT KUASA</h1>
    
    <div class="content">
        Yang bertanda tangan di bawah ini:
    </div>
    
    <div class="data-list">
        <table>
            <tr>
                <td>1.</td>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $nama_pemberi }}</td>
            </tr>
            <tr>
                <td>2.</td>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $nik_pemberi }}</td>
            </tr>
            <tr>
                <td>3.</td>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $alamat_pemberi }}</td>
            </tr>
            <tr>
                <td>4.</td>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $jabatan_pemberi }}</td>
            </tr>
        </table>
    </div>
    
    <div class="content">
        Dengan ini memberi kuasa penuh kepada
    </div>
    
    <div class="data-list">
        <table>
            <tr>
                <td>1.</td>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $nama_penerima }}</td>
            </tr>
            <tr>
                <td>2.</td>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $nik_penerima }}</td>
            </tr>
            <tr>
                <td>3.</td>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $alamat_penerima }}</td>
            </tr>
        </table>
    </div>
    
    <div class="content">
        Untuk melaksanakan dan menandatangani semua dokumen yang berhubungan dengan kontrak atau PO Gabah/Beras termasuk pencairan dana yang diterbitkan oleh Perum BULOG Cabang Surakarta melalui Rekening BRI No <strong>{{ $no_rekening }}</strong> an <strong>{{ $nama_pemilik_rekening }}</strong>.
    </div>
    
    <div class="content">
        Demikian surat kuasa ini dibuat dalam keadaan sadar tanpa tekanan dan paksaan dari pihak manapun dan dapat dipergunakan sebagaimana mestinya.
    </div>
    
    <div class="location-date">
        <table style="float: right; border-collapse: collapse;">
            <tr>
                <td style="padding: 2px 5px; text-align: left;">Dibuat di</td>
                <td style="padding: 2px 5px; text-align: center;">:</td>
                <td style="padding: 2px 5px; text-align: left;">{{ $tempat }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px; text-align: left;">Tanggal</td>
                <td style="padding: 2px 5px; text-align: center;">:</td>
                <td style="padding: 2px 5px; text-align: left;">{{ $tanggal }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>
    
    <div class="signature-section">
        <table>
            <tr>
                <td>
                    <div>Yang Memberi Kuasa</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">{{ $nama_pemberi }}</div>
                </td>
                <td>
                    <div>Yang diberi Kuasa</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">{{ $nama_penerima }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
