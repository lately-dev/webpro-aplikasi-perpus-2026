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

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: buku.php");
        exit;
    }

    $id = $_POST["id"] ?? null;

    if (!$id || !ctype_digit((string) $id)) {
        header("Location: buku.php?status=error");
        exit;
    }

    try {
        $sql = "DELETE FROM buku WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->execute([":id" => $id]);

        if ($stmt->rowCount() > 0) {
            header("Location: buku.php?status=deleted");
        } else {
            header("Location: buku.php?status=not_found");
        }

        exit;

    } catch (PDOException $e) {
        error_log($e->getMessage());

        header("Location: buku.php?status=error");
        exit;
    }
?>