<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Hasil Klasifikasi</title>
    <style>
        @page {
            margin: 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            font-size: 10.5pt;
            font-weight: bold;
            margin: 1.5px 0;
            padding: 0;
            line-height: 1.15;
        }
        .content {
            text-align: justify;
            margin-bottom: 7px;
            font-size: 9pt;
        }
        .info-table {
            width: 100%;
            margin-bottom: 7px;
            border-spacing: 0;
        }
        .info-table td {
            padding: 1px 0;
            font-size: 9pt;
            vertical-align: top;
            line-height: 1.15;
        }
        .info-table td:first-child {
            width: 170px;
        }
        .info-table td:nth-child(2) {
            width: 13px;
        }
        .data-mitra {
            margin: 6px 0;
        }
        .data-mitra-title {
            font-weight: bold;
            margin: 6px 0 4px 0;
            font-size: 9pt;
        }
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin: 7px 0;
            font-size: 8pt;
        }
        .result-table th,
        .result-table td {
            border: 1px solid black;
            padding: 3px;
            vertical-align: top;
            line-height: 1.15;
        }
        .result-table th {
            background-color: #f5f5f5;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
        }
        .result-table td {
            font-size: 7.5pt;
        }
        .result-table td.center {
            text-align: center;
        }
        .conclusion {
            margin: 7px 0;
            text-align: justify;
            font-size: 9pt;
        }
        .signatures {
            margin-top: 12px;
            width: 100%;
        }
        .signature-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        .signature-center {
            width: 50%;
            margin: 0 auto;
            text-align: center;
            margin-top: 10px;
        }
        .signature-space {
            height: 40px;
        }
        .signature-text {
            margin: 0;
            line-height: 1.2;
            font-size: 8.5pt;
        }
        .section-header {
            font-weight: bold;
            text-align: center;
            margin: 4px 0;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BERITA ACARA</h1>
        <h1>HASIL KLASIFIKASI MITRA PANGAN</h1>
        <h1>PENGADAAN GABAH/BERAS DALAM NEGERI</h1>
        <h1>Nomor: {{ $nomor_surat }}</h1>
        <h1>TAHUN {{ $tahun }}</h1>
    </div>

    <div class="content">
        <p>Pada hari ini, {{ $hari }} tanggal {{ $tanggal }}, telah diselesaikan proses pemeriksaan terhadap Mitra Pangan ADA Gabah/Beras DN sebagai berikut :</p>
    </div>

    <table class="info-table">
        <tr>
            <td>NOMOR URUT KLASIFIKASI</td>
            <td>:</td>
            <td>{{ $nomor_urut }}</td>
        </tr>
        <tr>
            <td>TANGGAL KLASIFIKASI</td>
            <td>:</td>
            <td>{{ $tanggal_klasifikasi }}</td>
        </tr>
        <tr>
            <td>NOMOR ENTITAS BULOG</td>
            <td>:</td>
            <td>{{ $nomor_entitas }}</td>
        </tr>
        <tr>
            <td>UNIT PELAKSANA KLASIFIKASI</td>
            <td>:</td>
            <td>{{ $unit_pelaksana }}</td>
        </tr>
    </table>

    <div class="data-mitra">
        <p class="data-mitra-title">DATA MITRA KERJA,</p>
        <table class="info-table">
            <tr>
                <td>1. Nama Perusahaan</td>
                <td>:</td>
                <td>{{ $nama_perusahaan }}</td>
            </tr>
            <tr>
                <td>2. Badan Hukum/Usaha</td>
                <td>:</td>
                <td>{{ $badan_usaha }}</td>
            </tr>
            <tr>
                <td>3. Alamat Perusahaan</td>
                <td>:</td>
                <td>{{ $alamat }}</td>
            </tr>
            <tr>
                <td>4. Status</td>
                <td>:</td>
                <td>{{ $status }}</td>
            </tr>
        </table>
    </div>

    <p class="data-mitra-title">Hasil klasifikasi sebagai berikut :</p>

    <table class="result-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 30%;">KOMPONEN</th>
                <th style="width: 15%;">HASIL</th>
                <th style="width: 5%;">NO</th>
                <th style="width: 30%;">KOMPONEN</th>
                <th style="width: 15%;">HASIL</th>
            </tr>
        </thead>
        <tbody>
            <!-- PENGERINGAN -->
            <tr>
                <td colspan="3" class="section-header">I. PENGERINGAN</td>
                <td class="center">11</td>
                <td>Mesin Pemisah berdasarkan Warna</td>
                <td>{{ $hasil['mesin_pemisah_berdasarkan_warna'] }}</td></tr>
            </tr>
            <tr>
                <td class="center">1</td>
                <td>Mesin Pembersih Gabah</td>
                <td>{{ $hasil['mesin_pembersih_gabah'] }}</td>
                <td class="center">12</td>
                <td>Tangki Penyimpanan</td>
                <td>{{ $hasil['tangki_penyimpanan'] }}</td>
            </tr>
            <tr>
                <td class="center">2</td>
                <td>Lantai Jemur</td>
                <td>{{ $hasil['lantai_jemur'] }}</td>
                <td class="center">13</td>
                <td>Mesin Pengkemas</td>
                <td>{{ $hasil['mesin_pengemas'] }}</td>
            </tr>
            <tr>
                <td class="center">3</td>
                <td>Mesin Pengering</td>
                <td>{{ $hasil['mesin_pengering'] }}</td>
                <td class="center">14</td>
                <td>Mesin Jahit</td>
                <td>{{ $hasil['mesin_jahit'] }}</td>
            </tr>
            <tr>
                <td class="center">4</td>
                <td>Alat Pengering Lainnya</td>
                <td>{{ $hasil['alat_pengering_lainnya'] }}</td>
                <td colspan="3" class="section-header">III. SARANA PENYIMPANAN</td>
            </tr>

            <!-- PENGGILINGAN (RMU) -->
            <tr>
                <td colspan="3" class="section-header">II. PENGGILINGAN (RMU)</td>
                <td class="center">1</td>
                <td>Gudang Konvensional</td>
                <td>{{ $hasil['gudang_konvensional'] }}</td>
            </tr>
            <tr>
                <td class="center">1</td>
                <td>Mesin Pembersih Awal</td>
                <td>{{ $hasil['mesin_pembersih_awal'] }}</td>
                <td class="center">2</td>
                <td>Silo GKG / Hopper</td>
                <td>{{ $hasil['silo_gkg_hopper'] }}</td>
            </tr>
            <tr>
                <td class="center">2</td>
                <td>Mesin Pemecah Kulit</td>
                <td>{{ $hasil['mesin_pemecah_kulit'] }}</td>
                <td colspan="3" class="section-header">IV. SARANA ANGKUTAN</td>
            </tr>
            <tr>
                <td class="center">3</td>
                <td>Mesin Pembersih Sekam</td>
                <td>{{ $hasil['mesin_pembersih_sekam'] }}</td>
                <td class="center">1</td>
                <td>Truk</td>
                <td>{{ $hasil['truk'] }}</td>
            </tr>
            <tr>
                <td class="center">4</td>
                <td>Mesin Pemisah Gabah dengan Beras Pecah Kulit</td>
                <td>{{ $hasil['mesin_pemisah_gabah_pecah_kulit'] }}</td>
                <td colspan="3" class="section-header">V. KELENGKAPAN PEMERIKSAAN</td>
            </tr>
            <tr>
                <td class="center">5</td>
                <td>Mesin Pemisah Batu</td>
                <td>{{ $hasil['mesin_pemisah_batu'] }}</td>
                <td class="center">1</td>
                <td>Mini Lab</td>
                <td>{{ $hasil['mini_lab'] }}</td>
            </tr>
            <tr>
                <td class="center">6</td>
                <td>Mesin Penyosoh</td>
                <td>{{ $hasil['mesin_penyosoh'] }}</td>
                <td class="center">2</td>
                <td>Moisture Tester (G-Won + KETT)</td>
                <td>{{ $hasil['moisture_tester'] }}</td>
            </tr>
            <tr>
                <td class="center">7</td>
                <td>Mesin Pengkabut</td>
                <td>{{ $hasil['mesin_pengkabut'] }}</td>
                <td class="center">3</td>
                <td>Pembanding Derajat Sosoh (Monster)</td>
                <td>{{ $hasil['pembanding_derajat_sosoh'] }}</td>
            </tr>
            <tr>
                <td class="center">8</td>
                <td>Mesin Pemisah Menir</td>
                <td>{{ $hasil['mesin_pemesah_menir'] }}</td>
                <td colspan="3" class="section-header">VI. ORGANISASI</td>
            </tr>
            <tr>
                <td class="center">9</td>
                <td>Mesin Pemisah Katul</td>
                <td>{{ $hasil['mesin_pemisah_katul'] }}</td>
                <td class="center">1</td>
                <td>Bagian Quality Control</td>
                <td>{{ $hasil['bagian_quality_control'] }}</td>
            </tr>
            <tr>
                <td class="center">10</td>
                <td>Mesin Pemisah berdasarkan Ukuran</td>
                <td>{{ $hasil['mesin_pemisah_berdasarkan_ukuran'] }}</td>
                <td colspan="3" class="section-header" style="font-size: 10pt; padding: 8px;">KLASIFIKASI: {{ $klasifikasi->hasil_klasifikasi ?? 'C' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="conclusion">
        <p>Berdasarkan hasil tersebut, maka {{ $nama_perusahaan }} dinyatakan masuk dalam klasifikasi <strong>{{ $klasifikasi->hasil_klasifikasi ?? 'C' }}</strong></p>
    </div>

    <div class="signatures">
        <div class="signature-row">
            <!-- Left signature -->
            <div class="signature-box">
                <br>
                <p class="signature-text">Mitra Pangan,</p>
                <div class="signature-space"></div>
                <p class="signature-text"><u>{{ $nama_cp }}</u></p>
                <p class="signature-text">{{ $nama_perusahaan }}</p>
            </div>

            <!-- Right signature -->
            <div class="signature-box">
                <p class="signature-text">Perum BULOG Kantor Cabang {{ $unit_pelaksana }}</p>
                <p class="signature-text">Pelaksana Klasifikasi,</p>
                <div class="signature-space"></div>
                <p class="signature-text"><u>{{ $pelaksana->nama_karyawan }}</u></p>
                <p class="signature-text">{{ $pelaksana->jabatan }}</p>
            </div>
        </div>

        <!-- Bottom center signature -->
        <div class="signature-center">
            <p class="signature-text">Mengetahui,</p>
            <div class="signature-space"></div>
            <p class="signature-text"><u>{{ $pengetahui->nama_karyawan }}</u></p>
            <p class="signature-text">{{ $pengetahui->jabatan }}</p>
        </div>
    </div>
</body>
</html>
