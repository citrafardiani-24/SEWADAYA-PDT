<?php
if (!isset($pdo) || !isset($_SESSION['user_id'])) {
    // Jalur relatif dari views/rentals/my_rentals.php ke init.php di root proyek
    require_once __DIR__ . '/../../init.php'; 
    if (!isLoggedIn()) {
        redirect('index.php?action=login_form');
    }
}
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Penyewaan Saya</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-md">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-3 px-4 text-left">No.</th>
                    <th class="py-3 px-4 text-left">Item</th>
                    <th class="py-3 px-4 text-left">Kuantitas</th> <!-- Kolom Kuantitas Ditambahkan -->
                    <th class="py-3 px-4 text-left">Tgl. Sewa</th>
                    <th class="py-3 px-4 text-left">Tgl. Kembali (Est.)</th>
                    <th class="py-3 px-4 text-left">Tgl. Kembali (Aktual)</th>
                    <th class="py-3 px-4 text-left">Total Harga</th>
                    <th class="py-3 px-4 text-left">Status</th>
                    <th class="py-3 px-4 text-left">Terlambat (Hari)</th>
                    <th class="py-3 px-4 text-left">Denda</th>
                    <!-- Kolom Aksi dihapus untuk tampilan customer -->
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    // Ambil semua riwayat penyewaan pengguna yang sedang login dari database
                    // Pastikan kolom 'quantity' diambil dari tabel rentals
                    $stmt = $pdo->prepare("
                        SELECT r.rental_id, ci.name AS item_name, r.quantity, r.rental_date, r.return_date, r.actual_return_date, r.total_price, r.status
                        FROM rentals r
                        JOIN clothing_items ci ON r.item_id = ci.item_id
                        WHERE r.user_id = ?
                        ORDER BY r.created_at DESC
                    ");
                    $stmt->execute([$_SESSION['user_id']]);
                    $my_rentals = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($my_rentals) > 0) {
                        $counter = 1; // Inisialisasi counter nomor urut
                        foreach ($my_rentals as $rental):
                            // Panggil fungsi database untuk menghitung hari keterlambatan
                            $stmt_overdue = $pdo->prepare("SELECT CalculateOverdueDays(?) AS overdue_days");
                            $stmt_overdue->execute([$rental['rental_id']]);
                            $overdue_days_result = $stmt_overdue->fetch(PDO::FETCH_ASSOC);
                            $overdue_days = $overdue_days_result['overdue_days'];
                            $fine_amount = $overdue_days * DAILY_FINE_AMOUNT; // Hitung denda
                        ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4"><?= $counter++ ?>.</td>
                                <td class="py-3 px-4"><?= htmlspecialchars($rental['item_name']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($rental['quantity']) ?></td> <!-- Kuantitas ditampilkan -->
                                <td class="py-3 px-4"><?= htmlspecialchars($rental['rental_date']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($rental['return_date']) ?></td>
                                <td class="py-3 px-4"><?= $rental['actual_return_date'] ? htmlspecialchars($rental['actual_return_date']) : '-' ?></td>
                                <td class="py-3 px-4">Rp<?= number_format($rental['total_price'], 0, ',', '.') ?></td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                                        <?php
                                        switch($rental['status']) {
                                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'rented': echo 'bg-blue-100 text-blue-800'; break;
                                            case 'returned': echo 'bg-green-100 text-green-800'; break;
                                            case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= htmlspecialchars(ucfirst($rental['status'])) ?>
                                    </span>
                                    <?php if ($overdue_days > 0): ?>
                                        <br><span class="text-red-500 text-xs">(Terlambat <?= $overdue_days ?> hari)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?php if ($fine_amount > 0): ?>
                                        <span class="text-red-600 font-semibold">Rp<?= number_format($fine_amount, 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </td>
                                <!-- Sel Aksi dihapus untuk tampilan customer, fungsionalitas pengembalian dipindahkan ke history.php -->
                            </tr>
                        <?php endforeach;
                    } else {
                        // Perbarui colspan menjadi 10 karena ada 10 kolom sekarang
                        echo "<tr><td colspan='10' class='py-3 px-4 text-center text-gray-600'>Anda belum memiliki penyewaan aktif.</td></tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='10' class='py-3 px-4 text-center text-red-600'>Gagal memuat penyewaan: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<p class="mt-4 text-gray-600">Total penyewaan Anda:
    <?php
    try {
        $stmt_total = $pdo->prepare("SELECT GetUserTotalRentals(?) AS total");
        $stmt_total->execute([$_SESSION['user_id']]);
        $total_rentals = $stmt_total->fetchColumn();
        echo "<span class='font-bold'>" . htmlspecialchars($total_rentals) . "</span>";
    } catch (PDOException $e) {
        echo "<span class='text-red-600'>Error: " . $e->getMessage() . "</span>";
    }
    ?>
</p>
