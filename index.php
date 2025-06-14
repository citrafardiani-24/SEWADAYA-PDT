<?php
require_once __DIR__ . '/init.php';

$page_action = $_GET['action'] ?? 'home'; 

switch ($page_action) {
    case 'home':
        require_once __DIR__ . '/home.php';
        break;
    case 'history':
        require_once __DIR__ . '/history.php';
        break;
    case 'login_form': 
        require_once __DIR__ . '/login.php';
        break;
    case 'register_form': 
        require_once __DIR__ . '/register.php';
        break;
    case 'logout':
        require_once __DIR__ . '/logout.php';
        break;
    case 'admin_panel': 
        if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) {
            setMessage('Anda tidak memiliki izin untuk mengakses halaman ini.', 'error');
            redirect('index.php'); 
        }
        require_once __DIR__ . '/views/header.php';
        echo "<h1 class='text-4xl font-extrabold text-gray-900 mb-8 text-center'>Dashboard Admin</h1>";
        ?>
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Aksi Admin</h2>
            <p class="text-gray-600 mb-4">Di sini Anda bisa mengelola berbagai aspek sistem SewaDaya.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="index.php?action=manage_items" class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-semibold py-3 px-4 rounded-md text-center transition-colors">Kelola Pakaian</a>
                <a href="index.php?action=manage_users" class="bg-green-100 hover:bg-green-200 text-green-800 font-semibold py-3 px-4 rounded-md text-center transition-colors">Kelola Pengguna</a>
                <a href="index.php?action=manage_transactions" class="bg-purple-100 hover:bg-purple-200 text-purple-800 font-semibold py-3 px-4 rounded-md text-center transition-colors">Kelola Transaksi</a>
            </div>
        </div>
        <?php
        require_once __DIR__ . '/views/footer.php';
        break;
    case 'manage_items': /
        if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) {
            setMessage('Anda tidak memiliki izin untuk mengakses halaman ini.', 'error');
            redirect('index.php');
        }
        require_once __DIR__ . '/admin_kelola_pakaian.php';
        break;
    case 'manage_users': 
        if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) {
            setMessage('Anda tidak memiliki izin untuk mengakses halaman ini.', 'error');
            redirect('index.php');
        }
        require_once __DIR__ . '/admin_kelola_pengguna.php';
        break;
    case 'manage_transactions': 
        if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) {
            setMessage('Anda tidak memiliki izin untuk mengakses halaman ini.', 'error');
            redirect('index.php');
        }
        require_once __DIR__ . '/admin_kelola_transaksi.php'; // Memuat file actual
        break;
    case 'manage_returns': 
        if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) {
            setMessage('Anda tidak memiliki izin untuk mengakses halaman ini.', 'error');
            redirect('index.php');
        }
        require_once __DIR__ . '/views/header.php';
        echo "<h1 class='text-4xl font-extrabold text-gray-900 mb-8 text-center'>Kelola Pengembalian (Tidak Aktif)</h1>";
        echo "<div class='bg-white p-6 rounded-lg shadow-md mb-8'><p class='text-center text-gray-600'>Halaman ini tidak lagi diakses langsung dari menu admin karena sudah digabung ke 'Kelola Transaksi'.</p></div>";
        require_once __DIR__ . '/views/footer.php';
        break;
    case 'backup_db': 
        if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) {
            setMessage('Anda tidak memiliki izin untuk mengakses halaman ini.', 'error');
            redirect('index.php');
        }
        require_once __DIR__ . '/backup.php';
        break;
    default:
        setMessage('Halaman tidak ditemukan.', 'error');
        redirect('index.php');
        break;
}
?>
