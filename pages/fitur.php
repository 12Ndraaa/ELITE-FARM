<?php
// PENTING: session_start() HARUS dipanggil di awal setiap file PHP
// yang membutuhkan fitur session (seperti untuk login/logout atau keranjang).
// Pastikan tidak ada output HTML sebelum ini.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitur Unggulan - SmartFarm</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../src/style/style.css">

    <style>
    /* Konten di bawah ini adalah style spesifik untuk fitur.php saja */

    .container-main {
        /* Sesuaikan margin-top dengan tinggi navbar Anda (fixed-top) */
        margin-top: 100px;
        padding-bottom: 50px;
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://via.placeholder.com/1920x600/28a745/ffffff?text=SmartFarm+Features+Banner') no-repeat center center;
        background-size: cover;
        color: white;
        padding: 80px 0;
        text-align: center;
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
        margin-bottom: 50px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .hero-section h1 {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    }
    .hero-section p {
        font-size: 1.25rem;
        max-width: 800px;
        margin: 0 auto 30px auto;
        line-height: 1.6;
    }

    /* Features Grid */
    .features-grid .feature-item {
        background-color: white;
        border-radius: 0.75rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 30px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%; /* Ensure equal height cards */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
    }
    .features-grid .feature-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }
    .features-grid .feature-item .icon {
        font-size: 3.5rem;
        color: #28a745; /* SmartFarm green */
        margin-bottom: 20px;
    }
    .features-grid .feature-item h3 {
        font-size: 1.75rem;
        font-weight: 600;
        color: #343a40;
        margin-bottom: 15px;
    }
    .features-grid .feature-item p {
        font-size: 1rem;
        color: #6c757d;
        line-height: 1.6;
        flex-grow: 1; /* Allows paragraph to take remaining space */
    }

    /* How It Works / Benefits Section */
    .how-it-works-section {
        background-color: #e9f7ee; /* Light green background */
        padding: 60px 0;
        margin-top: 50px;
        border-radius: 1rem;
    }
    .how-it-works-section h2 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #28a745;
        margin-bottom: 40px;
        text-align: center;
    }
    .how-it-works-section .step {
        display: flex;
        align-items: flex-start;
        margin-bottom: 30px;
        position: relative;
    }
    .how-it-works-section .step .step-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #28a745;
        margin-right: 20px;
        background-color: white;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .how-it-works-section .step-content h4 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #343a40;
        margin-bottom: 10px;
    }
    .how-it-works-section .step-content p {
        font-size: 1rem;
        color: #5a6268;
        line-height: 1.6;
    }

    /* CTA Section */
    .cta-section {
        background-color: #28a745; /* SmartFarm green */
        color: white;
        padding: 60px 0;
        text-align: center;
        border-radius: 1rem;
        margin-top: 50px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .cta-section h2 {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    .cta-section p {
        font-size: 1.15rem;
        margin-bottom: 30px;
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
    }
    .cta-section .btn {
        font-size: 1.1rem;
        padding: 12px 30px;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .cta-section .btn-light {
        color: #28a745;
        background-color: white;
        border-color: white;
    }
    .cta-section .btn-light:hover {
        background-color: #e2e6ea;
        border-color: #dae0e5;
        transform: translateY(-2px);
    }

    /* General sections spacing */
    section {
        margin-bottom: 60px;
    }
    section:last-of-type {
        margin-bottom: 0;
    }
    </style>
</head>
<body>
    <?php include '../components/navbar.php'; ?>

    <div class="container-fluid hero-section">
        <div class="container">
            <h1>Maksimalkan Pertanian Anda dengan SmartFarm</h1>
            <p>Temukan bagaimana teknologi canggih kami merevolusi cara Anda bertani, dari pemantauan cerdas hingga irigasi otomatis, demi hasil yang lebih melimpah.</p>
            <a href="#fitur-utama" class="btn btn-light btn-lg">Jelajahi Fitur Unggulan <i class="fas fa-arrow-down ms-2"></i></a>
        </div>
    </div>

    <div class="container container-main">
        <section id="fitur-utama">
            <h2 class="text-center mb-5 fw-bold" style="color: #28a745;">Inovasi yang Mengubah Pertanian Anda</h2>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 features-grid">
                <div class="col">
                    <div class="feature-item">
                        <div class="icon"><i class="fas fa-seedling"></i></div>
                        <h3>Pemantauan Real-time Tanaman & Tanah</h3>
                        <p>Sensor IoT presisi tinggi memantau kelembaban tanah, suhu, pH, dan nutrisi esensial 24/7, memberikan data akurat langsung ke perangkat Anda.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-item">
                        <div class="icon"><i class="fas fa-water"></i></div>
                        <h3>Sistem Irigasi Cerdas & Otomatis</h3>
                        <p>Sistem kami secara otomatis menyesuaikan penyiraman berdasarkan kebutuhan tanaman dan kondisi cuaca, menghemat air hingga 50% dan energi.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-item">
                        <div class="icon"><i class="fas fa-drone"></i></div>
                        <h3>Analisis Kesehatan Tanaman via Drone</h3>
                        <p>Drone dilengkapi kamera multispektral memberikan gambaran detail kesehatan tanaman dan deteksi dini masalah, mempercepat tindakan korektif.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-item">
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                        <h3>Prediksi Panen & Optimalisasi Hasil</h3>
                        <p>Algoritma AI menganalisis data historis dan kondisi terkini untuk memprediksi hasil panen dan menyarankan tindakan optimalisasi.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-item">
                        <div class="icon"><i class="fas fa-cloud-sun"></i></div>
                        <h3>Kontrol Lingkungan Terpadu</h3>
                        <p>Atur suhu, kelembaban, dan pencahayaan (untuk greenhouse) secara otomatis, menciptakan kondisi ideal untuk setiap jenis tanaman.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-item">
                        <div class="icon"><i class="fas fa-bell"></i></div>
                        <h3>Notifikasi Instan & Peringatan Dini</h3>
                        <p>Dapatkan peringatan otomatis via aplikasi seluler untuk setiap anomali atau kebutuhan perawatan mendesak pada pertanian Anda.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-item">
                        <div class="icon"><i class="fas fa-tractor"></i></div>
                        <h3>Manajemen Peralatan Otomatis</h3>
                        <p>Integrasi dengan peralatan pertanian pintar memungkinkan kontrol dan penjadwalan otomatis untuk efisiensi operasional maksimal.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-item">
                        <div class="icon"><i class="fas fa-lightbulb"></i></div>
                        <h3>Konsultasi Ahli Pertanian Digital</h3>
                        <p>Akses ke tim ahli pertanian kami melalui platform, memberikan saran dan solusi berbasis data untuk tantangan spesifik Anda.</p>
                    </div>
                </div>
                <div class="col">
                    <div class="feature-item">
                        <div class="icon"><i class="fas fa-shield-alt"></i></div>
                        <h3>Keamanan Data & Privasi Terjamin</h3>
                        <p>Kami menjamin keamanan data pertanian Anda dengan enkripsi canggih dan kepatuhan standar privasi tertinggi.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="how-it-works-section">
            <h2 class="mb-5">Transformasi Pertanian dalam 3 Langkah Mudah</h2>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Instalasi & Konfigurasi Mudah</h4>
                                <p>Tim SmartFarm akan membantu Anda dalam instalasi sensor dan perangkat di lahan Anda. Konfigurasi awal dapat diselesaikan dengan cepat melalui panduan interaktif kami.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Pantau & Analisis Data Anda</h4>
                                <p>Setelah terpasang, sistem akan mulai mengumpulkan data secara otomatis. Anda dapat memantau semua informasi penting melalui dashboard web atau aplikasi seluler kami yang intuitif.</p>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Ambil Keputusan Cerdas & Panen Lebih Baik</h4>
                                <p>Dengan insight berbasis data dan rekomendasi dari AI, Anda dapat membuat keputusan yang lebih tepat untuk irigasi, pemupukan, dan pengendalian hama, yang berujung pada peningkatan hasil dan profitabilitas.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section">
            <h2>Siap untuk Pertanian Masa Depan?</h2>
            <p>Jangan biarkan potensi lahan Anda terbuang. Hubungi kami hari ini untuk konsultasi gratis dan lihat bagaimana SmartFarm dapat membawa pertanian Anda ke level selanjutnya.</p>
            <a href="/ELITE-FARM/pages/kontak.php" class="btn btn-light btn-lg"><i class="fas fa-envelope me-2"></i> Minta Demo Gratis</a>
        </section>
    </div>

    <?php include '../components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="../src/js/script.js"></script>
    <script>
        $(document).ready(function() {
            // Placeholder for cart count update, assuming your navbar has a cart-count element
            // This would ideally be dynamically loaded from a session or database in a full application.
            function updateCartCount(count) {
                $('.cart-count').text(count);
            }
            // Inisialisasi cart count menjadi 0 saat halaman dimuat
            // Ini bisa diganti dengan logika fetch dari session/database jika diperlukan
            updateCartCount(0); 
        });
    </script>
</body>
</html>