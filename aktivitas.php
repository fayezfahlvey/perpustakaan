<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

$username = $_SESSION['admin_username'];

$query = mysqli_query(
    $conn,
    "SELECT *
     FROM aktivitas
     ORDER BY waktu DESC
     LIMIT 200"
);

$jumlah_data = $query ? mysqli_num_rows($query) : 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>

<link rel="stylesheet" href="admin_style.css">

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1.0">

<title>Aktivitas - Perpustakaan</title>


<style>

/* =========================================================
   RESET
========================================================= */

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
   AKSESORIS SIDEBAR
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


.menu a:hover,
.menu a.active {
    background: rgba(201, 162, 39, 0.16);

    color: #8A6A0A;
}


.menu a i,
.menu a svg {
    color: #C9A227;
}


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


/* =========================================================
   CONTENT
========================================================= */

.content {
    padding: 35px;
}


/* =========================================================
   PAGE TITLE
========================================================= */

.page-title {
    margin-bottom: 25px;
}


.page-title small {
    color: #B08A20;

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
    color: #888888;

    font-size: 12px;

    margin-top: 6px;
}


/* =========================================================
   LOG AKTIVITAS HEADER
========================================================= */

.activity-header {
    background: white;

    border: 1px solid #e1e6e1;

    border-radius: 14px;

    padding: 23px 25px;

    margin-bottom: 18px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    box-shadow:
        0 4px 15px rgba(25, 45, 35, 0.04);
}


.activity-header-left {
    display: flex;

    align-items: center;

    gap: 16px;

    min-width: 0;
}


.activity-header-icon {
    width: 52px;
    height: 52px;

    flex-shrink: 0;

    border-radius: 12px;

    background: #edf3ee;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 22px;
}


.activity-header-text {
    min-width: 0;
}


.activity-header-text h2 {
    color: #123c2c;

    font-size: 16px;

    margin-bottom: 5px;
}


.activity-header-text p {
    color: #888888;

    font-size: 10px;
}


.activity-count {
    flex-shrink: 0;

    padding: 9px 14px;

    border-radius: 8px;

    background: #fff8dd;

    border: 1px solid #ead9a5;

    color: #8A6A0A;

    font-size: 9px;

    font-weight: bold;

    letter-spacing: .5px;
}


/* =========================================================
   TABLE CARD
========================================================= */

.card {
    background: white;

    border: 1px solid #e1e6e1;

    border-radius: 14px;

    padding: 25px;

    overflow-x: auto;

    box-shadow:
        0 4px 15px rgba(25, 45, 35, 0.04);
}


/* =========================================================
   TABLE
========================================================= */

table {
    width: 100%;

    border-collapse: separate;

    border-spacing: 0;

    min-width: 700px;
}


thead th {
    background: #f5f7f5;

    color: #315c49;

    text-align: left;

    font-size: 10px;

    font-weight: bold;

    padding: 15px 16px;

    border-bottom: 2px solid #C9A227;
}


thead th:first-child {
    border-radius: 8px 0 0 0;
}


thead th:last-child {
    border-radius: 0 8px 0 0;
}


tbody td {
    padding: 14px 16px;

    border-bottom: 1px solid #edf0ed;

    font-size: 10px;

    color: #444444;

    vertical-align: middle;
}


tbody tr {
    transition: background .2s ease;
}


tbody tr:hover {
    background: #fcfdfb;
}


tbody tr:last-child td {
    border-bottom: none;
}


/* =========================================================
   NOMOR
========================================================= */

.number-badge {
    width: 27px;
    height: 27px;

    border-radius: 7px;

    background: #f3f5f3;

    color: #315c49;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    font-size: 9px;

    font-weight: bold;
}


/* =========================================================
   AKTIVITAS
========================================================= */

.activity-cell {
    display: flex;

    align-items: center;

    gap: 10px;
}


.activity-cell-icon {
    width: 32px;
    height: 32px;

    flex-shrink: 0;

    border-radius: 8px;

    background: #FFF8DD;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 14px;
}


.activity-cell-text {
    color: #222222;

    font-size: 10px;

    font-weight: bold;

    max-width: 320px;

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;
}


/* =========================================================
   IP ADDRESS
========================================================= */

.ip-badge {
    display: inline-flex;

    align-items: center;

    padding: 6px 9px;

    background: #f4f6f4;

    border: 1px solid #e5e9e5;

    border-radius: 6px;

    color: #555555;

    font-size: 9px;

    font-family: monospace;
}


/* =========================================================
   WAKTU
========================================================= */

.time-cell {
    color: #666666;

    font-size: 9px;

    white-space: nowrap;
}


/* =========================================================
   STATUS
========================================================= */

.badge {
    display: inline-flex;

    align-items: center;

    gap: 6px;

    background: #edf6ef;

    color: #276047;

    padding: 6px 9px;

    border-radius: 6px;

    font-size: 8px;

    font-weight: bold;
}


.status-dot {
    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: #4d9b68;
}


/* =========================================================
   EMPTY DATA
========================================================= */

.empty {
    text-align: center;

    padding: 45px 20px;
}


.empty-icon {
    width: 52px;
    height: 52px;

    margin: 0 auto 12px;

    border-radius: 50%;

    background: #edf3ee;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 21px;
}


.empty strong {
    display: block;

    color: #315c49;

    font-size: 12px;

    margin-bottom: 5px;
}


.empty p {
    color: #888888;

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


    .content {
        padding: 25px;
    }


    .activity-header {
        align-items: flex-start;
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


    .activity-header {
        padding: 18px;

        flex-direction: column;

        align-items: flex-start;
    }


    .activity-count {
        align-self: flex-start;
    }


    .page-title h1 {
        font-size: 26px;
    }


    .card {
        padding: 15px;
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

            <span>
                Dashboard
            </span>

        </a>


        <a href="kelola_buku.php">

            📚

            <span>
                Koleksi Buku
            </span>

        </a>


        <a href="tambah_buku.php">

            ➕

            <span>
                Tambah Buku
            </span>

        </a>


        <div class="menu-title">
            MONITORING
        </div>


        <a href="pengunjung.php">

            👥

            <span>
                Pengunjung
            </span>

        </a>


        <a href="aktivitas.php" class="active">

            📋

            <span>
                Aktivitas
            </span>

        </a>


        <div class="menu-title">
            SISTEM
        </div>


        <a href="pengaturan.php">

            ⚙️

            <span>
                Pengaturan
            </span>

        </a>


        <a href="index.php" class="logout">

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


    <!-- TOPBAR -->

    <header class="topbar">

        <div class="topbar-title">
            Aktivitas Sistem
        </div>


        <div class="admin-info">

            <div class="admin-avatar">
                👤
            </div>


            <div class="admin-name">

                <?php echo htmlspecialchars($username); ?>

            </div>

        </div>

    </header>


    <!-- =====================================================
         CONTENT
    ====================================================== -->

    <div class="content">


        <!-- PAGE TITLE -->

        <div class="page-title">

            <small>
                MONITORING
            </small>


            <h1>
                Aktivitas Sistem
            </h1>


            <p>
                Riwayat aktivitas dan akses pada sistem perpustakaan.
            </p>

        </div>


        <!-- =================================================
             LOG AKTIVITAS
        ================================================== -->

        <div class="activity-header">


            <div class="activity-header-left">


                <div class="activity-header-icon">
                    📋
                </div>


                <div class="activity-header-text">

                    <h2>
                        Log Aktivitas
                    </h2>

                    <p>
                        Seluruh aktivitas sistem yang tercatat
                    </p>

                </div>


            </div>


            <div class="activity-count">

                <?php echo $jumlah_data; ?>

                DATA TERBARU

            </div>


        </div>


        <!-- =================================================
             TABLE
        ================================================== -->

        <div class="card">


            <?php if ($jumlah_data > 0): ?>


            <table>


                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Aktivitas
                        </th>

                        <th>
                            IP Address
                        </th>

                        <th>
                            Waktu
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php

                    $no = 1;

                    while ($row = mysqli_fetch_assoc($query)):

                    ?>


                    <tr>


                        <!-- NOMOR -->

                        <td>

                            <span class="number-badge">

                                <?php echo $no++; ?>

                            </span>

                        </td>


                        <!-- AKTIVITAS -->

                        <td>

                            <div class="activity-cell">


                                <div class="activity-cell-icon">
                                    📋
                                </div>


                                <div class="activity-cell-text">

                                    <?php

                                    echo htmlspecialchars(
                                        $row['aktivitas'] ?? '-'
                                    );

                                    ?>

                                </div>


                            </div>

                        </td>


                        <!-- IP ADDRESS -->

                        <td>

                            <span class="ip-badge">

                                <?php

                                echo htmlspecialchars(
                                    $row['ip_address'] ?? '-'
                                );

                                ?>

                            </span>

                        </td>


                        <!-- WAKTU -->

                        <td>

                            <span class="time-cell">

                                <?php

                                echo htmlspecialchars(
                                    $row['waktu'] ?? '-'
                                );

                                ?>

                            </span>

                        </td>


                        <!-- STATUS -->

                        <td>

                            <span class="badge">

                                <span class="status-dot"></span>

                                Tercatat

                            </span>

                        </td>


                    </tr>


                    <?php endwhile; ?>


                </tbody>


            </table>


            <?php else: ?>


            <!-- EMPTY DATA -->

            <div class="empty">


                <div class="empty-icon">
                    📋
                </div>


                <strong>
                    Belum Ada Aktivitas
                </strong>


                <p>
                    Belum terdapat aktivitas sistem yang tercatat.
                </p>


            </div>


            <?php endif; ?>


        </div>


    </div>


</main>


</body>

</html>