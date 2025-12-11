<?php
$DB_HOST = 'localhost';    
$DB_USER = 'root';         
$DB_PASS = '';             
$DB_NAME = 'db_ppdb';      

// Buat Koneksi
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Cek Koneksi
if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>