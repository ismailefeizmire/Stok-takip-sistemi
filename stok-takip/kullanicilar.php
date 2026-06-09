<?php 
include 'baglanti.php'; 
include 'header.php'; 

// GÜVENLİK DUVARI: Sadece admin yetkisi olanlar girebilir
if (!isset($_SESSION['yetki']) || $_SESSION['yetki'] != 'admin') {
    echo "<div class='alert alert-danger mt-5 border-0 bg-danger text-white shadow-lg'><i class='bi bi-shield-lock-fill me-2 fs-4'></i><strong class='fs-5'>Yetkisiz Erişim!</strong><br>Bu sayfayı görüntüleme yetkiniz bulunmamaktadır.</div>";
    include 'footer.php';
    exit;
}

// Yeni Kullanıcı Ekleme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kullanici_adi'])) {
    $yeni_kullanici = trim($_POST['kullanici_adi']);
    $yeni_sifre = md5($_POST['sifre']); 
    $yeni_yetki = $_POST['yetki'];

    $kontrol = $db->prepare("SELECT id FROM yoneticiler WHERE kullanici_adi = ?");
    $kontrol->execute([$yeni_kullanici]);
    
    if ($kontrol->rowCount() > 0) {
        echo "<div class='alert alert-warning border-0 bg-warning text-dark'><i class='bi bi-exclamation-triangle me-2'></i>Bu kullanıcı adı zaten kullanılıyor!</div>";
    } else {
        $ekle = $db->prepare("INSERT INTO yoneticiler (kullanici_adi, sifre, yetki) VALUES (?, ?, ?)");
        if ($ekle->execute([$yeni_kullanici, $yeni_sifre, $yeni_yetki])) {
            echo "<div class='alert alert-success border-0 bg-success text-white'><i class='bi bi-person-check me-2'></i>Kullanıcı başarıyla eklendi!</div>";
        }
    }
}

// Kullanıcı Silme
if (isset($_GET['sil'])) {
    $sil_id = $_GET['sil'];
    
    $kendi_sorgu = $db->prepare("SELECT kullanici_adi FROM yoneticiler WHERE id = ?");
    $kendi_sorgu->execute([$sil_id]);
    $silinecek_kisi = $kendi_sorgu->fetch(PDO::FETCH_ASSOC);

    // HATANIN ÇÖZÜLDÜĞÜ YER 1: Önce $silinecek_kisi var mı diye kontrol ediyoruz
    if ($silinecek_kisi && $silinecek_kisi['kullanici_adi'] != $_SESSION['kullanici_adi']) {
        $db->prepare("DELETE FROM yoneticiler WHERE id = ?")->execute([$sil_id]);
        header("Location: kullanicilar.php");
        exit;
    } elseif ($silinecek_kisi && $silinecek_kisi['kullanici_adi'] == $_SESSION['kullanici_adi']) {
        echo "<div class='alert alert-danger border-0 bg-danger text-white'><i class='bi bi-x-circle me-2'></i>Kendi hesabınızı silemezsiniz!</div>";
    }
}

// Yetki Değiştirme
if (isset($_GET['yetki_degistir']) && isset($_GET['id'])) {
    $degisecek_id = $_GET['id'];
    $yeni_yetki = $_GET['yetki_degistir'];
    
    $kendi_sorgu = $db->prepare("SELECT kullanici_adi FROM yoneticiler WHERE id = ?");
    $kendi_sorgu->execute([$degisecek_id]);
    $yetkisi_degisecek_kisi = $kendi_sorgu->fetch(PDO::FETCH_ASSOC);

    // HATANIN ÇÖZÜLDÜĞÜ YER 2: Önce $yetkisi_degisecek_kisi var mı diye kontrol ediyoruz
    if ($yetkisi_degisecek_kisi && $yetkisi_degisecek_kisi['kullanici_adi'] != $_SESSION['kullanici_adi']) {
        $db->prepare("UPDATE yoneticiler SET yetki = ? WHERE id = ?")->execute([$yeni_yetki, $degisecek_id]);
        header("Location: kullanicilar.php");
        exit;
    }
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-lg mb-4">
            <div class="card-header bg-dark border-bottom border-secondary py-3">
                <h5 class="mb-0 text-white"><i class="bi bi-person-plus me-2 text-primary"></i>Yeni Sistem Kullanıcısı</h5>
            </div>
            <div class="card-body p-4 bg-dark">
                <form action="" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" name="kullanici_adi" class="form-control bg-transparent text-white border-secondary" id="kullaniciAdi" placeholder="Kullanıcı Adı" required autocomplete="off">
                        <label for="kullaniciAdi" class="text-secondary">Kullanıcı Adı</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="sifre" class="form-control bg-transparent text-white border-secondary" id="sifre" placeholder="Şifre" required>
                        <label for="sifre" class="text-secondary">Şifre</label>
                    </div>
                    <div class="form-floating mb-4">
                        <select name="yetki" class="form-select bg-dark text-white border-secondary" id="yetki" required>
                            <option value="personel">Personel (Sadece Veri Girişi)</option>
                            <option value="admin">Admin (Tam Yetki)</option>
                        </select>
                        <label for="yetki" class="text-secondary">Hesap Yetkisi</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Kullanıcıyı Kaydet</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-dark border-bottom border-secondary py-3">
                <h5 class="mb-0 text-white"><i class="bi bi-people me-2 text-info"></i>Sisteme Kayıtlı Kullanıcılar</h5>
            </div>
            <div class="card-body p-0 bg-dark">
                <div class="table-responsive">
                    <table class="table table-dark table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4">Kullanıcı Adı</th>
                                <th>Rol / Yetki</th>
                                <th class="text-end pe-4">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $kul_sorgu = $db->query("SELECT * FROM yoneticiler ORDER BY id ASC");
                            $kullanicilar = $kul_sorgu->fetchAll(PDO::FETCH_ASSOC);

                            foreach ($kullanicilar as $kul) {
                                $rozet_renk = $kul['yetki'] == 'admin' ? 'bg-danger' : 'bg-primary';
                                $yetki_yazisi = $kul['yetki'] == 'admin' ? 'Yönetici (Admin)' : 'Personel';
                                
                                echo "<tr>
                                        <td class='ps-4'><strong class='text-white'>{$kul['kullanici_adi']}</strong>";
                                        
                                if ($kul['kullanici_adi'] == $_SESSION['kullanici_adi']) {
                                    echo " <span class='badge bg-success ms-2'>Siz</span>";
                                }

                                echo "  </td>
                                        <td><span class='badge {$rozet_renk}'>{$yetki_yazisi}</span></td>
                                        <td class='text-end pe-4'>";
                                
                                if ($kul['kullanici_adi'] != $_SESSION['kullanici_adi']) {
                                    if ($kul['yetki'] == 'personel') {
                                        echo "<a href='kullanicilar.php?id={$kul['id']}&yetki_degistir=admin' class='btn btn-sm btn-outline-warning me-2' title='Admin Yap'><i class='bi bi-arrow-up-circle'></i> Admin Yap</a>";
                                    } else {
                                        echo "<a href='kullanicilar.php?id={$kul['id']}&yetki_degistir=personel' class='btn btn-sm btn-outline-info me-2' title='Personel Yap'><i class='bi bi-arrow-down-circle'></i> Personel Yap</a>";
                                    }
                                    
                                    echo "<button type='button' class='btn btn-sm btn-outline-danger' data-bs-toggle='modal' data-bs-target='#kullaniciSilModali' data-kul-id='{$kul['id']}'><i class='bi bi-trash3'></i> Sil</button>";
                                } else {
                                    echo "<span class='text-secondary fst-italic'>Müdahale Edilemez</span>";
                                }
                                
                                echo "  </td>
                                      </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="kullaniciSilModali" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-dark border-secondary shadow-lg">
      <div class="modal-header border-bottom border-secondary">
        <h5 class="modal-title text-white"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Kullanıcı Silme Onayı</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body text-secondary fs-6">
        Bu kullanıcıyı sistemden tamamen silmek istediğinize emin misiniz? 
        <br><small class="text-danger mt-2 d-block"><i class="bi bi-info-circle me-1"></i>Dikkat: Bu işlem geri alınamaz ve kullanıcının sisteme erişimi anında kesilir.</small>
      </div>
      <div class="modal-footer border-top border-secondary">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">İptal</button>
        <a href="#" id="modalKullaniciSilButonu" class="btn btn-danger px-4"><i class="bi bi-trash3 me-2"></i>Evet, Sil</a>
      </div>
    </div>
  </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var kullaniciSilModali = document.getElementById('kullaniciSilModali');
        
        if (kullaniciSilModali) {
            kullaniciSilModali.addEventListener('show.bs.modal', function (event) {
                var buton = event.relatedTarget;
                var kulId = buton.getAttribute('data-kul-id');
                var modalSilButonu = document.getElementById('modalKullaniciSilButonu');
                modalSilButonu.href = 'kullanicilar.php?sil=' + kulId;
            });
        }
    });
</script>

<?php include 'footer.php'; ?>