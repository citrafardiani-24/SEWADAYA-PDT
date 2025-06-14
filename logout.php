<?php
require_once __DIR__ . '/init.php';

session_unset();

session_destroy();

setMessage('Anda telah logout.', 'success');

redirect('index.php?action=login_form'); 
?>
