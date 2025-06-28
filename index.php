<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Farm - Solusi Pertanian Cerdas</title>
    <!-- BOOTSTRAP CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="./src/style/style.css">
</head>
<body>
    <!-- NAVBAR -->
<?php 
include './components/navbar.php'
?>
    <!-- HERO -->
<section class="hero-section">
        <video autoplay loop muted playsinline poster="https://cdn.creazilla.com/videos/7942048/harvester-tractor-in-field-harvests-tractor-video-lg.mp4">
            <source src="https://cdn.pixabay.com/video/2023/10/13/184808-874264370_large.mp4" type="video/mp4">
            Browser Anda tidak mendukung tag video.
        </video>
        <div class="container">
            <h1>Masa Depan Pertanian Ada di Tangan Anda</h1>
            <p>Smart Farm menyediakan solusi inovatif untuk meningkatkan produktivitas, efisiensi, dan keberlanjutan pertanian melalui teknologi modern.</p>
            <a href="#products" class="btn btn-hero">Jelajahi Produk Kami <i class="fas fa-arrow-right ms-2"></i></a>
        </div>
    </section>

    <section id="features" class="container mb-5">
        <h2 class="section-title">Mengapa Memilih Smart Farm?</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="feature-card">
                    <div class="icon"><i class="fas fa-seedling"></i></div>
                    <h3>Produktivitas Unggul</h3>
                    <p>Optimalkan pertumbuhan tanaman dan hasil panen Anda dengan sistem pemantauan cerdas.</p>
                </div>
            </div>
            <div class="col">
                <div class="feature-card">
                    <div class="icon"><i class="fas fa-dollar-sign"></i></div>
                    <h3>Efisiensi Biaya</h3>
                    <p>Kurangi pemborosan air dan energi melalui otomatisasi dan analisis data yang akurat.</p>
                </div>
            </div>
            <div class="col">
                <div class="feature-card">
                    <div class="icon"><i class="fas fa-globe-americas"></i></div>
                    <h3>Pertanian Berkelanjutan</h3>
                    <p>Dukung praktik pertanian ramah lingkungan untuk masa depan yang lebih hijau.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="products" class="container mb-5">
        <h2 class="section-title">Produk Unggulan Smart Farm</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="product-card">
                    <img src="https://via.placeholder.com/400x200/28a745/ffffff?text=Sensor+Tanah" alt="Sensor Tanah Cerdas">
                    <div class="card-body">
                        <h5 class="card-title">Sensor Tanah Cerdas</h5>
                        <p class="card-text">Memantau kelembaban, pH, dan nutrisi tanah secara real-time untuk keputusan irigasi yang tepat.</p>
                        <div class="price">Rp 750.000</div>
                        <a href="#" class="btn btn-primary btn-sm btn-green mt-3"><i class="fas fa-shopping-cart me-4"></i> Beli Sekarang</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="product-card">
                    <img src="https://via.placeholder.com/400x200/28a745/ffffff?text=Sistem+Irigasi" alt="Sistem Irigasi Otomatis">
                    <div class="card-body">
                        <h5 class="card-title">Sistem Irigasi Otomatis</h5>
                        <p class="card-text">Sistem irigasi yang terintegrasi dengan sensor cuaca untuk penyiraman yang efisien.</p>
                        <div class="price">Rp 2.500.000</div>
                        <a href="#" class="btn btn-primary btn-sm btn-green mt-3"><i class="fas fa-shopping-cart me-1"></i> Beli Sekarang</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="product-card">
                    <img src="https://via.placeholder.com/400x200/28a745/ffffff?text=Drone+Pertanian" alt="Drone Pemantau Pertanian">
                    <div class="card-body">
                        <h5 class="card-title">Drone Pemantau Pertanian</h5>
                        <p class="card-text">Drone dengan kamera multispektral untuk analisis kesehatan tanaman dari udara.</p>
                        <div class="price">Rp 15.000.000</div>
                        <a href="#" class="btn btn-primary btn-sm btn-green mt-3"><i class="fas fa-shopping-cart me-1"></i> Beli Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container text-center mb-5">
        <div class="p-5 bg-white rounded-3 shadow-sm">
            <h2 class="section-title mb-4">Siap Mengubah Pertanian Anda?</h2>
            <p class="lead">Bergabunglah dengan revolusi pertanian cerdas bersama Smart Farm!</p>
            <a href="./admin/manajemen-produk.html" class="btn btn-hero btn-lg"><i class="fas fa-hand-point-right me-2"></i> Mulai Sekarang</a>
        </div>
    </section>
<!-- FOOOTER -->
 <?php 
 include './components/footer.php'
 ?> 
<!-- BOOTSTRAP SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- JS -->
     <script src="./src/js/script.js"></script>
</body>
</html>