<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>FORM-A Seleksi Mitra</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            margin: 3cm 2cm 2cm 2cm;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
        }
        .header p {
            margin: 5px 0;
            font-size: 12pt;
            font-weight: bold;
        }
        .nomor {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }
        .section-title {
            font-weight: bold;
            margin: 15px 0 5px 0;
        }
        .signature {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .signature-content {
            float: right;
            width: 50%;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
        .clear {
            clear: both;
        }
        .no-border-top {
            border-top: none;
        }
        .no-border-bottom {
            border-bottom: none;
        }
        .data-item {
        display: flex;
        margin-bottom: 2px;
        flex-wrap: nowrap;
        overflow: visible;
        }
        .label {
            width: 250px;  /* Perbesar lebar untuk mencakup nomor dan teks label */
            flex-shrink: 0;
            white-space: nowrap;
        }
        .colon {
            width: 10px;
            flex-shrink: 0;
            white-space: nowrap;
        }
        .value {
            flex: 1;
        }
        .section-heading {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>FORM - A</h2>
        <p>SELEKSI PENERIMAAN MITRA PANGAN</p>
        <p>PENGADAAN GABAH/BERAS DALAM NEGERI</p>
        <p>Nomor: {{ $nomor }}</p>
        <p>TAHUN {{ $tahun }}</p>
    </div>

    <!-- Bagian 1 - Data Seleksi -->
    <div class="data-item">
        <span class="label">Nomor Urut Seleksi</span>
        <span class="colon">:</span>
        <span>{{ $nomor_urut_seleksi }}</span>
    </div>
    <div class="data-item">
        <span class="label">Tanggal Seleksi</span>
        <span class="colon">:</span>
        <span>{{ $tanggal_seleksi }}</span>
    </div>
    <div class="data-item">
        <span class="label">Nomor Entitas Bulog</span>
        <span class="colon">:</span>
        <span>{{ $mitra->no_vms ?? '-' }}</span>
    </div>
    <div class="data-item">
        <span class="label">Unit Pelaksana Seleksi</span>
        <span class="colon">:</span>
        <span>ASIMPENAS</span>
    </div>

    <!-- Bagian 2 - Data Calon Mitra Kerja -->
    <div class="section-heading">DATA CALON MITRA KERJA</div>
    <div class="data-item">
        <span class="label">1&nbsp;&nbsp;&nbsp;Nama Perusahaan</span>
        <span class="colon">:</span>
        <span class="value">{{ $mitra->nama_perusahaan }}</span>
    </div>
    <div class="data-item">
        <span class="label">2&nbsp;&nbsp;&nbsp;Badan Hukum/Usaha</span>
        <span class="colon">:</span>
        <span class="value">{{ $mitra->badan_hukum_usaha }}</span>
    </div>
    <div class="data-item">
        <span class="label">3&nbsp;&nbsp;&nbsp;Alamat Perusahaan</span>
        <span class="colon">:</span>
        <span class="value">{{ $mitra->alamat_perusahaan }}</span>
    </div>
    <div class="data-item">
        <span class="label">4&nbsp;&nbsp;&nbsp;Nomor Telp Perusahaan</span>
        <span class="colon">:</span>
        <span class="value">{{ $mitra->no_telp_perusahaan }}</span>
    </div>
    <div class="data-item">
        <span class="label">5&nbsp;&nbsp;&nbsp;Nama Contact Person</span>
        <span class="colon">:</span>
        <span class="value">{{ $mitra->nama_cp }}</span>
    </div>
    <div class="data-item">
        <span class="label">6&nbsp;&nbsp;&nbsp;Nomor Telp/HP Contact Person</span>
        <span class="colon">:</span>
        <span class="value">{{ $mitra->no_telp_cp }}</span>
    </div>
    <div class="data-item">
        <span class="label">7&nbsp;&nbsp;&nbsp;Nama Bank Koresponden</span>
        <span class="colon">:</span>
        <span class="value">{{ $mitra->bank_korespondensi }}</span>
    </div>
    <div class="data-item">
        <span class="label">8&nbsp;&nbsp;&nbsp;Alamat Bank Koresponden</span>
        <span class="colon">:</span>
        <span class="value">{{ $mitra->alamat_bank }}</span>
    </div>
    <div class="data-item">
        <span class="label">9&nbsp;&nbsp;&nbsp;Nomor Rekening</span>
        <span class="colon">:</span>
        <span class="value">{{ $mitra->no_rekening }}</span>
    </div>
    <div class="data-item">
        <span class="label">10&nbsp;Status</span>
        <span class="colon">:</span>
        <span class="value">{{ $mitra->status_perusahaan }}</span>
    </div>

    <!-- Bagian 3 - Dokumen Perizinan -->
    <div class="section-title">DOKUMEN PERIJINAN</div>
    <table>
        <tr>
            <th width="5%">No</th>
            <th width="50%">Uraian</th>
            <th width="30%">Keterangan</th>
            <th width="15%">Masa Berlaku</th>
        </tr>
        <tr>
            <td>1</td>
            <td>Surat Permohonan</td>
            <td align="center">{{ $seleksi->surat_permohonan === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
            <td>{{ $seleksi->mb_surat_permohonan }}</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Akta Notaris</td>
            <td align="center">{{ $seleksi->akta_notaris === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
            <td>{{ $seleksi->mb_akta_notaris }}</td>
        </tr>
        <tr>
            <td>3</td>
            <td>NIB</td>
            <td align="center">{{ $seleksi->nib === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
            <td>{{ $seleksi->mb_nib }}</td>
        </tr>
        <tr>
            <td>4</td>
            <td>KTP</td>
            <td align="center">{{ $seleksi->ktp === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
            <td>{{ $seleksi->mb_ktp }}</td>
        </tr>
        <tr>
            <td>5</td>
            <td>No Rekening</td>
            <td align="center">{{ $seleksi->no_rekening === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
            <td>{{ $seleksi->mb_no_rekening }}</td>
        </tr>
        <tr>
            <td>6</td>
            <td>NPWP</td>
            <td align="center">{{ $seleksi->npwp === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
            <td>{{ $seleksi->mb_npwp }}</td>
        </tr>
        <tr>
            <td>7</td>
            <td>Surat Kuasa</td>
            <td align="center">{{ $seleksi->surat_kuasa === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
            <td>{{ $seleksi->mb_surat_kuasa }}</td>
        </tr>
    </table>

    <!-- Bagian 4 - Sarana Pengeringan -->
    <div class="section-title">SARANA PENGERINGAN</div>
    <table>
        <tr>
            <th width="5%">No</th>
            <th width="70%">Uraian</th>
            <th width="25%">Keterangan</th>
        </tr>
        <tr>
            <td>1</td>
            <td>Lantai Jemur</td>
            <td align="center">{{ $seleksi->lantai_jemur === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Sarana Lainnya</td>
            <td align="center">{{ $seleksi->sarana_lainnya === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
        </tr>
    </table>

    <!-- Bagian 5 - Sarana Penggilingan -->
    <div class="section-title">SARANA PENGGILINGAN</div>
    <table>
        <tr>
            <th width="5%">No</th>
            <th width="70%">Uraian</th>
            <th width="25%">Keterangan</th>
        </tr>
        <tr>
            <td>1</td>
            <td>Mesin Pemecah Kulit</td>
            <td align="center">{{ $seleksi->mesin_memecah_kulit === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Mesin Pemisah Gabah</td>
            <td align="center">{{ $seleksi->mesin_pemisah_gabah === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
        </tr>
        <tr>
            <td>3</td>
            <td>Mesin Penyosoh</td>
            <td align="center">{{ $seleksi->mesin_penyosoh === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
        </tr>
        <tr>
            <td>4</td>
            <td>Alat Pemisah Beras</td>
            <td align="center">{{ $seleksi->alat_pemisah_beras === 'ada' ? '1. Ada' : '2. Tidak Ada' }}</td>
        </tr>
    </table>

    <!-- Bagian Penutup -->
    <div style="margin-top: 20px;">
        <p>Dengan ini, kami menyatakan bahwa data dan/atau informasi yang kami berikan adalah benar.</p>
        
        <div class="signature">
            <div class="signature-content">
                <p>Calon Mitra Pangan,</p>
                <div class="signature-space"></div>
                <p><strong>{{ $mitra->nama_cp }}</strong><br>{{ $mitra->nama_perusahaan }}</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>