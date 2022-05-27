<?php
    require_once "baza.php";
    require_once "kontrolki.php";

class pomieszczenie {

    private $cselect = '<option value="%d">%s</option>';

    public function DajListeRodzajowPomieszczen(){
        $wynik = '';
        $sql = "SELECT ID_RODZAJU_POMIESZCZENIA, CONCAT_WS(' ',KOD_POMIESZCZENIA, NAZWA_POMIESZCZENIA) Nazwa FROM rodzaj_pomieszczenia";
        $db = new myBaza;
        $lisa = $db->query($sql);
        foreach ($lisa as $a) {
            $wynik.= sprintf($this->cselect, $a['ID_RODZAJU_POMIESZCZENIA'], $a['Nazwa']);
        }
        return $wynik;
    }

    public function DajDanePomieszczenia($idpomieszczenia){
        $wynik = '';
        $sql = "SELECT ID_POMIESZCZENIA, ID_RODZAJU_POMIESZCZENIA, ID_LOKALU, ID_JEDNOSTKI_ROZLICZENIOWEJ,
                IFNULL(POWIERZCHNIA_POMIESZCZENIA, 0) powierzchnia  FROM pomieszczenie WHERE ID_POMIESZCZENIA = $idpomieszczenia";
        $db = new myBaza;
        $pomieszcz = $db->query($sql);
        foreach ($pomieszcz as $a) {
            $powierzchnia = FloatToStr($a['powierzchnia']);
            $wynik=array($a['ID_POMIESZCZENIA'], $a['ID_LOKALU'], $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'],
             $a['ID_RODZAJU_POMIESZCZENIA'], $powierzchnia);
        }
        return $wynik;
    }

    public function Zapisz($idpomieszczenia, $idlokalu, $idbudynku, $idrodzaju, $powierzchnia){
        $powierzchnia = StrToFloat($powierzchnia);
        $db = new myBaza;
        if ($idpomieszczenia == 0){
            $sql = "INSERT INTO pomieszczenie (ID_LOKALU, ID_JEDNOSTKI_ROZLICZENIOWEJ,ID_RODZAJU_POMIESZCZENIA,POWIERZCHNIA_POMIESZCZENIA)
                VALUES ($idlokalu, $idbudynku, $idrodzaju, $powierzchnia)";
            $wynik = $db->Insert($sql);
        }
        else {
            $sql = "UPDATE pomieszczenie SET ID_RODZAJU_POMIESZCZENIA = $idrodzaju, POWIERZCHNIA_POMIESZCZENIA = $powierzchnia
                WHERE ID_POMIESZCZENIA = $idpomieszczenia";
            $db->Execute($sql);
            $wynik = $idpomieszczenia;
        }
        return $wynik;
    }

    public function ListaPomieszczen($idlokalu){
        $wynik = "";
        $sql = "SELECT ID_POMIESZCZENIA,NAZWA_POMIESZCZENIA, IFNULL(POWIERZCHNIA_POMIESZCZENIA, 0) pow,
        IFNULL((SELECT NUMER_FABRYCZNY FROM zainstalowany_podzielnik, zainstalowany_grzejnik WHERE zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
                AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA ORDER BY zainstalowany_podzielnik.DATA_INSTALACJI_PODZIELNIKA
                DESC LIMIT 1), 'bez podzielnika') podzielnik
        FROM pomieszczenie, rodzaj_pomieszczenia
        WHERE pomieszczenie.ID_RODZAJU_POMIESZCZENIA = rodzaj_pomieszczenia.ID_RODZAJU_POMIESZCZENIA AND ID_LOKALU = $idlokalu";
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $tmp = $a['NAZWA_POMIESZCZENIA'].' '.$a['podzielnik'].' ('.$a['ID_POMIESZCZENIA'].')' ;
            $wynik.= sprintf($this->cselect, $a['ID_POMIESZCZENIA'], $tmp);
        }
        return $wynik;
    }

    public function CzyMoznaUsunacPomieszczenie($idpomieszczenia){
        $wynik = 0;
        $db = new myBaza();
        //sprawdzam tylko czy zainstalowany jest podzielnik bo grzejnik bez podzielnika nie ma sensu i nie ma odczytów
        //chodzi głównie o to, że jakimś cudem może być wprowadzony grzejnik bez podzielnika i muszę móc to usunąć
        $sql = "pomieszczenie.ID_POMIESZCZENIA = zainstalowany_grzejnik.ID_POMIESZCZENIA AND zainstalowany_grzejnik.ID_ZG = zainstalowany_podzielnik.ID_ZG
                AND zainstalowany_podzielnik.DATA_ZDJECIA_PODZIELNIKA IS NULL AND pomieszczenie.ID_POMIESZCZENIA = $idpomieszczenia";
        $wynik = $db->Sprawdz("pomieszczenie, zainstalowany_grzejnik, zainstalowany_podzielnik", "ID_ZP", $sql);
        return $wynik;
    }

    public function UsunPomieszczenie($idpomieszczenia){
        $db = new myBaza();
        try{
            $db->beginTransaction();
            $sql = "DELETE FROM stan_podzielnika WHERE ID_ZP = (SELECT ID_ZP FROM zainstalowany_podzielnik, zainstalowany_grzejnik, pomieszczenie
                WHERE pomieszczenie.ID_POMIESZCZENIA = zainstalowany_grzejnik.ID_POMIESZCZENIA AND zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
                AND zainstalowany_podzielnik.DATA_ZDJECIA_PODZIELNIKA IS NOT NULL AND stan_podzielnika.ID_ZP = zainstalowany_podzielnik.ID_ZP
                AND pomieszczenie.ID_POMIESZCZENIA  = $idpomieszczenia)";
            $db->ExecuteTran($sql);
            $sql = "DELETE FROM zainstalowany_podzielnik WHERE ID_ZG = (SELECT ID_ZG FROM zainstalowany_grzejnik, pomieszczenie
            WHERE pomieszczenie.ID_POMIESZCZENIA = zainstalowany_grzejnik.ID_POMIESZCZENIA AND zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
            AND zainstalowany_podzielnik.DATA_ZDJECIA_PODZIELNIKA IS NOT NULL AND pomieszczenie.ID_POMIESZCZENIA = $idpomieszczenia)";
            $db->ExecuteTran($sql);
            $sql = "DELETE FROM zainstalowany_grzejnik WHERE zainstalowany_grzejnik.ID_POMIESZCZENIA = $idpomieszczenia";
            $db->ExecuteTran($sql);
            $sql = "DELETE FROM pomieszczenie WHERE ID_POMIESZCZENIA = $idpomieszczenia";
            $db->ExecuteTran($sql);
            $db->commit();
        }
        catch  (Exception $e) {
            $db->rollBack();
            echo "Failed: " . $e->getMessage();
        }

        return 0;
    }
}
?>