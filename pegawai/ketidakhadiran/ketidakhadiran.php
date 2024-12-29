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

$judul = 'Ketidak Hadiran';
include('../layout/header.php');
include_once('../../config.php');

$id = $_SESSION['id'];
$result = mysqli_query($con, "SELECT * FROM ketidakhadiran WHERE id_pegawai = '$id' ORDER BY id DESC");
?>

<div class="page-body">
    <div class="container-xl">

        <a href="<?= base_url('pegawai/ketidakhadiran/pengajuan.php') ?>" class="btn btn-primary">Tambah Data</a>
        <table class="table table-bordered mt-2">
            <tr class="text-center">
                <th>No.</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Deskripsi</th>
                <th>File</th>
                <th>Status Pengajuan</th>
                <th>Aksi</th>
            </tr>
            <?php if (mysqli_num_rows($result) === 0) { ?>
                <tr>
                    <td colspan="7">Data Ketidak Hadiran Kosong</td>
                </tr>
            <?php } else { ?>
                <?php $no = 1;
                while ($data = mysqli_fetch_array($result)) : ?>
                    <td><?= $no++ ?></td>
                    <td><?= date('d F Y', strtotime($data['tanggal'])) ?></td>
                    <td><?= $data['keterangan'] ?></td>
                    <td><?= $data['deskripsi'] ?></td>
                    <td class="text-center">
                        <a target="_blank" href="<?= base_url('assets/file_ketidakhadiran/' . $data['file']) ?>" class="badge badge-pill bg-primary">Download</a>
                    </td>
                    <td class="text-center"><?= $data['status_pengajuan'] ?></td>
                    <td class="text-center">
                        <a href="edit.php?id=<?= $data['id'] ?>" class="badge badge-pill bg-success">Update</a>
                        <a href="hapus.php?id=<?= $data['id'] ?>" class="badge badge-pill bg-danger tombol-hapus">Delete</a>
                    </td>

                <?php endwhile; ?>
            <?php } ?>
        </table>

    </div>
</div>

<?php include('../layout/footer.php') ?>