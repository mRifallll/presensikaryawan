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

$judul = "Edit Data Lokasi Presensi";
include('../layout/header.php');
require_once('../../config.php');

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama_lokasi = htmlspecialchars($_POST['nama_lokasi']);
    $alamat_lokasi = htmlspecialchars($_POST['alamat_lokasi']);
    $tipe_lokasi = htmlspecialchars($_POST['tipe_lokasi']);
    $latitude = htmlspecialchars($_POST['latitude']);
    $longitude = htmlspecialchars($_POST['longitude']);
    $radius = htmlspecialchars($_POST['radius']);
    $zona_waktu = htmlspecialchars($_POST['zona_waktu']);
    $jam_masuk = htmlspecialchars($_POST['jam_masuk']);
    $jam_pulang = htmlspecialchars($_POST['jam_pulang']);

    $pesan_kesalahan = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($nama_lokasi)) {
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Nama lokasi wajib diisi";
        }

        if (empty($alamat_lokasi)) {
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Alamat lokasi wajib diisi";
        }
        if (empty($tipe_lokasi)) {
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Tipe lokasi wajib diisi";
        }
        if (empty($latitude)) {
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Latitude wajib diisi";
        }
        if (empty($longitude)) {
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Longitude wajib diisi";
        }
        if (empty($radius)) {
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Radius wajib diisi";
        }
        if (empty($zona_waktu)) {
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Zona waktu wajib diisi";
        }
        if (empty($jam_masuk)) {
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Jam masuk wajib diisi";
        }
        if (empty($jam_pulang)) {
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Jam pulang wajib diisi";
        }
        if (!empty($pesan_kesalahan)) {
            $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
        } else {
            $result = mysqli_query($con, "UPDATE lokasi_presensi SET 
            nama_lokasi='$nama_lokasi', 
            alamat_lokasi='$alamat_lokasi', 
            tipe_lokasi='$tipe_lokasi',
            latitude='$latitude',
            longitude='$longitude', 
            radius='$radius',
            zona_waktu='$zona_waktu',
            jam_masuk='$jam_masuk',
            jam_pulang='$jam_pulang'
            WHERE id=$id");
            if ($result) {
                $_SESSION['berhasil'] = "Data Berhasil Diupdate";
                header("location: lokasi_presensi.php");
                exit;
            } else {
                $_SESSION['gagal'] = "Data Gagal Diupdate";
            }
        }
    }
}

$id = isset($_GET['id']) ? $_GET['id'] : $_POST['id'];
$result = mysqli_query($con, "SELECT * FROM lokasi_presensi WHERE id=$id");

if ($result) {
    $lokasi = mysqli_fetch_array($result);
    $nama_lokasi = $lokasi['nama_lokasi'];
    $alamat_lokasi = $lokasi['alamat_lokasi'];
    $tipe_lokasi = $lokasi['tipe_lokasi'];
    $latitude = $lokasi['latitude'];
    $longitude = $lokasi['longitude'];
    $radius = $lokasi['radius'];
    $zona_waktu = $lokasi['zona_waktu'];
    $jam_masuk = $lokasi['jam_masuk'];
    $jam_pulang = $lokasi['jam_pulang'];
} else {
    echo "Error fetching record: " . mysqli_error($con);
}
?>

<!-- Halaman Utama -->
<div class="page-body">
    <div class="container-xl">
        <div class="card col-md-5">
            <div class="card-body">
                <form action="<?= base_url('admin/data_lokasi_presensi/edit.php') ?>" method="POST"> <!-- Menghapus fungsi base_url untuk menggunakan jalur relatif -->
                    <div class="mb-2">
                        <label class="mb-2" for="">Nama Lokasi</label><br>
                        <input type="text" class="form-control col-md-12" name="nama_lokasi" value="<?= htmlspecialchars($nama_lokasi) ?>">
                    </div>

                    <div class="mb-2">
                        <label class="mb-2" for="">Alamat Lokasi</label><br>
                        <input type="text" class="form-control col-md-12" name="alamat_lokasi" value="<?= htmlspecialchars($alamat_lokasi) ?>">
                    </div>

                    <div class="mb-2">
                        <label class="mb-2" for="">Tipe Lokasi</label><br>
                        <select name="tipe_lokasi" class="form-control">
                            <option value="">--Pilih Tipe Lokasi--</option>
                            <option <?php if ($tipe_lokasi == 'Pusat') echo 'selected'; ?> value="Pusat">Pusat</option>
                            <option <?php if ($tipe_lokasi == 'Cabang') echo 'selected'; ?> value="Cabang">Cabang</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="mb-2" for="">Latitude</label><br>
                        <input type="text" class="form-control col-md-12" name="latitude" value="<?= htmlspecialchars($latitude) ?>">
                    </div>

                    <div class="mb-2">
                        <label class="mb-2" for="">Longitude</label><br>
                        <input type="text" class="form-control col-md-12" name="longitude" value="<?= htmlspecialchars($longitude) ?>">
                    </div>

                    <div class="mb-2">
                        <label class="mb-2" for="">Radius</label><br>
                        <input type="text" class="form-control col-md-12" name="radius" value="<?= htmlspecialchars($radius) ?>">
                    </div>

                    <div class="mb-2">
                        <label class="mb-2" for="">Zona Waktu</label><br>
                        <select name="zona_waktu" class="form-control">
                            <option value="">--Pilih Zona Waktu--</option>
                            <option <?php if ($zona_waktu == 'WIB') echo 'selected'; ?> value="WIB">WIB</option>
                            <option <?php if ($zona_waktu == 'WIT') echo 'selected'; ?> value="WIT">WIT</option>
                            <option <?php if ($zona_waktu == 'WITA') echo 'selected'; ?> value="WITA">WITA</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="mb-2" for="">Jam Masuk</label><br>
                        <input type="text" class="form-control col-md-12" name="jam_masuk" value="<?= htmlspecialchars($jam_masuk) ?>">
                    </div>

                    <div class="mb-2">
                        <label class="mb-2" for="">Jam Pulang</label><br>
                        <input type="text" class="form-control col-md-12" name="jam_pulang" value="<?= htmlspecialchars($jam_pulang) ?>">
                    </div>

                    <input type="hidden" value="<?= htmlspecialchars($id) ?>" name="id">
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('../layout/footer.php') ?>