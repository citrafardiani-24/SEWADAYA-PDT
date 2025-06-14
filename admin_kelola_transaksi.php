<?php

require_once __DIR__ . '/init.php'; // Sertakan file inisialisasi

if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) {
    setMessage('Anda tidak memiliki izin untuk mengakses halaman ini.', 'error');
    redirect('index.php'); // Arahkan kembali ke halaman utama jika tidak diizinkan
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_action'])) {
    $action = $_POST['form_action'];

    if ($action === 'update_rental_status' || $action === 'cancel_rental') { 
        $rental_id = $_POST['rental_id'];
        $new_status = ($action === 'cancel_rental') ? 'cancelled' : $_POST['new_status']; 
        $actual_return_date = null;

        $allowed_statuses = ['pending', 'rented', 'returned', 'cancelled'];
        if (!in_array($new_status, $allowed_statuses)) {
            setMessage('Status tidak valid.', 'error');
            redirect('index.php?action=manage_transactions');
        }

        if ($new_status === 'returned') {
            $actual_return_date = date('Y-m-d');
        }

        try {
            $stmt_current_status = $pdo->prepare("SELECT status, item_id FROM rentals WHERE rental_id = ?");
            $stmt_current_status->execute([$rental_id]);
            $current_rental_data = $stmt_current_status->fetch(PDO::FETCH_ASSOC);

            if (!$current_rental_data) {
                setMessage('Transaksi penyewaan tidak ditemukan.', 'error');
                redirect('index.php?action=manage_transactions');
            }

            $current_status = $current_rental_data['status'];
            $item_id = $current_rental_data['item_id'];

            if ($new_status === 'rented' && $current_status === 'pending') {
                $stmt = $pdo->prepare("UPDATE rentals SET status=? WHERE rental_id=?");
                $stmt->execute([$new_status, $rental_id]);
                setMessage('Status transaksi berhasil diperbarui menjadi ' . ucfirst($new_status) . '.', 'success');

            } elseif ($new_status === 'returned' && $current_status !== 'returned') {
                if ($current_status !== 'returned') {
                    $stmt = $pdo->prepare("CALL ReturnRental(?, ?)");
                    $stmt->execute([$rental_id, $actual_return_date]);
                    setMessage('Transaksi berhasil ditandai sebagai ' . ucfirst($new_status) . ' dan stok dikembalikan.', 'success');
                } else {
                    setMessage('Transaksi sudah berstatus ' . ucfirst($current_status) . '.', 'error');
                }
            } elseif ($new_status === 'cancelled' && $current_status !== 'returned' && $current_status !== 'cancelled') { 
                $stmt_stock_return = $pdo->prepare("UPDATE clothing_items SET stock = stock + 1 WHERE item_id = ?");
                $stmt_stock_return->execute([$item_id]);
                
                $stmt = $pdo->prepare("UPDATE rentals SET status=? WHERE rental_id=?");
                $stmt->execute([$new_status, $rental_id]);
                setMessage('Transaksi berhasil dibatalkan dan stok dikembalikan.', 'success'); // Pesan lebih jelas
            } else {
                setMessage('Perubahan status tidak diizinkan dari status saat ini (' . ucfirst($current_status) . ') ke ' . ucfirst($new_status) . '.', 'error');
            }
            
        } catch (PDOException $e) {
            setMessage('Gagal memperbarui status transaksi: ' . $e->getMessage(), 'error');
        }
    }
    redirect('index.php?action=manage_transactions'); // Redirect setelah POST
}

require_once __DIR__ . '/views/header.php';
?>

<h1 class="text-4xl font-extrabold text-gray-900 mb-8 text-center">Kelola Transaksi</h1>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Daftar Semua Transaksi Penyewaan & Pengembalian</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-md">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-3 px-4 text-left">No.</th>
                    <th class="py-3 px-4 text-left">Item</th>
                    <th class="py-3 px-4 text-left">Kuantitas</th> <!-- Kolom Kuantitas Ditambahkan -->
                    <th class="py-3 px-4 text-left">Penyewa</th>
                    <th class="py-3 px-4 text-left">Tgl. Sewa</th>
                    <th class="py-3 px-4 text-left">Tgl. Kembali (Est.)</th>
                    <th class="py-3 px-4 text-left">Tgl. Kembali (Aktual)</th>
                    <th class="py-3 px-4 text-left">Total Harga</th>
                    <th class="py-3 px-4 text-left">Status</th>
                    <th class="py-3 px-4 text-left">Terlambat (Hari)</th>
                    <th class="py-3 px-4 text-left">Denda</th> <!-- Kolom Denda -->
                    <th class="py-3 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("
                        SELECT
                            r.rental_id,
                            ci.name AS item_name,
                            r.quantity, -- Ambil kuantitas dari tabel rentals
                            u.username AS customer_username,
                            r.rental_date,
                            r.return_date,
                            r.actual_return_date,
                            r.total_price,
                            r.status,
                            (SELECT CalculateOverdueDays(r.rental_id)) AS overdue_days -- Memanggil fungsi DB
                        FROM
                            rentals r
                        JOIN
                            clothing_items ci ON r.item_id = ci.item_id
                        JOIN
                            users u ON r.user_id = u.user_id
                        ORDER BY
                            r.created_at DESC
                    ");
                    $all_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($all_transactions) > 0) {
                        $counter = 1; // Inisialisasi counter nomor urut
                        foreach ($all_transactions as $transaction):
                            $fine_amount = $transaction['overdue_days'] * DAILY_FINE_AMOUNT; // Hitung denda
                        ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4"><?= $counter++ ?>.</td>
                                <td class="py-3 px-4"><?= htmlspecialchars($transaction['item_name']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($transaction['quantity']) ?></td> <!-- Kuantitas ditampilkan -->
                                <td class="py-3 px-4"><?= htmlspecialchars($transaction['customer_username']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($transaction['rental_date']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($transaction['return_date']) ?></td>
                                <td class="py-3 px-4"><?= $transaction['actual_return_date'] ? htmlspecialchars($transaction['actual_return_date']) : '-' ?></td>
                                <td class="py-3 px-4">Rp<?= number_format($transaction['total_price'], 0, ',', '.') ?></td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        <?php
                                        switch($transaction['status']) {
                                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'rented': echo 'bg-blue-100 text-blue-800'; break;
                                            case 'returned': echo 'bg-green-100 text-green-800'; break;
                                            case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= htmlspecialchars(ucfirst($transaction['status'])) ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <?php if ($transaction['overdue_days'] > 0): ?>
                                        <span class="text-red-500 font-semibold"><?= $transaction['overdue_days'] ?> hari</span>
                                    <?php else: ?>
                                        <span>0 hari</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?php if ($fine_amount > 0): ?>
                                        <span class="text-red-600 font-semibold">Rp<?= number_format($fine_amount, 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 flex flex-col items-start space-y-1"> <!-- Menggunakan flexbox untuk tata letak tombol vertikal -->
                                    <?php if ($transaction['status'] === 'pending'): ?>
                                        <form action="admin_kelola_transaksi.php" method="POST" class="w-full">
                                            <input type="hidden" name="rental_id" value="<?= htmlspecialchars($transaction['rental_id']) ?>">
                                            <input type="hidden" name="form_action" value="update_rental_status">
                                            <input type="hidden" name="new_status" value="rented">
                                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-1 px-2 rounded-md transition duration-200 w-full">Konfirmasi Sewa</button>
                                        </form>
                                        <form action="admin_kelola_transaksi.php" method="POST" class="w-full mt-1" onsubmit="return confirm('Anda yakin ingin membatalkan transaksi ini? Stok akan dikembalikan.');">
                                            <input type="hidden" name="rental_id" value="<?= htmlspecialchars($transaction['rental_id']) ?>">
                                            <input type="hidden" name="form_action" value="cancel_rental"> <!-- Aksi terpisah untuk pembatalan -->
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs py-1 px-2 rounded-md transition duration-200 w-full">Batalkan</button>
                                        </form>
                                    <?php elseif ($transaction['status'] === 'rented'): ?>
                                        <form action="admin_kelola_transaksi.php" method="POST" class="w-full">
                                            <input type="hidden" name="rental_id" value="<?= htmlspecialchars($transaction['rental_id']) ?>">
                                            <input type="hidden" name="form_action" value="update_rental_status">
                                            <input type="hidden" name="new_status" value="returned">
                                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-xs py-1 px-2 rounded-md transition duration-200 w-full" onclick="return confirm('Anda yakin ingin menandai transaksi ini sebagai dikembalikan?');">Dikembalikan</button>
                                        </form>
                                        <form action="admin_kelola_transaksi.php" method="POST" class="w-full mt-1" onsubmit="return confirm('Anda yakin ingin membatalkan transaksi ini? Stok akan dikembalikan.');">
                                            <input type="hidden" name="rental_id" value="<?= htmlspecialchars($transaction['rental_id']) ?>">
                                            <input type="hidden" name="form_action" value="cancel_rental"> <!-- Aksi terpisah untuk pembatalan -->
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs py-1 px-2 rounded-md transition duration-200 w-full">Batalkan</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-500 text-xs">Aksi Selesai</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach;
                    } else {
                        echo "<tr><td colspan='11' class='py-3 px-4 text-center text-gray-600'>Tidak ada transaksi penyewaan.</td></tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='11' class='py-3 px-4 text-center text-red-600'>Gagal memuat transaksi: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/views/footer.php';