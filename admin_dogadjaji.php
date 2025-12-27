<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: login.php");
    exit;
}

include 'inc/db_pdo.php';

$poruka = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['izmeni'])) {
    try {
        $id = (int)$_POST['id'];
        $naziv = trim($_POST['naziv']);
        $mesto = trim($_POST['mesto']);
        $lokacija = trim($_POST['lokacija']);
        $opis = trim($_POST['opis']);
        $cena = (float)$_POST['cena'];
        $broj_karata = (int)$_POST['broj_karata'];
        $dostupne_karata = (int)$_POST['dostupne_karata'];
        $datum = $_POST['datum'];

        // Obrada slike
        $slika = trim($_POST['stara_slika'] ?? '');

        if (isset($_FILES['slika']) && $_FILES['slika']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['slika']['tmp_name'];
            $fileName = basename($_FILES['slika']['name']);
            $uploadDir = 'uploads/';
            $targetFile = $uploadDir . time() . '_' . $fileName;

            if (move_uploaded_file($tmpName, $targetFile)) {
                $slika = $targetFile;
            } else {
                throw new Exception("Greška pri uploadu slike.");
            }
        }

        $stmt = $pdo->prepare("UPDATE dogadjaji SET 
            naziv = ?, mesto = ?, lokacija = ?, opis = ?, cena = ?, broj_karata = ?, dostupne_karata = ?, datum = ?, slika = ?
            WHERE id = ?");
        $stmt->execute([$naziv, $mesto, $lokacija, $opis, $cena, $broj_karata, $dostupne_karata, $datum, $slika, $id]);
        $poruka = "Događaj uspešno izmenjen.";
    } catch (Exception $e) {
        $poruka = "Greška: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['obrisi'])) {
    $id = (int)$_POST['obrisi'];
    $stmt = $pdo->prepare("DELETE FROM dogadjaji WHERE id = ?");
    $stmt->execute([$id]);
    $poruka = "Događaj je obrisan.";
}

$stmt = $pdo->query("SELECT * FROM dogadjaji ORDER BY datum ASC");
$dogadjaji = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Upravljanje događajima</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<?php include 'navigacija.php'; ?>

<div class="container my-4">
    <h1 class="mb-4">Upravljanje događajima</h1>

    <?php if ($poruka): ?>
        <div class="alert alert-info"><?= htmlspecialchars($poruka) ?></div>
    <?php endif; ?>

    <div class="row">
        <?php foreach ($dogadjaji as $d): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($d['slika'])): ?>
                        <img src="<?= htmlspecialchars($d['slika']) ?>" class="card-img-top" alt="<?= htmlspecialchars($d['naziv']) ?>">
                    <?php else: ?>
                        <img src="img/default.jpg" class="card-img-top" alt="Podrazumevana slika događaja">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($d['naziv']) ?></h5>
                        <p class="card-text mb-1"><strong>Mesto:</strong> <?= htmlspecialchars($d['mesto']) ?></p>
                        <p class="card-text mb-1"><strong>Datum:</strong> <?= date('d.m.Y H:i', strtotime($d['datum'])) ?></p>
                        <p class="card-text mb-1"><strong>Cena:</strong> <?= number_format($d['cena'], 2, ',', '.') ?> RSD</p>
                        <p class="card-text mb-1"><strong>Preostale karte:</strong> <?= $d['dostupne_karata'] ?></p>
                        <div class="mt-auto">
                            <button class="btn btn-sm btn-warning" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal"
                                data-id="<?= $d['id'] ?>"
                                data-naziv="<?= htmlspecialchars($d['naziv']) ?>"
                                data-mesto="<?= htmlspecialchars($d['mesto']) ?>"
                                data-lokacija="<?= htmlspecialchars($d['lokacija']) ?>"
                                data-opis="<?= htmlspecialchars($d['opis']) ?>"
                                data-cena="<?= $d['cena'] ?>"
                                data-broj_karata="<?= $d['broj_karata'] ?>"
                                data-dostupne_karata="<?= $d['dostupne_karata'] ?>"
                                data-datum="<?= date('Y-m-d\TH:i', strtotime($d['datum'])) ?>"
                                data-slika="<?= htmlspecialchars($d['slika'] ?? 'img/default.jpg') ?>">Izmeni</button>

                            <button class="btn btn-sm btn-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModal"
                                data-id="<?= $d['id'] ?>"
                                data-naziv="<?= htmlspecialchars($d['naziv']) ?>"
                            >Obriši</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form method="POST" id="editForm" class="modal-content" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Izmena događaja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zatvori"></button>
      </div>
      <div class="modal-body">
          <input type="hidden" name="id" id="edit-id" value="">

          <div class="mb-3">
            <label for="edit-naziv" class="form-label">Naziv</label>
            <input type="text" name="naziv" class="form-control" id="edit-naziv" required>
          </div>
          <div class="mb-3">
            <label for="edit-mesto" class="form-label">Mesto</label>
            <input type="text" name="mesto" class="form-control" id="edit-mesto" required>
          </div>
          <div class="mb-3">
            <label for="edit-lokacija" class="form-label">Lokacija</label>
            <input type="text" name="lokacija" class="form-control" id="edit-lokacija">
          </div>
          <div class="mb-3">
            <label for="edit-opis" class="form-label">Opis</label>
            <textarea name="opis" class="form-control" id="edit-opis" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label for="edit-cena" class="form-label">Cena (RSD)</label>
            <input type="number" step="0.01" name="cena" class="form-control" id="edit-cena" required>
          </div>
          <div class="mb-3">
            <label for="edit-broj_karata" class="form-label">Ukupan broj karata</label>
            <input type="number" name="broj_karata" class="form-control" id="edit-broj_karata" required>
          </div>
          <div class="mb-3">
            <label for="edit-dostupne_karata" class="form-label">Preostale karte</label>
            <input type="number" name="dostupne_karata" class="form-control" id="edit-dostupne_karata" required>
          </div>
          <div class="mb-3">
            <label for="edit-datum" class="form-label">Datum i vreme</label>
            <input type="datetime-local" name="datum" class="form-control" id="edit-datum" required>
          </div>
          <div class="mb-3">
            <label for="edit-slika" class="form-label">Izaberite sliku</label>
            <input type="file" name="slika" class="form-control" id="edit-slika" accept="image/*">
          </div>
          <input type="hidden" name="stara_slika" id="edit-stara_slika" value="">
      </div>
      <div class="modal-footer">
        <button type="submit" name="izmeni" class="btn btn-primary">Sačuvaj izmene</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Otkaži</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="deleteForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Potvrda brisanja</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zatvori"></button>
      </div>
      <div class="modal-body">
        Da li ste sigurni da želite da obrišete događaj <strong id="delete-event-name"></strong>?
      </div>
      <div class="modal-footer">
        <input type="hidden" name="obrisi" id="delete-id" value="">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Otkaži</button>
        <button type="submit" class="btn btn-danger">Obriši</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        document.getElementById('edit-id').value = button.getAttribute('data-id');
        document.getElementById('edit-naziv').value = button.getAttribute('data-naziv');
        document.getElementById('edit-mesto').value = button.getAttribute('data-mesto');
        document.getElementById('edit-lokacija').value = button.getAttribute('data-lokacija');
        document.getElementById('edit-opis').value = button.getAttribute('data-opis');
        document.getElementById('edit-cena').value = button.getAttribute('data-cena');
        document.getElementById('edit-broj_karata').value = button.getAttribute('data-broj_karata');
        document.getElementById('edit-dostupne_karata').value = button.getAttribute('data-dostupne_karata');
        document.getElementById('edit-datum').value = button.getAttribute('data-datum');
        document.getElementById('edit-stara_slika').value = button.getAttribute('data-slika');
    });

    const deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        document.getElementById('delete-id').value = button.getAttribute('data-id');
        document.getElementById('delete-event-name').textContent = button.getAttribute('data-naziv');
    });
</script>

</body>
</html>
