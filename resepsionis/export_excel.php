<?php
require_once '../controller/resepsionis.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

$kelasList = getAllKelas();
$kelasX = array_filter($kelasList, fn($k) => $k['kelas'] === 'X');
$kelasXI = array_filter($kelasList, fn($k) => $k['kelas'] === 'XI');
$kelasXII = array_filter($kelasList, fn($k) => $k['kelas'] === 'XII');

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Absensi');

// Header Laporan
$sheet->mergeCells('A1:N1');
$sheet->setCellValue('A1', 'LAPORAN PIKET KBM HARIAN');
$sheet->mergeCells('A2:N2');
$sheet->setCellValue('A2', 'SMK NEGERI 17 JAKARTA');
$sheet->mergeCells('A3:N3');
$sheet->setCellValue('A3', 'SEMESTER GANJIL TAHUN PELAJARAN 2024-2025');

// Style Header Laporan
$headerStyles = [
    'font' => ['bold' => true, 'size' => 14],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
];
$sheet->getStyle('A1:M3')->applyFromArray($headerStyles);

// Jarak dari header
$startRow = 5;

// Set header untuk setiap grup (Kelas X, XI, XII)
$columns = ['A', 'G', 'M']; // Kolom awal untuk setiap grup
$grupNama = ['Kelas X', 'Kelas XI', 'Kelas XII'];
$grupData = [$kelasX, $kelasXI, $kelasXII];

foreach ($grupData as $index => $kelasGrup) {
    $col = $columns[$index];
    $sheet->mergeCells("{$col}{$startRow}:" . chr(ord($col) + 1) . "{$startRow}");
    $sheet->setCellValue("{$col}{$startRow}", $grupNama[$index]);
    $sheet->getStyle("{$col}{$startRow}")->applyFromArray($headerStyles);

    // baris kosong setelah header grup
    $row = $startRow + 2;

    // Set lebar kolom
    $sheet->getColumnDimension($col)->setWidth(25); // Kolom Nama
    $sheet->getColumnDimension(chr(ord($col) + 1))->setWidth(15); // Kolom Keterangan

    // Loop kelas dalam grup
    foreach ($kelasGrup as $kelas) {
        // Nama Kelas
        $sheet->mergeCells("{$col}{$row}:" . chr(ord($col) + 1) . "{$row}");
        $sheet->setCellValue("{$col}{$row}", $kelas['nama_kelas']);
        $sheet->getStyle("{$col}{$row}")->applyFromArray($headerStyles);

        // Ambil data absensi
        $dataAbsensi = getAbsensiByKelasAndTanggal($kelas['id_kelas'], $tanggal);
        $jumlahBaris = max(5, count($dataAbsensi));

        // Header Tabel
        $row++;
        $sheet->setCellValue("{$col}{$row}", 'Nama');
        $sheet->setCellValue(chr(ord($col) + 1) . "{$row}", 'Keterangan');
        $sheet->getStyle("{$col}{$row}:" . chr(ord($col) + 1) . "{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Data Tabel
        $startDataRow = $row + 1; // Baris awal data
        for ($i = 0; $i < $jumlahBaris; $i++) {
            $row++;
            $sheet->setCellValue("{$col}{$row}", isset($dataAbsensi[$i]['nama_siswa']) ? $dataAbsensi[$i]['nama_siswa'] : '');
            $sheet->setCellValue(chr(ord($col) + 1) . "{$row}", isset($dataAbsensi[$i]['keterangan']) ? $dataAbsensi[$i]['keterangan'] : '');
        }

        // Border untuk Tabel
        $sheet->getStyle("{$col}{$startDataRow}:" . chr(ord($col) + 1) . "{$row}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // baris kosong setelah tabel
        $row += 2;
    }
}


// Download file
$filename = "Laporan_Absensi_{$tanggal}.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"{$filename}\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
