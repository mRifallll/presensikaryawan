<?php
session_start();
ob_start();
if (!isset($_SESSION['login'])) {
    header("location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION['role'] != 'admin') {
    header("location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Tambah Data Jabatan";
include('../layout/header.php');
require_once('../../config.php');

if (isset($_POST['submit'])) {
    $jabatan = htmlspecialchars($_POST['jabatan']);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($jabatan)) {
            $pesan = "Nama jabatan wajib diisi";
        }
        if (!empty($pesan)) {
            $_SESSION['validasi'] = $pesan;
        } else {
            $result = mysqli_query($con, "INSERT INTO jabatan(jabatan) values ('$jabatan')");
            $_SESSION['berhasil'] = "Data Berhasil disimpan";
            header("location: jabatan.php");
            exit;
        }
    }
}

?>
<!-- Page body -->
<div class="page-body">
    <div class="container-xl">

        <div class="card col-md-5">
            <div class="card-body">
                <form action="<?= base_url('admin/data_jabatan/tambah.php') ?>" method="POST">
                    <div class="mb-2">
                        <label class="mb-2" for="">Nama Jabatan</label><br>
                        <input type="text" class="from-control col-md-12" name="jabatan" placeholder="Input jabatan">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>

        </div>
    </div>
</div>

<?php include('../layout/footer.php') ?>