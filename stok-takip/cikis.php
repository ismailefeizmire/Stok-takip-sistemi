<?php
session_start();
// Tüm oturum değişkenlerini sil
session_unset();
// Oturumu tamamen yok et
session_destroy();

// Çıkış yaptıktan sonra tekrar giriş sayfasına yönlendir
header("Location: login.php");
exit;
?>