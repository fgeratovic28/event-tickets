<?php
include 'inc/db_pdo.php';
include 'klase/Dogadjaj.php';

$dogadjaj = new Dogadjaj($pdo);
$dogadjaji = $dogadjaj->dohvatiSve();
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Početna - Kupovina ulaznica</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <style>
        <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
        color: #333;
    }

    .hero {
        background: url('slike/hero.jpg') no-repeat center center;
        background-size: cover;
        color: #fff;
        padding: 140px 15px 120px;
        text-align: center;
        position: relative;
        box-shadow: inset 0 0 0 2000px rgba(0,0,0,0.4);
    }
    .hero h1.display-4 {
        font-weight: 700;
        font-size: 3.5rem;
        text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
        margin-bottom: 20px;
    }

    .hero p.lead {
        font-size: 1.4rem;
        margin-bottom: 35px;
        text-shadow: 1px 1px 6px rgba(0,0,0,0.6);
    }

    .btn-success {
        background-color: #28a745;
        border: none;
        font-weight: 600;
        padding: 12px 28px;
        font-size: 1.1rem;
        box-shadow: 0 4px 8px rgba(40,167,69,0.4);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }
    .btn-success:hover {
        background-color: #218838;
        box-shadow: 0 6px 12px rgba(33,136,56,0.6);
    }

    .btn-outline-light {
        color: #fff;
        border: 2px solid #fff;
        font-weight: 600;
        padding: 12px 28px;
        font-size: 1.1rem;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .btn-outline-light:hover {
        background-color: #fff;
        color: #333;
    }

    .container.my-5 {
        max-width: 1140px;
    }

    h2.mb-4.text-center {
        font-weight: 700;
        font-size: 2.5rem;
        color: #003366;
        margin-bottom: 40px;
    }

    .event-card {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: #fff;
    }

    .event-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .card-body {
        padding: 2rem 1.5rem;
        display: flex;
        flex-direction: column;
    }

    .card-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: #003366;
        margin-bottom: 0.75rem;
    }

    .card-text {
        font-size: 1.1rem;
        color: #555;
        margin-bottom: 1rem;
    }

    .card-text strong {
        color: #222;
    }

    .btn-primary {
        background-color: #003366;
        border: none;
        font-weight: 600;
        padding: 10px 24px;
        font-size: 1rem;
        margin-top: auto;
        align-self: flex-start;
        box-shadow: 0 4px 10px rgba(0, 51, 102, 0.4);
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #002244;
        box-shadow: 0 6px 16px rgba(0, 34, 68, 0.6);
    }

    footer {
        background-color: #003366;
        color: white;
        padding: 25px 0;
        margin-top: 60px;
        font-size: 1rem;
    }
</style>
</head>
<body>

<?php include 'navigacija.php'; ?>

<section class="hero">
    <div class="container hero-content">
        <h1 class="display-4 font-weight-bold">Dobrodošli na Kupovinu Ulaznica</h1>
        <p class="lead mb-4">Pronađite i kupite ulaznice za najbolje događaje u gradu brzo i jednostavno.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn btn-lg btn-success mr-3">Registruj se</a>
            <a href="login.php" class="btn btn-lg btn-outline-light">Prijavi se</a>
        <?php else: ?>
            <a href="dogadjaji.php" class="btn btn-lg btn-success mr-3">Pregledaj događaje</a>
            <a href="profil.php" class="btn btn-lg btn-outline-light">Moj profil</a>
        <?php endif; ?>
    </div>
</section>

<div class="container my-5">
    <h2 class="mb-4 text-center">Najnoviji događaji</h2>
    <div class="row">
        <?php if (count($dogadjaji) == 0): ?>
            <div class="col-12">
                <p class="text-center">Trenutno nema dostupnih događaja.</p>
            </div>
        <?php else: ?>
            <?php foreach (array_slice($dogadjaji, 0, 3) as $d): ?>
                <div class="col-md-4 mb-4">
                    <div class="card event-card h-100">
                        <?php if (!empty($d['slika'])): ?>
                            <img src="<?= htmlspecialchars($d['slika']) ?>" class="card-img-top" alt="<?= htmlspecialchars($d['naziv']) ?>">
                        <?php else: ?>
                            <img src="img/default.jpg" class="card-img-top" alt="Slika događaja">
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($d['naziv']) ?></h5>
                            <p class="card-text mb-1"><strong>Mesto:</strong> <?= htmlspecialchars($d['mesto']) ?></p>
                            <p class="card-text mb-1"><strong>Lokacija:</strong> <?= htmlspecialchars($d['lokacija']) ?></p>
                            <p class="card-text mb-1"><strong>Datum:</strong> <?= date("d.m.Y", strtotime($d['datum'])) ?></p>
                            <p class="card-text mb-1"><strong>Cena:</strong> <?= number_format($d['cena'], 2, ',', '.') ?> RSD</p>
                            <p class="card-text mb-1"><strong>Dostupno karata:</strong> <?= (int)$d['dostupne_karata'] ?></p>
                            <?php if (!empty($d['opis'])): ?>
                                <p class="card-text mb-3"><?= nl2br(htmlspecialchars($d['opis'])) ?></p>
                            <?php endif; ?>
                            <a href="kupi.php?id=<?= $d['id'] ?>" class="btn btn-primary mt-auto">Kupi ulaznicu</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="container my-5">
    <h2 class="mb-4 text-center">Svi događaji</h2>
    <div class="row">
        <?php if (count($dogadjaji) == 0): ?>
            <div class="col-12">
                <p class="text-center">Trenutno nema dostupnih događaja.</p>
            </div>
        <?php else: ?>
            <?php foreach ($dogadjaji as $d): ?>
                <div class="col-md-4 mb-4">
                    <div class="card event-card h-100">
                        <?php if (!empty($d['slika'])): ?>
                            <img src="<?= htmlspecialchars($d['slika']) ?>" class="card-img-top" alt="<?= htmlspecialchars($d['naziv']) ?>">
                        <?php else: ?>
                            <img src="img/default.jpg" class="card-img-top" alt="Slika događaja">
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($d['naziv']) ?></h5>
                            <p class="card-text mb-1"><strong>Mesto:</strong> <?= htmlspecialchars($d['mesto']) ?></p>
                            <p class="card-text mb-1"><strong>Lokacija:</strong> <?= htmlspecialchars($d['lokacija']) ?></p>
                            <p class="card-text mb-1"><strong>Datum:</strong> <?= date("d.m.Y", strtotime($d['datum'])) ?></p>
                            <p class="card-text mb-1"><strong>Cena:</strong> <?= number_format($d['cena'], 2, ',', '.') ?> RSD</p>
                            <p class="card-text mb-1"><strong>Dostupno karata:</strong> <?= (int)$d['dostupne_karata'] ?></p>
                            <?php if (!empty($d['opis'])): ?>
                                <p class="card-text mb-3"><?= nl2br(htmlspecialchars($d['opis'])) ?></p>
                            <?php endif; ?>
                            <a href="kupi.php?id=<?= $d['id'] ?>" class="btn btn-primary mt-auto">Kupi ulaznicu</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>



<footer class="text-center">
    <div class="container">
        <p>&copy; <?= date('Y') ?> Kupovina Ulaznica. Sva prava zadržana.</p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
