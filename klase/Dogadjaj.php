<?php
class Dogadjaj {
    private $pdo;

    public $id;
    public $naziv;
    public $mesto;
    public $datum;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function validiraj($naziv, $mesto, $datum) {
        if (!ctype_alpha(str_replace(' ', '', $naziv)) || !ctype_alpha(str_replace(' ', '', $mesto))) {
            throw new Exception("Naziv i mesto moraju sadržati samo slova i razmake.");
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $datum)) {
            throw new Exception("Datum nije validan format.");
        }
        $this->naziv = $naziv;
        $this->mesto = $mesto;
        $this->datum = $datum;
    }

    public function dodaj() {
        $stmt = $this->pdo->prepare("INSERT INTO dogadjaji (naziv, mesto, datum) VALUES (?, ?, ?)");
        return $stmt->execute([$this->naziv, $this->mesto, $this->datum]);
    }

    public function azuriraj($id) {
        $stmt = $this->pdo->prepare("UPDATE dogadjaji SET naziv=?, mesto=?, datum=? WHERE id=?");
        return $stmt->execute([$this->naziv, $this->mesto, $this->datum, $id]);
    }

    public function obrisi($id) {
        $stmt = $this->pdo->prepare("DELETE FROM dogadjaji WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function dohvatiSve() {
        $stmt = $this->pdo->query("SELECT * FROM dogadjaji ORDER BY datum ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dohvatiPoId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM dogadjaji WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
