<?php
ob_start();
session_start();

if (!isset($_SESSION['login'])) {
    header("location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION['role'] != 'pegawai') {
    header("location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

$judul = 'Rekap Presensi';
include_once('../../config.php');

require('../../assets/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$id = $_SESSION['id'];
$tanggal_dari = $_POST['tanggal_dari'];
$tanggal_sampai = $_POST['tanggal_sampai'];
$result = mysqli_query($con, "SELECT * FROM presensi WHERE id_pegawai = '$id' AND tgl_masuk BETWEEN '$tanggal_dari' AND '$tanggal_sampai' ORDER BY tgl_masuk DESC");

$lokasi_presensi = $_SESSION['lokasi_presensi'];
$lokasi = mysqli_query($con, "SELECT nama_lokasi, jam_masuk FROM lokasi_presensi");
while ($lokasi_result = mysqli_fetch_array($lokasi)) :
    $jam_masuk_kantor = date('H:i:s', strtotime($lokasi_result['jam_masuk']));
endwhile;


$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'REKAP PRESENSI');
$sheet->setCellValue('A2', 'Tanggal Awal');
$sheet->setCellValue('A3', 'Tanggal Akhir');
$sheet->setCellValue('C2', $tanggal_dari);
$sheet->setCellValue('C3', $tanggal_sampai);
$sheet->setCellValue('A5', 'NO');
$sheet->setCellValue('B5', 'TANGGAL MASUK');
$sheet->setCellValue('C5', 'JAM MASUK');
$sheet->setCellValue('D5', 'TANGGAL KELUAR');
$sheet->setCellValue('E5', 'JAM KELUAR');
$sheet->setCellValue('F5', 'TOTAL JAM KERJA');
$sheet->setCellValue('G5', 'TOTAL JAM TERLAMBAT');

$sheet->mergeCells('A1:F1');
$sheet->mergeCells('A2:B2');
$sheet->mergeCells('A3:B3');

$no = 1;
$row = 6;
while ($data = mysqli_fetch_array($result)) {

    // menghitung total jam kerja
    $jam_tanggal_masuk = date('Y-m-d H:i:s', strtotime($data['tgl_masuk'] . ' ' . $data['jam_masuk']));
    $jam_tanggal_keluar = date('Y-m-d H:i:s', strtotime($data['tgl_keluar'] . ' ' . $data['jam_keluar']));

    $timestamp_masuk = strtotime($jam_tanggal_masuk);
    $timestamp_keluar = strtotime($jam_tanggal_keluar);

    $selisih = $timestamp_keluar - $timestamp_masuk;

    $total_jam_kerja = floor($selisih / 3600);
    $selisih -= $total_jam_kerja * 3600;
    $selisih_menit_kerja = floor($selisih / 60);

    // // menghitung total jam terlambat
    $jam_masuk = date('H:i:s', strtotime($data['jam_masuk']));
    $timestamp_jam_masuk_real = strtotime($jam_masuk);
    $timestamp_jam_masuk_kantor = strtotime($jam_masuk_kantor);

    $terlambat = $timestamp_jam_masuk_kantor - $timestamp_jam_masuk_real;

    $total_jam_terlambat = floor($terlambat / 3600);
    $terlambat -= $total_jam_terlambat * 3600;
    $selisih_menit_terlambat = floor($terlambat / 60);

    $sheet->setCellValue('A' . $row, $no);
    $sheet->setCellValue('B' . $row, $data['tgl_masuk']);
    $sheet->setCellValue('C' . $row, $data['jam_masuk']);
    $sheet->setCellValue('D' . $row, $data['tgl_keluar']);
    $sheet->setCellValue('E' . $row, $data['jam_keluar']);
    $sheet->setCellValue('F' . $row, $total_jam_kerja . ' Jam ' . $selisih_menit_kerja . ' Menit ');
    $sheet->setCellValue('G' . $row, $total_jam_terlambat . ' Jam ' . $selisih_menit_terlambat . ' Menit ');


    $no++;
    $row++;
}

// var_dump($sheet);
// die();

// mendownload file lewat browser dan tersimpan di file local
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan Presensi.xlsx"');
header('Cache-Control: max-age=0');

$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
