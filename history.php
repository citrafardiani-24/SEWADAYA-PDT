<?php
require_once __DIR__ . '/init.php';

if (!isLoggedIn()) {
    redirect('index.php?action=login_form'); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'return_item') {
    $rental_id = $_POST['rental_id'];
    $actual_return_date = date('Y-m-d'); 

    try {
        $stmt = $pdo->prepare("CALL ReturnRental(?, ?)");
        $stmt->execute([$rental_id, $actual_return_date]);
        
        setMessage('Pengembalian barang berhasil dicatat!', 'success');
    } catch (PDOException $e) {
        setMessage('Gagal mencatat pengembalian: ' . $e->getMessage(), 'error');
    }
    redirect('index.php?action=history'); 
}

require_once __DIR__ . '/views/header.php';
?>

<h1 class="text-4xl font-extrabold text-gray-900 mb-8 text-center">Riwayat Penyewaan Saya</h1>

<?php
require_once __DIR__ . '/views/rentals/my_rentals.php';

require_once __DIR__ . '/models/return_modal.php'; 

require_once __DIR__ . '/views/footer.php';
?>
