<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

$username = $_SESSION['admin_username'];

$total = 0;
$hari_ini = 0;
$kemarin = 0;

$q = mysqli_query($conn, "SELECT COUNT(*) AS total FROM aktivitas");
if ($q) {
    $total = mysqli_fetch_assoc($q)['total'];
}

$q = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM aktivitas
     WHERE DATE(waktu) = CURDATE()"
);

if ($q) {
    $hari_ini = mysqli_fetch_assoc($q)['total'];
}

$q = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
     FROM aktivitas
     WHERE DATE(waktu) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)"
);

if ($q) {
    $kemarin = mysqli_fetch_assoc($q)['total'];
}

$data = mysqli_query(
    $conn,
    "SELECT *
     FROM aktivitas
     ORDER BY waktu DESC
     LIMIT 100"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

<link rel="stylesheet" href="admin_style.css">

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width,initial-scale=1.0">

<title>Pengunjung - Perpustakaan</title>

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
   TETAP SEPERTI DASHBOARD
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


.menu-title {
    color: #8A6A0A;

    font-size: 9px;

    font-weight: bold;

    letter-spacing: 1.5px;

    margin: 20px 10px 8px;
}


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


/* =========================================================
   PAGE HEADER
========================================================= */

.page-title {
    margin-bottom: 28px;
}


.page-title small {
    display: block;

    color: #B08A20;

    font-size: 9px;

    font-weight: bold;

    letter-spacing: 2px;

    margin-bottom: 5px;
}


.page-title h1 {
    color: #111111;

    font-family: Georgia, serif;

    font-size: 34px;

    line-height: 1.1;

    margin-top: 0;
}


.page-title p {
    color: #777777;

    font-size: 12px;

    margin-top: 7px;
}


/* =========================================================
   STATISTICS
========================================================= */

.stats {
    display: grid;

    grid-template-columns: repeat(3, 1fr);

    gap: 18px;

    margin-bottom: 30px;
}


.stat {
    position: relative;

    background:
        radial-gradient(
            circle at 92% 10%,
            rgba(216, 184, 106, 0.18),
            transparent 25%
        ),
        #ffffff;

    border: 1px solid #e1e5e1;

    border-radius: 15px;

    padding: 24px;

    min-height: 150px;

    overflow: hidden;

    transition:
        transform .2s ease,
        box-shadow .2s ease,
        border-color .2s ease;
}


.stat::after {
    content: "";

    position: absolute;

    width: 85px;
    height: 85px;

    right: -35px;
    bottom: -35px;

    background: rgba(216, 184, 106, 0.08);

    border-radius: 50%;
}


.stat:hover {
    transform: translateY(-3px);

    border-color: rgba(201, 162, 39, 0.45);

    box-shadow:
        0 10px 25px rgba(0, 0, 0, 0.06);
}


/* ICON */

.stat-icon {
    width: 50px;
    height: 50px;

    background:
        linear-gradient(
            135deg,
            #FFF8DD,
            #F5E5AE
        );

    border-radius: 12px;

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 21px;

    margin-bottom: 17px;

    box-shadow:
        inset 0 0 0 1px rgba(201, 162, 39, 0.10);
}


.stat strong {
    display: block;

    font-size: 27px;

    line-height: 1;

    color: #111111;

    margin-bottom: 7px;

    position: relative;

    z-index: 2;
}


.stat span {
    display: block;

    font-size: 10px;

    color: #777777;

    position: relative;

    z-index: 2;
}


/* =========================================================
   RIWAYAT PENGUNJUNG
========================================================= */

.table-card {

    background: #ffffff;

    border: 1px solid #e1e5e1;

    border-radius: 15px;

    overflow: hidden;

    box-shadow:
        0 4px 15px rgba(0, 0, 0, 0.025);
}


/* =========================================================
   TABLE HEADER
========================================================= */

.table-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    padding: 25px 28px;

    border-bottom: 1px solid #edf0ed;
}


.table-header-left h2 {

    color: #111111;

    font-family: Georgia, serif;

    font-size: 21px;

    margin-bottom: 5px;
}


.table-header-left p {

    color: #888888;

    font-size: 10px;
}


/* =========================================================
   MONITORING BADGE
========================================================= */

.monitoring-badge {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    padding: 7px 12px;

    border-radius: 20px;

    background: #FFF8DD;

    border: 1px solid #E9CB70;

    color: #8A6A0A;

    font-size: 9px;

    font-weight: bold;

    white-space: nowrap;
}


.monitoring-dot {

    width: 6px;
    height: 6px;

    border-radius: 50%;

    background: #C9A227;

    box-shadow:
        0 0 0 3px rgba(201, 162, 39, 0.12);
}


/* =========================================================
   TABLE
========================================================= */

.table-wrapper {

    width: 100%;

    overflow-x: auto;
}


table {

    width: 100%;

    border-collapse: collapse;

    min-width: 650px;
}


thead th {

    background: #f7f8f7;

    color: #315c49;

    text-align: left;

    font-size: 9px;

    font-weight: bold;

    letter-spacing: .7px;

    padding: 15px 18px;

    border-bottom: 1px solid #e8ece8;
}


tbody td {

    padding: 15px 18px;

    border-bottom: 1px solid #edf0ed;

    font-size: 10px;

    color: #555555;
}


tbody tr {

    transition: background .15s ease;
}


tbody tr:hover {

    background: #fcfcfa;
}


tbody tr:last-child td {

    border-bottom: none;
}


/* =========================================================
   NUMBER
========================================================= */

.number-cell {

    color: #9A9A9A;

    font-weight: bold;

    width: 55px;
}


/* =========================================================
   ACTIVITY
========================================================= */

.activity-cell {

    color: #222222;

    font-weight: bold;
}


/* =========================================================
   IP
========================================================= */

.ip-cell {

    color: #777777;

    font-family: Arial, sans-serif;
}


/* =========================================================
   TIME
========================================================= */

.time-cell {

    color: #777777;

    white-space: nowrap;
}


/* =========================================================
   EMPTY STATE
========================================================= */

.empty-state {

    min-height: 290px;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    text-align: center;

    padding: 40px 20px;
}


.empty-icon {

    width: 68px;
    height: 68px;

    border-radius: 50%;

    background:
        linear-gradient(
            135deg,
            #FFF8DD,
            #F5E5AE
        );

    display: flex;

    align-items: center;
    justify-content: center;

    font-size: 27px;

    margin-bottom: 17px;

    box-shadow:
        inset 0 0 0 1px rgba(201, 162, 39, 0.12);
}


.empty-state h3 {

    color: #222222;

    font-size: 16px;

    margin-bottom: 7px;
}


.empty-state p {

    color: #999999;

    font-size: 11px;
}


/* =========================================================
   BADGE DATA
========================================================= */

.activity-badge {

    display: inline-flex;

    align-items: center;

    gap: 5px;

    background: #FFF8DD;

    color: #8A6A0A;

    padding: 5px 9px;

    border-radius: 6px;

    font-size: 8px;

    font-weight: bold;
}


.activity-badge-dot {

    width: 5px;
    height: 5px;

    background: #C9A227;

    border-radius: 50%;
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
        grid-template-columns: repeat(2, 1fr);
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

    .stats {
        grid-template-columns: 1fr;
    }

    .table-header {
        align-items: flex-start;

        gap: 15px;

        flex-direction: column;
    }

}

</style>

</head>

<body>


<!-- ================= SIDEBAR ================= -->

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


        <a href="pengunjung.php" class="active">
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


        <a href="pengaturan.php">
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


<!-- ================= MAIN ================= -->

<main class="main">


<header class="topbar">

    <div class="topbar-title">
        Pengunjung
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


<div class="content">


<!-- =====================================================
     PAGE TITLE
===================================================== -->

<div class="page-title">

    <small>
        MONITORING
    </small>

    <h1>
        Pengunjung
    </h1>

    <p>
        Pantau aktivitas pengunjung pada sistem perpustakaan.
    </p>

</div>


<!-- =====================================================
     STATISTICS
===================================================== -->

<div class="stats">


    <!-- HARI INI -->

    <div class="stat">

        <div class="stat-icon">
            👥
        </div>

        <strong>
            <?php echo $hari_ini; ?>
        </strong>

        <span>
            Pengunjung Hari Ini
        </span>

    </div>


    <!-- KEMARIN -->

    <div class="stat">

        <div class="stat-icon">
            🗓️
        </div>

        <strong>
            <?php echo $kemarin; ?>
        </strong>

        <span>
            Pengunjung Kemarin
        </span>

    </div>


    <!-- TOTAL -->

    <div class="stat">

        <div class="stat-icon">
            📊
        </div>

        <strong>
            <?php echo $total; ?>
        </strong>

        <span>
            Total Aktivitas
        </span>

    </div>


</div>


<!-- =====================================================
     RIWAYAT PENGUNJUNG
===================================================== -->

<div class="table-card">


    <div class="table-header">

        <div class="table-header-left">

            <h2>
                Riwayat Pengunjung
            </h2>

            <p>
                100 aktivitas terbaru yang tercatat dalam sistem.
            </p>

        </div>


        <div class="monitoring-badge">

            <span class="monitoring-dot"></span>

            Monitoring Aktif

        </div>

    </div>


    <div class="table-wrapper">


    <table>

        <thead>

        <tr>

            <th>
                NO
            </th>

            <th>
                AKTIVITAS
            </th>

            <th>
                IP ADDRESS
            </th>

            <th>
                WAKTU
            </th>

        </tr>

        </thead>


        <tbody>

        <?php

        $no = 1;

        if (mysqli_num_rows($data) > 0):

            while($row = mysqli_fetch_assoc($data)):

        ?>

        <tr>

            <td class="number-cell">
                <?php echo $no++; ?>
            </td>


            <td class="activity-cell">

                <?php
                echo htmlspecialchars(
                    $row['aktivitas'] ?? '-'
                );
                ?>

            </td>


            <td class="ip-cell">

                <?php
                echo htmlspecialchars(
                    $row['ip_address'] ?? '-'
                );
                ?>

            </td>


            <td class="time-cell">

                <?php
                echo htmlspecialchars(
                    $row['waktu'] ?? '-'
                );
                ?>

            </td>

        </tr>

        <?php

            endwhile;

        else:

        ?>

        <!-- =================================================
             EMPTY STATE
        ================================================== -->

        <tr>

            <td colspan="4">

                <div class="empty-state">

                    <div class="empty-icon">
                        👥
                    </div>

                    <h3>
                        Belum Ada Aktivitas
                    </h3>

                    <p>
                        Belum ada data pengunjung yang tercatat.
                    </p>

                </div>

            </td>

        </tr>

        <?php endif; ?>

        </tbody>

    </table>


    </div>

</div>


</div>

</main>

</body>

</html>