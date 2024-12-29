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
$judul = "Tambah Pegawai";
include('../layout/header.php');
require_once('../../config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $new_nip = mysqli_query($con, "SELECT nip FROM pegawai ORDER BY nip DESC LIMIT 1");

    if (mysqli_num_rows($new_nip) > 0) {
        $row = mysqli_fetch_assoc($new_nip);
        $nip_db = $row['nip'];
        $nip_db = explode("-", $nip_db);
        $no_baru = (int)$nip_db[1] + 1;
        $nip_baru = "SA-" . str_pad($no_baru, 3, 0, STR_PAD_LEFT);
    } else {
        $nip_baru = "SA-001";
    }

    $nip = $nip_baru;
    $nama = htmlspecialchars($_POST['nama']);
    $jenis_kelamin = htmlspecialchars($_POST['jenis_kelamin']);
    $alamat = htmlspecialchars($_POST['alamat']);
    $no_hp = htmlspecialchars($_POST['no_hp']);
    $jabatan = htmlspecialchars($_POST['jabatan']);
    $username = htmlspecialchars($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = htmlspecialchars($_POST['role']);
    $status = htmlspecialchars($_POST['status']);
    $lokasi_presensi = htmlspecialchars($_POST['lokasi_presensi']);

    if (isset($_FILES['foto'])) {
        $file = $_FILES['foto'];
        $nama_file = $file['name'];
        $file_tmp = $file['tmp_name'];
        $ukuran_file = $file['size'];
        $file_direktori = "../../assets/img/foto_pegawai/" . $nama_file;

        $ambil_eks = pathinfo($nama_file, PATHINFO_EXTENSION);
        $ekstensi_diizinkan = ["jpg", "png", "jpg", "jpeg"];
        $max_ukuranfile = 10 * 1024 * 1024;

        move_uploaded_file($file_tmp, $file_direktori);
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
    if (!in_array(strtolower($ambil_eks), $ekstensi_diizinkan)) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Hanya File JPG, JPEG, dan PNG yang diperbolehkan";
    }
    if ($ukuran_file > $max_ukuranfile) {
        $pesan_kesalahan[] = "<i class='fa-solid fa-check'></i> Ukuran File Melebihi 10 mb";
    }


    if (!empty($pesan_kesalahan)) {
        $_SESSION['validasi'] = implode("<br>", $pesan_kesalahan);
    } else {
        $pegawai = mysqli_query($con, "INSERT INTO pegawai(nip, nama, jenis_kelamin, alamat, no_hp, jabatan, lokasi_presensi, foto) VALUES ('$nip', '$nama', '$jenis_kelamin', '$alamat', '$no_hp', '$jabatan', '$lokasi_presensi', '$nama_file')");

        $id_pegawai = mysqli_insert_id($con);
        $user = mysqli_query($con, "INSERT INTO users(id_pegawai, username, password, status, role) VALUES ('$id_pegawai', '$username', '$password', '$status', '$role')");

        $_SESSION['berhasil'] = 'Data berhasil disimpan';
        header("Location: pegawai.php");
        exit;
    }
}
?>

<div class="page-body">
    <div class="container-xl">

        <form action="<?= base_url('admin/data_pegawai/tambah.php') ?>" method="POST" enctype="multipart/form-data">
            <div class="row">

                <div class="col-md-6">
                    <div class="card">
                        <div class="card">
                            <div class="card-body">

                                <div class="mb-3">
                                    <label for="">Nama </label>
                                    <input type="text" class="form-control" name="nama" value="<?php if (isset($_POST['nama'])) echo $_POST['nama'] ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-control">
                                        <option value="">--Pilih Jenis Kelamin--</option>
                                        <option <?php if (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Laki - laki') echo 'selected'; ?> value="Laki - laki">Laki - laki</option>
                                        <option <?php if (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] == 'Perempuan') echo 'selected'; ?> value="Perempuan">Perempuan</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="">Alamat</label>
                                    <input type="text" class="form-control" name="alamat" value="<?php if (isset($_POST['alamat'])) echo $_POST['alamat'] ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">No. Handphone</label>
                                    <input type="text" class="form-control" name="no_hp" value="<?php if (isset($_POST['no_hp'])) echo $_POST['no_hp'] ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="">Jabatan</label>
                                    <select name="jabatan" class="form-control">
                                        <option value="">--Pilih Jabatan--</option>

                                        <?php
                                        $ambil_jabatan = mysqli_query($con, "SELECT * FROM jabatan ORDER BY jabatan ASC");

                                        while ($jabatan = mysqli_fetch_assoc($ambil_jabatan)) {
                                            $nama_jabatan = $jabatan['jabatan'];
                                            if (isset($_POST['jabatan']) && $_POST['jabatan'] == $nama_jabatan) {
                                                echo '<option value = "' . $nama_jabatan . '"
                                    selected = "selected">' . $nama_jabatan . '</option>';
                                            } else {
                                                echo '<option value = "' . $nama_jabatan . '">' . $nama_jabatan . '</opton>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="">Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">--Pilih Status--</option>
                                        <option <?php if (isset($_POST['status']) && $_POST['status'] == 'Aktif') echo 'selected'; ?> value="aktif">Aktif</option>
                                        <option <?php if (isset($_POST['status']) && $_POST['status'] == 'Tidak Aktif ') echo 'selected'; ?> value="tidak_aktif ">Tidak Aktif </option>
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
                                <input type="text" class="form-control" name="username" value="<?php if (isset($_POST['username'])) echo $_POST['username'] ?>">
                            </div>
                            <div class="mb-3">
                                <label for="">Password </label>
                                <input type="password" class="form-control" name="password" value="">
                            </div>
                            <div class="mb-3">
                                <label for="">Ulangi Password </label>
                                <input type="password" class="form-control" name="ulangi_password" value="">
                            </div>
                            <div class="mb-3">
                                <label for="">Role</label>
                                <select name="role" class="form-control">
                                    <option value="">--Pilih Role--</option>
                                    <option <?php if (isset($_POST['role']) && $_POST['role'] == 'admin') echo 'selected'; ?> value="admin">Admin</option>
                                    <option <?php if (isset($_POST['role']) && $_POST['role'] == 'pegawai') echo 'selected'; ?> value="pegawai">Pegawai </option>
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
                                        if (isset($_POST['lokasi_presensi']) && $_POST['lokasi_presensi'] == $nama_lokasi) {
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
                                <input type="file" class="form-control" name="foto" value="">
                            </div>
                            <button type="submit" class="btn btn-primary" name="submit">Simpan</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</div>
</div>
</div>

<?php include('../layout/footer.php') ?>