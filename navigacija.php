<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Online Ulaznice</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Početna</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="dogadjaji.php">Događaji</a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>

          <li class="nav-item">
            <a class="nav-link" href="profil.php">Moj profil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="kupi.php">Kupovina</a>
          </li>

          <?php if ($_SESSION['user_id'] == 1): // admin ?>
            <li class="nav-item">
              <a class="nav-link" href="admin.php">Dodaj događaj</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="admin_dogadjaji.php">Uredi dogadjaje</a>
            </li>
          <?php endif; ?>

          <li class="nav-item">
            <a class="nav-link" href="logout.php">Odjava</a>
          </li>

        <?php else: ?>

          <li class="nav-item">
            <a class="nav-link" href="login.php">Prijava</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="register.php">Registracija</a>
          </li>

        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<style>
  .navbar {
    background-color: #0056b3 !important;
    box-shadow: 0 2px 8px rgba(0, 86, 179, 0.4);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .navbar-brand {
    font-weight: 700;
    font-size: 1.6rem;
    color: #ffffff !important;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.25);
    transition: color 0.3s ease;
  }

  .navbar-brand:hover {
    color: #ffcc00 !important;
  }

  .navbar-nav .nav-link {
    color: #e0e7ff !important;
    font-weight: 600;
    font-size: 1rem;
    margin-left: 1rem;
    transition: color 0.3s ease;
    text-shadow: 0 0 3px rgba(0, 0, 50, 0.3);
  }

  .navbar-nav .nav-link:hover,
  .navbar-nav .nav-link:focus {
    color: #ffcc00 !important;
    text-shadow: 0 0 6px #ffcc00;
  }

  .navbar-toggler {
    border: 2px solid #ffcc00 !important;
  }

  .navbar-toggler-icon {
    filter: brightness(0) invert(1);
  }
</style>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
