<?php
include 'inc/db_mysqli.php';
include 'klase/User.php';

$user = new User($conn);
$greska = "";
$uspeh = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $user->validiraj($_POST['ime'], $_POST['email'], $_POST['lozinka']);
        if ($user->register()) {
            $uspeh = "Uspešno registrovan! Možete se prijaviti.";
        }
    } catch (Exception $e) {
        $greska = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <title>Registracija</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <style>
        body {
            background: #f8f9fa;
        }
        .card {
            max-width: 500px;
            margin: 50px auto;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .card-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
<?php include 'navigacija.php'; ?>
<div class="container">
    <div class="card p-4">
        <h2 class="card-title">Registracija</h2>
        <?php if ($greska): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($greska) ?></div>
        <?php endif; ?>
        <?php if ($uspeh): ?>
            <div class="alert alert-success"><?= htmlspecialchars($uspeh) ?></div>
        <?php endif; ?>
        <form method="POST" novalidate>
            <div class="form-group">
                <label for="ime">Ime</label>
                <input type="text" name="ime" id="ime" class="form-control" required value="<?= isset($_POST['ime']) ? htmlspecialchars($_POST['ime']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="email">Email adresa</label>
                <input type="email" name="email" id="email" class="form-control" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            <div class="form-group">
                <label for="lozinka">Lozinka</label>
                <input type="password" name="lozinka" id="lozinka" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Registruj se</button>
        </form>
    </div>
</div>
</body>
</html>
