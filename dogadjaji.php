<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'inc/db_mysqli.php';

$result = $conn->query("SELECT * FROM dogadjaji ORDER BY datum ASC");
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <title>Događaji</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navigacija.php'; ?>
<div class="container mt-4">
    <h2>Predstojeći događaji</h2>
    <div class="row">
        <?php while ($dog = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <?php if ($dog['slika']): ?>
                        <img src="<?= htmlspecialchars($dog['slika']) ?>" class="card-img-top" alt="Slika događaja">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($dog['naziv']) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($dog['opis']) ?></p>
                        <p><strong>Datum:</strong> <?= date('d.m.Y H:i', strtotime($dog['datum'])) ?></p>
                        <p><strong>Lokacija:</strong> <?= htmlspecialchars($dog['lokacija']) ?></p>
                        <p><strong>Cena:</strong> <?= number_format($dog['cena'], 2) ?> RSD</p>
                        <p><strong>Preostalo karata:</strong> <?= (int)$dog['dostupne_karata'] ?></p>
                        <a href="kupi.php?id=<?= $dog['id'] ?>" class="btn btn-success">Kupi kartu</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
