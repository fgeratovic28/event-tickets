<?php
$conn = new mysqli(
    "sql201.infinityfree.com",
    "if0_40774056",
    "W7rLrc9kpAJJ8R0",
    "if0_40774056_kupovina_ulaznica"
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
