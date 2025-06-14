<?php
require_once 'init.php';

if (isLoggedIn()) {
    redirect('home.php');
}

$local_message = '';
$local_message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            setMessage('Login berhasil! Selamat datang, ' . $user['username'] . '.', 'success');
            redirect('home.php');
        } else {
            $local_message = 'Username atau password salah.';
            $local_message_type = 'error';
        }
    } catch (PDOException $e) {
        $local_message = 'Error saat login: ' . $e->getMessage();
        $local_message_type = 'error';
    }
}

require_once 'views/header.php';

displayMessage($local_message, $local_message_type);
?>

<div class="bg-white p-8 rounded-lg shadow-lg max-w-md mx-auto">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Login</h2>
    <form action="login.php" method="POST">
        <div class="mb-4">
            <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
            <input type="text" id="username" name="username" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-6">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
            <input type="password" id="password" name="password" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-md focus:outline-none focus:shadow-outline transition duration-200 shadow-md">
                Login
            </button>
            <a href="register.php" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                Belum punya akun? Register
            </a>
        </div>
    </form>
</div>

<?php
require_once 'views/footer.php';
?>
