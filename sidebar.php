<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {

    header("Location: admin.php");
    exit;

}

$username = $_SESSION['admin_username'] ?? 'Administrator';

?>

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

        <a
            href="dashboard.php"
            class="<?php echo ($active_page == 'dashboard') ? 'active' : ''; ?>"
        >
            📊
            <span>Dashboard</span>
        </a>

        <a
            href="kelola_buku.php"
            class="<?php echo ($active_page == 'kelola_buku') ? 'active' : ''; ?>"
        >
            📚
            <span>Koleksi Buku</span>
        </a>

        <a
            href="tambah_buku.php"
            class="<?php echo ($active_page == 'tambah_buku') ? 'active' : ''; ?>"
        >
            ➕
            <span>Tambah Buku</span>
        </a>

        <div class="menu-title">
            MONITORING
        </div>

        <a
            href="pengunjung.php"
            class="<?php echo ($active_page == 'pengunjung') ? 'active' : ''; ?>"
        >
            👥
            <span>Pengunjung</span>
        </a>

        <a
            href="aktivitas.php"
            class="<?php echo ($active_page == 'aktivitas') ? 'active' : ''; ?>"
        >
            📋
            <span>Aktivitas</span>
        </a>

        <div class="menu-title">
            SISTEM
        </div>

        <a
            href="pengaturan.php"
            class="<?php echo ($active_page == 'pengaturan') ? 'active' : ''; ?>"
        >
            ⚙️
            <span>Pengaturan</span>
        </a>

        <a
            href="logout.php"
            class="logout"
            onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');"
        >
            🚪
            <span>Keluar</span>
        </a>

    </nav>

</aside>