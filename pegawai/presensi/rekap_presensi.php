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
include('../layout/header.php');
include_once('../../config.php');

if (empty($_GET['tanggal_dari'])) {
    $id = $_SESSION['id'];
    $result = mysqli_query($con, "SELECT * FROM presensi WHERE id_pegawai = '$id' ORDER BY tgl_masuk DESC");
} else {
    $id = $_SESSION['id'];
    $tanggal_dari = $_GET['tanggal_dari'];
    $tanggal_sampai = $_GET['tanggal_sampai'];
    $result = mysqli_query($con, "SELECT * FROM presensi WHERE id_pegawai = '$id' AND tgl_masuk BETWEEN '$tanggal_dari' AND '$tanggal_sampai' ORDER BY tgl_masuk DESC");
}

$lokasi_presensi = $_SESSION['lokasi_presensi'];
$lokasi = mysqli_query($con, "SELECT nama_lokasi, jam_masuk FROM lokasi_presensi");

while ($lokasi_result = mysqli_fetch_array($lokasi)) :
    $jam_masuk_kantor = date('H:i:s', strtotime($lokasi_result['jam_masuk']));
endwhile;
?>

<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-md-2">
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Export Excel
                </button>
            </div>
            <div class="col-md-10">
                <form method="GET">
                    <div class="input-group">
                        <input type="date" class="form-control" name="tanggal_dari">
                        <input type="date" class="form-control" name="tanggal_sampai">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-bordered">
            <tr class="text-center">
                <th>NO.</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Total Jam Kerja</th>
                <th>Total Terlambat</th>
            </tr>

            <?php if (mysqli_num_rows($result) === 0) { ?>
                <tr>
                    <td colspan="6">Data Rekap Presensi Masih kosong</td>
                </tr>
            <?php } else { ?>

                <?php
                $no = 1;
                while ($rekap = mysqli_fetch_array($result)) :
                    // menghitung total jam kerja
                    $jam_tanggal_masuk = date('Y-m-d H:i:s', strtotime($rekap['tgl_masuk'] . ' ' . $rekap['jam_masuk']));
                    $jam_tanggal_keluar = date('Y-m-d H:i:s', strtotime($rekap['tgl_keluar'] . ' ' . $rekap['jam_keluar']));

                    $timestamp_masuk = strtotime($jam_tanggal_masuk);
                    $timestamp_keluar = strtotime($jam_tanggal_keluar);

                    $selisih = $timestamp_keluar - $timestamp_masuk;

                    $total_jam_kerja = floor($selisih / 3600);
                    $selisih -= $total_jam_kerja * 3600;
                    $selisih_menit_kerja = floor($selisih / 60);

                    // // menghitung total jam terlambat
                    $jam_masuk = date('H:i:s', strtotime($rekap['jam_masuk']));
                    $timestamp_jam_masuk_real = strtotime($jam_masuk);
                    $timestamp_jam_masuk_kantor = strtotime($jam_masuk_kantor);

                    $terlambat = $timestamp_jam_masuk_kantor - $timestamp_jam_masuk_real;

                    $total_jam_terlambat = floor($terlambat / 3600);
                    $terlambat -= $total_jam_terlambat * 3600;
                    $selisih_menit_terlambat = floor($terlambat / 60);
                ?>

                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d F Y', strtotime($rekap['tgl_masuk'])) ?></td>
                        <td class="text-center"><?= $rekap['jam_masuk'] ?></td>
                        <td class="text-center"><?= $rekap['jam_keluar'] ?></td>
                        <td class="text-center">
                            <?php if ($rekap['tgl_keluar'] == '0000-00-00') : ?>
                                <span>0 Jam 0 Menit</span>
                            <?php else : ?>
                                <?= $total_jam_kerja . ' Jam ' . $selisih_menit_kerja . ' Menit' ?>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($total_jam_terlambat < 0) : ?>
                                <span class="badge bg-success">On Time</span>
                            <?php else : ?>
                                <?= $total_jam_terlambat . ' Jam ' . $selisih_menit_terlambat . ' Menit' ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php } ?>
        </table>
    </div>
</div>
<div class="modal" id="exampleModal" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Excel Rekap Presensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= base_url('pegawai/presensi/rekap_excel.php') ?>">
                <div class="modal-body">

                    <div class="md-3">
                        <label for="">Tanggal Awal</label>
                        <input type="date" class="form-control" name="tanggal_dari">
                    </div>

                    <div class="md-3">
                        <label for="">Tanggal Akhir</label>
                        <input type="date" class="form-control" name="tanggal_sampai">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php include('../layout/footer.php') ?>