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
    "SELECT foto
     FROM books
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
    header("Location: kelola_buku.php");
    exit;
}

if (isset($_POST['hapus'])) {

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM books
         WHERE book_id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "s",
        $id
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    // Hapus file foto jika ada
    if (
        !empty($book['foto']) &&
        file_exists($book['foto'])
    ) {

        unlink($book['foto']);

    }

    header("Location: kelola_buku.php");

    exit;

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<title>Konfirmasi Hapus</title>

<style>

body {
    font-family:Arial,sans-serif;
    background:#f5f7f5;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}

.box {
    width:400px;
    background:white;
    padding:30px;
    border-radius:12px;
    text-align:center;
    border:1px solid #ead6d6;
}

h1 {
    color:#9b4545;
    font-size:22px;
}

p {
    color:#777;
    font-size:11px;
    line-height:1.6;
    margin:15px 0 25px;
}

.delete {
    background:#a33a3a;
    color:white;
    border:0;
    padding:11px 18px;
    border-radius:7px;
    cursor:pointer;
    font-weight:bold;
}

.cancel {
    display:inline-block;
    padding:11px 18px;
    margin-left:8px;
    border-radius:7px;
    text-decoration:none;
    color:#123c2c;
    background:#edf3ee;
    font-size:11px;
}

</style>

</head>

<body>

<div class="box">

<h1>⚠️ Hapus Buku?</h1>

<p>

Apakah Anda yakin ingin menghapus buku:

<br><br>

<strong>
<?= htmlspecialchars($book['judul']) ?>
</strong>

<br>

Book ID:
<?= htmlspecialchars($id) ?>

<br><br>

Data yang dihapus tidak dapat dikembalikan melalui sistem.

</p>

<form method="POST" style="display:inline;">

<button
    type="submit"
    name="hapus"
    class="delete"
>
    Ya, Hapus
</button>

</form>

<a
    href="kelola_buku.php"
    class="cancel"
>
    Batal
</a>

</div>

</body>

</html>