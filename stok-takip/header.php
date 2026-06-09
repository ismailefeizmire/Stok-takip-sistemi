<?php
// Oturumu başlat
session_start();

// İŞTE HAYAT KURTARAN KOD BURADA: Output Buffering (Çıktı Tamponlama)
ob_start(); 

if (!isset($_SESSION['admin_giris'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Takip Yazılımı</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <script>
        const savedTheme = localStorage.getItem('stokTema') || 'dark';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        
        /* KARANLIK TEMA ÖZELLEŞTİRMELERİ */
        [data-bs-theme="dark"] body { background-color: #121212; color: #e0e0e0; }
        [data-bs-theme="dark"] .navbar { background-color: #1a1d20 !important; }
        [data-bs-theme="dark"] .card { background-color: #1e2125; border-color: #2c3034; }
        
        /* AYDINLIK TEMA ÖZELLEŞTİRMELERİ */
        [data-bs-theme="light"] body { background-color: #f8f9fa; color: #212529; }
        [data-bs-theme="light"] .navbar { background-color: #ffffff !important; border-bottom: 1px solid #dee2e6; }
        [data-bs-theme="light"] .navbar-brand, [data-bs-theme="light"] .nav-link { color: #212529 !important; }
        [data-bs-theme="light"] .card { background-color: #ffffff; border-color: #dee2e6; }
        
        [data-bs-theme="light"] .bg-dark { background-color: #ffffff !important; }
        [data-bs-theme="light"] .text-white { color: #212529 !important; }
        [data-bs-theme="light"] .border-secondary { border-color: #dee2e6 !important; }
        [data-bs-theme="light"] .table-dark { 
            --bs-table-bg: #ffffff; 
            --bs-table-color: #212529; 
            --bs-table-border-color: #dee2e6;
            --bs-table-hover-bg: rgba(0,0,0,0.05);
            color: #212529;
        }
        [data-bs-theme="light"] .form-control, 
        [data-bs-theme="light"] .form-select { 
            background-color: #ffffff !important; 
            color: #212529 !important; 
            border-color: #ced4da !important; 
        }
        [data-bs-theme="light"] .modal-content { background-color: #ffffff !important; border-color: #dee2e6; }
        [data-bs-theme="light"] .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
        [data-bs-theme="light"] #temaDegistirBtn { color: #212529; border-color: #212529; }

        /* GENEL GÖRÜNÜM AYARLARI */
        .navbar { box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: background-color 0.3s; }
        .card { border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s, background-color 0.3s; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important; }
        .btn { border-radius: 8px; font-weight: 500; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark mb-4 py-3">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="index.php">
                <i class="bi bi-box-seam text-primary me-2"></i>Stok Takip Sistemi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-list-ul me-1"></i>Ürün Listesi</a></li>
                    <li class="nav-item"><a class="nav-link" href="urun_ekle.php"><i class="bi bi-plus-circle me-1"></i>Yeni Ürün</a></li>
                    <li class="nav-item"><a class="nav-link" href="kategoriler.php"><i class="bi bi-tags me-1"></i>Kategoriler</a></li>
                    
                    <?php if (isset($_SESSION['yetki']) && $_SESSION['yetki'] == 'admin'): ?>
                        <li class="nav-item"><a class="nav-link" href="kullanicilar.php"><i class="bi bi-people-fill me-1"></i>Kullanıcılar</a></li>
                    <?php endif; ?>
                    
                    <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                        <button class="btn btn-outline-light btn-sm px-3 py-2" id="temaDegistirBtn" title="Temayı Değiştir">
                            <i class="bi bi-sun-fill" id="temaIkon"></i>
                        </button>
                    </li>

                    <li class="nav-item ms-lg-2 mt-2 mt-lg-0">
                        <a class="btn btn-danger btn-sm px-3 py-2" href="cikis.php"><i class="bi bi-box-arrow-right me-1"></i>Çıkış</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const temaBtn = document.getElementById('temaDegistirBtn');
            const temaIkon = document.getElementById('temaIkon');
            const htmlElement = document.documentElement;

            function ikonGuncelle(tema) {
                if (tema === 'dark') {
                    temaIkon.classList.replace('bi-moon-fill', 'bi-sun-fill');
                    if(temaBtn.classList.contains('btn-outline-dark')) {
                        temaBtn.classList.replace('btn-outline-dark', 'btn-outline-light');
                    }
                } else {
                    temaIkon.classList.replace('bi-sun-fill', 'bi-moon-fill');
                    if(temaBtn.classList.contains('btn-outline-light')) {
                        temaBtn.classList.replace('btn-outline-light', 'btn-outline-dark');
                    }
                }
            }

            ikonGuncelle(htmlElement.getAttribute('data-bs-theme'));

            temaBtn.addEventListener('click', () => {
                const mevcutTema = htmlElement.getAttribute('data-bs-theme');
                const yeniTema = mevcutTema === 'dark' ? 'light' : 'dark';
                
                htmlElement.setAttribute('data-bs-theme', yeniTema);
                localStorage.setItem('stokTema', yeniTema);
                ikonGuncelle(yeniTema);
            });
        });
    </script>

    <div class="container"></div>