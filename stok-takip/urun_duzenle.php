<?php 
include 'baglanti.php'; 
include 'header.php'; 

// URL'den ID gelmemişse ana sayfaya yönlendir
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// Mevcut ürünün bilgilerini veritabanından çekiyoruz
$sorgu = $db->prepare("SELECT * FROM urunler WHERE id = ?");
$sorgu->execute([$id]);
$urun = $sorgu->fetch(PDO::FETCH_ASSOC);

// Eğer böyle bir ürün yoksa
if (!$urun) {
    echo "<div class='alert alert-danger'>Ürün bulunamadı!</div>";
    include 'footer.php';
    exit;
}

// Form gönderildiğinde (Güncelle butonuna basıldığında) çalışacak kodlar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $urun_adi = $_POST['urun_adi'];
    $kategori = $_POST['kategori'];
    $stok_miktari = $_POST['stok_miktari'];
    $alis_fiyati = $_POST['alis_fiyati'];
    $satis_fiyati = $_POST['satis_fiyati'];

    // PDO ile güvenli güncelleme işlemi
    $guncelle_sorgu = $db->prepare("UPDATE urunler SET urun_adi = ?, kategori = ?, stok_miktari = ?, alis_fiyati = ?, satis_fiyati = ? WHERE id = ?");
    $guncelle = $guncelle_sorgu->execute([$urun_adi, $kategori, $stok_miktari, $alis_fiyati, $satis_fiyati, $id]);

    if ($guncelle) {
        echo "<div class='alert alert-success'>Ürün başarıyla güncellendi!</div>";
        // Güncel veriyi anında formda göstermek için $urun değişkenini yeniliyoruz
        $urun['urun_adi'] = $urun_adi;
        $urun['kategori'] = $kategori;
        $urun['stok_miktari'] = $stok_miktari;
        $urun['alis_fiyati'] = $alis_fiyati;
        $urun['satis_fiyati'] = $satis_fiyati;
    } else {
        echo "<div class='alert alert-danger'>Güncelleme sırasında bir hata oluştu.</div>";
    }
}
?>

<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        <h4 class="mb-0">Ürün Düzenle: <?php echo $urun['urun_adi']; ?></h4>
    </div>
    <div class="card-body">
        <form action="" method="POST">
            <div class="mb-3">
                <label>Ürün Adı</label>
                <input type="text" name="urun_adi" class="form-control" value="<?php echo $urun['urun_adi']; ?>" required>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Kategori</label>
                    <input type="text" name="kategori" class="form-control" value="<?php echo $urun['kategori']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label>Stok Miktarı</label>
                    <input type="number" name="stok_miktari" class="form-control" value="<?php echo $urun['stok_miktari']; ?>" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label>Alış Fiyatı (₺)</label>
                    <input type="number" step="0.01" name="alis_fiyati" class="form-control" value="<?php echo $urun['alis_fiyati']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label>Satış Fiyatı (₺)</label>
                    <input type="number" step="0.01" name="satis_fiyati" class="form-control" value="<?php echo $urun['satis_fiyati']; ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-warning w-100">Değişiklikleri Kaydet</button>
            <a href="index.php" class="btn btn-secondary w-100 mt-2">Listeye Dön</a>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>