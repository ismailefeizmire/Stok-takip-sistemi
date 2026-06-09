<?php 
include 'baglanti.php'; 
include 'header.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $urun_adi = $_POST['urun_adi'];
    $kategori = $_POST['kategori']; // Select box'tan gelen kategori
    $stok_miktari = $_POST['stok_miktari'];
    $alis_fiyati = $_POST['alis_fiyati'];
    $satis_fiyati = $_POST['satis_fiyati'];

    $sorgu = $db->prepare("INSERT INTO urunler (urun_adi, kategori, stok_miktari, alis_fiyati, satis_fiyati) VALUES (?, ?, ?, ?, ?)");
    if ($sorgu->execute([$urun_adi, $kategori, $stok_miktari, $alis_fiyati, $satis_fiyati])) {
        echo "<div class='alert alert-success border-0 bg-success text-white'><i class='bi bi-check-circle me-2'></i>Ürün başarıyla eklendi!</div>";
    } else {
        echo "<div class='alert alert-danger border-0 bg-danger text-white'><i class='bi bi-x-circle me-2'></i>Hata oluştu.</div>";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-lg mt-3">
            <div class="card-header bg-dark border-bottom border-secondary py-3">
                <h5 class="mb-0 text-white"><i class="bi bi-box-seam me-2 text-primary"></i>Yeni Ürün Kaydı</h5>
            </div>
            <div class="card-body p-4 bg-dark">
                <form action="" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" name="urun_adi" class="form-control bg-transparent text-white border-secondary" id="urunAdi" placeholder="Ürün Adı" required>
                        <label for="urunAdi" class="text-secondary">Ürün Adı</label>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="kategori" class="form-select bg-dark text-white border-secondary" id="kategori" required>
                                    <option value="" disabled selected>Listeden Bir Kategori Seçin</option>
                                    <?php
                                    $kat_sorgu = $db->query("SELECT * FROM kategoriler ORDER BY kategori_adi ASC");
                                    while ($kategori_satiri = $kat_sorgu->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$kategori_satiri['kategori_adi']}'>{$kategori_satiri['kategori_adi']}</option>";
                                    }
                                    ?>
                                </select>
                                <label for="kategori" class="text-secondary">Kategori</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" name="stok_miktari" class="form-control bg-transparent text-white border-secondary" id="stok" placeholder="Stok" required>
                                <label for="stok" class="text-secondary">Stok Miktarı</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" name="alis_fiyati" class="form-control bg-transparent text-white border-secondary" id="alis" placeholder="Alış" required>
                                <label for="alis" class="text-secondary">Alış Fiyatı (₺)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" step="0.01" name="satis_fiyati" class="form-control bg-transparent text-white border-secondary" id="satis" placeholder="Satış" required>
                                <label for="satis" class="text-secondary">Satış Fiyatı (₺)</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 flex-grow-1"><i class="bi bi-save me-2"></i>Ürünü Kaydet</button>
                        <a href="index.php" class="btn btn-outline-secondary px-4 py-2"><i class="bi bi-arrow-left me-2"></i>İptal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>