<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Permohonan MPP</title>
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
        
        .header-right {
            text-align: right;
            margin-bottom: 40px;
        }
        
        .header-right .location-date {
            margin-bottom: 30px;
        }
        
        .header-left .subject {
            margin-bottom: 0;
            text-align: left;
        }
        
        .recipient {
            text-align: left;
            margin-bottom: 30px;
            line-height: 1.4;
            position: relative;
            left: 67%;
            width: 45%;
        }
        
        .opening {
            text-align: justify;
        }
        
        .identity-table {
            width: calc(100% - 40px);
            margin: 0 0 20px 40px;
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
        
        .attachment-list {
            margin: 20px 0 25px 30px;
            line-height: 1.4;
        }
        
        .closing {
            text-align: justify;
            margin: 25px 0;
            line-height: 1.6;
        }
        
        .signature {
            text-align: center;
            margin-top: 40px;
            width: 100%;
        }

        .signature-space {
            height: 40px;
        }

        .signature-name {
            text-decoration: underline;
            font-weight: bold;
            display: block;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header dengan posisi kanan -->
    <div class="header-right">
        <div class="location-date">
            Surakarta, {{ $tanggal_permohonan }}
        </div>
    </div>

    <!-- Header dengan posisi kanan -->
    <div class="header-left">
        <div class="subject">
            Perihal : Permohonan Mitra Gabah/Beras <br> 
            DN Tahun {{ $tahun }}
        </div>
    </div>
    
    <!-- Tujuan Surat -->
    <div class="recipient">
        Kepada <strong> Yth :<br>
        Pemimpin / Wakil Pemimpin<br>
        Perum BULOG<br>
        Kancab Surakarta</strong><br>
        Di <strong>TEMPAT</strong>
    </div>
    
    <!-- Pembukaan -->
    <div class="opening">
        Dengan hormat, yang bertanda tangan dibawah ini :
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
    
    <!-- Isi Surat Paragraf 1 -->
    <div class="content">
        Sehubungan dengan pembukaan masa pengadaan gabah / beras di Perum BULOG Kantor Cabang Surakarta, bersama ini kami bermaksud mengajukan permohonan pendaftaran menjadi Mitra Pangan Pengadaan Gabah / Beras Tahun {{ $tahun }} di Kantor Cabang Surakarta.
    </div>
    
    <!-- Isi Surat Paragraf 2 -->
    <div class="content">
        Sebagai kelengkapan, bersama ini kami lampirkan salinan dokumen administrasi dan data teknis.
    </div>
    
    <!-- Daftar Lampiran -->
    <div class="attachment-list">
        &nbsp;&nbsp;&nbsp;&nbsp;1. Permohonan<br>
        &nbsp;&nbsp;&nbsp;&nbsp;2. KTP<br>
        &nbsp;&nbsp;&nbsp;&nbsp;3. NPWP<br>
        &nbsp;&nbsp;&nbsp;&nbsp;4. NIB<br>
        &nbsp;&nbsp;&nbsp;&nbsp;5. Akta Pendirian<br>
        &nbsp;&nbsp;&nbsp;&nbsp;6. No Rekening<br>
        &nbsp;&nbsp;&nbsp;&nbsp;7. Surat Pernyataan PKP/Non PKP<br>
        &nbsp;&nbsp;&nbsp;&nbsp;8. Surat Kuasa
    </div>
    
    <!-- Penutup -->
    <div class="closing">
        Demikian kami sampaikan, semoga permohonan kami dapat diterima dengan baik. Atas perhatian Bapak kami ucapkan terima kasih.
    </div>
    
    <!-- Tanda Tangan -->
    <div class="signature">
        Hormat kami,
        <div class="signature-space"></div>
        <span class="signature-name">{{ $nama_cp }}</span>
    </div>
</body>
</html>