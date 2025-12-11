@php
    // Helper function untuk mengkonversi simbol Unicode ke ASCII untuk perbandingan dan display
    function normalizeValue($value) {
        if (!$value) return $value;
        $value = str_replace('≤', '<', $value);
        $value = str_replace('≥', '>', $value);
        return $value;
    }
    
    // Helper function untuk display - konversi simbol untuk ditampilkan di PDF
    function displayValue($value) {
        if (!$value) return $value;
        $value = str_replace('≤', '<', $value);
        $value = str_replace('≥', '>', $value);
        return $value;
    }
@endphp
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
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
        }
        .header p {
            margin: 3px 0;
            font-size: 12pt;
            font-weight: bold;
        }
        .info-section {
            
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 2px 0;
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
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 2px;
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
            
        }
        .signature-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .signature-space {
            height: 50px;
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
                <td><strong>{{ $nomor_urut_klasifikasi }}</strong></td>
            </tr>
            <tr>
                <td>TANGGAL KLASIFIKASI</td>
                <td>:</td>
                <td><strong>{{ date('d/m/Y', strtotime($klasifikasi->created_at)) }}</strong></td>
            </tr>
            <tr>
                <td>NOMOR ENTITAS BULOG</td>
                <td>:</td>
                <td><strong>{{ $nomor_entitas }}</strong></td>
            </tr>
            <tr>
                <td>UNIT PELAKSANA SELEKSI</td>
                <td>:</td>
                <td><strong>{{ $unit_pelaksana }}</strong></td>
            </tr>
        </table>

            <!-- <div style="margin-top: 8px;"> -->
            <p style="font-weight: bold;">DATA MITRA KERJA:</p>
            <table class="info-table">
                <tr>
                    <td>1. Nama Perusahaan</td>
                    <td>:</td>
                    <td><strong>{{ $klasifikasi->mitra->nama_perusahaan }}</strong></td>
                </tr>
                <tr>
                    <td>2. Badan Hukum/Usaha</td>
                    <td>:</td>
                    <td><strong>{{ $klasifikasi->mitra->badan_hukum_usaha }}</strong></td>
                </tr>
                <tr>
                    <td>3. Alamat Perusahaan</td>
                    <td>:</td>
                    <td><strong>{{ $klasifikasi->mitra->alamat_perusahaan }}</strong></td>
                </tr>
                <tr>
                    <td>4. Status</td>
                    <td>:</td>
                    <td><strong>{{ $klasifikasi->mitra->status_perusahaan ?? 'Penggilingan' }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="2" width="40">No</th>
                <th rowspan="2" width="200">Komponen</th>
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
                <td class="section-header" style="text-align: center;">I</td>
                <td class="section-header">PENGERINGAN</td>
                <td class="section-header"></td>
                <td class="section-header"></td>
                <td class="section-header"></td>
                <td class="section-header"></td>
            </tr>
            @php $no = 1; @endphp
            @foreach([
                ['name' => 'Mesin Pembersih Gabah', 'field' => 'mesin_pembersih_gabah', 'unit' => 'ton/hari', 'options' => ['1. Tidak Ada', '2. Ada | < 20', '3. Ada | > 20']],
                ['name' => 'Lantai Jemur', 'field' => 'lantai_jemur', 'unit' => 'ton/hari', 'options' => ['1. Tidak ada', '2. Ada | 1 s/d 10', '3. Ada | > 10']],
                ['name' => 'Mesin Pengering', 'field' => 'mesin_pengering', 'unit' => 'ton/hari', 'options' => ['1. Tidak ada', '2. Ada | < 20', '3. Ada | > 20']],
                ['name' => 'Alat Pengering Lainnya', 'field' => 'alat_pengering_lainnya', 'unit' => 'ton/hari', 'options' => ['1. Ada | < 1', '2. Tidak Disyaratkan', '3. Tidak Disyaratkan']]
            ] as $item)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['unit'] }}</td>
                @foreach($item['options'] as $option)
                <td style="text-align: center; font-size: 8pt;">
                    @if(normalizeValue($klasifikasi->{$item['field']}) === normalizeValue($option))
                        <strong style="font-size: 10pt;">{{ $option }}</strong>
                    @else
                        {{ $option }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- PENGGILINGAN -->
            <tr>
                <td class="section-header" style="text-align: center;">II</td>
                <td class="section-header">PENGGILINGAN (RMP)</td>
                <td class="section-header"></td>
                <td class="section-header"></td>
                <td class="section-header"></td>
                <td class="section-header"></td>
            </tr>
            @php $no = 1; @endphp
            @foreach([
                ['name' => 'Mesin Pembersih Awal', 'field' => 'mesin_pembersih_awal', 'unit' => 'ton/jam', 'options' => ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3']],
                ['name' => 'Mesin Pemecah Kulit', 'field' => 'mesin_pemecah_kulit', 'unit' => 'ton/jam', 'options' => ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3']],
                ['name' => 'Mesin Pembersih Sekam', 'field' => 'mesin_pembersih_sekam', 'unit' => 'ton/jam', 'options' => ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3']],
                ['name' => 'Mesin Pemisah Gabah Pecah Kulit', 'field' => 'mesin_pemisah_gabah_pecah_kulit', 'unit' => 'ton/jam', 'options' => ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3']],
                ['name' => 'Mesin Pemisah Batu', 'field' => 'mesin_pemisah_batu', 'unit' => 'ton/jam', 'options' => ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3']],
                ['name' => 'Mesin Penyosoh', 'field' => 'mesin_penyosoh', 'unit' => 'ton/jam; pass', 'options' => ['1. Ada | < 1 | 1', '2. Ada | 1 s/d 3 | 1', '3. Ada | > 3 | 2']],
                ['name' => 'Mesin Pengkabut', 'field' => 'mesin_pengkabut', 'unit' => 'ton/jam; pass', 'options' => ['1. Ada | < 1 | 1', '2. Ada | 1 s/d 3 | 1', '3. Ada | > 3 | 2']],
                ['name' => 'Mesin Pemisah Menir', 'field' => 'mesin_pemesah_menir', 'unit' => 'ton/jam', 'options' => ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3']],
                ['name' => 'Mesin Pemisah Katul', 'field' => 'mesin_pemisah_katul', 'unit' => 'ton/jam', 'options' => ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3']],
                ['name' => 'Mesin Pemisah Berdasarkan Ukuran', 'field' => 'mesin_pemisah_berdasarkan_ukuran', 'unit' => 'ton/jam', 'options' => ['1. Ada | < 1', '2. Ada | 1 s/d 3', '3. Ada | > 3']],
                ['name' => 'Mesin Pemisah Berdasarkan Warna', 'field' => 'mesin_pemisah_berdasarkan_warna', 'unit' => 'ton/jam', 'options' => ['1. Tidak ada', '2. Ada | 1 s/d 3', '3. Ada | > 3']],
                ['name' => 'Tangki Penyimpanan', 'field' => 'tangki_penyimpanan', 'unit' => 'ton/unit', 'options' => ['1. Tidak ada', '2. Ada | < 10', '3. Ada | > 10']],
                ['name' => 'Mesin Pengemas', 'field' => 'mesin_pengemas', 'unit' => 'ton/jam', 'options' => ['1. Tidak ada', '2. Ada | Semi Otomatis', '3. Ada | Full Otomatis']],
                ['name' => 'Mesin Jahit', 'field' => 'mesin_jahit', 'unit' => 'unit', 'options' => ['1. Tidak ada', '2. Ada | Semi Otomatis', '3. Ada | Full Otomatis']]
            ] as $item)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['unit'] }}</td>
                @foreach($item['options'] as $option)
                <td style="text-align: center; font-size: 8pt;">
                    @if(normalizeValue($klasifikasi->{$item['field']}) === normalizeValue($option))
                        <strong style="font-size: 10pt;">{{ $option }}</strong>
                    @else
                        {{ $option }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- SARANA PENYIMPANAN -->
            <tr>
                <td class="section-header" style="text-align: center;">III</td>
                <td class="section-header">SARANA PENYIMPANAN</td>
                <td class="section-header"></td>
                <td class="section-header"></td>
                <td class="section-header"></td>
                <td class="section-header"></td>
            </tr>
            @php $no = 1; @endphp
            @foreach([
                ['name' => 'Gudang Konvensional', 'field' => 'gudang_konvensional', 'unit' => 'ton', 'options' => ['1. Tidak ada', '2. Ada | < 3000', '3. Ada | > 3000']],
                ['name' => 'Silo GKG / Hopper', 'field' => 'silo_gkg_hopper', 'unit' => 'ton', 'options' => ['1. Tidak ada', '2. Tidak Ada', '3. Ada | > 2000']]

            ] as $item)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['unit'] }}</td>
                @foreach($item['options'] as $option)
                <td style="text-align: center; font-size: 8pt;">
                    @if(normalizeValue($klasifikasi->{$item['field']}) === $option)
                        <strong style="font-size: 10pt;">{{ $option }}</strong>
                    @else
                        {{ $option }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- SARANA ANGKUTAN -->
            <tr>
                <td class="section-header" style="text-align: center;">IV</td>
                <td class="section-header">SARANA ANGKUTAN</td>
                <td class="section-header"></td>
                <td class="section-header"></td>
                <td class="section-header"></td>
                <td class="section-header"></td>
            </tr>
            @php $no = 1; @endphp
            @foreach([
                ['name' => 'Truk', 'field' => 'truk', 'unit' => 'unit', 'options' => ['1. Tidak ada', '2. Ada | 1 s/d 5', '3. Ada | > 5']]
            ] as $item)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['unit'] }}</td>
                @foreach($item['options'] as $option)
                <td style="text-align: center; font-size: 8pt;">
                    @if(normalizeValue($klasifikasi->{$item['field']}) === $option)
                        <strong style="font-size: 10pt;">{{ $option }}</strong>
                    @else
                        {{ $option }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- KELENGKAPAN PEMERIKSAAN -->
            <tr>
                <td class="section-header" style="text-align: center;">V</td>
                <td class="section-header">KELENGKAPAN PEMERIKSAAN</td>
                <td class="section-header"></td>
                <td class="section-header"></td>
                <td class="section-header"></td>
                <td class="section-header"></td>
            </tr>
            @php $no = 1; @endphp
                        @foreach([
                ['name' => 'Mini Lab', 'field' => 'mini_lab', 'unit' => 'unit', 'options' => ['1. Tidak ada', '2. Ada | Tidak Khusus', '3. Ada | Ruang Khusus']],
                ['name' => 'Moisture Tester', 'field' => 'moisture_tester', 'unit' => 'unit', 'options' => ['1. Tidak ada', '2. Ada | Berfungsi', '3. Ada | Lengkap | Berfungsi']],
                ['name' => 'Pembanding Derajat Sosoh', 'field' => 'pembanding_derajat_sosoh', 'unit' => 'unit', 'options' => ['1. Tidak ada', '2. Ada | Tidak Sesuai', '3. Ada | Sesuai Standar']]
            ] as $item)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['unit'] }}</td>
                @foreach($item['options'] as $option)
                <td style="text-align: center; font-size: 8pt;">
                    @if(normalizeValue($klasifikasi->{$item['field']}) === $option)
                        <strong style="font-size: 10pt;">{{ $option }}</strong>
                    @else
                        {{ $option }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach

            <!-- ORGANISASI -->
            <tr>
                <td class="section-header" style="text-align: center;">VI</td>
                <td class="section-header">ORGANISASI</td>
                <td class="section-header"></td>
                <td class="section-header"></td>
                <td class="section-header"></td>
                <td class="section-header"></td>
            </tr>
            @php $no = 1; @endphp
            @foreach([
                ['name' => 'Bagian Quality Control', 'field' => 'bagian_quality_control', 'unit' => 'orang', 'options' => ['1. Tidak ada', '2. Ada | Merangkap', '3. Ada | Tidak Merangkap']]
            ] as $item)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $item['name'] }}</td>
                <td style="text-align: center;">{{ $item['unit'] }}</td>
                @foreach($item['options'] as $option)
                <td style="text-align: center; font-size: 8pt;">
                    @if(normalizeValue($klasifikasi->{$item['field']}) === $option)
                        <strong style="font-size: 10pt;">{{ $option }}</strong>
                    @else
                        {{ $option }}
                    @endif
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
        <div class="signature-section">
                <p>Dengan ini, kami menyatakan bahwa data dan/atau informasi yang kami berikan adalah benar.</p>
                
                @php
                    $namaPenandatangan = $klasifikasi->mitra->nama_cp;
                    if ($klasifikasi->mitra->surat_kuasa === 'Ada' && !empty($klasifikasi->mitra->nama_yang_dikuasakan)) {
                        $namaPenandatangan = $klasifikasi->mitra->nama_yang_dikuasakan;
                    }
                @endphp
                
                <div class="signature-box">
                    <p>Mitra Pangan,</p>
                    <div class="signature-space"></div>
                    <p><u style="font-weight: bold;">{{ $namaPenandatangan }}</u><br>
                    {{ $klasifikasi->mitra->nama_perusahaan }}</p>
                </div>
            </div>
        </div>
</body>
</html>