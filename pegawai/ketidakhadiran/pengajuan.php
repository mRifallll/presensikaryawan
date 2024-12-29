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

$judul = 'Pengajuan Ketidak Hadiran';
include('../layout/header.php');
include_once('../../config.php');

if (isset($_POST['submit'])) {
    $id = $_POST['id_pegawai'];
    $keterangan = $_POST['keterangan'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];
    $status_pengajuan = 'PENDING';

    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        $nama_file = $file['name'];
        $file_tmp = $file['tmp_name'];
        $ukuran_file = $file['size'];
        $file_direktori = "../../assets/file_ketidakhadiran/" . $nama_file;

        $ambil_eks = pathinfo($nama_file, PATHINFO_EXTENSION);
        $ekstensi_diizinkan = ["jpg", "png", "jpg", "pdf"];
        $max_ukuranfile = 10 * 1024 * 1024;

        move_uploaded_file($file_tmp, $file_direktori);
    }
    $pesan_kesalahan = [];

    if (empty($keterangan)) {
        $pesan_kesalahan[] = "<i class ='fa-solid fa-check'></i>Keterangan wajib diisi";
    }
    if (empty($tanggal)) {
        $pesan_kesalahan[] = "<i class ='fa-solid fa-check'></i>Tanggal wajib diisi";
    }
    if (empty($deskripsi)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Deskripsi wajib diisi";
    }
    if (!in_array(strtolower($ambil_eks), $ekstensi_diizinkan)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Hanya File JPG, JPEG, dan PNG yang diperbolehkan";
    }
    if ($ukuran_file > $max_ukuranfile) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Ukuran File Melebihi 10 mb";
    }

    if (!empty($pesan_kesalahan)) {
        $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
    } else {
        $result = mysqli_query($con, "INSERT INTO ketidakhadiran(id_pegawai, keterangan, tanggal, deskripsi, status_pengajuan, file) VALUES ('$id', '$keterangan', '$tanggal', '$deskripsi', '$status_pengajuan', '$nama_file')");

        $_SESSION['berhasil'] = 'Data berhasil disimpan';
        header("Location: ketidakhadiran.php");
        exit;
    }
}

$id = $_SESSION['id'];
$result = mysqli_query($con, "SELECT * FROM ketidakhadiran WHERE id_pegawai = '$id' ORDER BY id DESC");
?>

<div class="page-body">
    <div class="container-xl">

        <div class="card col-md-6">
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" value="<?= $_SESSION['id'] ?>" name="id_pegawai">

                    <div class="col-mb-3">
                        <label for="">Keterangan</label>
                        <select name="keterangan" class="form-control">
                            <option value="">--Keterangan--</option>
                            <option <?php if (isset($_POST['keterangan']) && $_POST['keterangan'] == 'Cuti') echo 'selected'; ?> value="Cuti">Cuti</option>
                            <option <?php if (isset($_POST['keterangan']) && $_POST['keterangan'] == 'Izin') echo 'selected'; ?> value="Izin">Izin</option>
                            <option <?php if (isset($_POST['keterangan']) && $_POST['keterangan'] == 'Sakit') echo 'selected'; ?> value="Sakit">Sakit</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="">Tanggal</label>
                        <input type="date" class="form-control" name="tanggal">
                    </div>

                    <div class="mb-3">
                        <label for="">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" cols="30" rows="5"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="">Surat Keterangan</label>
                        <input type="file" class="form-control" name="file">
                    </div>

                    <button type="submit" class="btn btn-primary" name="submit">Ajukan</button>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include('../layout/footer.php'); ?>