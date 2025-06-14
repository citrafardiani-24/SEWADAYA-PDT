<?php

$password_to_hash = "admin123";

$hashed_password = password_hash($password_to_hash, PASSWORD_DEFAULT);

echo "Kata sandi yang akan di-hash: <strong>" . htmlspecialchars($password_to_hash) . "</strong><br>";
echo "Hash yang dihasilkan: <strong>" . htmlspecialchars($hashed_password) . "</strong><br><br>";
echo "Salin hash di atas dan perbarui kolom 'password' untuk pengguna 'admin' di tabel 'users' di phpMyAdmin Anda.";
echo "<br><br><strong>PENTING: Hapus file ini dari server Anda setelah selesai!</strong>";
?>
