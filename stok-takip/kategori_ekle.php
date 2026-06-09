<?php 
include 'baglanti.php'; 
include 'header.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kategori_adi = trim($_POST['kategori_adi']);

    if (!empty($kategori_adi)) {
        $sorgu = $db->prepare("INSERT INTO kategoriler (kategori_adi) VALUES (?)");
        if ($sorgu->execute([$kategori_adi])) {
            echo "<div class='alert alert-success border-0 bg-success text-white'><i class='bi bi-check-circle me-2'></i>Kategori başarıyla eklendi!</div>";
        } else {
            echo "<div class='alert alert-danger border-0 bg-danger text-white'><i class='bi bi-x-circle me-2'></i>Hata oluştu.</div>";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-lg mt-5">
            <div class="card-header bg-dark border-bottom border-secondary py-3">
                <h5 class="mb-0 text-white"><i class="bi bi-tags me-2 text-warning"></i>Yeni Kategori Ekle</h5>
            </div>
            <div class="card-body p-4 bg-dark">
                <form action="" method="POST">
                    <div class="form-floating mb-4">
                        <input type="text" name="kategori_adi" class="form-control bg-transparent text-white border-secondary" id="kategoriAdi" placeholder="Kategori Adı" required>
                        <label for="kategoriAdi" class="text-secondary">Kategori Adı Giriniz</label>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning px-4 py-2 flex-grow-1 fw-bold text-dark"><i class="bi bi-save me-2"></i>Kategoriyi Kaydet</button>
                        <a href="index.php" class="btn btn-outline-secondary px-4 py-2"><i class="bi bi-arrow-left me-2"></i>İptal</a>
                    </div>
                </form>

                <h6 class="text-secondary mt-5 mb-3 border-bottom border-secondary pb-2">Mevcut Kategoriler</h6>
                <div class="d-flex flex-wrap gap-2">
                    <?php
                    $kategoriler = $db->query("SELECT * FROM kategoriler ORDER BY kategori_adi ASC")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($kategoriler as $kat) {
                        echo "<span class='badge bg-secondary fs-6 py-2 px-3 fw-normal border border-secondary'>{$kat['kategori_adi']}</span>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>