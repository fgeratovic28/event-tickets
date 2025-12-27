<?php
try {
    $pdo = new PDO(
        "mysql:host=sql201.infinityfree.com;dbname=if0_40774056_kupovina_ulaznica;charset=utf8",
        "if0_40774056",
        "W7rLrc9kpAJJ8R0"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

