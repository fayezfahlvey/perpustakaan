<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "perpustakaan_kejaksaan"
);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

?>