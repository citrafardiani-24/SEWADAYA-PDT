<?php
require_once __DIR__ . '/init.php'; // Sertakan file inisialisasi

if (!isLoggedIn() || !hasRole(ROLE_ADMIN)) {
    setMessage('Anda tidak memiliki izin untuk mengakses halaman ini.', 'error');
    redirect('index.php'); // Arahkan kembali ke halaman utama jika tidak diizinkan
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['form_action'] ?? ''; 

    if ($action === 'add_user' || $action === 'edit_user') {
        $user_id = $_POST['user_id'] ?? null;
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'] ?? ''; // Hanya jika password diubah/ditambah
        $role = $_POST['role'] ?? ROLE_CUSTOMER; // Default role customer

        if (empty($username) || empty($email)) {
            setMessage('Username dan Email wajib diisi.', 'error');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setMessage('Format email tidak valid.', 'error');
        } elseif ($action === 'add_user' && empty($password)) {
            setMessage('Password wajib diisi untuk pengguna baru.', 'error');
        } else {

            // Validasi 1: Admin tidak bisa mengubah perannya sendiri menjadi non-admin
            if ($action === 'edit_user' && $user_id == $_SESSION['user_id'] && $role !== ROLE_ADMIN) {
                setMessage('Anda tidak dapat mengubah peran akun admin Anda sendiri menjadi non-admin.', 'error');
                redirect('index.php?action=manage_users'); // Redirect untuk mencegah perubahan
            }
            
            // Validasi 2: Mencegah perubahan peran dari customer menjadi admin (untuk pengguna lain)
            if ($action === 'edit_user' && $user_id != $_SESSION['user_id']) { // Jika mengedit pengguna lain
                $stmt_get_current_role = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
                $stmt_get_current_role->execute([$user_id]);
                $current_user_role_db = $stmt_get_current_role->fetchColumn();

                if ($current_user_role_db === ROLE_CUSTOMER && $role === ROLE_ADMIN) {
                    setMessage('Admin tidak memiliki izin untuk mengubah peran pengguna \'customer\' menjadi \'admin\'.', 'error');
                    redirect('index.php?action=manage_users');
                }
            }


            try {
                // Periksa duplikasi username/email (kecuali untuk pengguna yang sedang diedit)
                $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND (user_id <> ? OR ? IS NULL)");
                $stmt_check->execute([$username, $email, $user_id, $user_id]);
                if ($stmt_check->fetchColumn() > 0) {
                    setMessage('Username atau Email sudah terdaftar untuk pengguna lain.', 'error');
                } else {
                    if ($action === 'add_user') {
                        $hashed_password = hashPassword($password);
                        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$username, $email, $hashed_password, $role]);
                        setMessage('Pengguna berhasil ditambahkan!', 'success');
                    } elseif ($action === 'edit_user' && $user_id) {
                        $sql = "UPDATE users SET username=?, email=?, role=? WHERE user_id=?";
                        $params = [$username, $email, $role, $user_id];

                        if (!empty($password)) { // Jika password diisi, update password
                            $hashed_password = hashPassword($password);
                            $sql = "UPDATE users SET username=?, email=?, password=?, role=? WHERE user_id=?";
                            $params = [$username, $email, $hashed_password, $role, $user_id];
                        }
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        setMessage('Pengguna berhasil diperbarui!', 'success');
                    }
                }
            } catch (PDOException $e) {
                setMessage('Gagal menyimpan pengguna: ' . $e->getMessage(), 'error');
            }
        }
    }
    elseif ($action === 'delete_user') {
        $user_id = $_POST['user_id'];
        // Pastikan admin tidak menghapus dirinya sendiri
        if ($user_id == $_SESSION['user_id']) {
            setMessage('Anda tidak bisa menghapus akun Anda sendiri!', 'error');
        } else {
            try {

                $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                $stmt->execute([$user_id]);
                setMessage('Pengguna berhasil dihapus!', 'success');
            } catch (PDOException $e) {
                setMessage('Gagal menghapus pengguna. Mungkin ada data terkait (penyewaan, pembayaran) yang perlu dihapus terlebih dahulu.', 'error');
            }
        }
    }
    redirect('index.php?action=manage_users'); // Redirect setelah POST
}

require_once __DIR__ . '/views/header.php';
?>

<h1 class="text-4xl font-extrabold text-gray-900 mb-8 text-center">Kelola Pengguna</h1>

<div class="text-center mb-8">
    <button onclick="openAddUserModal()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-md shadow-md transition duration-200">
        Tambah Pengguna Baru
    </button>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Daftar Semua Pengguna</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-md">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="py-3 px-4 text-left">No.</th>
                    <th class="py-3 px-4 text-left">Username</th>
                    <th class="py-3 px-4 text-left">Email</th>
                    <th class="py-3 px-4 text-left">Role</th>
                    <th class="py-3 px-4 text-left">Tanggal Daftar</th>
                    <th class="py-3 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT user_id, username, email, role, created_at FROM users ORDER BY username ASC");
                    $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($all_users) > 0) {
                        $counter = 1; // Inisialisasi counter nomor urut
                        foreach ($all_users as $user): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-4"><?= $counter++ ?>.</td>
                                <td class="py-3 px-4"><?= htmlspecialchars($user['username']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($user['email']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars(date('Y-m-d', strtotime($user['created_at']))) ?></td>
                                <td class="py-3 px-4">
                                    <!-- Tombol Edit: Memanggil JS untuk membuka modal dan mengisi data -->
                                    <!-- Meneruskan ID pengguna yang sedang login ke JS untuk validasi klien -->
                                    <button onclick='openEditUserModal(<?= json_encode($user) ?>, <?= $_SESSION['user_id'] ?>)' class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm py-1 px-3 rounded-md transition duration-200 mr-2">Edit</button>
                                    <form action="admin_kelola_pengguna.php" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.');">
                                        <input type="hidden" name="form_action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm py-1 px-3 rounded-md transition duration-200" <?= ($user['user_id'] == $_SESSION['user_id']) ? 'disabled' : '' ?>>Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach;
                    } else {
                        echo "<tr><td colspan='6' class='py-3 px-4 text-center text-gray-600'>Tidak ada pengguna di database.</td></tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='6' class='py-3 px-4 text-center text-red-600'>Gagal memuat pengguna: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/models/add_user_modal.php';
require_once __DIR__ . '/models/edit_user_modal.php';

require_once __DIR__ . '/views/footer.php';
?>