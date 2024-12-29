<?php
session_start();
ob_start();
if (!isset($_SESSION['login'])) {
    header("location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION['role'] != 'admin') {
    header("location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}

$judul = "Edit Data Jabatan";
include('../layout/header.php');
require_once('../../config.php');

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $jabatan = htmlspecialchars($_POST['jabatan']);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($jabatan)) {
            $pesan = "Nama jabatan wajib diisi";
        }
        if (!empty($pesan)) {
            $_SESSION['validasi'] = $pesan;
        } else {
            $result = mysqli_query($con, "UPDATE jabatan SET jabatan='$jabatan' WHERE id=$id");
            if ($result) {
                $_SESSION['berhasil'] = "Data Berhasil Diupdate";
                header("location: jabatan.php");
                exit;
            }
        }
    }
}

$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$result = mysqli_query($con, "SELECT * FROM jabatan WHERE id=$id");

if ($result) {
    $jabatan = mysqli_fetch_array($result);
    $nama_jb = $jabatan['jabatan'];
} else {
    echo "Error fetching record: " . mysqli_error($con);
}
?>

<!-- Halaman Utama -->
<div class="page-body">
    <div class="container-xl">

        <div class="card col-md-5">
            <div class="card-body">
                <form action="" method="POST"> <!-- Menghapus fungsi base_url untuk menggunakan jalur relatif -->
                    <div class="mb-2">
                        <label class="mb-2" for="">Nama Jabatan</label><br>
                        <input type="text" class="form-control col-md-12" name="jabatan" value="<?= htmlspecialchars($nama_jb) ?>">
                    </div>
                    <input type="hidden" value="<?= htmlspecialchars($id) ?>" name="id">
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </form>
            </div>

        </div>
    </div>
</div>

<?php include('../layout/footer.php') ?>