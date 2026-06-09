<?php 
include 'baglanti.php'; 
include 'header.php'; 

// Yeni Kategori Ekleme İşlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kategori_adi'])) {
    $kategori_adi = trim($_POST['kategori_adi']);

    if (!empty($kategori_adi)) {
        $sorgu = $db->prepare("INSERT INTO kategoriler (kategori_adi) VALUES (?)");
        if ($sorgu->execute([$kategori_adi])) {
            echo "<div class='alert alert-success border-0 bg-success text-white'><i class='bi bi-check-circle me-2'></i>Kategori başarıyla eklendi!</div>";
        } else {
            echo "<div class='alert alert-danger border-0 bg-danger text-white'><i class='bi bi-x-circle me-2'></i>Kategori eklenirken hata oluştu.</div>";
        }
    }
}

// Kategori Silme İşlemi
if (isset($_GET['sil'])) {
    $sil_id = $_GET['sil'];
    $sil_sorgu = $db->prepare("DELETE FROM kategoriler WHERE id = ?");
    $sil_sorgu->execute([$sil_id]);
    header("Location: kategoriler.php");
    exit;
}
?>

<div class="row">
    <div class="col-md-5">
        <div class="card border-0 shadow-lg mb-4">
            <div class="card-header bg-dark border-bottom border-secondary py-3">
                <h5 class="mb-0 text-white"><i class="bi bi-plus-circle-dotted me-2 text-warning"></i>Yeni Kategori Ekle</h5>
            </div>
            <div class="card-body p-4 bg-dark">
                <form action="" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" name="kategori_adi" class="form-control bg-transparent text-white border-secondary" id="kategoriAdi" placeholder="Kategori Adı" required>
                        <label for="kategoriAdi" class="text-secondary">Kategori Adı</label>
                    </div>
                    <button type="submit" class="btn btn-warning w-100 fw-bold text-dark"><i class="bi bi-save me-2"></i>Kaydet</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-dark border-bottom border-secondary py-3">
                <h5 class="mb-0 text-white"><i class="bi bi-tags me-2 text-info"></i>Kayıtlı Kategoriler</h5>
            </div>
            <div class="card-body p-0 bg-dark">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Kategori Adı</th>
                            <th class="text-end pe-4">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $kat_sorgu = $db->query("SELECT * FROM kategoriler ORDER BY id DESC");
                        $kategoriler = $kat_sorgu->fetchAll(PDO::FETCH_ASSOC);

                        if (count($kategoriler) > 0) {
                            foreach ($kategoriler as $kat) {
                                // Buton artık JS confirm yerine data-bs-toggle ile modalı açıyor
                                echo "<tr>
                                        <td class='ps-4 text-secondary'>#{$kat['id']}</td>
                                        <td><strong>{$kat['kategori_adi']}</strong></td>
                                        <td class='text-end pe-4'>
                                            <button type='button' class='btn btn-sm btn-outline-danger' data-bs-toggle='modal' data-bs-target='#kategoriSilModali' data-kat-id='{$kat['id']}'><i class='bi bi-trash3'></i> Sil</button>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' class='text-center py-4 text-secondary'>Henüz kategori eklenmemiş.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="kategoriSilModali" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark border-secondary shadow-lg">
      <div class="modal-header border-bottom border-secondary">
        <h5 class="modal-title text-white"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Kategori Silme Onayı</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body text-secondary fs-6">
        Bu kategoriyi silmek istediğinize emin misiniz? 
        <br><small class="text-danger mt-2 d-block"><i class="bi bi-info-circle me-1"></i>Dikkat: Bu işlem geri alınamaz.</small>
      </div>
      <div class="modal-footer border-top border-secondary">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">İptal</button>
        <a href="#" id="modalKategoriSilButonu" class="btn btn-danger px-4"><i class="bi bi-trash3 me-2"></i>Evet, Sil</a>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var kategoriSilModali = document.getElementById('kategoriSilModali');
        
        if (kategoriSilModali) {
            kategoriSilModali.addEventListener('show.bs.modal', function (event) {
                // Modalı tetikleyen butonu bul
                var buton = event.relatedTarget;
                
                // Butondaki data-kat-id değerini (Kategori ID'sini) al
                var katId = buton.getAttribute('data-kat-id');
                
                // Onay butonunun linkini güncelle
                var modalSilButonu = document.getElementById('modalKategoriSilButonu');
                modalSilButonu.href = 'kategoriler.php?sil=' + katId;
            });
        }
    });
</script>

<?php include 'footer.php'; ?>