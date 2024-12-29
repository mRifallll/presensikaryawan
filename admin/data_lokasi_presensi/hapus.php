<?php

session_start();
require_once('../../config.php');

$id = $_GET['id'];

$result = mysqli_query($con, "DELETE FROM lokasi_presensi WHERE id = $id");

$_SESSION['berhasil'] = "Data Berhasil Dihapus";
header("location: lokasi_presensi.php");
exit;

include('../layout/footer.php');
