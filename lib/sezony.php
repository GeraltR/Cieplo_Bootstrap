<?php

require_once "baza.php";
require_once "kontrolki.php";
require_once "rozliczenie.php";

class Sezon {
    private $cselect = '<option value="%d">%s</option>';

    private $cnagwidoksezonow = '<thead>
                                <tr class="tabNaglowek">
                                    <th>Nazwa sezonu</th>
                                    <th class="tabRight">Początek</th>
                                    <th class="tabRight">Koniec</th>
                                </tr>
                            </thead>';
    private $cwidoksezonow = '<tr class="tabSelect" onClick=ZaznaczSezon("%d")>
                                <td class="tabText" id="vsezon%d">%s</td>
                                <td class="tabRight" id="vsezpoczatek%d"><a href="#" id="sez%d">%s</a></td>
                                <td class="tabRight" id="vsezkoniec%d">%s</td>
                            </tr>';

    private $cformularzOdczytowNag = '<thead>
                                        <tr class="tabNaglowek">
                                            <th>Kod lokalu</th>
                                            <th>Nr lok.</th>
                                            <th>Pomieszczenie</th>
                                            <th colspan="2">Nr fabryczny podzielnika</th>
                                            <th>Brak odczytu</th>
                                            <th>Odczyt</th>
                                            <th> </th>
                                        </tr>
                                    </thead>';
    private $cformularzOdczytow = '<tr class="tabFormularz%s" id="rowodczytyidlok%d">
                                    %s
                                    <td>%s</td>
                                    <td %s %s>%s</td>%s
                                    <td class="checkodczyt">
                                    <div class="form-check">
                                    <input class="form-check-input position-static" type="checkbox" id="odczC%d" value="" aria-label="..." onchange="OdczCheckClick(event,%d)">
                                    </div>
                                    </td>
                                    <td><input class="inputCyfry wartOdczytu" type="text" id="odczP%d" value="%s" onkeypress=" return OdcztyKey(event, %d)"
                                        onfocusin="InputClick(event)" onclick="InputClick(event)" onkeyup="KlawiszUp(event, %d)" autocomplete="off"></td>
                                    <td><i class="bi bi-hourglass-split" id="odczF%d"></i></td>
                                </tr>';

    private function DajDateSezonu($idsezonu, $rodzajdaty){
        $sql = "SELECT $rodzajdaty datasez FROM sezon_grzewczy WHERE ID_SEZONU = $idsezonu";
        $db = new myBaza;
        $datasez = $db->query($sql);
        foreach ($datasez as $a) {
            $wynik = $a['datasez'];
        }
        return $wynik;

    }

    public function DajSezonDlaBudynku($idbudynku){
        $wynik = 0;
        if ($idbudynku == 0)
            $sql = "SELECT ID_SEZONU FROM sezon_grzewczy
                    WHERE EXISTS (SELECT 1 FROM jednostka_rozliczeniowa
                            WHERE jednostka_rozliczeniowa.ID_SEZONU = sezon_grzewczy.ID_SEZONU)
                    ORDER BY SEZON_ZAMKNIETY, DATA_POCZATKU DESC, NAZWA_SEZONU LIMIT 1";
        else
            $sql = "SELECT ID_SEZONU FROM jednostka_rozliczeniowa WHERE ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik = $a['ID_SEZONU'];
        }
        return $wynik;
    }

    public function DajSezonDlaLokalu($idlokalu){
        $wynik = 0;
        $sql = "SELECT sezon_grzewczy.ID_SEZONU, CONCAT_WS(' ', sezon_grzewczy.NAZWA_SEZONU, YEAR(sezon_grzewczy.DATA_KONCA)) Nazwa
        FROM sezon_grzewczy, jednostka_rozliczeniowa, jednostka_uzytkowa
        WHERE sezon_grzewczy.ID_SEZONU = jednostka_rozliczeniowa.ID_SEZONU
        AND jednostka_rozliczeniowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ
        AND jednostka_uzytkowa.ID_LOKALU = $idlokalu Limit 1 ";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik = array($a['ID_SEZONU'], $a['Nazwa'], $idlokalu);
        }
        return $wynik;
    }

    public function Lista($idbudynku){
        $sql = "SELECT ID_SEZONU, NAZWA_SEZONU FROM sezon_grzewczy ORDER BY SEZON_ZAMKNIETY, DATA_POCZATKU DESC, NAZWA_SEZONU";
        $tmp ="";
        $db = new myBaza;
        $sezony = $db->query($sql);
        foreach ($sezony as $a) {
            $tmp .= sprintf($this->cselect, $a['ID_SEZONU'], $a['NAZWA_SEZONU']);
        }
        $tmp = $tmp;
        $idsezonu = $this->DajSezonDlaBudynku($idbudynku);
        return Array($tmp,$idsezonu);
    }

    public function WidokSezonow($idsezonu = 0){
        $wynik = "";
        $sql = "SELECT ID_SEZONU, NAZWA_SEZONU, DATA_POCZATKU, DATA_KONCA FROM sezon_grzewczy ORDER BY DATA_POCZATKU";
        $db = new myBaza;
        $listasezonow = $db->query($sql);
        foreach ($listasezonow as $a) {
            if ($idsezonu == 0)
                $idsezonu = $a['ID_SEZONU'];
            $wynik .= sprintf ($this->cwidoksezonow, $a['ID_SEZONU'], $a['ID_SEZONU'], $a['NAZWA_SEZONU'], $a['ID_SEZONU'],
            $a['ID_SEZONU'],$db->PolskaData($a['DATA_POCZATKU']),$a['ID_SEZONU'],$db->PolskaData($a['DATA_KONCA']));
        }
        $wynik = $this->cnagwidoksezonow.$wynik;
        return array($wynik, $idsezonu);
    }

    public function UpdateSezon($idsezonu, $nazwa, $poczatek, $koniec){
        $wynik = 0;
        $db = new myBaza;
        if ($idsezonu == 0){
            $sql = "INSERT INTO sezon_grzewczy(ID_SEZONU, NAZWA_SEZONU, DATA_POCZATKU, DATA_KONCA, SEZON_ZAMKNIETY)
                VALUE ($idsezonu, '$nazwa', '$poczatek', '$koniec', 0)";
            $wynik = $db->Insert($sql);
        }
        else {
            $sql = "UPDATE sezon_grzewczy SET  NAZWA_SEZONU = '$nazwa', DATA_POCZATKU = '$poczatek', DATA_KONCA = '$koniec'
                    WHERE ID_SEZONU = $idsezonu";
            $db->Execute($sql);
            $wynik = $idsezonu;
        }
        return $wynik;
    }

    public function CzyMoznaUsunac($idsezonu){
        $sql = "SELECT ID_SEZONU, NAZWA_JEDNOSTKI_ROZLICZENIOWEJ FROM jednostka_rozliczeniowa WHERE ID_SEZONU=$idsezonu";
        $wynik = "";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a){
            $wynik .= $a['NAZWA_JEDNOSTKI_ROZLICZENIOWEJ'].", ";
        }
        if (strlen($wynik) > 2) {
            $wynik = substr($wynik, 0, strlen($wynik) - 2);
            $tmp = strpos($wynik, ",");
            if ($tmp === false)
                $wynik = "Sezon jest powiązany z jednostką rozliczeniową: $wynik";
            else {
                if (strlen($wynik) > 100)
                    $wynik = substr($wynik, 0, 100).'...';
                $wynik = "Sezon jest powiązany z jednostkami rozliczeniowymi: $wynik";
            }
        }
        return $wynik;
    }

    public function UsunSezon($idsezonu){
        $sql= "DELETE FROM sezon_grzewczy WHERE ID_SEZONU = $idsezonu";
        $db = new myBaza;
        $db->Execute($sql);
        return 0;
    }

    public function UsunBudynekZSezonu($idbudynku){
        $sql = "UPDATE jednostka_rozliczeniowa SET ID_SEZONU = null where ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku";
        $db = new myBaza;
        $db->Execute($sql);
        return 0;
    }

    public function DodajBudynekDoSezonu($idsezonu, $idbudynku){
        $sql = "UPDATE jednostka_rozliczeniowa SET ID_SEZONU = $idsezonu where ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku";
        $db = new myBaza;
        $db->Execute($sql);
        return $idsezonu;
    }

    public function DodajDateOdczytu($idbudynku){
        $sql = "SELECT Max(stan_podzielnika.DATA_ODCZYTU) dataodczytu FROM jednostka_uzytkowa,  pomieszczenie, zainstalowany_grzejnik, zainstalowany_podzielnik, stan_podzielnika
        WHERE jednostka_uzytkowa.ID_LOKALU = pomieszczenie.ID_LOKALU AND pomieszczenie.ID_POMIESZCZENIA = zainstalowany_grzejnik.ID_POMIESZCZENIA
        AND zainstalowany_grzejnik.ID_ZG = zainstalowany_podzielnik.ID_ZG AND zainstalowany_podzielnik.ID_ZP = stan_podzielnika.ID_ZP
        AND stan_podzielnika.STATUS_ODCZYTU_PODZIELNIKA = 10 AND stan_podzielnika.WSKAZANIE_PODZIELNIKA > 0
        AND jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku";
        $db = new myBaza;
        $dataodczytu = $db->query($sql);
        foreach ($dataodczytu as $a) {
            $wynik = $a['dataodczytu'];
        }
        if ($wynik === null)
            $wynik =  date("Y-m-d");
        return $wynik;
    }

    public function CzyBylZdjetyPodzelnik($idpomieszczenia){
        $sql = "select * from stan_podzielnika, zainstalowany_podzielnik, zainstalowany_grzejnik, pomieszczenie, jednostka_uzytkowa, jednostka_rozliczeniowa, sezon_grzewczy
        WHERE stan_podzielnika.ID_ZP = zainstalowany_podzielnik.ID_ZP
        AND zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
        AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
        AND pomieszczenie.ID_LOKALU = jednostka_uzytkowa.ID_LOKALU
        AND sezon_grzewczy.ID_SEZONU = jednostka_rozliczeniowa.ID_SEZONU
        AND jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = jednostka_rozliczeniowa.ID_JEDNOSTKI_ROZLICZENIOWEJ
        AND stan_podzielnika.DATA_ODCZYTU BETWEEN sezon_grzewczy.DATA_POCZATKU AND sezon_grzewczy.DATA_KONCA
        AND stan_podzielnika.STATUS_ODCZYTU_PODZIELNIKA = 17
        AND pomieszczenie.ID_POMIESZCZENIA = $idpomieszczenia";
        $wynik = 0;
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik = 1;
        }
        return $wynik;
    }

    public function FormularzDoOdczytowPodzielnikow($idbudynku, $dataodczytu){
        $sql = "SELECT lokal.KodLok, lokal.kodsm, lokal.ID_LOKALU, zainstalowany_podzielnik.ID_ZP,
        rodzaj_pomieszczenia.NAZWA_POMIESZCZENIA, zainstalowany_podzielnik.NUMER_FABRYCZNY, pomieszczenie.ID_POMIESZCZENIA,
        (SELECT WSKAZANIE_PODZIELNIKA FROM stan_podzielnika
            WHERE stan_podzielnika.ID_ZP = zainstalowany_podzielnik.ID_ZP and stan_podzielnika.DATA_ODCZYTU = '$dataodczytu') wskazanie,
        (SELECT STATUS_ODCZYTU_PODZIELNIKA FROM stan_podzielnika
            WHERE stan_podzielnika.ID_ZP = zainstalowany_podzielnik.ID_ZP and stan_podzielnika.DATA_ODCZYTU = '$dataodczytu') statusPodz,
        CONCAT_WS('/', (SELECT  Nr_budynku FROM nieruchomosci WHERE nieruchomosci.Id_nieruchomosci = lokal.Id_nieruchomosci),  NR_LOKALU) NrLokalu
        FROM zainstalowany_podzielnik, zainstalowany_grzejnik, pomieszczenie, lokal, rodzaj_pomieszczenia, jednostka_uzytkowa
        WHERE zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
        and zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA and pomieszczenie.ID_LOKALU = lokal.ID_LOKALU
        and pomieszczenie.ID_RODZAJU_POMIESZCZENIA = rodzaj_pomieszczenia.ID_RODZAJU_POMIESZCZENIA
        and zainstalowany_grzejnik.DATA_ZDJECIA_GRZEJNIKA is null and zainstalowany_podzielnik.DATA_ZDJECIA_PODZIELNIKA is null
        AND lokal.ID_JEDNOSTKI_UZYTKOWEJ = jednostka_uzytkowa.ID_JEDNOSTKI_UZYTKOWEJ AND jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku
        ORDER BY lokal.KodLok, lokal.kodsm, rodzaj_pomieszczenia.NAZWA_POMIESZCZENIA";
        $wynik = '';
        $oldLokal = 0;
        $tmp = '';
        $rowpocz = '';
        $klasa17 = '';
        $statusy = array();
        $i = 1;
        $rozliczenie = new Rozliczenie;
        $db = new myBaza;
        $listapodzielnikow = $db->query($sql);
        foreach ($listapodzielnikow as $a) {
            if ($oldLokal != $a['ID_LOKALU']){
                $tmp = sprintf ('<td><button type="button" class="btn-xs btn-outline-secondary" onclick="SzukajWezlaZGrzej(%d, 1)">%s</button></td><td>%s</td>',
                $a['ID_LOKALU'],$a['KodLok'], $a['NrLokalu']);
                $oldLokal = $a['ID_LOKALU'];
                $zaliczkacolspan = '';
                $zaliczkainput =  sprintf('<td class="tabFormularzZaliczka">%s zł</td>', FloatToStr($rozliczenie->DajZaliczkeCO($oldLokal)));
                $rowpocz = ' table-info';
            }
            else {
                $tmp = '<td colspan = 2> </td>';
                $zaliczkainput = '';
                $zaliczkacolspan = 'colspan="2"';
                $rowpocz = '';
            }
            if ($this->CzyBylZdjetyPodzelnik($a['ID_POMIESZCZENIA']) != 0)
                $klasa17 = ' class="kolorred" ';
            else $klasa17 = '';
            $wynik.=sprintf($this->cformularzOdczytow, $rowpocz, $a['ID_LOKALU'], $tmp, $a['NAZWA_POMIESZCZENIA'], $klasa17, $zaliczkacolspan,
            $a['NUMER_FABRYCZNY'], $zaliczkainput, $i, $a['ID_ZP'], $i, FloatToStr($a['wskazanie']), $a['ID_ZP'], $a['ID_ZP'], $i);
            if($a['statusPodz'] == 16)
                $statusy[$i] = 1;
            else
                $statusy[$i] = 0;
            $i = $i + 1;
        }
        $wynik = $this->cformularzOdczytowNag.$wynik;
        return array ($wynik, $statusy);
    }

    public function DajDatyOdczytowPodzielnikow($idbudynku){
        $wynik = '';
        $sql = "SELECT stan_podzielnika.DATA_ODCZYTU, Sum(stan_podzielnika.WSKAZANIE_PODZIELNIKA) Ile
        FROM stan_podzielnika, zainstalowany_podzielnik, zainstalowany_grzejnik, pomieszczenie, jednostka_uzytkowa
        WHERE stan_podzielnika.ID_ZP = zainstalowany_podzielnik.ID_ZP
        AND zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
        AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA AND pomieszczenie.ID_LOKALU = jednostka_uzytkowa.ID_LOKALU
        AND zainstalowany_grzejnik.DATA_ZDJECIA_GRZEJNIKA is null and zainstalowany_podzielnik.DATA_ZDJECIA_PODZIELNIKA is null
        AND STATUS_ODCZYTU_PODZIELNIKA = 10
        AND jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku
        GROUP BY stan_podzielnika.DATA_ODCZYTU
        HAVING Ile > 0
        ORDER BY stan_podzielnika.DATA_ODCZYTU DESC";
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf($this->cselect, $a['DATA_ODCZYTU'], DajPolskaDate($a['DATA_ODCZYTU']));
        }
        return $wynik;
    }

    public function ZapiszOdczyt($idpodz, $wartosc, $dataodczytu, $bezodczytu){
        if ($bezodczytu === 'N')
            $status = 10;
        else $status = 16;
        $wartosc = StrToFloat($wartosc);
        $sql = "ID_ZP = $idpodz AND DATA_ODCZYTU = '$dataodczytu'";
        $db = new myBaza;
        $idodczytu = $db->Sprawdz('stan_podzielnika', 'ID_ODCZYTU_PODZIELNIKA', $sql);
        if ($idodczytu != 0){
            $sql = "UPDATE stan_podzielnika  SET WSKAZANIE_PODZIELNIKA = $wartosc, STATUS_ODCZYTU_PODZIELNIKA = $status
            WHERE ID_ODCZYTU_PODZIELNIKA = $idodczytu ";
            $db->Execute($sql);
        }
        else{
            $sql = "INSERT INTO stan_podzielnika (ID_ZP, DATA_ODCZYTU, WSKAZANIE_PODZIELNIKA, STATUS_ODCZYTU_PODZIELNIKA)
                    VALUES ($idpodz, '$dataodczytu', $wartosc, $status)";
            $idodczytu = $db->Insert($sql);
        }
        $wynik = $idodczytu;
        return $wynik;
    }

    public function SezonPoczatek($idsezonu){
        return $this->DajDateSezonu($idsezonu, 'DATA_POCZATKU');

    }
    public function SezonKoniec($idsezonu){
        return $this->DajDateSezonu($idsezonu, 'DATA_KONCA');
    }

}




?>