<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.php");
    exit;
}
include 'inc/db_mysqli.php';

$greska = $uspeh = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $naziv = trim($_POST['naziv']);
    $opis = trim($_POST['opis']);
    $datum = $_POST['datum'];
    $mesto = trim($_POST['mesto']);
    $lokacija = trim($_POST['lokacija']);
    $cena = floatval($_POST['cena']);
    $broj_karata = intval($_POST['broj_karata']);
    $dostupne__karta = $broj_karata;

    $dostupne_karata = isset($broj_karata) ? $broj_karata : 0;

    $slika = null;
    if (!empty($_FILES['slika']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $slika = $targetDir . basename($_FILES['slika']['name']);
        move_uploaded_file($_FILES['slika']['tmp_name'], $slika);
    }

   $stmt = $conn->prepare("INSERT INTO dogadjaji (naziv, opis, datum, mesto, lokacija, cena, broj_karata, dostupne_karata, slika) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssdiis", $naziv, $opis, $datum, $mesto, $lokacija, $cena, $broj_karata, $dostupne_karata, $slika);

    if ($stmt->execute()) {
        $uspeh = "Događaj uspešno dodat!";
    } else {
        $greska = "Greška pri dodavanju događaja.";
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Dodaj događaj</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navigacija.php'; ?>
<div class="container mt-4">
    <h2>Dodaj novi događaj</h2>
    <?php if ($greska): ?><div class="alert alert-danger"><?= $greska ?></div><?php endif; ?>
    <?php if ($uspeh): ?><div class="alert alert-success"><?= $uspeh ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3"><label class="form-label">Naziv</label><input type="text" name="naziv" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Opis</label><textarea name="opis" class="form-control" required></textarea></div>
        <div class="mb-3"><label class="form-label">Datum i vreme</label><input type="datetime-local" name="datum" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Mesto</label><input type="text" name="mesto" class="form-control" required></div> <!-- dodato mesto -->
        <div class="mb-3"><label class="form-label">Lokacija</label><input type="text" name="lokacija" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Cena (RSD)</label><input type="number" step="0.01" name="cena" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Broj dostupnih karata</label><input type="number" name="broj_karata" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Slika događaja</label><input type="file" name="slika" class="form-control"></div>
        <button type="submit" class="btn btn-primary">Dodaj događaj</button>
    </form>
</div>
</body>
</html>
