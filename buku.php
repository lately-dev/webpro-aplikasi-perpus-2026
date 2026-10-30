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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Profile</title>

    <style>
        .bg-shadow { background-color: #00000086;  }
    </style>
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
        <div class="container-fluid d-none fixed-top bg-shadow vh-100 d-flex align-items-center justify-content-center" id="dialog">
            <div class="p-4 text-center bg-light rounded">
                <h2>Apakah kamu yakin?</h2>
                <p id="judulHapus" class="fw-bold text-danger" class="m-0"></p>
                <p class="mb-3">Buku yang dihapus tidak dapat dikembalikan.</p>
                <form action="hapus-buku.php" method="POST">
                    <input type="hidden" name="id" id="idHapus">
                    <button type="submit" class="btn btn-danger">Ya</button>
                    <button type="button" onclick="tutupDialog()" class="btn btn-outline-success">Tidak</button>
                </form>
            </div>
        </div>

        <div class="row">
            <aside class="col-md-3">
                <div class="bg-light border rounded p-3">
                <h2>Menu</h2>
                <ul class="list-group">
                    <li class="list-group-item"><a href="dashboard.html" class="text-reset text-decoration-none fw-bold">Dashboard</a></li>
                    <li class="list-group-item"><a href="#" class="text-reset text-decoration-none">Daftar Buku</a></li>
                    <li class="list-group-item">Form Peminjaman</li>
                </ul>
                </div>
            </aside>

            <main class="col-md-9">
                <section>
                    <h2>Form Tambah Buku</h2>
                    <form method="POST" id="form-buku" novalidate>
                        <input type="hidden" name="id" value="" />
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="judul" class="form-label">Judul Buku</label>
                                <input type="text" class="form-control" name="judul" id="judul" required minlength="3"> 
                                <div class="invalid-feedback"> 
                                    Minimal 3 karakter
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="penulis" class="form-label">Penulis</label>
                                <input type="text" class="form-control" name="penulis" id="penulis" required>
                                <div class="invalid-feedback">
                                    Isi Penulis
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="terbit" class="form-label">Tahun Terbit</label>
                                <input type="number" class="form-control" min="1900" max="2024" name="tahun" id="terbit" required>
                                <div class="invalid-feedback">
                                    Isi Tahun Terbit
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="stok" class="form-label">Stok</label>
                                <input type="number" class="form-control" min="0" step="1" name="stok" id="stok" required>
                                <div class="invalid-feedback">
                                    Stok tidak boleh kosong
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">Tambah Buku</button>
                    </form>

                    <?php
                        if ($_SERVER["REQUEST_METHOD"] === "POST") {
                            $judul = trim($_POST["judul"]);
                            $penulis = trim($_POST["penulis"]);
                            $tahun = trim($_POST["tahun"]);
                            $stok = trim($_POST["stok"]);

                            try {
                                $sql = "INSERT INTO buku
                                        (judul, penulis, tahun_terbit, stok)
                                        VALUES (:judul, :penulis, :tahun, :stok)";

                                $stmt = $conn->prepare($sql);
                                $stmt->execute([
                                    ":judul" => $judul,
                                    ":penulis" => $penulis,
                                    ":tahun" => $tahun,
                                    ":stok" => $stok
                                ]);

                                header("Location: buku.php?status=success");
                                exit;

                            } catch (PDOException $e) {
                                error_log($e->getMessage());

                                header("Location: buku.php?status=error");
                                exit;
                            }
                        }
                    ?>
                </section>

                <section class="border rounded p-3 mt-4">
                    <?php
                        $status = $_GET['status'] ?? '';

                        if ($status === 'success') {
                            echo '<div class="alert alert-success">
                                    Buku berhasil ditambahkan!
                                </div>';
                        } elseif ($status === 'error') {
                            echo '<div class="alert alert-danger">
                                    Gagal menambahkan buku.
                                </div>';
                        }
                    ?>
                    <h2>Daftar Buku</h2>
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul</th>
                                <th>Penulis</th>
                                <th>Tahun Terbit</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="daftar-buku">
                            <?php
                                $sql = "SELECT * FROM buku";
                                $stmt = $conn->query($sql);
                                $buku = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                foreach ($buku as $index => $row) {
                                    echo "<tr>";
                                    echo "<td>" . ($index + 1) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["judul"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["penulis"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["tahun_terbit"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["stok"]) . "</td>";
                                    echo "<td>";
                                    echo '<button class="btn btn-sm btn-warning" onclick="window.location.href=\'edit-buku.php?id=' . $row["id"] . '\'"> Edit </button>';
                                    echo "<button class='btn btn-sm btn-danger mx-2' onclick='konfirmasiHapus(" .(int) $row['id'] . "," . json_encode($row["judul"]) . ")'>Hapus</button>";
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </section>
            </main>
        </div>
    </div>
    <footer class="bg-light text-center">
        <hr class="m-0">
        <p class="p-3 mb-0">&copy; 2026 Website Praktik. All rights reserved.</p>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        const dialog = document.getElementById('dialog');

        function konfirmasiHapus(id, judul) {
            document.getElementById('idHapus').value = id;
            document.getElementById('judulHapus').textContent = judul;

            dialog.classList.remove('d-none');
        }

        function tutupDialog() {
            dialog.classList.remove('d-block');
            dialog.classList.add('d-none');
        }
    </script>
    <!--<script>
        let daftarBuku = [
            { id: 1, judul: "Laskar Pelangi", penulis: "Andrea Hirata", terbit: 2005, stok: 3 },
            { id: 2, judul: "Roby Si Pengarang", penulis: "Roby Ariga", terbit: 2019, stok: 5 },
            { id: 3, judul: "Lutfi Penulis", penulis: "Lutfi Ghifari", terbit: 2021, stok: 7 }
        ];
        let nextId = 4;

        const formbuku = document.getElementById("form-buku");

        function renderDaftarBuku() {
            const tabelbuku = document.getElementById("daftar-buku");
            tabelbuku.innerHTML = "";
            daftarBuku.forEach((buku, index) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${buku.judul}</td>
                    <td>${buku.penulis}</td>
                    <td>${buku.terbit}</td>
                    <td>${buku.stok}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editBuku(${buku.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="hapusBuku(${buku.id})">Hapus</button>
                    </td>
                `;
                tabelbuku.appendChild(row);
            });
        }   
        renderDaftarBuku();

        formbuku.addEventListener("submit", function(event) {
            event.preventDefault();
            if (!formbuku.checkValidity()) {
                event.stopPropagation();
                formbuku.classList.add("was-validated");
                return;
            }

            const id = formbuku.elements["id"].value;
            const judul = formbuku.elements["judul"].value;
            const penulis = formbuku.elements["penulis"].value;
            const terbit = parseInt(formbuku.elements["terbit"].value);
            const stok = parseInt(formbuku.elements["stok"].value);

            daftarBuku.push({ id: nextId++, judul, penulis, terbit, stok });
            formbuku.reset();
            formbuku.classList.remove("was-validated");
            renderDaftarBuku();
        });
    </script>-->
</body>
</html>
