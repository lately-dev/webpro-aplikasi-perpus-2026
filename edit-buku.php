<?php
    $host = "localhost";
    $user = "root";
    $password = "";
    $db = "perpus";

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
        die("Koneksi gagal: " . $e->getMessage());
    }

    $id = $_GET["id"] ?? null;

    if (!$id || !ctype_digit((string) $id)) {
        header("Location: buku.php");
        exit;
    }

    $sql = "SELECT * FROM buku WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([":id" => $id]);

    $data_buku = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data_buku) {
        header("Location: buku.php?status=not_found");
        exit;
    }
    
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $judul = trim($_POST["judul"] ?? "");
        $penulis = trim($_POST["penulis"] ?? "");
        $tahun = trim($_POST["tahun"] ?? "");
        $stok = trim($_POST["stok"] ?? "");

        try {
            $sql = "UPDATE buku SET
                        judul = :judul,
                        penulis = :penulis,
                        tahun_terbit = :tahun,
                        stok = :stok
                    WHERE id = :id";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ":judul" => $judul,
                ":penulis" => $penulis,
                ":tahun" => $tahun,
                ":stok" => $stok,
                ":id" => $id
            ]);

            header("Location: buku.php?status=updated");
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = "Gagal memperbarui data buku.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Edit</title>
</head>
<body>
    <!--<header class="bg-primary text-white p-3">
        <h1 class="h3 m-0">Website Praktik</h1>
    </header>-->

    <nav class="navbar navbar-dark bg-success">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Website Praktik</a>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
        </div>
    </nav>

    <div class="container-fluid my-4">
        <div class="row">
            <aside class="col-md-3">
                <div class="bg-light border rounded p-3">
                <h2>Menu</h2>
                <ul class="list-group">
                    <li class="list-group-item"><a href="buku.php" class="text-reset text-decoration-none fw-bold">Dashboard</a></li>
                    <li class="list-group-item"><a href="#" class="text-reset text-decoration-none">Daftar Buku</a></li>
                    <li class="list-group-item">Form Peminjaman</li>
                </ul>
                </div>
            </aside>

            <main class="col-md-9">
                <section>
                    <h2>Form Edit Buku</h2>
                    <form method="POST" id="form-buku" novalidate>
                        <input type="hidden" name="id" value="<?= htmlspecialchars($data_buku['id'] ?? '') ?>" />
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="judul" class="form-label">Judul Buku</label>
                                <input type="text" class="form-control" name="judul" id="judul" value="<?= htmlspecialchars($data_buku['judul'] ?? '') ?>" required minlength="3"> 
                                <div class="invalid-feedback"> 
                                    Minimal 3 karakter
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="penulis" class="form-label">Penulis</label>
                                <input type="text" class="form-control" name="penulis" id="penulis" value="<?= htmlspecialchars($data_buku['penulis'] ?? '') ?>" required>
                                <div class="invalid-feedback">
                                    Isi Penulis
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="terbit" class="form-label">Tahun Terbit</label>
                                <input type="number" class="form-control" min="1900" max="2024" name="tahun" id="terbit" value="<?= htmlspecialchars($data_buku['tahun_terbit'] ?? '') ?>" required>
                                <div class="invalid-feedback">
                                    Isi Tahun Terbit
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="stok" class="form-label">Stok</label>
                                <input type="number" class="form-control" min="0" step="1" name="stok" id="stok" value="<?= htmlspecialchars($data_buku['stok'] ?? '') ?>" required>
                                <div class="invalid-feedback">
                                    Stok tidak boleh kosong
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning">Edit Buku</button>
                    </form>
                </section>

                <div class="mt-4">
                    <?php
                        $status = $_GET['status'] ?? '';

                        if ($status === 'success') {
                            echo '<div class="alert alert-success">
                                    Buku berhasil diedit!
                                </div>';
                        } elseif ($status === 'error') {
                            echo '<div class="alert alert-danger">
                                    Gagal mengedit buku.
                                </div>';
                        }
                    ?>
                </div>
            </main>
        </div>
    </div>
    <footer class="bg-light text-center">
        <hr class="m-0">
        <p class="p-3 mb-0">&copy; 2026 Website Praktik. All rights reserved.</p>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>