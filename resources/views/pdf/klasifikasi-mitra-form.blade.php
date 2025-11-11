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
        .check-mark {
        font-family: 'Arial', sans-serif;
        font-weight: bold;
        }
        .selected {
            color: black;
        }
        .not-selected {
            color: transparent;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FORM - B</h1>
        <p>KLASIFIKASI MITRA PANGAN</p>
        <p>PENGADAAN GABAH/BERAS DALAM NEGERI</p>
        <p>Nomor: {{ $nomor }}</p>
        <p>TAHUN {{ $tahun }}</p>
    </div>

    <!-- Data section with proper spacing -->
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>NOMOR URUT SELEKSI</td>
                <td>:</td>
                <td>{{ $nomor_urut_klasifikasi }}</td>
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
                    <td>{{ $klasifikasi->mitra->badan_hukum_usaha }}</td>
                </tr>
                <tr>
                    <td>3. Alamat Perusahaan</td>
                    <td>:</td>
                    <td>{{ $klasifikasi->mitra->alamat_perusahaan }}</td>
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
                ['name' => 'Lantai Jemur', 'field' => 'lantai_jemur', 'unit' => 'ton/hari'],
                ['name' => 'Mesin Pengering', 'field' => 'mesin_pengering', 'unit' => 'ton/hari'],
                ['name' => 'Alat Pengering Lainnya', 'field' => 'alat_pengering_lainnya', 'unit' => 'ton/hari']
            ] as $item)
            <tr>
                <td class="text-center" >{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['unit'] }}</td>
                @foreach(['3', '2', '1'] as $value)
                <td style="text-align: center; font-size: 8pt;">
                    @if($klasifikasi->{$item['field']} === $value)
                        {{ $getKlasifikasiHasil($item['field'], $value) }}
                    @else
                        -
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- PENGGILINGAN -->
            <tr>
                <td colspan="6" class="section-header">II. PENGGILINGAN (RMP)</td>
            </tr>
            @php $no = 1; @endphp
            @foreach([
                ['name' => 'Mesin Pembersih Awal', 'field' => 'mesin_pembersih_awal', 'unit' => 'ton/jam'],
                ['name' => 'Mesin Pemecah Kulit', 'field' => 'mesin_pemecah_kulit', 'unit' => 'ton/jam'],
                ['name' => 'Mesin Pembersih Sekam', 'field' => 'mesin_pembersih_sekam', 'unit' => 'ton/jam'],
                ['name' => 'Mesin Pemisah Gabah Pecah Kulit', 'field' => 'mesin_pemisah_gabah_pecah_kulit', 'unit' => 'ton/jam'],
                ['name' => 'Mesin Pemisah Batu', 'field' => 'mesin_pemisah_batu', 'unit' => 'ton/jam'],
                ['name' => 'Mesin Penyosoh', 'field' => 'mesin_penyosoh', 'unit' => 'ton/jam; pass'],
                ['name' => 'Mesin Pengkabut', 'field' => 'mesin_pengkabut', 'unit' => 'ton/jam; pass'],
                ['name' => 'Mesin Pemisah Menir', 'field' => 'mesin_pemesah_menir', 'unit' => 'ton/jam'],
                ['name' => 'Mesin Pemisah Katul', 'field' => 'mesin_pemisah_katul', 'unit' => 'ton/jam'],
                ['name' => 'Mesin Pemisah Berdasarkan Ukuran', 'field' => 'mesin_pemisah_berdasarkan_ukuran', 'unit' => 'ton/jam'],
                ['name' => 'Mesin Pemisah Berdasarkan Warna', 'field' => 'mesin_pemisah_berdasarkan_warna', 'unit' => 'ton/jam'],
                ['name' => 'Tangki Penyimpanan', 'field' => 'tangki_penyimpanan', 'unit' => 'ton/unit'],
                ['name' => 'Mesin Pengemas', 'field' => 'mesin_pengemas', 'unit' => 'ton/jam'],
                ['name' => 'Mesin Jahit', 'field' => 'mesin_jahit', 'unit' => 'unit']
            ] as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['unit'] }}</td>
                @foreach(['3', '2', '1'] as $value)
                <td style="text-align: center; font-size: 8pt;">
                    @if($klasifikasi->{$item['field']} === $value)
                        {{ $getKlasifikasiHasil($item['field'], $value) }}
                    @else
                        -
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- SARANA PENYIMPANAN -->
            <tr>
                <td colspan="6" class="section-header">III. SARANA PENYIMPANAN</td>
            </tr>
            @php $no = 1; @endphp
            @foreach([
                ['name' => 'Gudang Konvensional', 'field' => 'gudang_konvensional', 'unit' => 'ton'],
                ['name' => 'Silo GKG / Hopper', 'field' => 'silo_gkg_hopper', 'unit' => 'ton']

            ] as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['unit'] }}</td>
                @foreach(['3', '2', '1'] as $value)
                <td style="text-align: center; font-size: 8pt;">
                    @if($klasifikasi->{$item['field']} === $value)
                        {{ $getKlasifikasiHasil($item['field'], $value) }}
                    @else
                        -
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- SARANA ANGKUTAN -->
            <tr>
                <td colspan="6" class="section-header">IV. SARANA ANGKUTAN</td>
            </tr>
            @php $no = 1; @endphp
            @foreach([
                ['name' => 'Truk', 'field' => 'truk', 'unit' => 'unit']
            ] as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['unit'] }}</td>
                @foreach(['3', '2', '1'] as $value)
                <td style="text-align: center; font-size: 8pt;">
                    @if($klasifikasi->{$item['field']} === $value)
                        {{ $getKlasifikasiHasil($item['field'], $value) }}
                    @else
                        -
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- KELENGKAPAN PEMERIKSAAN -->
            <tr>
                <td colspan="6" class="section-header">V. KELENGKAPAN PEMERIKSAAN</td>
            </tr>
            @php $no = 1; @endphp
            @foreach([
                ['name' => 'Mini Lab', 'field' => 'mini_lab', 'unit' => 'unit'],
                ['name' => 'Moisture Tester', 'field' => 'moisture_tester', 'unit' => 'unit'],
                ['name' => 'Pembanding Derajat Sosoh', 'field' => 'pembanding_derajat_sosoh', 'unit' => 'unit']
            ] as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['unit'] }}</td>
                @foreach(['3', '2', '1'] as $value)
                <td style="text-align: center; font-size: 8pt;">
                    @if($klasifikasi->{$item['field']} === $value)
                        {{ $getKlasifikasiHasil($item['field'], $value) }}
                    @else
                        -
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- ORGANISASI -->
            <tr>
                <td colspan="6" class="section-header">VI. ORGANISASI</td>
            </tr>
            @php $no = 1; @endphp
            @foreach([
                ['name' => 'Bagian Quality Control', 'field' => 'bagian_quality_control', 'unit' => 'orang']
            ] as $item)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['unit'] }}</td>
                @foreach(['3', '2', '1'] as $value)
                <td style="text-align: center; font-size: 8pt;">
                    @if($klasifikasi->{$item['field']} === $value)
                        {{ $getKlasifikasiHasil($item['field'], $value) }}
                    @else
                        -
                    @endif
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
                    <p><u>{{ $klasifikasi->mitra->nama_cp }}</u><br>
                    {{ $klasifikasi->mitra->nama_perusahaan }}</p>
                </div>
            </div>
        </div>
</body>
</html>