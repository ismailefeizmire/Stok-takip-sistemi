<?php 
include 'baglanti.php'; 
include 'header.php'; 

$toplam_cesit = $db->query("SELECT COUNT(*) FROM urunler")->fetchColumn();
$toplam_stok = $db->query("SELECT SUM(stok_miktari) FROM urunler")->fetchColumn() ?? 0;
$kritik_sayisi = $db->query("SELECT COUNT(*) FROM urunler WHERE stok_miktari <= 10")->fetchColumn();
?>

<style>
    @keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(220,53,69,0.5); } 70% { box-shadow: 0 0 0 15px rgba(220,53,69,0); } 100% { box-shadow: 0 0 0 0 rgba(220,53,69,0); } }
    .animate-pulse { animation: pulse 2s infinite; border: 1px solid #dc3545; }
    .stat-icon { font-size: 2.5rem; opacity: 0.8; }
</style>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="fw-bold mb-0">Gösterge Paneli</h2>
        <p class="text-secondary mb-0">İşletmenizin anlık envanter durumu</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="urun_ekle.php" class="btn btn-primary px-4 py-2 shadow-sm"><i class="bi bi-plus-lg me-2"></i>Yeni Ürün Ekle</a>
    </div>
</div>

<div class="row mb-4 g-3">
    <div class="col-md-4">
        <div class="card bg-dark border-primary border-opacity-50 h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="text-primary text-uppercase fw-bold mb-1">Ürün Çeşidi</h6>
                    <h2 class="display-5 fw-bold mb-0"><?php echo $toplam_cesit; ?></h2>
                </div>
                <i class="bi bi-boxes stat-icon text-primary"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-dark border-success border-opacity-50 h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="text-success text-uppercase fw-bold mb-1">Toplam Stok</h6>
                    <h2 class="display-5 fw-bold mb-0"><?php echo $toplam_stok; ?></h2>
                </div>
                <i class="bi bi-stack stat-icon text-success"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-dark <?php echo $kritik_sayisi > 0 ? 'border-danger animate-pulse' : 'border-secondary'; ?> h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="text-danger text-uppercase fw-bold mb-1">Kritik Stok</h6>
                    <h2 class="display-5 fw-bold mb-0"><?php echo $kritik_sayisi; ?></h2>
                </div>
                <i class="bi bi-exclamation-triangle stat-icon text-danger"></i>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 mb-4 bg-dark">
    <div class="card-body p-2">
        <form method="GET" action="index.php" class="d-flex">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-transparent border-secondary text-white"><i class="bi bi-search"></i></span>
                <input type="text" name="arama" class="form-control bg-transparent border-secondary text-white" placeholder="Ürün adı veya kategori ara..." value="<?php echo isset($_GET['arama']) ? htmlspecialchars($_GET['arama']) : ''; ?>">
                <button type="submit" class="btn btn-outline-primary px-4">Ara</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-lg">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-borderless table-hover align-middle mb-0">
                <thead class="table-dark border-bottom border-secondary">
                    <tr>
                        <th class="ps-4 py-3">ID</th>
                        <th class="py-3">Ürün Adı</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3">Stok</th>
                        <th class="py-3">Alış (₺)</th>
                        <th class="py-3">Satış (₺)</th>
                        <th class="text-end pe-4 py-3">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_GET['arama']) && !empty($_GET['arama'])) {
                        $aranan = "%" . $_GET['arama'] . "%";
                        $sorgu = $db->prepare("SELECT * FROM urunler WHERE urun_adi LIKE ? OR kategori LIKE ? ORDER BY id DESC");
                        $sorgu->execute([$aranan, $aranan]);
                    } else {
                        $sorgu = $db->query("SELECT * FROM urunler ORDER BY id DESC");
                    }
                    $urunler = $sorgu->fetchAll(PDO::FETCH_ASSOC);

                    if ($urunler) {
                        foreach ($urunler as $urun) {
                            $isKritik = $urun['stok_miktari'] <= 10;
                            $rowClass = $isKritik ? "bg-danger bg-opacity-10 border-start border-danger border-4" : "border-start border-transparent border-4";
                            $badge = $isKritik ? "<span class='badge bg-danger ms-2 rounded-pill'><i class='bi bi-bell-fill me-1'></i>Kritik</span>" : "";
                            
                            echo "<tr class='{$rowClass} border-bottom border-secondary'>
                                    <td class='ps-4 text-secondary'>#{$urun['id']}</td>
                                    <td><strong class='text-white'>{$urun['urun_adi']}</strong></td>
                                    <td><span class='badge bg-secondary rounded-pill fw-normal px-3'>{$urun['kategori']}</span></td>
                                    <td class='fs-5'>{$urun['stok_miktari']} {$badge}</td>
                                    <td class='text-secondary'>" . number_format($urun['alis_fiyati'], 2, ',', '.') . "</td>
                                    <td class='text-success fw-bold'>" . number_format($urun['satis_fiyati'], 2, ',', '.') . "</td>
                                    <td class='text-end pe-4'>
                                        <a href='urun_duzenle.php?id={$urun['id']}' class='btn btn-sm btn-outline-info rounded-circle p-2 me-1' title='Düzenle'><i class='bi bi-pencil-square'></i></a>
                                        <button type='button' class='btn btn-sm btn-outline-danger rounded-circle p-2' title='Sil' data-bs-toggle='modal' data-bs-target='#silmeModali' data-urun-id='{$urun['id']}'><i class='bi bi-trash3'></i></button>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-5 text-secondary'><i class='bi bi-inbox fs-1 d-block mb-3'></i>Kayıt bulunamadı.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

  <div class="modal fade" id="silmeModali" tabindex="-1" aria-labelledby="silmeModaliLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark border-secondary shadow-lg">
      <div class="modal-header border-bottom border-secondary">
        <h5 class="modal-title text-white" id="silmeModaliLabel"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Silme Onayı</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body text-secondary fs-6">
        Bu ürünü sistemden kalıcı olarak silmek istediğinize emin misiniz? Bu işlem geri alınamaz.
      </div>
      <div class="modal-footer border-top border-secondary">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">İptal</button>
        <a href="#" id="modalSilButonu" class="btn btn-danger px-4"><i class="bi bi-trash3 me-2"></i>Evet, Sil</a>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var silmeModali = document.getElementById('silmeModali');
        
        if (silmeModali) {
            silmeModali.addEventListener('show.bs.modal', function (event) {
                // Modalı tetikleyen butonu yakala
                var buton = event.relatedTarget;
                
                // Butonun içindeki data-urun-id değerini al
                var urunId = buton.getAttribute('data-urun-id');
                
                // Modalın içindeki "Evet, Sil" butonunu bul (BOŞLUK HATASI DÜZELTİLDİ)
                var modalSilButonu = document.getElementById('modalSilButonu');
                
                // "Evet, Sil" butonunun linkini, yakaladığımız ID ile güncelle
                modalSilButonu.href = 'urun_sil.php?id=' + urunId;
            });
        }
    });
</script>

<?php include 'footer.php'; ?>
