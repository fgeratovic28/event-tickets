<?php
class Ulaznica {
    private $pdo;

    public $id;
    public $dogadjaj_id;
    public $korisnik_id;
    public $kolicina;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function validiraj($dogadjaj_id, $kolicina) {
        if (!is_numeric($dogadjaj_id) || $dogadjaj_id <= 0) {
            throw new Exception("Neispravan ID događaja.");
        }
        if (!is_numeric($kolicina) || $kolicina < 1) {
            throw new Exception("Količina mora biti pozitivan broj.");
        }
        $this->dogadjaj_id = (int)$dogadjaj_id;
        $this->kolicina = (int)$kolicina;
    }

   public function kupi($korisnik_id) {
    try {
        $this->pdo->beginTransaction();

        $stmt = $this->pdo->prepare("SELECT dostupne_karata FROM dogadjaji WHERE id = ?");
        $stmt->execute([$this->dogadjaj_id]);
        $dogadjaj = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dogadjaj) {
            throw new Exception("Događaj nije pronađen.");
        }

        if ($dogadjaj['dostupne_karata'] < $this->kolicina) {
            throw new Exception("Nema dovoljno karata na stanju.");
        }

        $stmt = $this->pdo->prepare("INSERT INTO ulaznice (dogadjaj_id, korisnik_id, kolicina) VALUES (?, ?, ?)");
        $stmt->execute([$this->dogadjaj_id, $korisnik_id, $this->kolicina]);

        $stmt = $this->pdo->prepare("UPDATE dogadjaji SET dostupne_karata = dostupne_karata - ? WHERE id = ?");
        $stmt->execute([$this->kolicina, $this->dogadjaj_id]);

        $this->pdo->commit();
        return true;
    } catch (Exception $e) {
        $this->pdo->rollBack();
        throw $e;
    }
}

    public function dohvatiZaKorisnika($korisnik_id) {
        $stmt = $this->pdo->prepare("
            SELECT u.*, d.naziv, d.mesto, d.datum
            FROM ulaznice u
            JOIN dogadjaji d ON u.dogadjaj_id = d.id
            WHERE u.korisnik_id = ?
            ORDER BY u.kupljeno DESC
        ");
        $stmt->execute([$korisnik_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
