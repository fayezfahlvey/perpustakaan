<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

$username = $_SESSION['admin_username'];

$pesan = "";
$tipe = "";

if (isset($_POST['ubah_password'])) {

    $password_lama = $_POST['password_lama'] ?? "";
    $password_baru = $_POST['password_baru'] ?? "";
    $konfirmasi = $_POST['konfirmasi'] ?? "";

    if ($password_lama === "" ||
        $password_baru === "" ||
        $konfirmasi === "") {

        $pesan = "Semua kolom password wajib diisi.";
        $tipe = "error";

    } elseif ($password_baru !== $konfirmasi) {

        $pesan = "Konfirmasi password tidak cocok.";
        $tipe = "error";

    } elseif (strlen($password_baru) < 6) {

        $pesan = "Password baru minimal 6 karakter.";
        $tipe = "error";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT password FROM admin WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $_SESSION['admin_id']
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $admin = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if (!$admin) {

            $pesan = "Data admin tidak ditemukan.";
            $tipe = "error";

        } else {

            $valid = password_verify(
                $password_lama,
                $admin['password']
            );

            if (!$valid &&
                $password_lama !== $admin['password']) {

                $pesan = "Password lama salah.";
                $tipe = "error";

            } else {

                $hash = password_hash(
                    $password_baru,
                    PASSWORD_DEFAULT
                );

                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE admin
                     SET password = ?
                     WHERE id = ?"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "si",
                    $hash,
                    $_SESSION['admin_id']
                );

                if (mysqli_stmt_execute($stmt)) {

                    $pesan = "Password berhasil diperbarui.";
                    $tipe = "success";

                } else {

                    $pesan = "Password gagal diperbarui.";
                    $tipe = "error";
                }

                mysqli_stmt_close($stmt);
            }
        }
    }
}

$q = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM books"
);

$total_buku = $q
    ? mysqli_fetch_assoc($q)['total']
    : 0;

$q = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM aktivitas"
);

$total_aktivitas = $q
    ? mysqli_fetch_assoc($q)['total']
    : 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>

<link rel="stylesheet" href="admin_style.css">

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width,initial-scale=1.0"
>

<title>
Pengaturan - Perpustakaan
</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    background: #f5f7f5;
    color: #182230;
}


/* =========================================================
   SIDEBAR
========================================================= */

.sidebar {
    position: fixed;
    left: 0;
    top: 0;

    width: 235px;
    height: 100vh;

    /* Gradasi putih / silver muda → gold */
    background:
        radial-gradient(
            circle at 85% 10%,
            rgba(216, 184, 106, 0.35) 0%,
            rgba(216, 184, 106, 0.12) 20%,
            transparent 42%
        ),
        linear-gradient(
            160deg,
            #ffffff 0%,
            #f5f5f5 42%,
            #eeeeee 65%,
            #D8B86A 100%
        );

    color: #111111;

    padding: 25px 15px;

    border-right: 1px solid rgba(201, 162, 39, 0.35);

    box-shadow:
        5px 0 25px rgba(0, 0, 0, 0.08);

    overflow: hidden;

    z-index: 1000;
}


/* =========================================================
   AKSESORIS BACKGROUND SIDEBAR
========================================================= */

.sidebar::before {
    content: "";

    position: absolute;

    width: 180px;
    height: 180px;

    top: -70px;
    right: -70px;

    background:
        radial-gradient(
            circle,
            rgba(201, 162, 39, 0.28) 0%,
            rgba(201, 162, 39, 0.12) 35%,
            transparent 70%
        );

    border-radius: 50%;

    pointer-events: none;
}


.sidebar::after {
    content: "";

    position: absolute;

    width: 140px;
    height: 140px;

    left: -75px;
    bottom: 40px;

    background:
        radial-gradient(
            circle,
            rgba(192, 192, 192, 0.35) 0%,
            rgba(192, 192, 192, 0.12) 40%,
            transparent 70%
        );

    border-radius: 50%;

    pointer-events: none;
}


/* =========================================================
   LOGO SIDEBAR
========================================================= */

.logo {
    display: flex;

    align-items: center;

    gap: 10px;

    padding: 0 10px;

    margin-bottom: 35px;

    position: relative;

    z-index: 2;
}


/* =========================================================
   LOGO KEJARI
========================================================= */

.logo-icon {

    width: 40px;
    height: 40px;

    background: #D8B86A;

    border-radius: 9px;

    display: flex;

    align-items: center;
    justify-content: center;

    overflow: hidden;

    flex-shrink: 0;

}


.logo-icon img {

    width: 30px;
    height: 30px;

    object-fit: contain;

    display: block;

}


/* =========================================================
   TEXT LOGO SIDEBAR
========================================================= */

.logo-text strong {

    display: block;

    font-size: 11px;

    letter-spacing: 1px;

    color: #111111;

    font-weight: bold;

}


.logo-text small {

    font-size: 8px;

    color: #555555;

}


/* =========================================================
   MENU TITLE
========================================================= */

.menu-title {

    color: #8A6A0A;

    font-size: 9px;

    font-weight: bold;

    letter-spacing: 1.5px;

    margin: 20px 10px 8px;

}


/* =========================================================
   MENU
========================================================= */

.menu {

    display: flex;

    flex-direction: column;

    gap: 4px;

}


.menu a {

    color: #222222;

    text-decoration: none;

    padding: 11px 12px;

    border-radius: 7px;

    font-size: 12px;

    font-weight: 500;

    display: flex;

    gap: 10px;

    align-items: center;

    transition: .2s;

}


/* =========================================================
   MENU HOVER / ACTIVE
========================================================= */

.menu a:hover,
.menu a.active {

    background: rgba(201, 162, 39, 0.16);

    color: #8A6A0A;

}


/* =========================================================
   MENU ICON
========================================================= */

.menu a i,
.menu a svg {

    color: #C9A227;

}


/* =========================================================
   LOGOUT
========================================================= */

.menu a.logout {

    color: #A33A3A;

}


.menu a.logout:hover {

    background: rgba(163, 58, 58, 0.10);

    color: #8B2E2E;

}

/* =========================
   MAIN
========================= */

.main {

    margin-left: 235px;

    min-height: 100vh;
}


/* =========================
   TOPBAR
========================= */

.topbar {

    height: 70px;

    background: white;

    border-bottom: 1px solid #e5e9e5;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 35px;
}

.topbar-title {

    color: #123c2c;

    font-size: 18px;

    font-weight: bold;
}

.admin-info {

    display: flex;

    align-items: center;

    gap: 10px;
}

.admin-avatar {

     width: 35px;
    height: 35px;

    border-radius: 50%;

    background: white;

    display: flex;

    align-items: center;
    justify-content: center;
}

.admin-name {

    font-size: 11px;

    color: #315c49;

    font-weight: bold;
}


/* =========================
   CONTENT
========================= */

.content {

    padding: 35px;
}

.page-title {

    margin-bottom: 25px;
}

.page-title small {

    color: #b08f4c;

    font-size: 9px;

    font-weight: bold;

    letter-spacing: 1.5px;
}

.page-title h1 {

    color: #123c2c;

    font-family: Georgia, serif;

    font-size: 30px;

    margin-top: 5px;
}

.page-title p {

    color: #888;

    font-size: 12px;

    margin-top: 6px;
}


/* =========================
   MESSAGE
========================= */

.message {

    padding: 12px 15px;

    border-radius: 8px;

    font-size: 11px;

    margin-bottom: 20px;
}

.success {

    background: #e8f5ed;

    color: #276047;
}

.error {

    background: #fbeaea;

    color: #a33a3a;
}


/* =========================
   SETTINGS
========================= */

.settings-grid {

    display: grid;

    grid-template-columns: repeat(2,1fr);

    gap: 20px;
}

.card {

    background: white;

    border: 1px solid #e5e9e5;

    border-radius: 11px;

    padding: 25px;
}

.card.full {

    grid-column: 1/-1;
}

.card-header {

    display: flex;

    align-items: center;

    gap: 12px;

    margin-bottom: 20px;
}

.card-icon {

    width: 40px;
    height: 40px;

    background: #edf3ee;

    border-radius: 9px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 18px;
}

.card-header h2 {

    color: #123c2c;

    font-family: Georgia, serif;

    font-size: 18px;
}

.card-header p {

    color: #999;

    font-size: 9px;

    margin-top: 3px;
}


/* =========================
   INFO
========================= */

.info-row {

    display: flex;

    justify-content: space-between;

    align-items: center;

    padding: 12px 0;

    border-bottom: 1px solid #edf0ed;
}

.info-row:last-child {

    border-bottom: none;
}

.info-label {

    color: #777;

    font-size: 10px;
}

.info-value {

    color: #123c2c;

    font-size: 11px;

    font-weight: bold;
}

.status {

    display: flex;

    align-items: center;

    gap: 8px;
}

.status-dot {

    width: 8px;
    height: 8px;

    border-radius: 50%;

    background: #3d9b62;
}


/* =========================
   FORM
========================= */

.form-group {

    margin-bottom: 15px;
}

.form-group label {

    display: block;

    color: #315c49;

    font-size: 10px;

    font-weight: bold;

    margin-bottom: 6px;
}

.form-group input {

    width: 100%;

    padding: 11px;

    border: 1px solid #dfe4df;

    border-radius: 7px;

    outline: none;

    font-size: 11px;
}

.form-group input:focus {

    border-color: #123c2c;
}


/* =========================
   BUTTON
========================= */

.button {

    padding: 11px 18px;

    border: none;

    border-radius: 7px;

    background: #C9A227;

    color: white;

    font-size: 11px;

    font-weight: bold;

    cursor: pointer;

    text-decoration: none;

    display: inline-block;
}

.button:hover {

    background: #C9A227;
}


/* =========================
   QUICK LINKS
========================= */

.quick-links {

    display: grid;

    grid-template-columns: repeat(3,1fr);

    gap: 12px;
}

.quick-link {

    border: 1px solid #e5e9e5;

    border-radius: 8px;

    padding: 15px;

    text-decoration: none;
}

.quick-link:hover {

    border-color: #123c2c;
}

.quick-link strong {

    display: block;

    color: #123c2c;

    font-size: 11px;

    margin-bottom: 5px;
}

.quick-link span {

    color: #999;

    font-size: 9px;
}


/* =========================
   LOGOUT
========================= */

.logout-box {

    border-color: #ead6d6;

    background: #fffafa;
}

.logout-box h2 {

    color: #9b4545;
}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:900px) {

    .sidebar {

        width: 70px;
    }

    .logo-text,
    .menu-title,
    .menu a span {

        display: none;
    }

    .main {

        margin-left: 70px;
    }

    .settings-grid {

        grid-template-columns: 1fr;
    }

    .card.full {

        grid-column: auto;
    }

}

@media(max-width:600px) {

    .sidebar {

        display: none;
    }

    .main {

        margin-left: 0;
    }

    .content {

        padding: 20px;
    }

    .quick-links {

        grid-template-columns: 1fr;
    }

}

</style>

</head>

<body>


<!-- =========================
     SIDEBAR
========================= -->

<aside class="sidebar">

    <div class="logo">

        <div class="logo-icon">

            <img
                src="assets/logo-kejari-cimahi.png"
                alt="Logo Kejaksaan Negeri Kota Cimahi"
            >

        </div>

        <div class="logo-text">

            <strong>
                PERPUSTAKAAN
            </strong>

            <small>
                KEJAKSAAN KOTA CIMAHI
            </small>

        </div>

    </div>


    <div class="menu-title">
        UTAMA
    </div>


    <nav class="menu">

        <a href="dashboard.php">
            📊
            <span>Dashboard</span>
        </a>

        <a href="kelola_buku.php">
            📚
            <span>Koleksi Buku</span>
        </a>

        <a href="tambah_buku.php">
            ➕
            <span>Tambah Buku</span>
        </a>


        <div class="menu-title">
            MONITORING
        </div>


        <a href="pengunjung.php">
            👥
            <span>Pengunjung</span>
        </a>

        <a href="aktivitas.php">
            📋
            <span>Aktivitas</span>
        </a>


        <div class="menu-title">
            SISTEM
        </div>


        <a href="pengaturan.php" class="active">
            ⚙️
            <span>Pengaturan</span>
        </a>


        <a
            href="index.php"
            class="logout"
        >
            🚪
            <span>Keluar</span>
        </a>

    </nav>

</aside>


<!-- =========================
     MAIN
========================= -->

<main class="main">


<header class="topbar">

    <div class="topbar-title">
        Pengaturan
    </div>


    <div class="admin-info">

        <div class="admin-avatar">
            👤
        </div>

        <div class="admin-name">

            <?php
            echo htmlspecialchars($username);
            ?>

        </div>

    </div>

</header>


<div class="content">


<div class="page-title">

    <small>
        SISTEM ADMINISTRATOR
    </small>

    <h1>
        Pengaturan
    </h1>

    <p>
        Kelola akun administrator dan sistem perpustakaan.
    </p>

</div>


<?php if($pesan !== ""): ?>

<div class="message <?php echo $tipe; ?>">

    <?php
    echo htmlspecialchars($pesan);
    ?>

</div>

<?php endif; ?>


<div class="settings-grid">


<!-- PROFIL -->

<section class="card">

    <div class="card-header">

        <div class="card-icon">
            👤
        </div>

        <div>

            <h2>
                Profil Administrator
            </h2>

            <p>
                Informasi akun administrator.
            </p>

        </div>

    </div>


    <div class="info-row">

        <span class="info-label">
            Username
        </span>

        <span class="info-value">

            <?php
            echo htmlspecialchars($username);
            ?>

        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Status
        </span>

        <span class="info-value status">

            <span class="status-dot"></span>

            Aktif

        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Hak Akses
        </span>

        <span class="info-value">
            Administrator
        </span>

    </div>

</section>


<!-- KEAMANAN -->

<section class="card">

    <div class="card-header">

        <div class="card-icon">
            🔐
        </div>

        <div>

            <h2>
                Keamanan Akun
            </h2>

            <p>
                Ganti password administrator.
            </p>

        </div>

    </div>


    <form method="POST">


        <div class="form-group">

            <label>
                PASSWORD LAMA
            </label>

            <input
                type="password"
                name="password_lama"
                placeholder="Masukkan password lama"
                required
            >

        </div>


        <div class="form-group">

            <label>
                PASSWORD BARU
            </label>

            <input
                type="password"
                name="password_baru"
                placeholder="Minimal 6 karakter"
                required
            >

        </div>


        <div class="form-group">

            <label>
                KONFIRMASI PASSWORD
            </label>

            <input
                type="password"
                name="konfirmasi"
                placeholder="Ulangi password baru"
                required
            >

        </div>


        <button
            type="submit"
            name="ubah_password"
            class="button"
        >
            🔐 Ubah Password
        </button>


    </form>

</section>


<!-- INFORMASI PERPUSTAKAAN -->

<section class="card">

    <div class="card-header">

        <div class="card-icon">
            📚
        </div>

        <div>

            <h2>
                Informasi Perpustakaan
            </h2>

            <p>
                Informasi dasar sistem.
            </p>

        </div>

    </div>


    <div class="info-row">

        <span class="info-label">
            Nama
        </span>

        <span class="info-value">
            Perpustakaan
        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Instansi
        </span>

        <span class="info-value">
            Kejaksaan Negeri Kota Cimahi
        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Sistem
        </span>

        <span class="info-value">
            Digitalisasi Perpustakaan
        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Tahun
        </span>

        <span class="info-value">
            2026
        </span>

    </div>

</section>


<!-- STATUS SISTEM -->

<section class="card">

    <div class="card-header">

        <div class="card-icon">
            🖥️
        </div>

        <div>

            <h2>
                Status Sistem
            </h2>

            <p>
                Kondisi sistem saat ini.
            </p>

        </div>

    </div>


    <div class="info-row">

        <span class="info-label">
            Database
        </span>

        <span class="info-value status">

            <span class="status-dot"></span>

            Terhubung

        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Total Buku
        </span>

        <span class="info-value">

            <?php
            echo $total_buku;
            ?>
            Koleksi

        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Aktivitas
        </span>

        <span class="info-value">

            <?php
            echo $total_aktivitas;
            ?>

        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Website
        </span>

        <span class="info-value status">

            <span class="status-dot"></span>

            Online

        </span>

    </div>

</section>


<!-- AKSES CEPAT -->

<section class="card full">

    <div class="card-header">

        <div class="card-icon">
            ⚡
        </div>

        <div>

            <h2>
                Akses Cepat
            </h2>

            <p>
                Menu pengelolaan sistem.
            </p>

        </div>

    </div>


    <div class="quick-links">


        <a
            href="tambah_buku.php"
            class="quick-link"
        >

            <strong>
                ➕ Tambah Buku
            </strong>

            <span>
                Tambahkan koleksi baru.
            </span>

        </a>


        <a
            href="kelola_buku.php"
            class="quick-link"
        >

            <strong>
                📚 Kelola Buku
            </strong>

            <span>
                Edit atau hapus koleksi.
            </span>

        </a>


        <a
            href="aktivitas.php"
            class="quick-link"
        >

            <strong>
                📋 Aktivitas
            </strong>

            <span>
                Lihat riwayat aktivitas.
            </span>

        </a>


    </div>

</section>


<!-- INFORMASI WEBSITE -->

<section class="card full">

    <div class="card-header">

        <div class="card-icon">
            🌐
        </div>

        <div>

            <h2>
                Informasi Website
            </h2>

            <p>
                Informasi sistem digitalisasi perpustakaan.
            </p>

        </div>

    </div>


    <div class="info-row">

        <span class="info-label">
            Platform
        </span>

        <span class="info-value">
            Sistem Informasi Perpustakaan
        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Pengelola
        </span>

        <span class="info-value">
            Administrator Perpustakaan
        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Versi
        </span>

        <span class="info-value">
            1.0
        </span>

    </div>


    <div class="info-row">

        <span class="info-label">
            Tahun Pengembangan
        </span>

        <span class="info-value">
            2026
        </span>

    </div>

</section>


<!-- LOGOUT -->

<section class="card full logout-box">

    <div class="card-header">

        <div class="card-icon">
            🚪
        </div>

        <div>

            <h2>
                Keluar dari Sistem
            </h2>

            <p>
                Kembali ke halaman utama perpustakaan.
            </p>

        </div>

    </div>


    <a
        href="index.php"
        class="button"
        onclick="return confirm('Apakah Anda yakin ingin keluar dari halaman administrator?');"
    >
        🚪 Kembali ke Halaman Utama
    </a>

</section>


</div>

</div>

</main>

</body>
</html>