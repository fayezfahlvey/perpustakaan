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

if (isset($_POST['simpan'])) {

    $book_id = trim($_POST['book_id']);
    $judul = trim($_POST['judul']);
    $tempat_rak = trim($_POST['tempat_rak']);

    $foto = "";

    if ($book_id === "" || $judul === "" || $tempat_rak === "") {

        $pesan = "Book ID, judul dan tempat rak wajib diisi.";
        $tipe = "error";

    } else {

        $cek = mysqli_prepare(
            $conn,
            "SELECT book_id FROM books WHERE book_id = ?"
        );

        mysqli_stmt_bind_param($cek, "s", $book_id);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $pesan = "Book ID sudah digunakan.";
            $tipe = "error";

        } else {

            if (
                isset($_FILES['foto']) &&
                $_FILES['foto']['error'] === 0
            ) {

                $folder = "uploads/books/";

                if (!is_dir($folder)) {
                    mkdir($folder, 0777, true);
                }

                $nama = basename($_FILES['foto']['name']);

                $ekstensi = strtolower(
                    pathinfo($nama, PATHINFO_EXTENSION)
                );

                $allowed = ['jpg', 'jpeg', 'png', 'webp'];

                if (in_array($ekstensi, $allowed)) {

                    $nama_file =
                        $book_id . "_" . time() . "." . $ekstensi;

                    $target = $folder . $nama_file;

                    if (
                        move_uploaded_file(
                            $_FILES['foto']['tmp_name'],
                            $target
                        )
                    ) {

                        $foto = $target;

                    }

                }

            }

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO books
                (book_id, judul, tempat_rak, foto)
                VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $book_id,
                $judul,
                $tempat_rak,
                $foto
            );

            if (mysqli_stmt_execute($stmt)) {

                $pesan = "Buku berhasil ditambahkan.";
                $tipe = "success";

            } else {

                $pesan = "Buku gagal ditambahkan.";
                $tipe = "error";

            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}
?>

<!DOCTYPE html>

<html lang="id">

<head>

<link
    rel="stylesheet"
    href="admin_style.css"
>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width,initial-scale=1.0"
>

<title>
    Tambah Buku - Perpustakaan
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


/* =====================================================
   CONTENT
===================================================== */

.content {

    padding: 35px;

    max-width: 850px;

}


/* =====================================================
   PAGE TITLE
===================================================== */

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


/* =====================================================
   CARD
===================================================== */

.card {

    background: white;

    border: 1px solid #e5e9e5;

    border-radius: 11px;

    padding: 25px;

}


/* =====================================================
   MESSAGE
===================================================== */

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


/* =====================================================
   FORM
===================================================== */

.form-group {

    margin-bottom: 18px;

}


.form-group label {

    display: block;

    color: #315c49;

    font-size: 10px;

    font-weight: bold;

    margin-bottom: 7px;

}


.form-group input {

    width: 100%;

    padding: 12px;

    border: 1px solid #dfe4df;

    border-radius: 7px;

    outline: none;

    font-size: 11px;

}


.form-group input:focus {

    border-color: #123c2c;

}


.help {

    font-size: 9px;

    color: #999;

    margin-top: 5px;

}


/* =====================================================
   BUTTON
===================================================== */

.button {

    padding: 12px 20px;

    border: none;

    border-radius: 7px;

    background: #C9A227;

    color: white;

    font-size: 11px;

    font-weight: bold;

    cursor: pointer;

}


.button:hover {

    background: #C9A227;

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


<a
    href="tambah_buku.php"
    class="active"
>

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
    href="index.php"
    class="logout"
>

    🚪

    <span>
        Keluar
    </span>

</a>


</nav>

</aside>


<!-- ================= MAIN ================= -->

<main class="main">


<header class="topbar">


<div class="topbar-title">

    Tambah Buku

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
        KOLEKSI PERPUSTAKAAN
    </small>

    <h1>
        Tambah Buku
    </h1>

    <p>
        Tambahkan buku baru ke dalam database perpustakaan.
    </p>

</div>


<?php if($pesan !== ""): ?>


<div class="message <?php echo $tipe; ?>">

    <?php
    echo htmlspecialchars($pesan);
    ?>

</div>


<?php endif; ?>


<section class="card">


<form
    method="POST"
    enctype="multipart/form-data"
>


<div class="form-group">


<label>
    BOOK ID
</label>


<input
    type="text"
    name="book_id"
    placeholder="Contoh: BKA01357"
    required
>


<div class="help">

    Format: BK + kode rak + kode unik buku.

</div>


</div>


<div class="form-group">


<label>
    JUDUL BUKU
</label>


<input
    type="text"
    name="judul"
    placeholder="Masukkan judul buku"
    required
>


</div>


<div class="form-group">


<label>
    TEMPAT RAK
</label>


<input
    type="text"
    name="tempat_rak"
    placeholder="Contoh: Rak A01"
    required
>


</div>


<div class="form-group">


<label>
    FOTO BUKU
</label>


<input
    type="file"
    name="foto"
    accept=".jpg,.jpeg,.png,.webp"
>


<div class="help">

    Format yang didukung: JPG, JPEG, PNG, WEBP.

</div>


</div>


<button
    type="submit"
    name="simpan"
    class="button"
>

    💾 Simpan Buku

</button>


</form>


</section>


</div>

</main>


</body>

</html>