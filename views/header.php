<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/constants.php';

$message_from_session = $_SESSION['message'] ?? '';
$message_type_from_session = $_SESSION['message_type'] ?? '';

unset($_SESSION['message']);
unset($_SESSION['message_type']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SewaDaya - Rental Pakaian Adat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }
        *:focus-visible {
            outline: 2px solid theme('colors.blue.500');
            outline-offset: 2px;
            border-radius: theme('borderRadius.md');
        }
        .container {
            max-width: 960px;
        }
        .message-box {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .message-box.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message-box.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .message-close-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: inherit;
        }
        .item-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .item-card img {
            max-height: 192px;
            width: 100%;
            object-fit: cover;
        }
        #rentalModal, #returnModal, #addItemModal, #editItemModal, #addUserModal, #editUserModal { 
            backdrop-filter: blur(4px); 
            background-color: rgba(17, 24, 39, 0.7); 
        }
        .overflow-x-auto table {
            min-width: 640px;
        }
        @media (max-width: 480px) {
            .container {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">>
    <nav class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-white text-2xl font-bold rounded-md px-3 py-1 hover:bg-white hover:text-blue-600 transition-colors">
                SewaDaya
            </a>
            <div>
                <?php if (isLoggedIn()): ?>
                    <span class="text-white mr-4">Halo, <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($_SESSION['role']) ?>)</span>
                    <?php if (!hasRole(ROLE_ADMIN)):  ?>
                        <a href="index.php?action=history" class="bg-white text-blue-600 px-4 py-2 rounded-md shadow hover:bg-blue-100 transition-colors mr-2">Penyewaan Saya</a>
                    <?php endif; ?>

                    <?php if (hasRole(ROLE_ADMIN)): ?>
                        <a href="index.php?action=admin_panel" class="bg-white text-blue-600 px-4 py-2 rounded-md shadow hover:bg-blue-100 transition-colors mr-2">Admin Panel</a>
                        <a href="index.php?action=backup_db" class="bg-white text-blue-600 px-4 py-2 rounded-md shadow hover:bg-blue-100 transition-colors mr-2">Backup DB</a>
                    <?php endif; ?>
                    <a href="index.php?action=logout" class="bg-white text-blue-600 px-4 py-2 rounded-md shadow hover:bg-blue-100 transition-colors">Logout</a>
                <?php else:  ?>
                    <a href="index.php?action=login_form" class="bg-white text-blue-600 px-4 py-2 rounded-md shadow hover:bg-blue-100 transition-colors mr-2">Login</a>
                    <a href="index.php?action=register_form" class="bg-white text-blue-600 px-4 py-2 rounded-md shadow hover:bg-blue-100 transition-colors">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main class="container mx-auto mt-8 p-4 flex-grow">
        <?php displayMessage($message_from_session, $message_type_from_session);  ?>
