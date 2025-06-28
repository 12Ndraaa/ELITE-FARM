<?php
session_start(); // Selalu mulai sesi di bagian paling atas setiap file PHP

// --- Pemeriksaan Akses (Access Control) ---
// Periksa apakah pengguna sudah login, memiliki sesi yang aktif,
// dan memiliki peran (role) sebagai 'admin'.
// Jika salah satu kondisi tidak terpenuhi, pengguna akan dialihkan
// ke halaman login dan eksekusi skrip akan dihentikan.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect ke halaman login admin. Sesuaikan path jika berbeda.
    header('Location: ../admin/login.php');
    exit(); // Sangat penting: hentikan eksekusi skrip setelah header redirect
}

// --- Koneksi Database ---
// Menggunakan file koneksi database yang terpisah untuk menjaga kebersihan kode.
require_once '../connection.php'; // Pastikan path ini benar relatif terhadap manajemen-produk.php

$success_message = "";
$error_message = "";
$edit_product = null; // Variabel untuk menyimpan data produk yang akan diedit

// --- Fungsi CRUD ---

// 1. Tambah Produk (Create)
if (isset($_POST['add_product'])) {
    $kode = htmlspecialchars($_POST['kode']);
    $nama = htmlspecialchars($_POST['nama']);
    $satuan = htmlspecialchars($_POST['satuan']);
    $harga = htmlspecialchars($_POST['harga']);
    $image = ''; // Menggunakan 'image' sesuai rulebook

    // Proses upload gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $base_project_dir = dirname(__DIR__); 
        $target_dir = $base_project_dir . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR; 
        
        if (!is_dir($target_dir)) {
            $error_message = "Folder upload tidak ditemukan atau tidak dapat diakses. Pastikan folder 'uploads' ada di direktori ELITE_FARM.";
        } elseif (!is_writable($target_dir)) {
            $error_message = "Folder upload tidak memiliki izin tulis. Pastikan izin 'Write/Modify' diberikan.";
        }

        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            if (empty($error_message) && move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // File berhasil diupload
            } else if (empty($error_message)) {
                $error_message = "Maaf, terjadi kesalahan saat mengupload file gambar. (Error M_U_F)";
                $image = ''; // Reset jika gagal upload
            }
        } else {
            $error_message = "File bukan gambar yang valid atau rusak.";
            $image = '';
        }
    }

    if (empty($error_message)) {
        $stmt = $con->prepare("INSERT INTO produk (kode, nama, satuan, harga, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdds", $kode, $nama, $satuan, $harga, $image);

        if ($stmt->execute()) {
            $success_message = "Produk berhasil ditambahkan!";
        } else {
            if ($stmt->errno == 1062) {
                $error_message = "Kode produk '$kode' sudah ada. Harap gunakan kode lain.";
            } else {
                $error_message = "Gagal menambahkan produk: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

// 2. Edit Produk (Update) - Mengisi form
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $con->prepare("SELECT id, kode, nama, satuan, harga, image FROM produk WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $edit_product = $result->fetch_assoc();
    } else {
        $error_message = "Produk tidak ditemukan.";
    }
    $stmt->close();
}

// 2. Edit Produk (Update) - Memproses form
if (isset($_POST['update_product'])) {
    $id = htmlspecialchars($_POST['product_id']);
    $kode = htmlspecialchars($_POST['kode']);
    $nama = htmlspecialchars($_POST['nama']);
    $satuan = htmlspecialchars($_POST['satuan']);
    $harga = htmlspecialchars($_POST['harga']);
    $current_image = htmlspecialchars($_POST['current_image']);

    $image_update = $current_image;

    // Proses upload gambar baru (jika ada)
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $base_project_dir = dirname(__DIR__);
        $target_dir = $base_project_dir . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
        
        if (!is_dir($target_dir)) {
            $error_message = "Folder upload tidak ditemukan atau tidak dapat diakses. Pastikan folder 'uploads' ada di direktori ELITE_FARM.";
        } elseif (!is_writable($target_dir)) {
            $error_message = "Folder upload tidak memiliki izin tulis. Pastikan izin 'Write/Modify' diberikan.";
        }

        $image_new = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_new;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            if (empty($error_message) && move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_update = $image_new;
            } else if (empty($error_message)) {
                $error_message = "Maaf, terjadi kesalahan saat mengupload file gambar baru. (Error M_U_F)";
            }
        } else {
            $error_message = "File bukan gambar yang valid atau rusak.";
        }
    }

    if (empty($error_message)) {
        $stmt = $con->prepare("UPDATE produk SET kode = ?, nama = ?, satuan = ?, harga = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sssdsi", $kode, $nama, $satuan, $harga, $image_update, $id);

        if ($stmt->execute()) {
            $success_message = "Produk berhasil diperbarui!";
            $edit_product = null; // Sembunyikan form edit setelah update
        } else {
             if ($stmt->errno == 1062) {
                $error_message = "Kode produk '$kode' sudah ada. Harap gunakan kode lain.";
            } else {
                $error_message = "Gagal memperbarui produk: " . $stmt->error;
            }
        }
        $stmt->close();
    }
}

// 3. Hapus Produk (Delete)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $con->prepare("SELECT image FROM produk WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product_to_delete = $result->fetch_assoc();
    $stmt->close();

    if ($product_to_delete && !empty($product_to_delete['image'])) {
        $base_project_dir = dirname(__DIR__);
        $image_path = $base_project_dir . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $product_to_delete['image']; 
        if (file_exists($image_path)) {
            // unlink($image_path); // Uncomment untuk menghapus file gambar fisik
        }
    }

    $stmt = $con->prepare("DELETE FROM produk WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $success_message = "Produk berhasil dihapus!";
    } else {
        $error_message = "Gagal menghapus produk: " . $stmt->error;
    }
    $stmt->close();
}

// 4. Baca Produk (Read) - Ambil semua produk untuk ditampilkan
$products = [];
// Asumsi: Anda memiliki tabel bernama 'produk' dengan kolom 'id', 'kode', 'nama', 'satuan', 'harga', 'image'.
$result = $con->query("SELECT id, kode, nama, satuan, harga, image FROM produk ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    $result->free();
} else {
    $error_message = "Gagal mengambil data produk: " . $con->error;
}

$con->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - SmartFarm Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
    <link rel="stylesheet" href="../src/style/style.css">
    <style>
        /* Gaya dasar untuk body dan font */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa; /* Warna latar belakang cerah */
        }
        /* Penyesuaian margin top untuk konten agar tidak tertutup navbar fixed-top */
        .container-main {
            margin-top: 100px; /* Memberikan ruang lebih dari navbar */
            padding-bottom: 50px; /* Ruang di bagian bawah */
        }
        /* Styling untuk judul halaman */
        h2 {
            color: #343a40; /* Warna teks gelap */
            margin-bottom: 30px; /* Jarak bawah yang cukup */
            text-align: center; /* Posisikan di tengah */
            font-weight: 600;
        }
        /* Gaya untuk tombol utama (Tambah Produk) */
        .btn-success-custom {
            background-color: #28a745; /* Warna hijau Bootstrap success */
            border-color: #28a745;
            padding: 10px 20px;
            border-radius: .5rem;
            font-weight: 500;
            transition: all 0.3s ease; /* Transisi halus saat hover */
        }
        .btn-success-custom:hover {
            background-color: #218838; /* Warna hijau sedikit lebih gelap saat hover */
            border-color: #1e7e34;
            transform: translateY(-2px); /* Efek angkat sedikit */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Tambah shadow */
        }
        /* Styling untuk tabel */
        .table-custom {
            background-color: #ffffff; /* Latar belakang tabel putih */
            border-radius: .75rem; /* Sudut membulat */
            overflow: hidden; /* Penting untuk radius di sudut tabel */
            box-shadow: 0 0 20px rgba(0,0,0,0.05); /* Sedikit bayangan untuk efek "melayang" */
        }
        .table-custom thead th {
            background-color: #f0f2f5; /* Latar belakang header abu-abu muda */
            color: #495057; /* Warna teks gelap untuk header */
            font-weight: 600;
            vertical-align: middle;
            padding: 12px 15px;
        }
        .table-custom tbody tr:hover {
            background-color: #f1f3f5; /* Warna latar belakang saat hover pada baris */
        }
        .table-custom tbody td {
            vertical-align: middle;
            padding: 12px 15px;
        }
        /* Gaya untuk tombol aksi di dalam tabel */
        .table-actions .btn {
            border-radius: .3rem;
            font-size: 0.85rem;
            padding: 6px 10px;
            margin-right: 5px;
        }
        .table-actions .btn-info {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }
        .table-actions .btn-info:hover {
            background-color: #138496;
            border-color: #117a8b;
        }
        .table-actions .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            color: white;
        }
        .table-actions .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        /* CSS untuk Navbar dan Efek Scroll */
        .navbar {
            transition: background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            background-color: transparent; /* Awalnya transparan */
            box-shadow: none;
            position: fixed; /* Penting agar bisa scroll */
            width: 100%;
            z-index: 1030; /* Di atas sidebar */
        }
        .navbar.scrolled {
            background-color: #ffffff !important; /* Warna putih saat di-scroll */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); /* Tambah shadow */
        }
        .table img {
            max-width: 80px;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include '../components/navbar.php'; // Include navbar ?>

    <div class="container container-main">
        <h2 class="mb-4">Manajemen Produk</h2>
        <p class="text-center text-muted mb-4">Halaman ini adalah dashboard untuk mengelola daftar produk Anda. Hanya dapat diakses oleh Admin.</p>

        <?php if (isset($success_message) && $success_message != ""): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($error_message) && $error_message != ""): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-end mb-4">
            <a href="#productForm" class="btn btn-success-custom" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="productForm">
                <i class="fas fa-plus me-1"></i> Tambah Produk Baru
            </a>
        </div>

        <div class="collapse mb-4 <?php echo ($edit_product || isset($_POST['add_product']) || isset($_POST['update_product'])) ? 'show' : ''; ?>" id="productForm">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <?php echo $edit_product ? 'Edit Produk' : 'Tambah Produk Baru'; ?>
                </div>
                <div class="card-body">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <?php if ($edit_product): ?>
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($edit_product['id']); ?>">
                            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($edit_product['image']); ?>">
                        <?php endif; ?>
                        
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode</label>
                            <input type="text" class="form-control" id="kode" name="kode" 
                                value="<?php echo $edit_product ? htmlspecialchars($edit_product['kode']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" 
                                value="<?php echo $edit_product ? htmlspecialchars($edit_product['nama']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="satuan" class="form-label">Satuan</label>
                            <input type="text" class="form-control" id="satuan" name="satuan" 
                                value="<?php echo $edit_product ? htmlspecialchars($edit_product['satuan']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" step="0.01" 
                                value="<?php echo $edit_product ? htmlspecialchars($edit_product['harga']) : ''; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Gambar</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <?php if ($edit_product && !empty($edit_product['image'])): ?>
                                <small class="text-muted mt-2">Gambar saat ini: <a href="../uploads/<?php echo htmlspecialchars($edit_product['image']); ?>" target="_blank"><?php echo htmlspecialchars($edit_product['image']); ?></a></small><br>
                                <img src="../uploads/<?php echo htmlspecialchars($edit_product['image']); ?>" alt="Current Image" style="max-width: 100px; margin-top: 10px;">
                            <?php endif; ?>
                        </div>
                        <button type="submit" name="<?php echo $edit_product ? 'update_product' : 'add_product'; ?>" 
                                class="btn btn-success">
                            <?php echo $edit_product ? '<i class="fas fa-save me-1"></i> Update' : '<i class="fas fa-plus me-1"></i> Save'; ?>
                        </button>
                        <?php if ($edit_product): ?>
                            <a href="manajemen-produk.php?action=delete&id=<?php echo htmlspecialchars($edit_product['id']); ?>" class="btn btn-danger btn-action" onclick="return confirm('Anda yakin ingin menghapus produk ini?');">
                                <i class="fas fa-trash-alt me-1"></i> Delete
                            </a>
                        <?php endif; ?>
                        <a href="manajemen-produk.php" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover table-custom">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['nama']); ?>">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['kode']); ?></td>
                                <td><?php echo htmlspecialchars($product['nama']); ?></td>
                                <td><?php echo htmlspecialchars($product['satuan']); ?></td>
                                <td>Rp <?php echo number_format(htmlspecialchars($product['harga']), 0, ',', '.'); ?></td>
                                <td class="text-center table-actions">
                                    <a href="manajemen-produk.php?action=edit&id=<?php echo htmlspecialchars($product['id']); ?>#productForm" class="btn btn-info"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="manajemen-produk.php?action=delete&id=<?php echo htmlspecialchars($product['id']); ?>" class="btn btn-danger" onclick="return confirm('Anda yakin ingin menghapus produk ini?');"><i class="fas fa-trash-alt"></i> Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">Tidak ada produk yang tersedia.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../src/js/script.js"></script>
    <script>
        // Animasi Navbar saat scroll
        $(window).scroll(function() {
            if ($(this).scrollTop() > 50) {
                $('.navbar').addClass('scrolled');
            } else {
                $('.navbar').removeClass('scrolled');
            }
        });

        // JS untuk scroll ke form jika edit atau setelah submit
        document.addEventListener('DOMContentLoaded', function() {
            var productForm = document.getElementById('productForm');
            if (window.location.hash === '#productForm' || productForm.classList.contains('show')) {
                productForm.scrollIntoView({ behavior: 'smooth' });
            }
        });
    </script>
</body>
</html>