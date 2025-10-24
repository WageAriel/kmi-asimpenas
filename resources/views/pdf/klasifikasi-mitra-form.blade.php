<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>FORM-B Klasifikasi Mitra Pangan</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
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
        .header p {
            margin: 5px 0;
            font-size: 12pt;
            font-weight: bold;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 200px;
        }
        .info-table td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-size: 11pt;
        }
        .main-table th {
            text-align: center;
            font-weight: bold;
        }
        .section-header {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .circle {
            font-family: 'Arial Unicode MS';
        }
        .filled-circle {
            color: black;
        }
        .empty-circle {
            color: #000;
        }
        .signature-section {
            margin-top: 30px;
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FORM - B</h1>
        <p>KLASIFIKASI MITRA PANGAN</p>
        <p>PENGADAAN GABAH/BERAS DALAM NEGERI</p>
        <p>Nomor: {{ $nomor }}</p>
        <p>TAHUN {{ date('Y') }}</p>
    </div>

    <!-- Data section with proper spacing -->
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>NOMOR URUT SELEKSI</td>
                <td>:</td>
                <td>{{ sprintf("%03d", $klasifikasi->id_klasifikasi_mitra) }}</td>
            </tr>
            <tr>
                <td>TANGGAL KLASIFIKASI</td>
                <td>:</td>
                <td>{{ date('d/m/Y', strtotime($klasifikasi->created_at)) }}</td>
            </tr>
            <tr>
                <td>NOMOR ENTITAS BULOG</td>
                <td>:</td>
                <td>{{ $nomor_entitas }}</td>
            </tr>
            <tr>
                <td>UNIT PELAKSANA SELEKSI</td>
                <td>:</td>
                <td>{{ $unit_pelaksana }}</td>
            </tr>
        </table>

            <div style="margin-top: 20px;">
            <p style="font-weight: bold; margin-bottom: 10px;">DATA MITRA KERJA:</p>
            <table class="info-table">
                <tr>
                    <td>1. Nama Perusahaan</td>
                    <td>:</td>
                    <td>{{ $klasifikasi->mitra->nama_perusahaan }}</td>
                </tr>
                <tr>
                    <td>2. Badan Hukum/Usaha</td>
                    <td>:</td>
                    <td>{{ $klasifikasi->mitra->badan_usaha }}</td>
                </tr>
                <tr>
                    <td>3. Alamat Perusahaan</td>
                    <td>:</td>
                    <td>{{ $klasifikasi->mitra->alamat }}</td>
                </tr>
                <tr>
                    <td>4. Status</td>
                    <td>:</td>
                    <td>{{ $klasifikasi->mitra->status ?? 'Penggilingan' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" width="40">No</th>
                <th rowspan="2" width="250">Komponen</th>
                <th rowspan="2" width="80">Satuan</th>
                <th colspan="3">Klasifikasi</th>
            </tr>
            <tr>
                <th width="80">C</th>
                <th width="80">B</th>
                <th width="80">A</th>
            </tr>
        </thead>
        <tbody>
            <!-- PENGERINGAN -->
            <tr>
                <td colspan="6" class="section-header">I. PENGERINGAN</td>
            </tr>
            @php $no = 1; @endphp
            @foreach([
                ['name' => 'Mesin Pembersih Gabah', 'field' => 'mesin_pembersih_gabah', 'unit' => 'ton/hari'],
                ['name' => 'Lantai Jemur', 'field' => 'lantai_jemur', 'unit' => 'ton/hari']
            ] as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td class="text-center">{{ $item['unit'] }}</td>
                @foreach(['3', '2', '1'] as $value)
                <td class="text-center">
                    <span class="circle {{ $klasifikasi->{$item['field']} === $value ? 'filled-circle' : 'empty-circle' }}">
                        {{ $klasifikasi->{$item['field']} === $value ? '●' : '○' }}
                    </span>
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- PENGGILINGAN -->
            <tr>
                <td colspan="6" class="section-header">II. PENGGILINGAN (RMP)</td>
            </tr>
            @foreach([
                ['name' => 'Mesin Pembersih Awal', 'field' => 'mesin_pembersih_awal', 'unit' => 'unit'],
                ['name' => 'Mesin Pemecah Kulit', 'field' => 'mesin_pemecah_kulit', 'unit' => 'unit'],
                ['name' => 'Mesin Pemisah Gabah', 'field' => 'mesin_pemisah_gabah', 'unit' => 'unit'],
                ['name' => 'Mesin Penyosoh', 'field' => 'mesin_penyosoh', 'unit' => 'unit']
            ] as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td class="text-center">{{ $item['unit'] }}</td>
                @foreach(['3', '2', '1'] as $value)
                <td class="text-center">
                    <span class="circle {{ $klasifikasi->{$item['field']} === $value ? 'filled-circle' : 'empty-circle' }}">
                        {{ $klasifikasi->{$item['field']} === $value ? '●' : '○' }}
                    </span>
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- SARANA PENYIMPANAN -->
            <tr>
                <td colspan="6" class="section-header">III. SARANA PENYIMPANAN</td>
            </tr>
            @foreach([
                ['name' => 'Gudang Konvensional', 'field' => 'gudang_konvensional', 'unit' => 'm²']
            ] as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td class="text-center">{{ $item['unit'] }}</td>
                @foreach(['3', '2', '1'] as $value)
                <td class="text-center">
                    <span class="circle {{ $klasifikasi->{$item['field']} === $value ? 'filled-circle' : 'empty-circle' }}">
                        {{ $klasifikasi->{$item['field']} === $value ? '●' : '○' }}
                    </span>
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- SARANA ANGKUTAN -->
            <tr>
                <td colspan="6" class="section-header">IV. SARANA ANGKUTAN</td>
            </tr>
            @foreach([
                ['name' => 'Truk', 'field' => 'truk', 'unit' => 'unit']
            ] as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td class="text-center">{{ $item['unit'] }}</td>
                @foreach(['3', '2', '1'] as $value)
                <td class="text-center">
                    <span class="circle {{ $klasifikasi->{$item['field']} === $value ? 'filled-circle' : 'empty-circle' }}">
                        {{ $klasifikasi->{$item['field']} === $value ? '●' : '○' }}
                    </span>
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- KELENGKAPAN PEMERIKSAAN -->
            <tr>
                <td colspan="6" class="section-header">V. KELENGKAPAN PEMERIKSAAN</td>
            </tr>
            @foreach([
                ['name' => 'Mini Lab', 'field' => 'mini_lab', 'unit' => 'unit']
            ] as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td class="text-center">{{ $item['unit'] }}</td>
                @foreach(['3', '2', '1'] as $value)
                <td class="text-center">
                    <span class="circle {{ $klasifikasi->{$item['field']} === $value ? 'filled-circle' : 'empty-circle' }}">
                        {{ $klasifikasi->{$item['field']} === $value ? '●' : '○' }}
                    </span>
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- ORGANISASI -->
            <tr>
                <td colspan="6" class="section-header">VI. ORGANISASI</td>
            </tr>
            @foreach([
                ['name' => 'Bagian Quality Control', 'field' => 'bagian_quality_control', 'unit' => '-']
            ] as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td class="text-center">{{ $item['unit'] }}</td>
                @foreach(['3', '2', '1'] as $value)
                <td class="text-center">
                    <span class="circle {{ $klasifikasi->{$item['field']} === $value ? 'filled-circle' : 'empty-circle' }}">
                        {{ $klasifikasi->{$item['field']} === $value ? '●' : '○' }}
                    </span>
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
        <div class="signature-section">
                <p>Dengan ini, kami menyatakan bahwa data dan/atau informasi yang kami berikan adalah benar.</p>
                
                <div class="signature-box">
                    <p>Mitra Pangan,</p>
                    <div class="signature-space"></div>
                    <p><u>{{ $klasifikasi->mitra->nama_pemilik }}</u><br>
                    {{ $klasifikasi->mitra->nama_perusahaan }}</p>
                </div>
            </div>
        </div>
</body>
</html>