<?php
ob_start();
session_start();
if (!isset($_SESSION['login'])) {
    header("location: ../../auth/login.php?pesan=belum_login");
} else if ($_SESSION['role'] != 'pegawai') {
    header("location: ../../auth/login.php?pesan=tolak_akses");
}
$judul = "Ubah Password";
include('../layout/header.php');
require_once('../../config.php');
$id = $_SESSION['id'];

if (isset($_POST['update'])) {
    $password_baru = password_hash($_POST['password_baru'], PASSWORD_DEFAULT);
    $ulangi_password_baru = password_hash($_POST['ulangi_password_baru'], PASSWORD_DEFAULT);

    $pesan_kesalahan = [];

    if (empty($_POST['password_baru'])) {
        $pesan_kesalahan[] = "<i class ='fa-solid fa-check'></i>Password Baru wajib diisi";
    }
    if (empty($_POST['ulangi_password_baru'])) {
        $pesan_kesalahan[] = "<i class ='fa-solid fa-check'></i>Ulangi Password Baru wajib diisi";
    }
    if ($_POST['password_baru'] != $_POST['ulangi_password_baru']) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Password Tidak Cocok";
    }
    if (!empty($pesan_kesalahan)) {
        $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
    } else {
        $pegawai = mysqli_query($con, "UPDATE users SET 
        password = '$password_baru'
    WHERE  id = $id");

        $_SESSION['berhasil'] = 'Password berhasil Diupdate';
        header("Location: ../home/home.php");
        exit;
    }
}
?>

<div class="page-body">
    <div class="container-xl">

        <form action="" method="POST">
            <div class="card col-md-6">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="">Password Baru</label>
                        <input type="password" name="password_baru" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="">Ulangi Password Baru</label>
                        <input type="password" name="ulangi_password_baru" class="form-control">
                    </div>

                    <input type="hidden" name="id" value="<?= $id ?>">
                    <button type="submit" class="btn btn-primary" name="update">Update</button>
                </div>
            </div>
        </form>

    </div>
</div>

<?php include('../layout/footer.php') ?>