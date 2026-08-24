<?php

session_start();

require_once "koneksi.php";


$pesan = "";

if (isset($_POST['login'])) {

    $username = trim($_POST['username'] ?? "");
    $password = $_POST['password'] ?? "";

    if ($username === "" || $password === "") {

        $pesan = "Username dan password wajib diisi.";

    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, username, password
             FROM admin
             WHERE username = ?
             LIMIT 1"
        );

        if (!$stmt) {

            $pesan = "Terjadi kesalahan pada sistem.";

        } else {

            mysqli_stmt_bind_param(
                $stmt,
                "s",
                $username
            );

            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            $admin = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);

            if (!$admin) {

                $pesan = "Username atau password salah.";

            } else {

                $valid = password_verify(
                    $password,
                    $admin['password']
                );

                /*
                 * Mendukung password biasa
                 * seperti 123456
                 */
                if (
                    !$valid &&
                    $password === $admin['password']
                ) {

                    $valid = true;

                }

                if ($valid) {

                    session_regenerate_id(true);

                    $_SESSION['admin_id'] =
                        $admin['id'];

                    $_SESSION['admin_username'] =
                        $admin['username'];

                    header(
                        "Location: dashboard.php"
                    );

                    exit;

                } else {

                    $pesan =
                        "Username atau password salah.";

                }

            }

        }

    }

}

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
Administrator - Perpustakaan
</title>

<style>

/* =========================================================
   RESET
========================================================= */

* {

    margin: 0;

    padding: 0;

    box-sizing: border-box;

}


/* =========================================================
   VARIABLES
========================================================= */

:root {

    --gold: #D4AF37;

    --gold-light: #F1D36B;

    --gold-dark: #A88718;

    --gold-soft: #FFF8D9;


    --silver: #C0C0C0;

    --silver-dark: #707070;

    --silver-light: #E5E5E5;

    --silver-soft: #F5F5F5;


    --white: #FFFFFF;

}


/* =========================================================
   BODY
========================================================= */

body {

    min-height: 100vh;

    display: flex;

    align-items: center;

    justify-content: center;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background:
        linear-gradient(
            135deg,
            var(--white),
            var(--silver-soft)
        );

    color: var(--silver-dark);

    position: relative;

    overflow: hidden;

}


/* =========================================================
   DEKORASI GOLD
========================================================= */

body::before {

    content: "";

    position: fixed;

    width: 500px;

    height: 500px;

    right: -180px;

    top: -180px;

    border-radius: 50%;

    border: 70px solid var(--gold-soft);

    opacity: .8;

}


body::after {

    content: "";

    position: fixed;

    width: 400px;

    height: 400px;

    left: -200px;

    bottom: -200px;

    border-radius: 50%;

    border: 50px solid var(--silver-light);

}


/* =========================================================
   LOGIN BOX
========================================================= */

.login-box {

    position: relative;

    z-index: 5;

    width: 390px;

    max-width: 90%;

    padding: 38px;

    background: var(--white);

    border: 1px solid var(--silver-light);

    border-radius: 15px;

    box-shadow:
        0 20px 50px rgba(192,192,192,.25);

}


/* =========================================================
   LOGO AREA
========================================================= */

.logo {

    text-align: center;

    margin-bottom: 28px;

}


/* =========================================================
   KUBUS / LOGO ICON
========================================================= */

.logo-icon {

    width: 60px;

    height: 60px;

    margin: 0 auto;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 13px;

    background:
        linear-gradient(
            135deg,
            var(--gold-light),
            var(--gold)
        );

    box-shadow:
        0 6px 15px rgba(212,175,55,.25);

    overflow: hidden;

}


/* =========================================================
   LOGO KEJARI DI DALAM KUBUS
========================================================= */

.logo-icon img {

    width: 40px;

    height: 40px;

    max-width: 40px;

    max-height: 40px;

    object-fit: contain;

    display: block;

}


/* =========================================================
   ADMINISTRATOR TITLE
========================================================= */

.logo h1 {

    margin-top: 15px;

    color: var(--gold-dark);

    font-family: Georgia, serif;

    font-size: 26px;

}


/* =========================================================
   SUBTITLE
========================================================= */

.subtitle {

    margin-top: 5px;

    color: var(--silver-dark);

    font-size: 9px;

}


/* =========================================================
   LOGIN TITLE
========================================================= */

.login-title {

    text-align: center;

    margin-bottom: 20px;

}


.login-title strong {

    color: var(--silver-dark);

    font-size: 12px;

    letter-spacing: 1px;

}


/* =========================================================
   MESSAGE
========================================================= */

.message {

    padding: 12px;

    margin-bottom: 18px;

    border-radius: 7px;

    background: var(--silver-soft);

    border-left: 4px solid var(--gold);

    color: var(--silver-dark);

    font-size: 10px;

}


/* =========================================================
   FORM
========================================================= */

.form-group {

    margin-bottom: 16px;

}


.form-group label {

    display: block;

    margin-bottom: 6px;

    color: var(--silver-dark);

    font-size: 9px;

    font-weight: bold;

    letter-spacing: .5px;

}


.form-group input {

    width: 100%;

    padding: 12px;

    border: 1px solid var(--silver);

    border-radius: 7px;

    outline: none;

    background: var(--white);

    color: var(--silver-dark);

    font-size: 11px;

}


.form-group input:focus {

    border-color: var(--gold);

    box-shadow:
        0 0 0 3px rgba(212,175,55,.10);

}


/* =========================================================
   BUTTON
========================================================= */

.login-button {

    width: 100%;

    padding: 13px;

    border: none;

    border-radius: 7px;

    background:
        linear-gradient(
            135deg,
            var(--gold-light),
            var(--gold)
        );

    color: var(--white);

    font-size: 11px;

    font-weight: bold;

    cursor: pointer;

    transition: .2s ease;

}


.login-button:hover {

    background:
        linear-gradient(
            135deg,
            var(--gold),
            var(--gold-dark)
        );

    transform: translateY(-1px);

}


/* =========================================================
   BACK
========================================================= */

.back {

    display: block;

    text-align: center;

    margin-top: 18px;

    color: var(--gold-dark);

    font-size: 10px;

    text-decoration: none;

}


.back:hover {

    color: var(--gold);

}


/* =========================================================
   FOOTER
========================================================= */

.login-footer {

    margin-top: 25px;

    padding-top: 15px;

    border-top: 1px solid var(--silver-light);

    text-align: center;

    color: var(--silver);

    font-size: 8px;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 500px) {

    .login-box {

        width: 390px;

        max-width: 92%;

        padding: 30px;

    }

    .logo-icon {

        width: 60px;

        height: 60px;

    }

    .logo-icon img {

        width: 40px;

        height: 40px;

    }

}

</style>

</head>


<body>


<div class="login-box">


    <!-- =====================================================
         LOGO
    ====================================================== -->

    <div class="logo">


        <!-- KUBUS LOGO -->

        <div class="logo-icon">

            <img
                src="assets/logo-kejari-cimahi.png"
                alt="Logo Kejaksaan Negeri Kota Cimahi"
            >

        </div>


        <!-- JUDUL -->

        <h1>
            Administrator
        </h1>


        <!-- SUBTITLE -->

        <div class="subtitle">

            Sistem Informasi Perpustakaan

            <br>

            Kejaksaan Negeri Kota Cimahi

        </div>


    </div>


    <!-- =====================================================
         LOGIN TITLE
    ====================================================== -->

    <div class="login-title">

        <strong>
            MASUK KE SISTEM
        </strong>

    </div>


    <!-- =====================================================
         PESAN ERROR
    ====================================================== -->

    <?php if ($pesan !== ""): ?>

        <div class="message">

            <?= htmlspecialchars($pesan) ?>

        </div>

    <?php endif; ?>


    <!-- =====================================================
         FORM LOGIN
    ====================================================== -->

    <form method="POST">


        <!-- USERNAME -->

        <div class="form-group">

            <label>
                USERNAME
            </label>

            <input
                type="text"
                name="username"
                placeholder="Masukkan username"
                autocomplete="username"
                required
            >

        </div>


        <!-- PASSWORD -->

        <div class="form-group">

            <label>
                PASSWORD
            </label>

            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                autocomplete="current-password"
                required
            >

        </div>


        <!-- BUTTON -->

        <button
            type="submit"
            name="login"
            class="login-button"
        >

            🔐 Masuk

        </button>


    </form>


    <!-- =====================================================
         KEMBALI
    ====================================================== -->

    <a
        href="index.php"
        class="back"
    >

        ← Kembali ke Halaman Utama

    </a>


    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <div class="login-footer">

        © 2026 Perpustakaan Kejaksaan Negeri Kota Cimahi

    </div>


</div>


</body>

</html>