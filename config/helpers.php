<?php

function displayMessage($message, $type) {
    if (!empty($message)) {
        $class = ($type === 'success') ? 'success' : 'error';
        echo "<div id='messageBox' class='message-box {$class} shadow-md'>
                <span>" . htmlspecialchars($message) . "</span>
                <button class='message-close-btn' onclick='document.getElementById(\"messageBox\").style.display=\"none\";'>&times;</button>
              </div>";
    }
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function hasRole($role) {
    if (!defined('ROLE_ADMIN')) {}
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hashedPassword) {
    return password_verify($password, $hashedPassword);
}

function uploadImage($file, $target_dir) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_file_name = uniqid('img_') . '.' . $imageFileType;
    $target_file = $target_dir . $new_file_name;

    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return false; // File bukan gambar
    }
    if ($file["size"] > 2 * 1024 * 1024) { 
        return false; // Ukuran file terlalu besar
    }
    if(!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        return false; // Format file tidak diizinkan
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file; 
    } else {
        return false;
    }
}
?>
