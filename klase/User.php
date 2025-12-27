<?php
session_start();

class User {
    private $conn;

    public $id;
    public $ime;
    public $email;
    private $lozinka;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function validiraj($ime, $email, $lozinka) {
        if (!ctype_alpha(str_replace(' ', '', $ime))) {
            throw new Exception("Ime mora sadržati samo slova i razmake.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Neispravan email.");
        }
        if (strlen($lozinka) < 6) {
            throw new Exception("Lozinka mora imati najmanje 6 karaktera.");
        }
        $this->ime = $ime;
        $this->email = $email;
        $this->lozinka = password_hash($lozinka, PASSWORD_DEFAULT);
    }

    public function register() {
        $stmt = $this->conn->prepare("SELECT id FROM korisnici WHERE email=?");
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            throw new Exception("Email je već registrovan.");
        }
        $stmt->close();

        $stmt = $this->conn->prepare("INSERT INTO korisnici (ime, email, lozinka) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $this->ime, $this->email, $this->lozinka);
        $rez = $stmt->execute();
        $stmt->close();
        return $rez;
    }

    public function login($email, $lozinka) {
        $stmt = $this->conn->prepare("SELECT id, ime, lozinka FROM korisnici WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id, $ime, $hash);
        if ($stmt->fetch()) {
            if (password_verify($lozinka, $hash)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_ime'] = $ime;
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function isLogged() {
        return isset($_SESSION['user_id']);
    }

    public static function logout() {
        session_unset();
        session_destroy();
    }
}
?>
