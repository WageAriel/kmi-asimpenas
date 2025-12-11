<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Pakta Integritas</title>
    <style>
        @page {
            margin: 1.5cm 2.5cm 1.5cm 2.5cm;
            size: A4;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            text-align: justify;
        }
        .header {
            text-align: center;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
            text-decoration: underline;
        }
        .opening {
            margin-bottom: 10px;
        }
        .identity-table {
            width: 100%;
            margin: 10px 0;
            border-collapse: collapse;
        }
        
        .identity-table td {
            padding: 1px 0;
            border: none;
            vertical-align: top;
        }
        
        .identity-table .label {
            width: 120px;
        }
        
        .identity-table .colon {
            width: 20px;
            text-align: left;
        }
        
        .identity-table .value {
            width: auto;
        }
        .content {
            margin: 10px 0;
        }
        .statement-list {
            margin: 10px 0;
            padding-left: 20px;
        }
        .statement-list li {
            margin-bottom: 8px;
            text-align: justify;
        }
        .closing {
            margin: 15px 0 10px 0;
        }
        .signature-section {
            margin-top: 20px;
            text-align: right;
        }
        .signature-location {
            margin-bottom: 60px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            display: inline-block;
            text-align: center;
            min-width: 200px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>PAKTA INTEGRITAS</h1>
    </div>

    <!-- Opening -->
    <div class="opening">
        Saya yang bertanda tangan dibawah ini, berwenang dan bertindak untuk dan atas nama :
    </div>

    <!-- Identitas -->
    <table class="identity-table">
        <tr>
            <td class="label">Nama</td>
            <td class="colon">:</td>
            <td class="value">{{ $nama_cp }}</td>
        </tr>
        <tr>
            <td class="label">Perusahaan</td>
            <td class="colon">:</td>
            <td class="value">{{ $nama_perusahaan }}</td>
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
        Dengan ini menyatakan dengan sebenarnya bahwa :
    </div>

    <!-- Statement List -->
    <ol class="statement-list">
        <li>Saya melaksanakan Pengadaan Gabah/Beras Dalam Negeri tahun {{ $tahun }} berdasarkan prinsip-prinsip itikad baik, dengan kecermatan yang tinggi, dan dalam keadaan bebas mandiri atau tidak dibawah tekanan maupun pengaruh dari pihak lain (independency).</li>
        
        <li>Gabah/Beras yang saya serahkan/ masukan ke Gudang Perusahaan Umum (Perum) BULOG merupakan hasil panen Gabah/ Beras baru dan bukan merupakan Gabah/ Beras hasil tindak kejahatan (seperti: hasil curian, hasil penipuan), hasil sitaan dan/atau hasil sengketa dengan pihak lain.</li>
        
        <li>Saya bersedia memenuhi persyaratan kualitas Gabah/Beras yang ditetapkan Perusahan Umum (Perum) BULOG.</li>
        
        <li>Apabila dalam pelaksanaannya saya terbukti melakukan penyimpangan sebagaimana dimaksud pada angka 2 dan angka 3, maka saya akan bertanggug jawab sepenuhnya atas segala resiko dan sanksi hukum yang berlaku dan membebaskan Perusahaan Umum (Perum) BULOG dari segala tuntutan hukum.</li>
        
        <li>Dalam menjalankan kepentingan saya tidak memiliki kepentingan pribadi ataupun tujuan untuk melakukan sesuatu untuk manfaat diri sendiri, maupun kepentingan pihak yang terkait dengan diri sendiri ataupun pihak yang terafiliasi, dan dengan demikian tidak memiliki posisi yang mengandung potensi benturan kepentingan (<i>conflict of interest rule</i>) termasuk dengan seluruh pihak yang terlibat dengan tindakan di atas.</li>
        
        <li>Melaksanakan proses tersebut dengan pemahaman yang cukup tentang berbagai peraturan dan kewajiban normatif lainnya yang terkait, dan memenuhi seluruh ketentuan dan peraturan perundang-undangan.</li>
    </ol>

    <!-- Closing -->
    <div class="closing">
        Demikian pernyataan ini disampaikan dengan sebenar-benarnya tanpa menyembunyikan fakta dan hal material apapun, dan akan bertanggung jawab sepenuhnya atas kebenaran dan hal-hal yang dinyatakan disini, serta akan bersedia bertanggung jawab baik secara perdata maupun pidana apabila laporan dan pernyataan ini tidak sesuai dengan keadaan sebenarnya.
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-location">
            Surakarta, {{ $tanggal }}
        </div>
        <div class="signature-name">
            {{ $nama_cp }}
        </div>
    </div>
</body>
</html>
