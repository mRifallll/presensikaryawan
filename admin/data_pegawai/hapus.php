<?php
ob_start();
session_start();

include('../layout/header.php');
require_once('../../config.php');

$id = $_GET['id'];

$result = mysqli_query($con, "DELETE FROM pegawai WHERE id=$id");

$_SESSION['berhasil'] = 'Data Berhasil Dihapus';
header("location: pegawai.php");
include('../layout/footer.php');
exit;
