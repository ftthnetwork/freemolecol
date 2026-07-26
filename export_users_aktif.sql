-- SQL Query untuk Export User Aktif
-- Database: bts_balifiber
-- Tabel: users

-- Query 1: Export ke CSV (MySQL)
SELECT * 
INTO OUTFILE '/tmp/users_aktif.csv' 
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
FROM users 
WHERE status = 'active' OR is_active = 1
ORDER BY id ASC;

-- Query 2: Alternatif - Lihat data user aktif
SELECT * 
FROM users 
WHERE status = 'active' OR is_active = 1
ORDER BY id ASC;

-- Query 3: Statistik user aktif
SELECT 
    COUNT(*) as total_user_aktif,
    MAX(created_at) as user_terbaru,
    MIN(created_at) as user_tertua
FROM users 
WHERE status = 'active' OR is_active = 1;

-- Query 4: User dengan detail info
SELECT 
    id,
    nik,
    nama,
    email,
    status,
    created_at,
    updated_at,
    last_login
FROM users 
WHERE status = 'active' OR is_active = 1
ORDER BY id ASC;
