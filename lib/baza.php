<?php
require_once "stale.php";
class myBaza {
    private $serwer = "localhost";
    private $uzytkownik = "nazwaUsera";
    private $nazwa_bazy = "nazwaBazy";
    private $haslo = "";
    private $dbcon;

    public $trybDebug;

    function __construct($tryb = false, $create = false)
    {
        try {
            $stale = new Stale();
            $this->trybDebug = $tryb;
            $this->haslo= $stale->tajnehaslo;
            if ($create == true){
                $this->dbcon = new PDO("mysql:host=".$this->serwer,
                                        $this->uzytkownik, $this->haslo,
                                        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'"]);
                $this->dbcon->exec("CREATE DATABASE $this->nazwa_bazy DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci");
                $this->dbcon = null;
            }
            $this->dbcon = new PDO("mysql:dbname=".$this->nazwa_bazy.";host=".$this->serwer,
                                        $this->uzytkownik, $this->haslo,
                                        [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8' COLLATE 'utf8_polish_ci'"]);
            $this->dbcon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => "Błąd podłączenia do dancyh:  $e->getMessage()"
            ]);
        }
    }

    function Sprawdz($ATabela, $APole, $AWarunek='') {
        $sql = "SELECT ".$APole." FROM ".$ATabela.(($AWarunek!='') ? " WHERE ":"").$AWarunek." LIMIT 1";
        $sprawdz = 0;
        try {
            $wynik = $this->dbcon->query($sql);
            foreach ($wynik as $w) {
                    $sprawdz = $w[$APole];
            }
            return $sprawdz;
        }
        catch (PDOException $e) {
                http_response_code(500);
                echo json_encode([
                    'message' => 'Błąd Sprawdz: ' . $e->getMessage()
                ]);
        }
    }

    function UruchomZdanie ($AZdanie){
        try {
            $statement = $this->dbcon->prepare($AZdanie);
            $statement->execute();
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => ($this->trybDebug)?"<br>$AZdanie":""."$e->getMessage()<br>"
            ]);
        }
    }

    function query ($AZdanie){
        try {
            return $this->dbcon->query($AZdanie);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => ($this->trybDebug)?"<br>$AZdanie":""."$e->getMessage()<br>"
            ]);
        }
    }

    function Execute ($AZdanie){
        try {
            $statement = $this->dbcon->prepare($AZdanie);
            $statement->execute();
            return 0;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => ($this->trybDebug)?"<br>$AZdanie":""."$e->getMessage()<br>"
            ]);
            return 1;
        }
    }

    function ExecuteTran ($AZdanie){
            $statement = $this->dbcon->prepare($AZdanie);
            $statement->execute();
    }

    function Insert($AZdanie){
        try {
            $statement = $this->dbcon->prepare($AZdanie);
            $statement->execute();
            $wynik = $this->dbcon->lastInsertId();
            return $wynik;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "message" => ($this->trybDebug)?"<br>$AZdanie":""."$e->getMessage()<br>"
            ]);
            return -1;
        }
    }

    function beginTransaction(){
        $this->dbcon->beginTransaction();
    }

    function commit(){
        $this->dbcon->commit();
    }

    function rollBack(){
        $this->dbcon->rollBack();
    }

    function Zaloguj($userName, $haslo) {
        $wynik=$this->Sprawdz("lista_pracownikow", "haslo", "USERNAME='$userName'");
        if (funkcjaSprawdzająca($haslo, $wynik))
          return (int) $this->Sprawdz("lista_pracownikow", "USERID", "USERNAME='$userName'");
        else return 0;
    }

    function Wyloguj($userid){
        $sql = "DELETE FROM log_dzialania WHERE USERID = $userid";
        $this->Execute($sql);
        return 0;
    }

    function NowaSesja($userid, $sesja){
        $this->Wyloguj($userid);
        $sql = "INSERT INTO log_dzialania (USERID, DZIALANIE) VALUES ($userid, '$sesja')";
        return $this->Insert($sql);
    }

    function CzySesja(){
        if (isset($_SESSION['idkto'])){
            $userid = $_SESSION['idkto'];
            $sesja = session_id();
            $wynik = $this->Sprawdz("log_dzialania", "USERID", "DZIALANIE = '$sesja' AND USERID = $userid");
        }
        else $wynik = 0;
        return $wynik;
    }

    function ZmienHaslo ($userid, $haslo) {
        $tmp = funkcja haszująca;
        $wynik = $this->Execute("UPDATE lista_pracownikow set haslo = '$tmp' where USERID = $userid");
        return $wynik;
    }

    function DajNazweUzytkownika($AId) {
        $wynik=$this->Sprawdz('lista_pracownikow', 'CONCAT_WS (" ",IMIE_PRACOWNIKA,NAZWISKO_PRACOWNIKA)', 'USERID='.$AId);
        $wynik = ltrim($wynik);
        return $wynik;
    }


    function DajUprawnienia($AId) {
        $wynik=$this->Sprawdz('lista_pracownikow', 'admin', 'USERID='.$AId);
        return $wynik;
    }

    function PolskaData($data){
        if ($data === null)
            $wynik = "";
        else {
            $wynik = date_format(date_create($data), 'd.m.Y');
        }
        return $wynik;
    }

}

?>