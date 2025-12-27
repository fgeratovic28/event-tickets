<?php
session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['korpa'])) {
    header("Location: kupi.php");
    exit;
}

include 'inc/db_pdo.php';
include 'klase/Ulaznica.php';

$greska = "";
$uspeh = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ime = trim($_POST['ime_na_kartici'] ?? '');
    $broj = trim($_POST['broj_kartice'] ?? '');
    $datum = trim($_POST['datum_isteka'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');

    if (empty($ime) || empty($broj) || empty($datum) || empty($cvv)) {
        $greska = "Molimo popunite sva polja za plaćanje.";
    } elseif (!preg_match('/^[0-9]{16}$/', $broj)) {
        $greska = "Neispravan broj kartice (mora imati 16 cifara).";
    } elseif (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $datum)) {
        $greska = "Neispravan format datuma isteka (MM/YY).";
    } elseif (!preg_match('/^\d{3}$/', $cvv)) {
        $greska = "Neispravan CVV (mora imati 3 cifre).";
    } else {
        $ulaznica = new Ulaznica($pdo);

        try {
            foreach ($_SESSION['korpa'] as $stavka) {
                $ulaznica->validiraj($stavka['id'], $stavka['kolicina']);
                $ulaznica->dogadjaj_id = $stavka['id'];
                $ulaznica->kolicina = $stavka['kolicina'];
                $ulaznica->kupi($_SESSION['user_id']);
            }

            unset($_SESSION['korpa']);
            $uspeh = "Plaćanje je uspešno izvršeno! Hvala na kupovini.";
        } catch (Exception $e) {
            $greska = "Greška prilikom upisa u bazu: " . $e->getMessage();
        }
    }
}

$ukupno = 0;
if (!empty($_SESSION['korpa'])) {
    foreach ($_SESSION['korpa'] as $stavka) {
        $ukupno += $stavka['cena'] * $stavka['kolicina'];
    }
}
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <title>Plaćanje</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
</head>
<body>
<?php include 'navigacija.php'; ?>

<div class="container mt-4" style="max-width: 600px;">
    <h2>Plaćanje</h2>

    <?php if ($greska): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($greska) ?></div>
    <?php endif; ?>
    <?php if ($uspeh): ?>
        <div class="alert alert-success"><?= htmlspecialchars($uspeh) ?></div>
        <a href="index.php" class="btn btn-primary mt-3">Nazad na početnu</a>
    <?php else: ?>
        <p class="mb-4">Ukupno za plaćanje: <strong><?= number_format($ukupno, 2, ',', '.') ?> RSD</strong></p>
        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="ime_na_kartici" class="form-label">Ime na kartici</label>
                <input type="text" class="form-control" id="ime_na_kartici" name="ime_na_kartici" required value="<?= isset($_POST['ime_na_kartici']) ? htmlspecialchars($_POST['ime_na_kartici']) : '' ?>">
            </div>
            <div class="mb-3">
                <label for="broj_kartice" class="form-label">Broj kartice</label>
                <input type="text" class="form-control" id="broj_kartice" name="broj_kartice" maxlength="16" pattern="\d{16}" required value="<?= isset($_POST['broj_kartice']) ? htmlspecialchars($_POST['broj_kartice']) : '' ?>">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="datum_isteka" class="form-label">Datum isteka (MM/YY)</label>
                    <input type="text" class="form-control" id="datum_isteka" name="datum_isteka" pattern="(0[1-9]|1[0-2])\/\d{2}" placeholder="MM/YY" required value="<?= isset($_POST['datum_isteka']) ? htmlspecialchars($_POST['datum_isteka']) : '' ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="cvv" class="form-label">CVV</label>
                    <input type="text" class="form-control" id="cvv" name="cvv" maxlength="3" pattern="\d{3}" required value="<?= isset($_POST['cvv']) ? htmlspecialchars($_POST['cvv']) : '' ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-success">Potvrdi plaćanje</button>
        </form>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
