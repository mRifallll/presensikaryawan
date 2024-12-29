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
$judul = "Edit Data Pegawai";
include('../layout/header.php');
require_once('../../config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = htmlspecialchars($_POST['nama']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $no_hp = htmlspecialchars($_POST['no_hp']);
    $jabatan = htmlspecialchars($_POST['jabatan']);
    $username = htmlspecialchars($_POST['username']);
    $role = htmlspecialchars($_POST['role']);
    $status = htmlspecialchars($_POST['status']);
    $lokasi_presensi = htmlspecialchars($_POST['lokasi_presensi']);

    if (empty($_POST['password'])) {
        $password = $_POST['password_lama'];
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    if ($_FILES['fotobaru']['error'] === 4) {
        $nama_file = $_POST['fotolama'];
    } else {
        if (isset($_FILES['fotobaru'])) {
            $file = $_FILES['fotobaru'];
            $nama_file = $file['name'];
            $file_tmp = $file['tmp_name'];
            $ukuran_file = $file['size'];
            $file_direktori = "../../assets/img/foto_pegawai/" . $nama_file;

            $ambil_eks = pathinfo($nama_file, PATHINFO_EXTENSION);
            $ekstensi_diizinkan = ["jpg", "png", "jpg"];
            $max_ukuranfile = 10 * 1024 * 1024;

            move_uploaded_file($file_tmp, $file_direktori);
        }
    }

    $pesan_kesalahan = [];

    if (empty($nama)) {
        $pesan_kesalahan[] = "<i class ='fa-solid fa-check'></i>Nama wajib diisi";
    }
    if (empty($jenis_kelamin)) {
        $pesan_kesalahan[] = "<i class ='fa-solid fa-check'></i>Jenis Kelamin wajib diisi";
    }
    if (empty($alamat)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Alamat wajib diisi";
    }
    if (empty($no_hp)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> No. Handphone wajib diisi";
    }
    if (empty($jabatan)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Jabatan wajib diisi";
    }
    if (empty($username)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Username wajib diisi";
    }
    if (empty($role)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Role wajib diisi";
    }
    if (empty($status)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Status wajib diisi";
    }
    if (empty($lokasi_presensi)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Lokasi Presensi wajib diisi";
    }
    if (empty($password)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Password wajib diisi";
    }
    if ($_POST['password'] != $_POST['ulangi_password']) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Password Tidak Cocok";
    }

    if ($_FILES['fotobaru']['error'] !== 4) {
        if (!in_array(strtolower($ambil_eks), $ekstensi_diizinkan)) {
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Hanya File JPG, JPEG, dan PNG yang diperbolehkan";
        }
        if ($ukuran_file > $max_ukuranfile) {
            $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Ukuran File Melebihi 10 mb";
        }
    }
    if (!empty($pesan_kesalahan)) {
        $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
    } else {
        $pegawai = mysqli_query($con, "UPDATE pegawai SET 
        nama = '$nama',
        jenis_kelamin = '$jenis_kelamin',
        alamat = '$alamat',
        no_hp = '$no_hp',
        jabatan = '$jabatan',
        lokasi_presensi = '$lokasi_presensi',
        foto = '$nama_file' 
    WHERE  id = $id");

        // $id_pegawai = mysqli_insert_id($con);
        $user = mysqli_query($con, "UPDATE users SET 
        username = '$username',
        password = '$password',
        status = '$status',
        role = '$role'
    WHERE id = $id");

        $_SESSION['berhasil'] = 'Data berhasil Diupdate';
        header("Location: pegawai.php");
        exit;
    }
}
$id = isset($_GET['id']) ? $_GET['id']  : $_POST['id'];
$result = mysqli_query($con, "SELECT users.id_pegawai, users.username, users.password, users.status, users.role, pegawai. * FROM users JOIN pegawai ON users.id_pegawai = pegawai.id WHERE pegawai.id = $id ");

while ($pegawai =  mysqli_fetch_array($result)) {
    $nama = $pegawai['nama'];
    $jenis_kelamin = $pegawai['jenis_kelamin'];
    $alamat = $pegawai['alamat'];
    $no_hp = $pegawai['no_hp'];
    $jabatan = $pegawai['jabatan'];
    $username = $pegawai['username'];
    $password = $pegawai['password'];
    $status = $pegawai['status'];
    $lokasi_presensi = $pegawai['lokasi_presensi'];
    $role = $pegawai['role'];
    $foto = $pegawai['foto'];
}
?>

<div class="page-body">
    <div class="container-xl">

        <form action="<?= base_url('admin/data_pegawai/edit.php') ?>" method="POST" enctype="multipart/form-data">
            <div class="row">

                <div class="col-md-6">
                    <div class="card">
                        <div class="card">
                            <div class="card-body">

                                <div class="mb-3">
                                    <label for="">Nama </label>
                                    <input type="text" class="form-control" name="nama" value="<?= $nama ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-control">
                                        <option value="">--Pilih Jenis Kelamin--</option>
                                        <option <?php if ($jenis_kelamin == 'Laki - laki') echo 'selected'; ?> value="Laki - laki">Laki - laki</option>
                                        <option <?php if ($jenis_kelamin == 'Perempuan') echo 'selected'; ?> value="Perempuan">Perempuan</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="">Alamat</label>
                                    <input type="text" class="form-control" name="alamat" value="<?= $alamat ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">No. Handphone</label>
                                    <input type="text" class="form-control" name="no_hp" value="<?= $no_hp ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Jabatan</label>
                                    <select name="jabatan" class="form-control">
                                        <option value="">--Pilih Jabatan--</option>

                                        <?php
                                        $ambil_jabatan = mysqli_query($con, "SELECT * FROM jabatan ORDER BY jabatan ASC");

                                        while ($row = mysqli_fetch_assoc($ambil_jabatan)) {
                                            $nama_jabatan = $row['jabatan'];
                                            if ($jabatan == $nama_jabatan) {
                                                echo '<option value = "' . $nama_jabatan . '"
                                    selected = "selected">' . $nama_jabatan . '</option>';
                                            } else {
                                                echo '<option value = ""' . $nama_jabatan . '">' . $nama_jabatan . '</opton>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">--Pilih Status--</option>
                                        <option <?php if ($status == 'aktif') echo 'selected'; ?> value="aktif">Aktif</option>
                                        <option <?php if ($status == 'tidak_aktif') echo 'selected'; ?> value="tidak_aktif">Tidak - aktif</option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="">Username </label>
                                <input type="text" class="form-control" name="username" value="<?= $username ?>">
                            </div>
                            <div class="mb-3">
                                <label for="">Password </label>
                                <input type="hidden" value="<?= $password ?>" name="password_lama">
                                <input type="password" class="form-control" name="password">
                            </div>
                            <div class="mb-3">
                                <label for="">Ulangi Password </label>
                                <input type="password" class="form-control" name="ulangi_password" value="">
                            </div>
                            <div class="mb-3">
                                <label for="">Role</label>
                                <select name="role" class="form-control">
                                    <option value="">--Pilih Role--</option>
                                    <option <?php if ($role == 'admin') echo 'selected'; ?> value="admin">Admin</option>
                                    <option <?php if ($role == 'pegawai') echo 'selected'; ?> value="pegawai">Pegawai </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="">Lokasi Presensi</label>
                                <select name="lokasi_presensi" class="form-control">
                                    <option value="">--Pilih Lokasi Presensi--</option>
                                    <?php
                                    $ambil_lp = mysqli_query($con, "SELECT * FROM lokasi_presensi ORDER BY nama_lokasi ASC");
                                    while ($lokasi = mysqli_fetch_assoc($ambil_lp)) {
                                        $nama_lokasi = $lokasi['nama_lokasi'];
                                        if ($lokasi_presensi == $nama_lokasi) {
                                            echo '<option value = "' . $nama_lokasi . '"
                                    selected = "selected">' . $nama_lokasi . '</option>';
                                        } else {
                                            echo '<option value = "' . $nama_lokasi . '">' . $nama_lokasi . '</opton>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="">Foto </label>
                                <input type="hidden" value="<?= $foto ?>" name="fotolama">
                                <input type="file" class="form-control" name="fotobaru">
                            </div>

                            <input type="hidden" value="<?= $id ?>" name="id">

                            <button type="submit" class="btn btn-primary" name="edit">Update</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
</div>
</div>

<?php include('../layout/footer.php') ?>