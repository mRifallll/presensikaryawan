<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("location: ../../auth/login.php?pesan=belum_login");
    exit;
} else if ($_SESSION['role'] != 'admin') {
    header("location: ../../auth/login.php?pesan=tolak_akses");
    exit;
}
$judul = "Data Jabatan";
include('../layout/header.php');
require_once('../../config.php');
// Mengurutkan data dari belakang
$result = mysqli_query($con, "SELECT * FROM jabatan ORDER BY id DESC");
?>
<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <a href="../../admin/data_jabatan/tambah.php" class="btn btn-primary"><span class="text"><i class="fa-solid fa-circle-plus"></i></span>Tambah data</a>

        <div class="row row-deck row-cards mt-2">
            <table class="table table-bordered">
                <tr class="text-center">
                    <th>No.</th>
                    <th>Nama Jabatan</th>
                    <th>Aksi</th>
                </tr>
                <?php if (mysqli_num_rows($result) === 0) : ?>
                    <tr>
                        <td class="text-center" colspan="3">Data Masih Kosong, Silahkan tambahkan data baru</td>
                    </tr>
                <?php else : ?>
                    <?php $no = 1;
                    while ($jabatan = mysqli_fetch_array($result)) : ?>
                        <tr class="text-center">
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($jabatan['jabatan']) ?></td>
                            <td>
                                <!-- membuat aksi pada edit dan delete -->
                                <a href="../../admin/data_jabatan/edit.php?id=<?= $jabatan['id'] ?>" class="badge bg-primary badge-pill">Edit</a>
                                <a href="../../admin/data_jabatan/hapus.php?id=<?= $jabatan['id'] ?>" class="badge bg-danger badge-pill tombol-hapus">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile ?>
                <?php endif ?>
            </table>

        </div>
    </div>
</div>

<?php include('../layout/footer.php') ?>