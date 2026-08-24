<?php
require_once "koneksi.php";

/* =========================================================
   PENCARIAN / AMBIL DATA BUKU
========================================================= */

$keyword = "";

if (isset($_GET['search'])) {

    $keyword = trim($_GET['search']);

}


/* =========================================================
   QUERY BUKU
========================================================= */

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

    if (!$stmt) {
        die("Prepare gagal: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "sss",
        $search,
        $search,
        $search
    );

    mysqli_stmt_execute($stmt);

    $query = mysqli_stmt_get_result($stmt);

    if (!$query) {
        die("Query gagal: " . mysqli_error($conn));
    }

} else {

    $query = mysqli_query(
        $conn,
        "SELECT *
         FROM books
         ORDER BY book_id DESC"
    );

    if (!$query) {
        die("Query gagal: " . mysqli_error($conn));
    }

}


/* =========================================================
   TOTAL BUKU / HASIL PENCARIAN
========================================================= */

$total_buku = mysqli_num_rows($query);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Koleksi Buku - Perpustakaan Kejaksaan Negeri Kota Cimahi
</title>


<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {

    font-family: Arial, sans-serif;

    background: #ffffff;

    color: #111111;

}


/* =========================
   HEADER
========================= */

header {

    height: 95px;

    background: white;

    border-bottom: 1px solid #e5e5e5;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 12%;

    position: sticky;

    top: 0;

    z-index: 100;

}


/* =========================
   LOGO
========================= */

.logo {

    display: flex;

    align-items: center;

    gap: 14px;

}

.logo img {

    width: 57px;

    height: 57px;

    object-fit: contain;

}

.logo-text strong {

    display: block;

    color: #000000;

    font-size: 17px;

    letter-spacing: 1px;

}

.logo-text small {

    display: block;

    color: #555;

    font-size: 9px;

    margin-top: 5px;

}


/* =========================
   NAVBAR
========================= */

nav {

    display: flex;

    align-items: center;

    gap: 35px;

}

nav a {

    text-decoration: none;

    color: #444;

    font-size: 13px;

    transition: 0.2s;

}

nav a:hover {

    color: #C9A227;

}

.admin-button {

    background: #C9A227;

    color: #FFFFFF !important;

    padding: 14px 28px;

    border-radius: 8px;

    font-weight: bold;

}


/* =========================
   CONTENT
========================= */

.container {

    width: 75%;

    max-width: 1280px;

    margin: auto;

    padding: 45px 0 70px;

}


/* =========================
   TITLE
========================= */

.label {

    color: #B28A27;

    font-size: 10px;

    font-weight: bold;

    letter-spacing: 2px;

    margin-bottom: 12px;

}

h1 {

    color: #111111;

    font-family: Georgia, serif;

    font-size: 38px;

    margin-bottom: 10px;

}

.description {

    color: #444;

    font-size: 13px;

    margin-bottom: 35px;

}


/* =========================
   INFO
========================= */

.info {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 25px;

}

.total {

    color: #555;

    font-size: 13px;

}

.total strong {

    color: #111;

    font-size: 20px;

}


/* =========================
   SEARCH
========================= */

.search-form {

    display: flex;

    gap: 8px;

    margin-bottom: 20px;

}

.search-input {

    flex: 1;

    padding: 13px 15px;

    border: 1px solid #d8d8d8;

    border-radius: 7px;

    outline: none;

    font-size: 12px;

    color: #111;

    background: white;

}

.search-input::placeholder {

    color: #777;

}

.search-input:focus {

    border-color: #C9A227;

    box-shadow:
        0 0 0 3px rgba(201,162,39,.10);

}

.search-button {

    background: #C9A227;

    color: #FFFFFF;

    border: none;

    padding: 0 22px;

    border-radius: 7px;

    cursor: pointer;

    font-size: 12px;

    font-weight: bold;

    transition: .2s;

}

.search-button:hover {

    background: #C9A227;

    transform: translateY(-1px);

}


/* =========================
   RESET SEARCH
========================= */

.reset-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    background: #f1f1f1;

    color: #222;

    text-decoration: none;

    padding: 0 18px;

    border-radius: 7px;

    font-size: 12px;

    font-weight: bold;

    transition: .2s;

}

.reset-button:hover {

    background: #e3e3e3;

}


/* =========================
   HASIL PENCARIAN
========================= */

.search-info {

    margin-bottom: 25px;

    padding: 12px 15px;

    background: #fffaf0;

    border-left: 3px solid #C9A227;

    border-radius: 6px;

    color: #444;

    font-size: 12px;

}

.search-info strong {

    color: #111;

}


/* =========================
   GRID BUKU
========================= */

.book-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 22px;

}


/* =========================
   CARD BUKU
========================= */

.book-card {

    background: white;

    border: 1px solid #e3e3e3;

    border-radius: 12px;

    overflow: hidden;

    transition: 0.2s;

}

.book-card:hover {

    transform: translateY(-4px);

    box-shadow:
        0 8px 25px rgba(0,0,0,.08);

    border-color: #D8B86A;

}


/* =========================
   FOTO
========================= */

.book-image {

    width: 100%;

    height: 280px;

    background: #f5f5f5;

    display: flex;

    align-items: center;

    justify-content: center;

    overflow: hidden;

}

.book-image img {

    width: 100%;

    height: 100%;

    object-fit: contain;

}


/* =========================
   CONTENT CARD
========================= */

.book-content {

    padding: 18px;

}

.book-title {

    color: #111111;

    font-size: 14px;

    line-height: 1.5;

    font-weight: bold;

    min-height: 63px;

}

.book-location {

    margin-top: 15px;

}

.book-location small {

    display: block;

    color: #666;

    font-size: 9px;

    margin-bottom: 5px;

}

.book-location strong {

    color: #B08A20;

    font-size: 12px;

}

.book-id {

    margin-top: 15px;

    color: #777;

    font-size: 10px;

}


/* =========================
   EMPTY
========================= */

.empty {

    grid-column: 1 / -1;

    text-align: center;

    padding: 60px 20px;

    color: #666;

    border: 1px solid #eee;

    border-radius: 10px;

    background: #fafafa;

}

.empty strong {

    display: block;

    color: #111;

    font-size: 16px;

    margin-bottom: 8px;

}

.empty p {

    color: #666;

    font-size: 12px;

}


/* =========================
   FOOTER
========================= */

footer {

    background: #E5E5E5;

    color: #111111;

    text-align: center;

    padding: 25px;

    font-size: 11px;

}


/* =========================
   RESPONSIVE
========================= */

@media(max-width:1000px) {

    .book-grid {

        grid-template-columns:
            repeat(3, 1fr);

    }

    header {

        padding: 0 5%;

    }

    .container {

        width: 90%;

    }

}

@media(max-width:700px) {

    header {

        height: auto;

        padding: 20px;

        flex-direction: column;

        gap: 20px;

    }

    nav {

        gap: 15px;

        flex-wrap: wrap;

        justify-content: center;

    }

    .book-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }

}

@media(max-width:500px) {

    .book-grid {

        grid-template-columns: 1fr;

    }

    h1 {

        font-size: 30px;

    }

    .search-form {

        flex-direction: column;

    }

    .search-button {

        height: 42px;

    }

    .reset-button {

        height: 42px;

    }

}

</style>

</head>


<body>


<!-- =========================
     HEADER
========================= -->

<header>

    <div class="logo">

        <img
            src="assets/logo-kejari-cimahi.png"
            alt="Logo Kejaksaan Negeri Kota Cimahi"
        >

        <div class="logo-text">

            <strong>
                PERPUSTAKAAN
            </strong>

            <small>
                KEJAKSAAN NEGERI KOTA CIMAHI
            </small>

        </div>

    </div>


    <nav>

        <a href="index.php">
            Beranda
        </a>

        <a href="koleksi.php">
            Koleksi
        </a>

        <a href="#">
            Kategori
        </a>

        <a href="#">
            Tentang
        </a>

        <a
            href="admin.php"
            class="admin-button"
        >
            Admin
        </a>

    </nav>

</header>


<!-- =========================
     CONTENT
========================= -->

<main class="container">


    <div class="label">

        KOLEKSI PERPUSTAKAAN

    </div>


    <h1>

        Semua Koleksi Buku

    </h1>


    <p class="description">

        Jelajahi seluruh koleksi buku yang tersedia
        di Perpustakaan Kejaksaan Negeri Kota Cimahi.

    </p>


    <!-- =========================
         SEARCH
    ========================= -->

    <form
        method="GET"
        action="koleksi.php"
        class="search-form"
    >

        <input
            type="text"
            name="search"
            class="search-input"
            placeholder="Cari judul buku, Book ID, atau lokasi rak..."
            value="<?php
                echo htmlspecialchars($keyword);
            ?>"
        >

        <button
            type="submit"
            class="search-button"
        >
            🔍 Cari
        </button>


        <?php if ($keyword !== ""): ?>

            <a
                href="koleksi.php"
                class="reset-button"
            >
                Reset
            </a>

        <?php endif; ?>

    </form>


    <!-- =========================
         INFORMASI HASIL SEARCH
    ========================= -->

    <?php if ($keyword !== ""): ?>

        <div class="search-info">

            Menampilkan hasil pencarian untuk:

            <strong>
                "<?php echo htmlspecialchars($keyword); ?>"
            </strong>

            —
            <?php echo $total_buku; ?>
            buku ditemukan.

        </div>

    <?php endif; ?>


    <!-- =========================
         TOTAL BUKU
    ========================= -->

    <div class="info">

        <div class="total">

            Menampilkan

            <strong>
                <?php echo $total_buku; ?>
            </strong>

            koleksi buku

        </div>

    </div>


    <!-- =========================
         BUKU
    ========================= -->

    <div class="book-grid">


    <?php if ($total_buku > 0): ?>


        <?php while ($book = mysqli_fetch_assoc($query)): ?>


            <div class="book-card">


                <!-- FOTO -->

                <div class="book-image">


                    <?php if (!empty($book['foto'])): ?>


                        <img
                            src="<?php
                                echo htmlspecialchars(
                                    $book['foto']
                                );
                            ?>"
                            alt="<?php
                                echo htmlspecialchars(
                                    $book['judul']
                                );
                            ?>"
                        >


                    <?php else: ?>


                        <span style="color:#aaa;">

                            Tidak ada foto

                        </span>


                    <?php endif; ?>


                </div>


                <!-- INFORMASI -->

                <div class="book-content">


                    <div class="book-title">

                        <?php

                        echo htmlspecialchars(
                            $book['judul']
                        );

                        ?>

                    </div>


                    <div class="book-location">


                        <small>

                            📍 LOKASI BUKU

                        </small>


                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $book['tempat_rak']
                            );

                            ?>

                        </strong>


                    </div>


                    <div class="book-id">

                        Book ID:

                        <?php

                        echo htmlspecialchars(
                            $book['book_id']
                        );

                        ?>

                    </div>


                </div>


            </div>


        <?php endwhile; ?>


    <?php else: ?>


        <div class="empty">

            <strong>
                Buku tidak ditemukan
            </strong>

            <?php if ($keyword !== ""): ?>

                <p>

                    Tidak ditemukan koleksi dengan kata:

                    "<strong>
                        <?php echo htmlspecialchars($keyword); ?>
                    </strong>"

                </p>

            <?php else: ?>

                <p>
                    Koleksi buku belum tersedia.
                </p>

            <?php endif; ?>


        </div>


    <?php endif; ?>


    </div>


</main>


<!-- =========================
     FOOTER
========================= -->

<footer>

    © <?php echo date('Y'); ?>

    Perpustakaan Kejaksaan Negeri Kota Cimahi

</footer>


</body>

</html>