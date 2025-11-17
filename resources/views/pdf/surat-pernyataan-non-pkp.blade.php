<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Surat Pernyataan Non PKP</title>
    <style>
        @page {
            margin: 3cm 2.5cm 2.5cm 2.5cm;
            size: A4;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            padding-bottom: 30px;
        }
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            padding: 0;
        }
        .opening {
            text-align: justify;
        }
        .identity-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }
        
        .identity-table td {
            padding: 0;
            border: none;
            vertical-align: top;
        }
        
        .identity-table .label {
            width: 80px;
        }
        
        .identity-table .colon {
            width: 20px;
            text-align: center;
        }
        
        .identity-table .value {
            width: auto;
        }
        .content {
            text-align: justify;
            margin: 10px 0;
            line-height: 1.6;
        }
        .location-date {
            text-align: right;
            margin: 40px 0 60px 0;
        }
        
        .signature {
            text-align: right;
            margin-top: 60px;
        }
        
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>SURAT PERNYATAAN NON PENGUSAHA KENA PAJAK (PKP)</h1>
    </div>

    <!-- Opening -->
    <div class="opening">
        Saya yang bertanda tangan dibawah ini :
    </div>

    <!-- Identitas dalam format 3 kolom -->
    <table class="identity-table">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td class="value">{{ $nama_cp }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="colon">:</td>
            <td class="value">{{ $alamat_perusahaan }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td class="colon">:</td>
            <td class="value">{{ $jabatan }}</td>
        </tr>
    </table>

    <!-- Content -->
    <div class="content">
        Dengan ini menyatakan bahwa kami adalah bukan Pengusaha Kena Pajak sebagaimana dimaksud pada Undang-Undang Pajak Pertambahan Nilai. Oleh karena itu terhadap penjualan/penyerahan Barang Kena Pajak atau Jasa Kena Pajak yang kami lakukan ke Perusahaan Bapak/Ibu, kami tidak menerbitkan Faktur Pajak.<br>
        Demikian Surat pernyataan ini dibuat dengan sebenarnya dan agar dapat dipergunakan sebagaimana mestinya
    </div>

    <!-- Location and Date -->
    <div class="location-date">
        Surakarta, {{ $tanggal }}
    </div>

    <!-- Signature -->
    <div class="signature">
        <span class="signature-name">{{ $nama_cp }}</span>
    </div>
</body>
</html>