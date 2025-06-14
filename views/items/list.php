<?php
if (!isset($pdo)) {
    require_once __DIR__ . '/../../init.php'; 
    if (!isLoggedIn()) {
        redirect('index.php?action=login_form');
    }
}
?>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Pakaian Tersedia</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM clothing_items WHERE stock > 0 ORDER BY name ASC");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($items) > 0) {
                foreach ($items as $item): ?>
                    <div class="border border-gray-200 rounded-lg p-4 shadow-sm flex flex-col item-card">
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="[Image of <?= htmlspecialchars($item['name']) ?>]" class="w-full h-48 object-cover rounded-md mb-4 shadow-sm">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2"><?= htmlspecialchars($item['name']) ?></h3>
                        <p class="text-gray-600 text-sm mb-2 flex-grow"><?= htmlspecialchars($item['description']) ?></p>
                        <div class="flex justify-between items-center text-sm text-gray-700 mb-2">
                            <span>Ukuran: <span class="font-medium"><?= htmlspecialchars(ucfirst($item['size'])) ?></span></span>
                        </div>
                        <p class="text-gray-800 text-lg font-bold mb-4">Harga: Rp<?= number_format($item['rental_price_per_day'], 0, ',', '.') ?>/hari</p>
                        <p class="text-gray-700 text-sm mb-4">Stok: <span class="font-semibold <?= $item['stock'] > 0 ? 'text-green-600' : 'text-red-600' ?>"><?= htmlspecialchars($item['stock']) ?></span></p>

                        <?php if ($item['stock'] > 0): ?>
                            <button onclick="openRentalModal(<?= $item['item_id'] ?>, '<?= htmlspecialchars($item['name']) ?>', <?= $item['stock'] ?>)"
                                    class="mt-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition duration-200 shadow-md">
                                Sewa Sekarang
                            </button>
                        <?php else: ?>
                            <button disabled class="mt-auto bg-gray-400 text-white font-bold py-2 px-4 rounded-md cursor-not-allowed">
                                Stok Habis
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach;
            } else {
                echo "<p class='col-span-full text-center text-gray-600'>Tidak ada pakaian tersedia saat ini.</p>";
            }
        } catch (PDOException $e) {
            echo "<p class='col-span-full text-center text-red-600'>Gagal memuat item: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
</div>
