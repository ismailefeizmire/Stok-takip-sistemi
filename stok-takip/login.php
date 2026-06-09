<?php
session_start();
include 'baglanti.php';

if (isset($_SESSION['admin_giris'])) { header("Location: index.php"); exit; }

$hata = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kullanici_adi = $_POST['kullanici_adi'];
    $sifre = md5($_POST['sifre']);

    $sorgu = $db->prepare("SELECT * FROM yoneticiler WHERE kullanici_adi = ? AND sifre = ?");
    $sorgu->execute([$kullanici_adi, $sifre]);
    $yonetici = $sorgu->fetch(PDO::FETCH_ASSOC);
    
    if ($yonetici) {
        $_SESSION['admin_giris'] = true;
        $_SESSION['kullanici_adi'] = $yonetici['kullanici_adi'];
        $_SESSION['yetki'] = $yonetici['yetki']; // Kullanıcının yetkisini hafızaya aldık
        header("Location: index.php");
        exit;
    } else {
        $hata = "Kullanıcı adı veya şifre hatalı!";
    }
}
?>
<!DOCTYPE html>
<html lang="tr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Sisteme Giriş</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: radial-gradient(circle at center, #2b2e33 0%, #121212 100%); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: rgba(30, 33, 37, 0.85); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); border-radius: 16px; padding: 3rem 2rem; width: 100%; max-width: 420px; box-shadow: 0 15px 35px rgba(0,0,0,0.5); }
    </style>
</head>
<body>

<div class="login-card text-center">
    <div class="mb-4">
        <i class="bi bi-shield-lock text-primary" style="font-size: 3rem;"></i>
    </div>
    <h3 class="mb-4 fw-bold">Yönetici Girişi</h3>
    
    <?php if ($hata != ""): ?>
        <div class="alert alert-danger bg-danger text-white border-0 py-2"><i class="bi bi-exclamation-triangle me-2"></i><?php echo $hata; ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="text-start">
        <div class="form-floating mb-3">
            <input type="text" name="kullanici_adi" class="form-control bg-dark border-secondary" id="kullaniciAdi" placeholder="admin" required autofocus>
            <label for="kullaniciAdi">Kullanıcı Adı</label>
        </div>
        <div class="form-floating mb-4">
            <input type="password" name="sifre" class="form-control bg-dark border-secondary" id="sifre" placeholder="Şifre" required>
            <label for="sifre">Şifre</label>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold fs-5"><i class="bi bi-box-arrow-in-right me-2"></i>Giriş Yap</button>
    </form>
</div>

</body>
</html>