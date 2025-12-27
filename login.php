<?php
include 'inc/db_mysqli.php';
include 'klase/User.php';

$user = new User($conn);
$greska = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!$user->login($_POST['email'], $_POST['lozinka'])) {
        $greska = "Neispravan email ili lozinka.";
    } else {
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8" />
    <title>Prijava</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 60px auto;
            padding: 30px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .login-container h2 {
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
        }
        .form-control {
            border-radius: 10px;
        }
        .btn-primary {
            border-radius: 10px;
            width: 100%;
        }
    </style>
</head>
<body>

<?php include 'navigacija.php'; ?>

<div class="container">
    <div class="login-container">
        <h2>Prijava</h2>

        <?php if ($greska): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($greska) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <label for="email" class="form-label">Email adresa</label>
                <input type="email" name="email" id="email" class="form-control" required 
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>

            <div class="mb-3">
                <label for="lozinka" class="form-label">Lozinka</label>
                <input type="password" name="lozinka" id="lozinka" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Prijavi se</button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
