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
            margin: 2cm 2cm 2cm 2cm;
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
            vertical-align: middle;
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
            margin-bottom: 3px;
            line-height: 1.5;
        }
        .data-item table {
            border: none;
            margin: 0;
        }
        .data-item td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
        .section-heading {
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .masa-berlaku {
            white-space: nowrap;
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
        <table>
            <tr>
                <td width="200">Nomor Urut Seleksi</td>
                <td width="10">:</td>
                <td>{{ $nomor_urut_seleksi }}</td>
            </tr>
        </table>
    </div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">Tanggal Seleksi</td>
                <td width="10">:</td>
                <td>{{ $tanggal_seleksi }}</td>
            </tr>
        </table>
    </div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">Nomor Entitas Bulog</td>
                <td width="10">:</td>
                <td>{{ $nomor_entitas_bulog }}</td>
            </tr>
        </table>
    </div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">Unit Pelaksana Seleksi</td>
                <td width="10">:</td>
                <td>{{ $unit_pelaksana }}</td>
            </tr>
        </table>
    </div>

    <!-- Bagian 2 - Data Calon Mitra Kerja -->
    <div class="section-heading">DATA CALON MITRA KERJA</div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">1 Nama Perusahaan</td>
                <td width="10">:</td>
                <td>{{ $mitra->nama_perusahaan }}</td>
            </tr>
        </table>
    </div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">2 Badan Hukum/Usaha</td>
                <td width="10">:</td>
                <td>{{ $mitra->badan_hukum_usaha }}</td>
            </tr>
        </table>
    </div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">3 Alamat Perusahaan</td>
                <td width="10">:</td>
                <td>{{ $mitra->alamat_perusahaan }}</td>
            </tr>
        </table>
    </div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">4 Nomor Telp Perusahaan</td>
                <td width="10">:</td>
                <td>{{ $mitra->no_telp_perusahaan }}</td>
            </tr>
        </table>
    </div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">5 Nama Contact Person</td>
                <td width="10">:</td>
                <td>{{ $mitra->nama_cp }}</td>
            </tr>
        </table>
    </div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">6 Nomor Telp/HP Contact Person</td>
                <td width="10">:</td>
                <td>{{ $mitra->no_telp_cp }}</td>
            </tr>
        </table>
    </div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">7 Nama Bank Koresponden</td>
                <td width="10">:</td>
                <td>{{ $mitra->bank_korespondensi }}</td>
            </tr>
        </table>
    </div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">8 Alamat Bank Koresponden</td>
                <td width="10">:</td>
                <td>{{ $mitra->alamat_bank }}</td>
            </tr>
        </table>
    </div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">9 Nomor Rekening</td>
                <td width="10">:</td>
                <td>{{ $mitra->no_rekening }}</td>
            </tr>
        </table>
    </div>
    <div class="data-item">
        <table>
            <tr>
                <td width="200">10 Status</td>
                <td width="10">:</td>
                <td>{{ $mitra->status_perusahaan }}</td>
            </tr>
        </table>
    </div>

    <!-- Bagian 3 - Dokumen Perizinan -->
    <div class="section-title">DOKUMEN PERIJINAN</div>
    <table>
        <tr>
            <th width="5%" rowspan="2" style="text-align: center;">No</th>
            <th width="45%" rowspan="2" style="text-align: center;">Uraian</th>
            <th colspan="2" style="text-align: center;">Keterangan</th>
            <th width="15%" rowspan="2" style="text-align: center;">Masa Berlaku</th>
        </tr>
        <tr>
            <th width="17.5%" style="text-align: center;">1. Ada</th>
            <th width="17.5%" style="text-align: center;">2. Tidak Ada</th>
        </tr>
        <tr>
            <td style="text-align: center;">1</td>
            <td>Surat Permohonan</td>
            <td style="text-align: center;">@if($seleksi->surat_permohonan === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->surat_permohonan !== 'ada')<strong>V</strong>@endif</td>
            <td class="masa-berlaku">
                @if($seleksi->mb_surat_permohonan)
                    {{ date('d-m-Y', strtotime($seleksi->mb_surat_permohonan)) }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">2</td>
            <td>Akta Notaris</td>
            <td style="text-align: center;">@if($seleksi->akta_notaris === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->akta_notaris !== 'ada')<strong>V</strong>@endif</td>
            <td class="masa-berlaku">
                @if($seleksi->mb_akta_notaris)
                    {{ date('d-m-Y', strtotime($seleksi->mb_akta_notaris)) }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">3</td>
            <td>NIB</td>
            <td style="text-align: center;">@if($seleksi->nib === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->nib !== 'ada')<strong>V</strong>@endif</td>
            <td class="masa-berlaku">
                @if($seleksi->mb_nib)
                    {{ date('d-m-Y', strtotime($seleksi->mb_nib)) }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">4</td>
            <td>KTP</td>
            <td style="text-align: center;">@if($seleksi->ktp === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->ktp !== 'ada')<strong>V</strong>@endif</td>
            <td class="masa-berlaku">Seumur Hidup</td>
        </tr>
        <tr>
            <td style="text-align: center;">5</td>
            <td>No Rekening</td>
            <td style="text-align: center;">@if($seleksi->no_rekening === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->no_rekening !== 'ada')<strong>V</strong>@endif</td>
            <td class="masa-berlaku">
                @if($seleksi->mb_no_rekening)
                    {{ date('d-m-Y', strtotime($seleksi->mb_no_rekening)) }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">6</td>
            <td>NPWP</td>
            <td style="text-align: center;">@if($seleksi->npwp === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->npwp !== 'ada')<strong>V</strong>@endif</td>
            <td class="masa-berlaku">
                @if($seleksi->mb_npwp)
                    {{ date('d-m-Y', strtotime($seleksi->mb_npwp)) }}
                @else
                    -
                @endif
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">7</td>
            <td>Surat Kuasa</td>
            <td style="text-align: center;">@if($seleksi->surat_kuasa === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->surat_kuasa !== 'ada')<strong>V</strong>@endif</td>
            <td class="masa-berlaku">
                @if($seleksi->mb_surat_kuasa)
                    {{ date('d-m-Y', strtotime($seleksi->mb_surat_kuasa)) }}
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    <!-- Bagian 4 - Sarana Pengeringan -->
    <div class="section-title">SARANA PENGERINGAN</div>
    <table>
        <tr>
            <th width="5%" rowspan="2" style="text-align: center;">No</th>
            <th width="60%" rowspan="2" style="text-align: center;">Uraian</th>
            <th colspan="2" style="text-align: center;">Keterangan</th>
        </tr>
        <tr>
            <th width="17.5%" style="text-align: center;">1. Ada</th>
            <th width="17.5%" style="text-align: center;">2. Tidak Ada</th>
        </tr>
        <tr>
            <td style="text-align: center;">1</td>
            <td>Lantai Jemur</td>
            <td style="text-align: center;">@if($seleksi->lantai_jemur === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->lantai_jemur !== 'ada')<strong>V</strong>@endif</td>
        </tr>
        <tr>
            <td style="text-align: center;">2</td>
            <td>Sarana Lainnya</td>
            <td style="text-align: center;">@if($seleksi->sarana_lainnya === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->sarana_lainnya !== 'ada')<strong>V</strong>@endif</td>
        </tr>
    </table>

    <!-- Bagian 5 - Sarana Penggilingan -->
    <div class="section-title">SARANA PENGGILINGAN</div>
    <table>
        <tr>
            <th width="5%" rowspan="2" style="text-align: center;">No</th>
            <th width="60%" rowspan="2" style="text-align: center;">Uraian</th>
            <th colspan="2" style="text-align: center;">Keterangan</th>
        </tr>
        <tr>
            <th width="17.5%" style="text-align: center;">1. Ada</th>
            <th width="17.5%" style="text-align: center;">2. Tidak Ada</th>
        </tr>
        <tr>
            <td style="text-align: center;">1</td>
            <td>Mesin Pemecah Kulit</td>
            <td style="text-align: center;">@if($seleksi->mesin_memecah_kulit === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->mesin_memecah_kulit !== 'ada')<strong>V</strong>@endif</td>
        </tr>
        <tr>
            <td style="text-align: center;">2</td>
            <td>Mesin Pemisah Gabah</td>
            <td style="text-align: center;">@if($seleksi->mesin_pemisah_gabah === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->mesin_pemisah_gabah !== 'ada')<strong>V</strong>@endif</td>
        </tr>
        <tr>
            <td style="text-align: center;">3</td>
            <td>Mesin Penyosoh</td>
            <td style="text-align: center;">@if($seleksi->mesin_penyosoh === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->mesin_penyosoh !== 'ada')<strong>V</strong>@endif</td>
        </tr>
        <tr>
            <td style="text-align: center;">4</td>
            <td>Alat Pemisah Beras</td>
            <td style="text-align: center;">@if($seleksi->alat_pemisah_beras === 'ada')<strong>V</strong>@endif</td>
            <td style="text-align: center;">@if($seleksi->alat_pemisah_beras !== 'ada')<strong>V</strong>@endif</td>
        </tr>
    </table>

    <!-- Bagian Penutup -->
    <div style="margin-top: 20px;">
        <p>Dengan ini, kami menyatakan bahwa data dan/atau informasi yang kami berikan adalah benar.</p>
        
        <div class="signature">
            <div class="signature-content">
                <p>Calon Mitra Pangan,</p>
                <div class="signature-space"></div>
                <p><strong><u>{{ $mitra->nama_cp }}</u></strong><br>{{ $mitra->nama_perusahaan }}</p>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</body>
</html>