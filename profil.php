<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'inc/db_pdo.php';
include 'klase/Ulaznica.php';

$ulaznica = new Ulaznica($pdo);
$ulaznice = $ulaznica->dohvatiZaKorisnika($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <title>Moj profil - Kupljene ulaznice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
</head>
<body>
<?php include 'navigacija.php'; ?>
<div class="container mt-4">
    <h2>Moje kupljene ulaznice</h2>
    <?php if (count($ulaznice) == 0): ?>
        <p>Nemate kupljenih ulaznica.</p>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Događaj</th>
                    <th>Mesto</th>
                    <th>Datum</th>
                    <th>Količina</th>
                    <th>Kupljeno</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ulaznice as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['naziv']) ?></td>
                        <td><?= htmlspecialchars($u['mesto']) ?></td>
                        <td><?= date("d.m.Y", strtotime($u['datum'])) ?></td>
                        <td><?= (int)$u['kolicina'] ?></td>
                        <td><?= date("d.m.Y H:i", strtotime($u['kupljeno'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>

</html>
