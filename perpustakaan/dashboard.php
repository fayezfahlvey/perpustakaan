<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$username = $_SESSION['admin_username'];

$total_buku = 0;
$total_aktivitas = 0;

/* =========================================================
   TOTAL BUKU
========================================================= */

$q = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM books"
);

if ($q) {
    $total_buku = mysqli_fetch_assoc($q)['total'];
}


/* =========================================================
   TOTAL AKTIVITAS
========================================================= */

$q = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM aktivitas"
);

if ($q) {
    $total_aktivitas = mysqli_fetch_assoc($q)['total'];
}


/* =========================================================
   AKTIVITAS TERBARU
   Menampilkan 5 aktivitas terakhir
========================================================= */

$aktivitas_terbaru = mysqli_query(
    $conn,
    "SELECT *
     FROM aktivitas
     ORDER BY waktu DESC
     LIMIT 5"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<link rel="stylesheet" href="admin_style.css">

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Dashboard Admin - Perpustakaan
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


/* =========================================================
   MAIN
========================================================= */

.main {

    margin-left: 235px;

    min-height: 100vh;

}


/* =========================================================
   TOPBAR
========================================================= */

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

    color: #111111;

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

    color: #333333;

    font-weight: bold;

}


/* =========================================================
   CONTENT
========================================================= */

.content {

    padding: 35px;

}


.welcome {

    margin-bottom: 28px;

}


.welcome small {

    color: #B08A20;

    font-size: 9px;

    font-weight: bold;

    letter-spacing: 1.5px;

}


.welcome h1 {

    color: #111111;

    font-family: Georgia, serif;

    font-size: 30px;

    margin-top: 5px;

}


.welcome p {

    color: #666666;

    font-size: 12px;

    margin-top: 5px;

}


/* =========================================================
   STATISTICS
========================================================= */

.stats {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 18px;

    margin-bottom: 25px;

}


.stat {

    background: white;

    border: 1px solid #e6eae6;

    border-radius: 11px;

    padding: 22px;

}


.stat-icon {

    width: 40px;
    height: 40px;

    background: #FFF8DD;

    border-radius: 9px;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 18px;

    margin-bottom: 15px;

}


.stat strong {

    display: block;

    color: #111111;

    font-size: 25px;

}


.stat span {

    color: #666666;

    font-size: 10px;

}


/* =========================================================
   SECTION AKTIVITAS
========================================================= */

.section {

    background: white;

    border: 1px solid #e5e9e5;

    border-radius: 11px;

    padding: 25px;

}


.section-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 18px;

}


.section-header h2 {

    color: #111111;

    font-family: Georgia, serif;

    font-size: 20px;

}


.section-header a {

    color: #B08A20;

    font-size: 10px;

    text-decoration: none;

    font-weight: bold;

}


.section-header a:hover {

    text-decoration: underline;

}


/* =========================================================
   ACTIVITY LIST
========================================================= */

.activity-list {

    display: flex;

    flex-direction: column;

}


.activity-item {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    padding: 14px 5px;

    border-bottom: 1px solid #edf0ed;

}


.activity-item:last-child {

    border-bottom: none;

}


.activity-left {

    display: flex;

    align-items: center;

    gap: 12px;

    min-width: 0;

}


.activity-icon {

    width: 36px;
    height: 36px;

    flex-shrink: 0;

    background: #FFF8DD;

    border-radius: 8px;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 15px;

}


.activity-info {

    min-width: 0;

}


.activity-name {

    color: #222222;

    font-size: 11px;

    font-weight: bold;

    margin-bottom: 4px;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

}


.activity-meta {

    color: #777777;

    font-size: 9px;

}


.activity-right {

    display: flex;

    align-items: center;

    gap: 15px;

    flex-shrink: 0;

}


.activity-time {

    color: #777777;

    font-size: 9px;

}


.activity-status {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    background: #FFF8DD;

    color: #8A6A0A;

    padding: 5px 8px;

    border-radius: 5px;

    font-size: 8px;

    font-weight: bold;

}


.activity-status-dot {

    width: 5px;
    height: 5px;

    border-radius: 50%;

    background: #C9A227;

}


/* =========================================================
   EMPTY ACTIVITY
========================================================= */

.empty {

    text-align: center;

    padding: 35px 20px;

    color: #777777;

    font-size: 11px;

}


.empty-icon {

    width: 45px;
    height: 45px;

    margin: 0 auto 10px;

    background: #FFF8DD;

    border-radius: 50%;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 18px;

}


/* =========================================================
   QUICK MENU
========================================================= */

.quick-menu {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 15px;

    margin-top: 25px;

}


.quick {

    border: 1px solid #e5e9e5;

    border-radius: 9px;

    padding: 18px;

    text-decoration: none;

    color: inherit;

}


.quick:hover {

    border-color: #C9A227;

}


.quick strong {

    display: block;

    color: #111111;

    font-size: 12px;

    margin-bottom: 5px;

}


.quick span {

    color: #777777;

    font-size: 9px;

}


/* =========================================================
   RESPONSIVE
========================================================= */

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


    .stats {

        grid-template-columns:
            repeat(2, 1fr);

    }


    .activity-right {

        flex-direction: column;

        align-items: flex-end;

        gap: 5px;

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


    .stats,
    .quick-menu {

        grid-template-columns: 1fr;

    }


    .activity-item {

        align-items: flex-start;

    }


    .activity-right {

        display: none;

    }


    .activity-name {

        white-space: normal;

    }

}

</style>

</head>


<body>


<!-- =========================================================
     SIDEBAR
========================================================= -->

<aside class="sidebar">


    <div class="logo">


        <!-- LOGO KEJARI -->

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


        <a
            href="dashboard.php"
            class="active"
        >
            📊
            <span>
                Dashboard
            </span>
        </a>


        <a
            href="kelola_buku.php"
        >
            📚
            <span>
                Koleksi Buku
            </span>
        </a>


        <a
            href="tambah_buku.php"
        >
            ➕
            <span>
                Tambah Buku
            </span>
        </a>


        <div class="menu-title">
            MONITORING
        </div>


        <a
            href="pengunjung.php"
        >
            👥
            <span>
                Pengunjung
            </span>
        </a>


        <a
            href="aktivitas.php"
        >
            📋
            <span>
                Aktivitas
            </span>
        </a>


        <div class="menu-title">
            SISTEM
        </div>


        <a
            href="pengaturan.php"
        >
            ⚙️
            <span>
                Pengaturan
            </span>
        </a>


        <a
            href="index.php"
            class="logout"
            onclick="return confirm('Kembali ke halaman utama?');"
        >
            🚪
            <span>
                Keluar
            </span>
        </a>


    </nav>


</aside>


<!-- =========================================================
     MAIN
========================================================= -->

<main class="main">


    <header class="topbar">


        <div class="topbar-title">
            Dashboard
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


        <!-- WELCOME -->

        <div class="welcome">


            <small>
                ADMINISTRATOR
            </small>


            <h1>

                Selamat Datang,
                <?php
                echo htmlspecialchars($username);
                ?>

            </h1>


            <p>
                Kelola dan pantau sistem digitalisasi perpustakaan.
            </p>


        </div>


        <!-- STATISTICS -->

        <div class="stats">


            <div class="stat">


                <div class="stat-icon">
                    📚
                </div>


                <strong>
                    <?php
                    echo $total_buku;
                    ?>
                </strong>


                <span>
                    Total Koleksi Buku
                </span>


            </div>


            <div class="stat">


                <div class="stat-icon">
                    👥
                </div>


                <strong>
                    <?php
                    echo $total_aktivitas;
                    ?>
                </strong>


                <span>
                    Total Aktivitas
                </span>


            </div>


            <div class="stat">


                <div class="stat-icon">
                    🖥️
                </div>


                <strong>
                    Online
                </strong>


                <span>
                    Status Sistem
                </span>


            </div>


        </div>


        <!-- =====================================================
             AKTIVITAS SISTEM
        ====================================================== -->

        <section class="section">


            <div class="section-header">


                <h2>
                    Aktivitas Sistem
                </h2>


                <a href="aktivitas.php">
                    Lihat Semua →
                </a>


            </div>


            <?php if ($aktivitas_terbaru && mysqli_num_rows($aktivitas_terbaru) > 0): ?>


                <div class="activity-list">


                    <?php while ($row = mysqli_fetch_assoc($aktivitas_terbaru)): ?>


                        <div class="activity-item">


                            <div class="activity-left">


                                <div class="activity-icon">
                                    👤
                                </div>


                                <div class="activity-info">


                                    <div class="activity-name">

                                        <?php
                                        echo htmlspecialchars(
                                            $row['aktivitas'] ?? '-'
                                        );
                                        ?>

                                    </div>


                                    <div class="activity-meta">

                                        IP Address:
                                        <?php
                                        echo htmlspecialchars(
                                            $row['ip_address'] ?? '-'
                                        );
                                        ?>

                                    </div>


                                </div>


                            </div>


                            <div class="activity-right">


                                <div class="activity-time">

                                    <?php
                                    echo htmlspecialchars(
                                        $row['waktu'] ?? '-'
                                    );
                                    ?>

                                </div>


                                <div class="activity-status">

                                    <span class="activity-status-dot"></span>

                                    Tercatat

                                </div>


                            </div>


                        </div>


                    <?php endwhile; ?>


                </div>


            <?php else: ?>


                <div class="empty">


                    <div class="empty-icon">
                        📋
                    </div>


                    Belum ada aktivitas pengunjung yang tercatat.


                </div>


            <?php endif; ?>


        </section>


        <!-- =====================================================
             QUICK MENU
        ====================================================== -->

        <div class="quick-menu">


            <a
                href="tambah_buku.php"
                class="quick"
            >

                <strong>
                    ➕ Tambah Buku
                </strong>

                <span>
                    Daftarkan koleksi baru.
                </span>

            </a>


            <a
                href="kelola_buku.php"
                class="quick"
            >

                <strong>
                    📚 Kelola Koleksi
                </strong>

                <span>
                    Edit atau hapus data buku.
                </span>

            </a>


            <a
                href="pengunjung.php"
                class="quick"
            >

                <strong>
                    👥 Pengunjung
                </strong>

                <span>
                    Lihat statistik pengunjung.
                </span>

            </a>


        </div>


    </div>


</main>


</body>

</html>