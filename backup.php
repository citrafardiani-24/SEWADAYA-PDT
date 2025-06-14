<?php
require_once 'init.php'; 

if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) {
    setMessage('Anda tidak memiliki izin untuk mengakses halaman ini.', 'error');
    redirect('login.php');
}

$backup_message = '';
$backup_type = '';

if (isset($_GET['action']) && $_GET['action'] === 'perform_backup') {
    $backup_dir = 'backups/';
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0775, true); 
    }

    $filename = 'backup_sewadaya_' . date('Ymd_His') . '.sql';
    $filepath = $backup_dir . $filename;

    $mysqldump_path = 'mysqldump'; 

    $command = escapeshellarg($mysqldump_path) . " -u" . escapeshellarg(DB_USER) .
               (DB_PASS ? " -p" . escapeshellarg(DB_PASS) : "") . " " .
               escapeshellarg(DB_NAME) . " > " . escapeshellarg($filepath) . " 2>&1"; 

    $output = null;
    $return_var = null;
    exec($command, $output, $return_var);

    if ($return_var === 0) {
        $backup_message = "Backup database berhasil dibuat: " . $filename;
        $backup_type = 'success';
    } else {
        $backup_message = "Gagal membuat backup database. Output: " . implode("\n", $output);
        $backup_type = 'error';
    }
}

require_once 'views/header.php';

displayMessage($backup_message, $backup_type);
?>

<div class="bg-white p-8 rounded-lg shadow-lg max-w-md mx-auto text-center">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Manajemen Backup Database</h2>
    <p class="text-gray-700 mb-4">Klik tombol di bawah ini untuk membuat backup database SewaDaya secara manual.</p>
    <a href="backup.php?action=perform_backup" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-md focus:outline-none focus:shadow-outline transition duration-200 shadow-md">
        Buat Backup Sekarang
    </a>
    <p class="text-sm text-gray-500 mt-4">Backup akan disimpan di folder `backups/`.</p>
    <p class="text-sm text-gray-500 mt-2">Untuk backup otomatis, konfigurasikan cron job di server Anda.</p>
</div>

<?php
require_once 'views/footer.php';
?>
