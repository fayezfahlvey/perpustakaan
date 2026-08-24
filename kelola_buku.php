<?php
session_start();
require_once "koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

$username = $_SESSION['admin_username'];

/* =========================
   SEARCH BUKU
========================= */

$keyword = "";

if (isset($_GET['search'])) {
    $keyword = trim($_GET['search']);
}

if ($keyword !== "") {

    $search = "%" . $keyword . "%";

    $stmt = mysqli_prepare(
        $conn,
        "SELECT *
         FROM books
         WHERE book_id LIKE ?
            OR judul LIKE ?
            OR tempat_rak LIKE ?
         ORDER BY book_id DESC"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $search,
        $search,
        $search
    );

    mysqli_stmt_execute($stmt);

    $query = mysqli_stmt_get_result($stmt);

} else {

    $query = mysqli_query(
        $conn,
        "SELECT *
         FROM books
         ORDER BY book_id DESC"
    );
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<link rel="stylesheet" href="admin_style.css">

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width,initial-scale=1.0">

<title>Kelola Buku - Perpustakaan</title>

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


/* =====================================================
   SIDEBAR
===================================================== */

.sidebar {
    position: fixed;

    left: 0;
    top: 0;

    width: 235px;
    height: 100vh;

    /* Background mengikuti tema index.php */
    background:
        radial-gradient(
            circle at 85% 10%,
            rgba(216, 184, 106, 0.38) 0%,
            rgba(216, 184, 106, 0.14) 20%,
            transparent 42%
        ),
        linear-gradient(
            160deg,
            #ffffff 0%,
            #f7f7f7 35%,
            #eeeeee 63%,
            #D8B86A 100%
        );

    color: #111111;

    padding: 25px 15px;

    border-right:
        1px solid rgba(201, 162, 39, 0.35);

    box-shadow:
        5px 0 25px rgba(0, 0, 0, 0.08);

    overflow: hidden;

    z-index: 1000;
}


/* =====================================================
   AKSESORIS SIDEBAR
===================================================== */

.sidebar::before {

    content: "";

    position: absolute;

    width: 180px;
    height: 180px;

    top: -70px;
    right: -70px;

    border-radius: 50%;

    background:
        radial-gradient(
            circle,
            rgba(201, 162, 39, 0.18) 0%,
            rgba(201, 162, 39, 0.05) 45%,
            transparent 70%
        );

    pointer-events: none;
}


.sidebar::after {

    content: "";

    position: absolute;

    width: 130px;
    height: 130px;

    bottom: -50px;
    left: -50px;

    border-radius: 50%;

    background:
        radial-gradient(
            circle,
            rgba(255,255,255,.75) 0%,
            rgba(255,255,255,.25) 45%,
            transparent 70%
        );

    pointer-events: none;
}


/* =====================================================
   LOGO
===================================================== */

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

    box-shadow:
        0 4px 10px rgba(201,162,39,.18);
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
}


.logo-text small {

    font-size: 8px;

    color: #666666;
}


/* =====================================================
   MENU TITLE
===================================================== */

.menu-title {

    color: #8A6A0A;

    font-size: 9px;

    font-weight: bold;

    letter-spacing: 1.5px;

    margin: 20px 10px 8px;

    position: relative;

    z-index: 2;
}


/* =====================================================
   MENU
===================================================== */

.menu {

    display: flex;

    flex-direction: column;

    gap: 4px;

    position: relative;

    z-index: 2;
}


.menu a {

    color: #333333;

    text-decoration: none;

    padding: 11px 12px;

    border-radius: 7px;

    font-size: 12px;

    font-weight: 500;

    display: flex;

    gap: 10px;

    align-items: center;

    transition:
        background .2s,
        color .2s,
        transform .2s;
}


/* =====================================================
   MENU HOVER / ACTIVE
===================================================== */

.menu a:hover {

    background:
        rgba(255,255,255,.70);

    color: #8A6A0A;

    transform:
        translateX(2px);
}


.menu a.active {

    background:
        linear-gradient(
            90deg,
            rgba(201,162,39,.22),
            rgba(255,255,255,.75)
        );

    color: #8A6A0A;

    font-weight: bold;

    border-left:
        3px solid #C9A227;

    padding-left: 9px;

    box-shadow:
        0 3px 10px rgba(0,0,0,.04);
}


.menu a.logout {

    color: #A33A3A;

}


.menu a.logout:hover {

    background: rgba(163,58,58,.08);

    color: #8E2F2F;
}


/* =====================================================
   MAIN
===================================================== */

.main {

    margin-left: 235px;

    min-height: 100vh;
}


/* =====================================================
   TOPBAR
===================================================== */

.topbar {

    height: 70px;

    background: white;

    border-bottom:
        1px solid #e5e9e5;

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


/* =====================================================
   CONTENT
===================================================== */

.content {

    padding: 35px;
}


/* =====================================================
   PAGE TITLE
===================================================== */

.page-title {

    margin-bottom: 25px;
}


.page-title small {

    color: #B18A18;

    font-size: 9px;

    font-weight: bold;

    letter-spacing: 1.5px;
}


.page-title h1 {

    color: #111111;

    font-family: Georgia, serif;

    font-size: 30px;

    margin-top: 5px;
}


.page-title p {

    color: #777777;

    font-size: 12px;

    margin-top: 6px;
}


/* =====================================================
   TOOLBAR
===================================================== */

.toolbar {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 20px;

    gap: 15px;
}


/* =====================================================
   SEARCH
===================================================== */

.search-form {

    display: flex;

    align-items: center;

    gap: 8px;

    flex: 1;

    max-width: 500px;
}


.search-input {

    width: 100%;

    padding: 11px 13px;

    border:
        1px solid #dfe1df;

    border-radius: 7px;

    outline: none;

    font-size: 11px;

    background: white;

    color: #111111;
}


.search-input::placeholder {

    color: #888888;
}


.search-input:focus {

    border-color: #C9A227;

    box-shadow:
        0 0 0 3px rgba(201,162,39,.10);
}


.search-button {

    border: none;

    background: #C9A227;

    color: white;

    padding: 11px 17px;

    border-radius: 7px;

    font-size: 11px;

    font-weight: bold;

    cursor: pointer;

    transition: .2s;
}


.search-button:hover {

    background: #D8B86A;

    color: #111111;
}


/* =====================================================
   RESET
===================================================== */

.reset-button {

    background: #F1F1F1;

    color: #333333;

    padding: 11px 13px;

    border-radius: 7px;

    text-decoration: none;

    font-size: 11px;

    transition: .2s;
}


.reset-button:hover {

    background: #E4E4E4;

    color: #111111;
}


/* =====================================================
   ADD BUTTON
===================================================== */

.add-button {

    background: #C9A227;

    color: white;

    padding: 11px 17px;

    border-radius: 7px;

    text-decoration: none;

    font-size: 11px;

    font-weight: bold;

    transition: .2s;
}


.add-button:hover {

    background: #D8B86A;

    color: #111111;
}


/* =====================================================
   TABLE
===================================================== */

.table-card {

    background: white;

    border:
        1px solid #e5e5e5;

    border-radius: 11px;

    padding: 20px;

    overflow-x: auto;

    box-shadow:
        0 3px 15px rgba(0,0,0,.025);
}


table {

    width: 100%;

    border-collapse: collapse;

    min-width: 900px;
}


th {

    background: #F7F7F7;

    color: #8A6A0A;

    text-align: left;

    font-size: 10px;

    padding: 13px;

    white-space: nowrap;
}


td {

    padding: 13px;

    border-bottom:
        1px solid #edf0ed;

    font-size: 11px;

    vertical-align: middle;

    color: #222222;
}


tr:hover td {

    background: #FFFCF3;
}


/* =====================================================
   LEBAR KOLOM
===================================================== */

th:first-child,
td:first-child {

    width: 80px;
}


th:nth-child(2),
td:nth-child(2) {

    width: 160px;

    white-space: nowrap;
}


th:nth-child(3),
td:nth-child(3) {

    min-width: 350px;
}


th:nth-child(4),
td:nth-child(4) {

    width: 130px;

    white-space: nowrap;
}


/* =====================================================
   KOLOM AKSI
===================================================== */

th:last-child,
td:last-child {

    width: 155px;

    min-width: 155px;

    white-space: nowrap;
}


/* =====================================================
   FOTO BUKU
===================================================== */

.book-img {

    width: 55px;

    height: 70px;

    object-fit: cover;

    border-radius: 5px;

    display: block;
}


.no-image {

    color: #999;

    font-size: 10px;
}


/* =====================================================
   AREA TOMBOL AKSI
===================================================== */

.action-buttons {

    display: flex;

    align-items: center;

    justify-content: flex-start;

    gap: 8px;

    width: 100%;

    white-space: nowrap;

    flex-wrap: nowrap;
}


/* =====================================================
   TOMBOL EDIT
===================================================== */

.edit {

    display: inline-flex !important;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    min-width: 58px;

    height: 32px;

    padding: 0 10px;

    margin: 0;

    background: #F1F1F1;

    color: #8A6A0A;

    border-radius: 5px;

    text-decoration: none;

    font-size: 10px;

    line-height: 1;

    white-space: nowrap;

    transition: .2s;
}


.edit:hover {

    background: #E5D8B1;

    color: #6F5508;
}


/* =====================================================
   TOMBOL HAPUS
===================================================== */

.delete {

    display: inline-flex !important;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    min-width: 65px;

    height: 32px;

    padding: 0 10px;

    margin: 0;

    background: #fbeaea;

    color: #a33a3a;

    border-radius: 5px;

    text-decoration: none;

    font-size: 10px;

    line-height: 1;

    white-space: nowrap;

    transition: .2s;
}


.delete:hover {

    background: #f5dada;

    color: #8e2f2f;
}


/* =====================================================
   RESPONSIVE
===================================================== */

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


    .toolbar {

        flex-direction: column;

        align-items: stretch;
    }


    .search-form {

        max-width: none;
    }


    .table-card {

        overflow-x: auto;
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


    .topbar {

        padding: 0 20px;
    }


    .table-card {

        padding: 12px;
    }

}

</style>

</head>


<body>


<!-- =====================================================
     SIDEBAR
===================================================== -->

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


        <a
            href="kelola_buku.php"
            class="active"
        >

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


        <a href="aktivitas.php">

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


        <a
            href="logout.php"
            class="logout"
            onclick="return confirm('Yakin ingin keluar dari sistem?');"
        >

            🚪

            <span>
                Keluar
            </span>

        </a>

    </nav>

</aside>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="main">


<header class="topbar">

    <div class="topbar-title">
        Koleksi Buku
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


<!-- =====================================================
     TITLE
===================================================== -->

<div class="page-title">

    <small>
        DATA PERPUSTAKAAN
    </small>


    <h1>
        Koleksi Buku
    </h1>


    <p>
        Kelola seluruh koleksi buku yang terdaftar dalam sistem.
    </p>

</div>


<!-- =====================================================
     TOOLBAR
===================================================== -->

<div class="toolbar">


    <!-- SEARCH -->

    <form
        method="GET"
        action="kelola_buku.php"
        class="search-form"
    >

        <input
            type="text"
            name="search"
            class="search-input"
            placeholder="Cari berdasarkan Book ID, judul, atau lokasi rak..."
            value="<?php echo htmlspecialchars($keyword); ?>"
        >


        <button
            type="submit"
            class="search-button"
        >

            🔍 Cari

        </button>


        <?php if ($keyword !== ""): ?>

            <a
                href="kelola_buku.php"
                class="reset-button"
            >

                Reset

            </a>

        <?php endif; ?>

    </form>


    <!-- TOTAL + TAMBAH -->

    <div>

        <strong>
            <?php echo mysqli_num_rows($query); ?>
        </strong>


        <span
            style="
                font-size:11px;
                color:#888;
            "
        >

            buku ditemukan

        </span>

    </div>


    <a
        href="tambah_buku.php"
        class="add-button"
    >

        ➕ Tambah Buku

    </a>


</div>


<!-- =====================================================
     TABLE
===================================================== -->

<div class="table-card">


<table>

<thead>

<tr>

    <th>
        Foto
    </th>


    <th>
        Book ID
    </th>


    <th>
        Judul Buku
    </th>


    <th>
        Lokasi Rak
    </th>


    <th>
        Aksi
    </th>

</tr>

</thead>


<tbody>


<?php if (mysqli_num_rows($query) > 0): ?>


    <?php while($book = mysqli_fetch_assoc($query)): ?>

    <tr>


        <!-- FOTO -->

        <td>

            <?php if(!empty($book['foto'])): ?>

                <img
                    src="<?php
                        echo htmlspecialchars(
                            $book['foto']
                        );
                    ?>"
                    class="book-img"
                    alt="Foto buku"
                >

            <?php else: ?>

                <div class="no-image">
                    Tidak ada foto
                </div>

            <?php endif; ?>

        </td>


        <!-- BOOK ID -->

        <td>

            <strong>

                <?php

                echo htmlspecialchars(
                    $book['book_id']
                );

                ?>

            </strong>

        </td>


        <!-- JUDUL -->

        <td>

            <?php

            echo htmlspecialchars(
                $book['judul']
            );

            ?>

        </td>


        <!-- LOKASI -->

        <td>

            📍

            <?php

            echo htmlspecialchars(
                $book['tempat_rak']
            );

            ?>

        </td>


        <!-- =================================================
             AKSI
        ================================================== -->

        <td class="action-cell">

            <div class="action-buttons">


                <a
                    href="edit_buku.php?id=<?php
                        echo urlencode(
                            $book['book_id']
                        );
                    ?>"
                    class="edit"
                >

                    ✏ Edit

                </a>


                <a
                    href="hapus_buku.php?id=<?php
                        echo urlencode(
                            $book['book_id']
                        );
                    ?>"
                    class="delete"
                    onclick="
                        return confirm(
                            'Yakin ingin menghapus buku ini?'
                        );
                    "
                >

                    🗑 Hapus

                </a>


            </div>

        </td>


    </tr>

    <?php endwhile; ?>


<?php else: ?>


    <tr>

        <td
            colspan="5"
            style="
                text-align:center;
                padding:35px;
                color:#999;
            "
        >

            <?php if ($keyword !== ""): ?>

                Tidak ditemukan buku dengan kata

                <strong>
                    "<?php echo htmlspecialchars($keyword); ?>"
                </strong>

            <?php else: ?>

                Belum ada buku yang terdaftar.

            <?php endif; ?>

        </td>

    </tr>


<?php endif; ?>


</tbody>

</table>


</div>


</div>

</main>


</body>

</html>