<?php
// Veritabanı bağlantımızı dahil ediyoruz
include 'baglanti.php';

// URL'den gelen bir 'id' değeri var mı kontrol ediyoruz
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // PDO ile güvenli silme işlemi
    $sorgu = $db->prepare("DELETE FROM urunler WHERE id = ?");
    $sil = $sorgu->execute([$id]);

    if ($sil) {
        // Silme başarılıysa index.php'ye (ana sayfaya) yönlendir
        header("Location: index.php?durum=silindi");
        exit;
    } else {
        echo "Silme işlemi sırasında bir hata oluştu.";
    }
} else {
    // Eğer ID gönderilmemişse ana sayfaya geri yolla
    header("Location: index.php");
    exit;
}
?>