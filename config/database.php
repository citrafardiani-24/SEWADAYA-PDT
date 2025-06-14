<?php
define('DB_HOST', 'localhost'); 
define('DB_USER', 'root');     
define('DB_PASS', '');         
define('DB_NAME', 'sewadaya'); 

$pdo = null;

function connectDB() {
    global $pdo; 

    if ($pdo === null) {
        try {
            // Buat instance PDO
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("SET time_zone = '+07:00';");
        } catch (PDOException $e) {
            die("<div class='p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400' role='alert'>
                    Koneksi database gagal: " . $e->getMessage() .
                "</div>");
        }
    }
    return $pdo; 
}
?>
