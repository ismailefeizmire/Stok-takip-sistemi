<?php
$host = 'localhost';
$dbname = 'stok_takip_db';
$username = 'root'; // Kendi veritabanı kullanıcı adını yaz
$password = ''; // Şifren varsa buraya yaz

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Hataları yakalayabilmek için hata modunu aktif ediyoruz
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Bağlantı Hatası: " . $e->getMessage();
    exit;
}
?>