<?php

session_start();

require_once "koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit;
}

$id = $_GET['id'] ?? '';

if ($id === '') {
    header("Location: kelola_buku.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM books
     WHERE book_id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$book = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$book) {
    die("Data buku tidak ditemukan.");
}

$pesan = "";

if (isset($_POST['update'])) {

    $judul = trim($_POST['judul']);
    $tempat_rak = trim($_POST['tempat_rak']);

    $foto = $book['foto'];

    if ($judul === "" || $tempat_rak === "") {

        $pesan = "Judul dan tempat rak wajib diisi.";

    } else {

        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] === UPLOAD_ERR_OK
        ) {

            $folder = "uploads/books/";

            if (!is_dir($folder)) {
                mkdir($folder,0777,true);
            }

            $extension = strtolower(
                pathinfo(
                    $_FILES['foto']['name'],
                    PATHINFO_EXTENSION
                )
            );

            $allowed = [
                "jpg",
                "jpeg",
                "png",
                "webp"
            ];

            if (in_array($extension,$allowed)) {

                $nama_file =
                    $id . "." . $extension;

                $tujuan =
                    $folder . $nama_file;

                if (
                    move_uploaded_file(
                        $_FILES['foto']['tmp_name'],
                        $tujuan
                    )
                ) {

                    $foto = $tujuan;

                }

            }

        }

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE books
             SET judul = ?,
                 tempat_rak = ?,
                 foto = ?
             WHERE book_id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $judul,
            $tempat_rak,
            $foto,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {

            header(
                "Location: kelola_buku.php"
            );

            exit;

        } else {

            $pesan = "Data gagal diperbarui.";

        }

        mysqli_stmt_close($stmt);

    }

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<title>Edit Buku</title>

<style>

body {
    font-family:Arial,sans-serif;
    background:#f5f7f5;
    padding:40px;
}

.card {
    max-width:650px;
    margin:auto;
    background:white;
    padding:30px;
    border-radius:12px;
}

h1 {
    color:#123c2c;
    font-family:Georgia,serif;
}

.group {
    margin:17px 0;
}

label {
    display:block;
    font-size:10px;
    font-weight:bold;
    margin-bottom:6px;
    color:#315c49;
}

input {
    width:100%;
    padding:12px;
    box-sizing:border-box;
    border:1px solid #ddd;
    border-radius:7px;
}

button {
    padding:12px 20px;
    border:0;
    border-radius:7px;
    background:#C9A227;
    color:white;
    cursor:pointer;
}

.back {
    margin-left:10px;
    font-size:11px;
    color:#123c2c;
}

.message {
    padding:10px;
    background:#fbeaea;
    color:#a33a3a;
    border-radius:7px;
    font-size:11px;
}

.current {
    max-width:120px;
    margin-bottom:10px;
}

</style>

</head>

<body>

<div class="card">

<h1>Edit Buku</h1>

<p style="font-size:11px;color:#888;">
    Book ID:
    <?= htmlspecialchars($book['book_id']) ?>
</p>

<?php if ($pesan): ?>

<div class="message">
    <?= htmlspecialchars($pesan) ?>
</div>

<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<div class="group">

<label>BOOK ID</label>

<input
    type="text"
    value="<?= htmlspecialchars($book['book_id']) ?>"
    disabled
>

</div>

<div class="group">

<label>JUDUL BUKU</label>

<input
    type="text"
    name="judul"
    value="<?= htmlspecialchars($book['judul']) ?>"
    required
>

</div>

<div class="group">

<label>TEMPAT RAK</label>

<input
    type="text"
    name="tempat_rak"
    value="<?= htmlspecialchars($book['tempat_rak']) ?>"
    required
>

</div>

<div class="group">

<label>FOTO BUKU</label>

<?php if (!empty($book['foto'])): ?>

<img
    src="<?= htmlspecialchars($book['foto']) ?>"
    class="current"
>

<?php endif; ?>

<input
    type="file"
    name="foto"
    accept=".jpg,.jpeg,.png,.webp"
>

</div>

<button
    type="submit"
    name="update"
>
    💾 Simpan Perubahan
</button>

<a
    href="kelola_buku.php"
    class="back"
>
</a>

</form>

</div>

</body>

</html>