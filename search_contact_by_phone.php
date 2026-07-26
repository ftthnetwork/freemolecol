<?php
/**
 * Script untuk Cari Contact Info Lengkap Berdasarkan Nomor Telepon
 * Database: bts_balifiber
 * File: search_contact_by_phone.php
 * 
 * Script ini akan:
 * 1. Auto-detect semua tabel di database
 * 2. Cari kolom yang berisi nomor telepon
 * 3. Query nomor 085338307625
 * 4. Return data lengkap dari user/contact
 */

// Database Configuration
$db_host = '172.16.0.92';
$db_user = 'yusa.febriyan';
$db_pass = '112233';
$db_name = 'bts_balifiber';

// Nomor yang dicari
$search_phone = '085338307625';

// Koneksi Database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset UTF-8
$conn->set_charset("utf8");

echo "====================================\n";
echo "PENCARIAN CONTACT INFO\n";
echo "====================================\n";
echo "Nomor yang dicari: $search_phone\n\n";

try {
    // Step 1: Get semua tabel di database
    echo "Step 1: Scan semua tabel di database...\n";
    echo str_repeat("-", 50) . "\n";
    
    $tables_result = $conn->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$db_name'");
    
    if (!$tables_result) {
        throw new Exception("Error getting tables: " . $conn->error);
    }
    
    $tables = array();
    while ($row = $tables_result->fetch_assoc()) {
        $tables[] = $row['TABLE_NAME'];
    }
    
    echo "Total tabel ditemukan: " . count($tables) . "\n";
    echo "Tabel: " . implode(", ", $tables) . "\n\n";
    
    // Step 2: Untuk setiap tabel, cek kolom yang mungkin berisi nomor telepon
    echo "Step 2: Cari kolom yang berisi nomor telepon...\n";
    echo str_repeat("-", 50) . "\n";
    
    $phone_columns = array();
    $phone_keywords = array('phone', 'telepon', 'mobile', 'hp', 'nomor', 'no_', 'contact', 'tel', 'whatsapp', 'wa');
    
    foreach ($tables as $table) {
        $columns_result = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' AND TABLE_SCHEMA = '$db_name'");
        
        while ($col = $columns_result->fetch_assoc()) {
            $col_name_lower = strtolower($col['COLUMN_NAME']);
            
            // Check jika nama kolom matches dengan phone keywords
            foreach ($phone_keywords as $keyword) {
                if (strpos($col_name_lower, $keyword) !== false) {
                    if (!isset($phone_columns[$table])) {
                        $phone_columns[$table] = array();
                    }
                    $phone_columns[$table][] = $col['COLUMN_NAME'];
                    echo "  ✓ Tabel: $table | Kolom: " . $col['COLUMN_NAME'] . "\n";
                    break;
                }
            }
        }
    }
    
    if (empty($phone_columns)) {
        echo "  ⚠ Tidak ada kolom phone ditemukan dengan keywords\n";
    }
    echo "\n";
    
    // Step 3: Search nomor telepon di semua tabel
    echo "Step 3: Search nomor $search_phone...\n";
    echo str_repeat("-", 50) . "\n";
    
    $results_found = false;
    
    foreach ($phone_columns as $table => $columns) {
        foreach ($columns as $phone_col) {
            // Cari nomor dengan berbagai format
            $search_variations = array(
                $search_phone,
                '+62' . substr($search_phone, 1), // 085... menjadi +62...
                '62' . substr($search_phone, 1),   // 085... menjadi 62...
                '0' . $search_phone,               // Jika ada format tanpa 0
                substr($search_phone, 1),          // Jika ada format 85...
                '%' . $search_phone . '%'           // Partial match
            );
            
            foreach ($search_variations as $variation) {
                $query = "SELECT * FROM $table WHERE `$phone_col` LIKE '%$variation%' LIMIT 5";
                $result = $conn->query($query);
                
                if ($result && $result->num_rows > 0) {
                    echo "\n✓ HASIL DITEMUKAN!\n";
                    echo "Tabel: $table | Kolom: $phone_col | Pencarian: $variation\n";
                    echo str_repeat("=", 50) . "\n";
                    
                    while ($row = $result->fetch_assoc()) {
                        echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
                        $results_found = true;
                    }
                }
            }
        }
    }
    
    // Step 4: Jika tidak ada di phone columns, cari di SEMUA kolom text
    if (!$results_found) {
        echo "\nStep 4: Extended search di semua kolom text...\n";
        echo str_repeat("-", 50) . "\n";
        
        foreach ($tables as $table) {
            $columns_result = $conn->query("SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                                           WHERE TABLE_NAME = '$table' 
                                           AND TABLE_SCHEMA = '$db_name' 
                                           AND (COLUMN_TYPE LIKE '%varchar%' OR COLUMN_TYPE LIKE '%text%' OR COLUMN_TYPE LIKE '%char%')");
            
            if ($columns_result) {
                while ($col = $columns_result->fetch_assoc()) {
                    $col_name = $col['COLUMN_NAME'];
                    
                    $query = "SELECT * FROM $table WHERE `$col_name` LIKE '%$search_phone%' LIMIT 5";
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        echo "\n✓ HASIL DITEMUKAN!\n";
                        echo "Tabel: $table | Kolom: $col_name\n";
                        echo str_repeat("=", 50) . "\n";
                        
                        while ($row = $result->fetch_assoc()) {
                            echo json_encode($row, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
                            $results_found = true;
                        }
                    }
                }
            }
        }
    }
    
    if (!$results_found) {
        echo "\n❌ TIDAK ADA DATA DITEMUKAN untuk nomor: $search_phone\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
} finally {
    if ($conn) {
        $conn->close();
    }
}

echo "\n====================================\n";
echo "SELESAI\n";
echo "====================================\n";
?>
