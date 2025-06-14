<?php
require_once __DIR__ . '/init.php'; 

if (!isLoggedIn()) {
    redirect('index.php?action=login_form'); // Arahkan ke index.php dengan action login_form
}

if (hasRole(ROLE_ADMIN)) {
    redirect('index.php?action=admin_panel');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'rent_item') {
    $item_id = $_POST['item_id'];
    $rental_date = $_POST['rental_date'];
    $return_date = $_POST['return_date'];
    $quantity = (int)$_POST['quantity']; 
    $user_id = $_SESSION['user_id'];

    if (empty($item_id) || empty($rental_date) || empty($return_date) || $quantity <= 0) {
        setMessage('Semua kolom harus diisi dan kuantitas harus lebih dari 0.', 'error');
    } elseif ($rental_date >= $return_date) {
        setMessage('Tanggal pengembalian harus setelah tanggal sewa.', 'error');
    } else {
        try {
            $stmt = $pdo->prepare("SELECT rental_price_per_day, stock FROM clothing_items WHERE item_id = ?");
            $stmt->execute([$item_id]);
            $item_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($item_data === false) {
                setMessage('Item tidak ditemukan.', 'error');
            } elseif ($item_data['stock'] < $quantity) { // Validasi stok mencukupi
                setMessage('Stok tidak mencukupi untuk kuantitas yang diminta. Stok tersedia: ' . $item_data['stock'], 'error');
            } else {
                $stmt = $pdo->prepare("CALL CreateRental(?, ?, ?, ?, ?, ?)"); // 6 parameter
                $stmt->execute([$user_id, $item_id, $quantity, $rental_date, $return_date, $item_data['rental_price_per_day']]);
                setMessage('Penyewaan berhasil diajukan! Silakan cek di "Penyewaan Saya".', 'success');
            }
        } catch (PDOException $e) {
            // Tangani error yang mungkin berasal dari stored procedure (misalnya, stok habis, jika validasi di SP juga ada)
            setMessage('Gagal mengajukan penyewaan: ' . $e->getMessage(), 'error');
        }
    }
    redirect('index.php');
}

require_once __DIR__ . '/views/header.php';
require_once __DIR__ . '/views/main.php';
?>

<?php
require_once __DIR__ . '/views/items/list.php';

require_once __DIR__ . '/models/rental_modal.php'; 
require_once __DIR__ . '/models/return_modal.php'; 

require_once __DIR__ . '/views/footer.php';
?>
