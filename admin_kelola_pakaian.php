<?php
require_once __DIR__ . '/init.php';

if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) {
    setMessage('Anda tidak memiliki izin untuk mengakses halaman ini.', 'error');
    redirect('index.php'); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['form_action'] ?? ''; 

    if ($action === 'add_item' || $action === 'edit_item') {
        $item_id = $_POST['item_id'] ?? null;
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $size = $_POST['size']; 
        $rental_price_per_day = (float)$_POST['rental_price_per_day'];
        $stock = (int)$_POST['stock'];

        if (empty($name) || empty($size) || $rental_price_per_day <= 0 || $stock < 0) { 
            setMessage('Semua kolom wajib diisi dan harga/stok harus valid.', 'error');
        } else {
            $image_path = null;
            $upload_success = true;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/'; 
                if (!is_dir($upload_dir)) { 
                    mkdir($upload_dir, 0775, true);
                }
                $uploaded_file_path = uploadImage($_FILES['image'], $upload_dir);
                if ($uploaded_file_path) {
                    $image_path = $uploaded_file_path;
                } else {
                    setMessage('Gagal mengunggah gambar. Pastikan format dan ukuran file benar (max 2MB, JPG/PNG/GIF).', 'error');
                    $upload_success = false;
                }
            } else if ($action === 'edit_item' && !empty($_POST['current_image_url'])) {
                $image_path = $_POST['current_image_url']; 
            }

            if ($upload_success) {
                try {
                    if ($action === 'add_item') {
                        $stmt = $pdo->prepare("INSERT INTO clothing_items (name, description, size, rental_price_per_day, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$name, $description, $size, $rental_price_per_day, $stock, $image_path]);
                        setMessage('Item pakaian berhasil ditambahkan!', 'success');
                    } elseif ($action === 'edit_item' && $item_id) {
                        $sql = "UPDATE clothing_items SET name=?, description=?, size=?, rental_price_per_day=?, stock=?";
                        $params = [$name, $description, $size, $rental_price_per_day, $stock];
                        
                        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK && $image_path) {
                            $sql .= ", image_url=?";
                            $params[] = $image_path;
                        } 
                        
                        $sql .= " WHERE item_id=?";
                        $params[] = $item_id;

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        setMessage('Item pakaian berhasil diperbarui!', 'success');
                    }
                } catch (PDOException $e) {
                    setMessage('Gagal menyimpan item: ' . $e->getMessage(), 'error');
                }
            }
        }
    } 
    elseif ($action === 'delete_item') {
        $item_id = $_POST['item_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM clothing_items WHERE item_id = ?");
            $stmt->execute([$item_id]);
            setMessage('Item pakaian berhasil dihapus!', 'success');
        } catch (PDOException $e) {
            setMessage('Gagal menghapus item: ' . $e->getMessage(), 'error');
        }
    }
    redirect('index.php?action=manage_items'); // Redirect setelah POST
}

require_once __DIR__ . '/views/header.php';
?>

<h1 class="text-4xl font-extrabold text-gray-900 mb-8 text-center">Kelola Pakaian Adat</h1>

<div class="text-center mb-8">
    <button onclick="openAddItemModal()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-md shadow-md transition duration-200">
        Tambah Pakaian Baru
    </button>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Daftar Semua Pakaian</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-md">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-3 px-4 text-left">No.</th>
                    <th class="py-3 px-4 text-left">Gambar</th>
                    <th class="py-3 px-4 text-left">Nama</th>
                    <th class="py-3 px-4 text-left">Ukuran</th>
                    <th class="py-3 px-4 text-left">Harga/Hari</th>
                    <th class="py-3 px-4 text-left">Stok</th>
                    <th class="py-3 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM clothing_items ORDER BY name ASC");
                    $all_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($all_items) > 0) {
                        $counter = 1;
                        foreach ($all_items as $item): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4"><?= $counter++ ?>.</td>
                                <td class="py-3 px-4">
                                    <?php if ($item['image_url']): ?>
                                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt=") ?>]" class="w-16 h-16 object-cover rounded-md">
                                    <?php else: ?>
                                        <span class="text-gray-400">Tidak ada gambar</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4"><?= htmlspecialchars($item['name']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars(ucfirst($item['size'])) ?></td>
                                <td class="py-3 px-4">Rp<?= number_format($item['rental_price_per_day'], 0, ',', '.') ?></td>
                                <td class="py-3 px-4"><span class="font-semibold <?= $item['stock'] > 0 ? 'text-green-600' : 'text-red-600' ?>"><?= htmlspecialchars($item['stock']) ?></span></td>
                                <td class="py-3 px-4">
                                    <button onclick='openEditItemModal(<?= json_encode($item) ?>)' class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm py-1 px-3 rounded-md transition duration-200 mr-2">Edit</button>
                                    <form action="admin_kelola_pakaian.php" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus item ini?');">
                                        <input type="hidden" name="form_action" value="delete_item">
                                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['item_id']) ?>">
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm py-1 px-3 rounded-md transition duration-200">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach;
                    } else {
                        echo "<tr><td colspan='7' class='py-3 px-4 text-center text-gray-600'>Tidak ada item pakaian di database.</td></tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='7' class='py-3 px-4 text-center text-red-600'>Gagal memuat item: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/models/add_item_modal.php';
require_once __DIR__ . '/models/edit_item_modal.php';

require_once __DIR__ . '/views/footer.php';
?>
