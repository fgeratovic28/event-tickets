<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'inc/db_pdo.php';
include 'klase/Ulaznica.php';
include 'klase/Dogadjaj.php';

$ulaznica = new Ulaznica($pdo);
$dogadjaj = new Dogadjaj($pdo);
$dogadjaji = $dogadjaj->dohvatiSve();

if (!isset($_SESSION['korpa'])) {
    $_SESSION['korpa'] = [];
}

$greska = "";
$uspeh = "";

if (isset($_GET['ukloni']) && is_numeric($_GET['ukloni'])) {
    $index = (int)$_GET['ukloni'];
    if (isset($_SESSION['korpa'][$index])) {
        array_splice($_SESSION['korpa'], $index, 1);
        $uspeh = "Stavka je uklonjena iz korpe.";
        header("Location: kupi.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $dogadjaj_id = $_POST['dogadjaj_id'] ?? null;
        $kolicina = (int)($_POST['kolicina'] ?? 1);

        $ulaznica->validiraj($dogadjaj_id, $kolicina);

        $izabrani = array_filter($dogadjaji, fn($d) => $d['id'] == $dogadjaj_id);
        $izabrani = array_values($izabrani)[0];
        
        $_SESSION['korpa'][] = [
            'id' => $izabrani['id'],
            'naziv' => $izabrani['naziv'],
            'mesto' => $izabrani['mesto'],
            'datum' => $izabrani['datum'],
            'cena' => $izabrani['cena'],
            'kolicina' => $kolicina
        ];

        $uspeh = "Dodata ulaznica u korpu!";
    } catch (Exception $e) {
        $greska = $e->getMessage();
    }
    
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <title>Kupi ulaznicu</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
</head>
<body>
<?php include 'navigacija.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h2>Kupi ulaznicu</h2>

            <?php if ($greska): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($greska) ?></div>
            <?php endif; ?>
            <?php if ($uspeh): ?>
                <div class="alert alert-success"><?= htmlspecialchars($uspeh) ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="form-group">
                    <label>Izaberite događaj</label>
                    <div class="d-flex flex-wrap gap-3">
                        <?php foreach ($dogadjaji as $d): ?>
                            <label class="card p-3" style="width: 18rem; cursor: pointer; border: 2px solid transparent;" 
                                   for="dogadjaj_<?= $d['id'] ?>" id="label_dogadjaj_<?= $d['id'] ?>">
                                <input type="radio" 
                                       name="dogadjaj_id" 
                                       id="dogadjaj_<?= $d['id'] ?>" 
                                       value="<?= $d['id'] ?>" 
                                       style="display: none;"
                                       required>
                                <h5><?= htmlspecialchars($d['naziv']) ?></h5>
                                <p class="mb-1"><?= htmlspecialchars($d['mesto']) ?></p>
                                <p class="mb-1"><?= date("d.m.Y", strtotime($d['datum'])) ?></p>
                                <p class="mb-1"><strong><?= number_format($d['cena'], 2) ?> RSD</strong></p>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <label for="kolicina">Količina</label>
                    <div class="input-group" style="max-width: 120px;">
                        <div class="input-group-prepend">
                            <button type="button" class="btn btn-outline-secondary" id="btn-decrease">-</button>
                        </div>
                        <input type="number" min="1" name="kolicina" id="kolicina" class="form-control text-center" required value="1" />
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="btn-increase">+</button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Dodaj u korpu</button>
            </form>
        </div>

        <div class="col-md-4">
            <h4>Vaša korpa</h4>
            <?php if (empty($_SESSION['korpa'])): ?>
                <p>Korpa je prazna.</p>
            <?php else: ?>
                <ul class="list-group mb-3">
                    <?php 
                    $ukupno = 0;
                    foreach ($_SESSION['korpa'] as $index => $stavka):
                        $subtotal = $stavka['cena'] * $stavka['kolicina'];
                        $ukupno += $subtotal;
                    ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($stavka['naziv']) ?></strong><br>
                                <?= $stavka['kolicina'] ?> x <?= number_format($stavka['cena'], 2) ?> RSD
                            </div>
                            <div>
                                <span class="mr-3"><?= number_format($subtotal, 2) ?> RSD</span>
                                <a href="#" class="btn btn-sm btn-danger btn-ukloni" data-index="<?= $index ?>">Ukloni</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <h5>Ukupno: <?= number_format($ukupno, 2) ?> RSD</h5>
                <a href="placanje.php" class="btn btn-success btn-block">Nastavi na plaćanje</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    input[type="radio"]:checked + h5,
    input[type="radio"]:checked + p,
    input[type="radio"]:checked ~ h5,
    input[type="radio"]:checked ~ p {
        font-weight: 700;
        color: #0056b3;
    }
</style>

<script>
    document.querySelectorAll('input[name="dogadjaj_id"]').forEach(input => {
        input.addEventListener('change', function() {
            document.querySelectorAll('label.card').forEach(label => {
                label.style.border = '2px solid transparent';
            });
            const label = document.getElementById('label_dogadjaj_' + this.value);
            if (label) label.style.border = '2px solid #0056b3';
        });
    });

    window.addEventListener('DOMContentLoaded', () => {
        const checked = document.querySelector('input[name="dogadjaj_id"]:checked');
        if (checked) {
            const label = document.getElementById('label_dogadjaj_' + checked.value);
            if (label) label.style.border = '2px solid #0056b3';
        }
    });

    const inputKolicina = document.getElementById('kolicina');
    document.getElementById('btn-increase').addEventListener('click', () => {
        let val = parseInt(inputKolicina.value) || 1;
        inputKolicina.value = val + 1;
    });
    document.getElementById('btn-decrease').addEventListener('click', () => {
        let val = parseInt(inputKolicina.value) || 1;
        if (val > 1) {
            inputKolicina.value = val - 1;
        }
    });
    document.querySelectorAll('.btn-ukloni').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const index = this.getAttribute('data-index');
        const potvrdaBtn = document.getElementById('potvrdiBrisanjeBtn');
        potvrdaBtn.href = `kupi.php?ukloni=${index}`;
        // Prikaz modala
        const modal = new bootstrap.Modal(document.getElementById('potvrdaModal'));
        modal.show();
    });
});
</script>

<div class="modal fade" id="potvrdaModal" tabindex="-1" aria-labelledby="potvrdaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="potvrdaModalLabel">Potvrda brisanja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zatvori"></button>
      </div>
      <div class="modal-body">
        Da li ste sigurni da želite da uklonite ovu stavku iz korpe?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Otkaži</button>
        <a href="#" class="btn btn-danger" id="potvrdiBrisanjeBtn">Ukloni</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>
