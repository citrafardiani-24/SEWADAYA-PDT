<?php

require_once 'init.php'; 

if (isLoggedIn()) {
    redirect('home.php');
}

$local_message = '';
$local_message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = ROLE_CUSTOMER; 

    if (empty($username) || empty($email) || empty($password)) {
        $local_message = 'Semua kolom harus diisi.';
        $local_message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $local_message = 'Format email tidak valid.';
        $local_message_type = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetchColumn() > 0) {
                $local_message = 'Username atau email sudah terdaftar.';
                $local_message_type = 'error';
            } else {
                $hashed_password = hashPassword($password);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password, $role]);
                
                setMessage('Pendaftaran berhasil! Silakan login.', 'success');
                redirect('login.php'); 
            }
        } catch (PDOException $e) {
            $local_message = 'Error saat pendaftaran: ' . $e->getMessage();
            $local_message_type = 'error';
        }
    }
}

require_once 'views/header.php';

displayMessage($local_message, $local_message_type);
?>

<div class="bg-white p-8 rounded-lg shadow-lg max-w-md mx-auto">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Register</h2>
    <form action="register.php" method="POST">
        <div class="mb-4">
            <label for="reg_username" class="block text-gray-700 text-sm font-bold mb-2">Username:</label>
            <input type="text" id="reg_username" name="username" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label for="reg_email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
            <input type="email" id="reg_email" name="email" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="mb-6">
            <label for="reg_password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
            <input type="password" id="reg_password" name="password" required class="shadow appearance-none border rounded-md w-full py-3 px-4 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-md focus:outline-none focus:shadow-outline transition duration-200 shadow-md">
                Register
            </button>
            <a href="login.php" class="inline-block align-baseline font-bold text-sm text-blue-600 hover:text-blue-800">
                Sudah punya akun? Login
            </a>
        </div>
    </form>
</div>

<?php
require_once 'views/footer.php';
?>