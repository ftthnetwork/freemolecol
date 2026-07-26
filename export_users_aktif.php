<?php
/**
 * Script untuk Export User Aktif ke CSV
 * Database: bts_balifiber
 * File: export_users_aktif.php
 */

// Database Configuration
$db_host = '172.16.0.92';
$db_user = 'yusa.febriyan';
$db_pass = '112233';
$db_name = 'bts_balifiber';

// Koneksi Database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset UTF-8
$conn->set_charset("utf8");

// Query untuk ambil user aktif
// Adjust kolom sesuai struktur tabel users Anda
$query = "SELECT * FROM users WHERE status = 'active' OR is_active = 1 ORDER BY id ASC";

$result = $conn->query($query);

if (!$result) {
    die("Query error: " . $conn->error);
}

// Setup header untuk download CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="users_aktif_' . date('Y-m-d_H-i-s') . '.csv"');

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis header kolom CSV
if ($result->num_rows > 0) {
    // Ambil field names dari result
    $row = $result->fetch_assoc();
    $headers = array_keys($row);
    fputcsv($output, $headers);
    
    // Tulis data pertama (yang sudah di-fetch)
    fputcsv($output, $row);
    
    // Tulis data sisanya
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
} else {
    // Jika tidak ada data, tulis pesan
    fputcsv($output, array('Tidak ada user aktif'));
}

// Close output
fclose($output);

// Close database connection
$conn->close();

// Exit
exit();
?>