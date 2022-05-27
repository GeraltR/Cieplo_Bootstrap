<?php

require_once "baza.php";
require_once "kontrolki.php";


class Lokale {
    private $cselect = '<option value="%d">%s</option>';
    private $ctabVLokNag = '<thead>
                                <tr class="tabNaglowek">
                                    <th>Kod</th>
                                    <th>Kod czynsze</th>
                                    <th>Numer</th>
                                    <th>Pow.</th>
                                    <th>Lokator</th>
                                    <th>Rodzaj</th>
                                    <th>Rmi</th>
                                </tr>
                            </thead>';
    private $ctabVLok = '<tr class="tabSelect" onClick=ZaznaczLokal("%d")>
    <td class="tabKodLok" id="vlokid%d">%s</td>
    <td class="tabKodLok"><a href="#" id="lok%d">%s</a></td>
    <td class="tabNumer">%s</td>
    <td class="tabPowierznia">%s</td>
    <td class="tabText">%s</td>
    <td class="tabRodzLok">%s</td>
    <td class="tabRmi">%s</td>
    <tr class="tabszczeg" id="szczeglok%d">
    <td colspan="7"><div class="pamTable blueTable"></div></td></tr>';

    private $ctabpomNag = '<div class="pamTableHeading">
                                <div class="pamTableRowHeading">
                                    <div class="pamTableHead">Pomieszczenie</div>
                                    <div class="pamTableHead"></div>
                                    <div class="pamTableHead">Grzejnik</div>
                                    <div class="pamTableHead"></div>
                                    <div class="pamTableHead">Montaż</div>
                                    <div class="pamTableHead"></div>
                                    <div class="pamTableHead">Podzielnik</div>
                                    <div class="pamTableHead"></div>
                                    <div class="pamTableHead">Montaż</div>
                                </div>
                            </div>';

    private $ctabpom = '<div class="pamTableRow">
                                <div class="pamTableCell"><button type="button" class="btn btn-outline-info btn-pomieszczenie" onclick="ZmienPomieszczenie(%d)">%s</button></div>
                                <div class="pamTableCell">%s</div>
                                <div class="pamTableCell">%s</div>
                                <div class="pamTableCell">%s</div>
                                <div class="pamTableCell"><button type="button" class="btn btn-light" onclick="ZmienGrzejnik(%d)">Zmień</button></div>
                                <div class="pamTableCell">%s</div>
                                <div class="pamTableCell">%s</div>
                                <div class="pamTableCell">%s</div>
                                <div class="pamTableCell"><button type="button" class="btn btn-light" onclick="ZmienPodzielnik(%d)">Zmień</button></div>
                        </div>';
    private $cfootpom = '<div class="pamTableRow">
                            <div class="pamTableCell"><button type="button" class="btn btn-outline-info btn-pom4" onclick="DodajPomieszczenie(%d)">Dodaj</button>
                            <button type="button" class="btn btn-outline-danger btn-pom4" onclick="UsunPomieszcznie(%d)">Usuń</button></div>
                            <div class="pamTableCell"></div>
                            <div class="pamTableCell"></div>
                            <div class="pamTableCell"><button type="button" class="btn btn-outline-secondary" onclick="WyslijRozliczenie(%d)">Wyślij</button></div>
                            <div class="pamTableCell"></div>
                            <div class="pamTableCell pogrubione dwadododlu"><p>Ostatnie</p></div>
                            <div class="pamTableCell pogrubione dwadododlu"><p> rozliczenie:</p></div>
                            <div class="pamTableCell pogrubione dwadododlu"><p>%s<br>%s</p></div>
                            <div class="pamTableCell"><button type="button" class="btn btn-outline-secondary" onclick="DrukujRozliczenie(%d)">Drukuj</button></div>
                        </div>';

    private $clistaPomGrzPodz = "SELECT
        pomieszczenie.ID_POMIESZCZENIA,
        (SELECT TYP_PODZIELNIKA FROM rodzaj_podzielnika, zainstalowany_podzielnik, zainstalowany_grzejnik
        WHERE rodzaj_podzielnika.ID_RODZAJU_PODZIELNIKA = zainstalowany_podzielnik.ID_RODZAJU_PODZIELNIKA
        AND zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
        AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
        ORDER BY DATA_INSTALACJI_PODZIELNIKA DESC LIMIT 1) TYP_PODZIELNIKA,
        (SELECT ID_ZP FROM zainstalowany_podzielnik, zainstalowany_grzejnik
        WHERE zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
        AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
        ORDER BY zainstalowany_podzielnik.DATA_INSTALACJI_PODZIELNIKA DESC LIMIT 1) ID_ZP,
        (SELECT NUMER_FABRYCZNY FROM zainstalowany_podzielnik, zainstalowany_grzejnik
        WHERE zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
        AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
        ORDER BY DATA_INSTALACJI_PODZIELNIKA DESC LIMIT 1) NUMER_FABRYCZNY,
        (SELECT DATA_INSTALACJI_PODZIELNIKA FROM zainstalowany_podzielnik, zainstalowany_grzejnik
        WHERE zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
        AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
        ORDER BY DATA_INSTALACJI_PODZIELNIKA DESC LIMIT 1) DATA_INSTALACJI_PODZIELNIKA,
        (SELECT ID_ZG FROM zainstalowany_grzejnik WHERE zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
        ORDER BY DATA_INSTALACJI_GRZEJNIKA DESC LIMIT 1) ID_ZG,
        (SELECT DATA_INSTALACJI_GRZEJNIKA FROM zainstalowany_grzejnik WHERE zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
        ORDER BY DATA_INSTALACJI_GRZEJNIKA DESC LIMIT 1) DATA_INSTALACJI_GRZEJNIKA,
        (SELECT LICZBA_ZEBEREK FROM wspolczynnik_kq, zainstalowany_grzejnik
        WHERE wspolczynnik_kq.ID_KQ = zainstalowany_grzejnik.ID_KQ
        AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA  ORDER BY DATA_INSTALACJI_GRZEJNIKA DESC LIMIT 1)LICZBA_ZEBEREK ,
        (SELECT NAZWA_PRODUCENTA_GRZEJNIKA FROM producent_grzejnika, wspolczynnik_kq, zainstalowany_grzejnik, rodzaj_grzejnika
        WHERE producent_grzejnika.ID_PRODUCENTA_GRZEJNIKA = rodzaj_grzejnika.ID_PRODUCENTA_GRZEJNIKA
        AND wspolczynnik_kq.ID_RODZAJU_GRZEJNIKA = rodzaj_grzejnika.ID_RODZAJU_GRZEJNIKA
        AND wspolczynnik_kq.ID_KQ = zainstalowany_grzejnik.ID_KQ AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
        ORDER BY DATA_INSTALACJI_GRZEJNIKA DESC LIMIT 1)NAZWA_PRODUCENTA_GRZEJNIKA,
        rodzaj_pomieszczenia.KOD_POMIESZCZENIA,
        rodzaj_pomieszczenia.NAZWA_POMIESZCZENIA
        FROM pomieszczenie, rodzaj_pomieszczenia
        WHERE pomieszczenie.ID_RODZAJU_POMIESZCZENIA = rodzaj_pomieszczenia.ID_RODZAJU_POMIESZCZENIA
        %s ORDER BY rodzaj_pomieszczenia.NAZWA_POMIESZCZENIA";

    function WidokLokali($budynek, $filtr = '') {
        if (strlen($filtr) > 0)
            $filtr = " and exists (SELECT 1 FROM  uzytkownik WHERE jednostka_uzytkowa.ID_UZYTKOWNIKA = uzytkownik.ID_UZYTKOWNIKA
                    AND CONCAT_WS('', uzytkownik.NAZWISKO, uzytkownik.IMIE, uzytkownik.NAZWA, KodLok, kodsm) like '%$filtr%')";
        $rmi="(SELECT Rmi_dla_lokalu FROM wspolczynniki_dla_lokalu where wspolczynniki_dla_lokalu.ID_LOKALU =
                lokal.ID_LOKALU LIMIT 1) wspolczynnik ";
        $sql="SELECT lokal.ID_LOKALU, lokal.NUMER_RODZAJU, NR_LOKALU, POWIERZCHNIA_LOKALU, KodLok, kodsm,
        (SELECT IFNULL(uzytkownik.NAZWA, CONCAT_WS(' ', uzytkownik.NAZWISKO, uzytkownik.IMIE)) FROM uzytkownik
          WHERE jednostka_uzytkowa.ID_UZYTKOWNIKA = uzytkownik.ID_UZYTKOWNIKA) NazwaUz,
        (SELECT SubStr(rodzaj_lokalu.NAZWA_RODZAJU,1,1) FROM  rodzaj_lokalu WHERE lokal.NUMER_RODZAJU = rodzaj_lokalu.NUMER_RODZAJU) rodzLok,
        $rmi FROM lokal, jednostka_uzytkowa WHERE jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = $budynek
        AND lokal.ID_LOKALU = jednostka_uzytkowa.ID_LOKALU
        $filtr ORDER BY KodLok, kodsm";
        $tmp="";
        $db=new myBaza;
        $listaLok=$db->query($sql);
        foreach ($listaLok as $a)
            $tmp.= sprintf( $this->ctabVLok, $a['ID_LOKALU'], $a['ID_LOKALU'], $a['KodLok'], $a['ID_LOKALU'], $a['kodsm'], $a['NR_LOKALU'],
            FloatToStr($a['POWIERZCHNIA_LOKALU']), $a['NazwaUz'],$a['rodzLok'], FloatToStr($a['wspolczynnik']), $a['ID_LOKALU']);
        $fulltabela='<table class="table table-striped table-hover table-sm" id="widokLokali">%s<tbody>%s</tbody></table>';
        $tabela=sprintf($fulltabela, $this->ctabVLokNag, $tmp);
        return $tabela;
    }

    public function DajBudnekDlaLokalu($idlokalu){
        $sql = "SELECT ID_JEDNOSTKI_ROZLICZENIOWEJ FROM jednostka_uzytkowa WHERE ID_LOKALU = $idlokalu";
        $wynik = 0;
        $db = new myBaza;
        $budynek = $db->query($sql);
        foreach ($budynek as $a) {
            $wynik = $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'];
        }
        return $wynik;
    }

    public function DajPowierzchnieLokalu($idlokalu){
        $sql = "SELECT POWIERZCHNIA_LOKALU FROM lokal WHERE ID_LOKALU = $idlokalu";
        $wynik = 0;
        $db = new myBaza;
        $powierzchnia = $db->query($sql);
        foreach ($powierzchnia as $a) {
            $wynik = $a['POWIERZCHNIA_LOKALU'];
        }
        return $wynik;
    }


    function DajWynikRozliczenia($idlokalu, $arch = 0){
        $sql = "SELECT K_Stale, K_Zuzycia+K_Przesylu Wynik FROM rozliczenie_lokalu, Rozliczenie, KosztyBudynku
                 WHERE rozliczenie_lokalu.IdRoz = Rozliczenie.IdRoz AND Rozliczenie.IdKos = KosztyBudynku.IdKos
                 AND ID_LOKALU = $idlokalu AND Aktualne = $arch";
        $wynik = 0;
        $db = new myBaza;
        $listaLok = $db->query($sql);
        foreach ($listaLok as $a) {
            if ($a['K_Stale'] != 0)
                $wynik = $a['K_Stale'];
            else
                $wynik = $wynik + $a['Wynik'];
        }
        return $wynik;
    }

    function DajZaliczki($idlokalu, $arch = 0){
        if ($arch == 0)
            $sql = "SELECT Zaliczka FROM zaliczki_bonifikaty_kary_sezon where ID_LOKALU = $idlokalu";
        else
            $sql = "SELECT SUM(zaliczki) Zaliczka FROM rozliczenie_lokalu, rozliczenie, kosztybudynku
                    WHERE rozliczenie_lokalu.IdRoz = rozliczenie.IdRoz
                    AND rozliczenie.IdKos = kosztybudynku.IdKos
                    AND kosztybudynku.Aktualne = $arch
                    AND ID_LOKALU = $idlokalu";
        $wynik = 0;
        $db = new myBaza;
        $listaLok = $db->query($sql);
        foreach ($listaLok as $a) {
            $wynik = $wynik + $a['Zaliczka'];
        }
        return $wynik;
    }

    function PomieszczeniaIGrzejnki($idlokalu){
        //
        $sql = sprintf ($this->clistaPomGrzPodz, "  AND pomieszczenie.ID_LOKALU = $idlokalu");
        $tmp = "";
        $db = new myBaza;
        $listapom = $db->query($sql);
        foreach ($listapom as $a) {
            $tmp .= sprintf($this->ctabpom, $a['ID_POMIESZCZENIA'], $a['NAZWA_POMIESZCZENIA'], $a['NAZWA_PRODUCENTA_GRZEJNIKA'], $a['LICZBA_ZEBEREK'],
            $db->PolskaData($a['DATA_INSTALACJI_GRZEJNIKA']),$a['ID_POMIESZCZENIA'], $a['TYP_PODZIELNIKA'],
            $a['NUMER_FABRYCZNY'], $db->PolskaData($a['DATA_INSTALACJI_PODZIELNIKA']),$a['ID_ZG']);
        }
        $rozlicz = round($this->DajZaliczki($idlokalu) - $this->DajWynikRozliczenia($idlokalu), 2);

        if ($rozlicz >= 0)
            $rodzaj = 'nadpłata';
        else
            $rodzaj = 'niedopłata';

        $rozlicz = ABS($rozlicz);

        $tmp .= sprintf($this->cfootpom, $idlokalu, $idlokalu, $idlokalu, $rodzaj, FloatToStr($rozlicz), $idlokalu);

        $tabela = sprintf('<td colspan="7"><div class="container"><div class="pamTableBody"><div class="pamTable blueTable">%s%s</div></div></div></td>',
                        $this->ctabpomNag, $tmp);
        return $tabela;
    }

    public function DajKodLokalu($idlokalu){
        $sql = "SELECT IFNULL(kodlok, kodSm) kod FROM lokal WHERE ID_LOKALU = $idlokalu";
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik = $a['kod'];
        }
        return $wynik;
    }

    public function DajPionowePolozenie($idlokalu){
        $sql = "SELECT NUMER_PIONOWY FROM lokal WHERE ID_LOKALU = $idlokalu";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik = $a['NUMER_PIONOWY'];
        }
        return $wynik;
    }

    public function DajPoziomePolozenie($idlokalu){
        $sql = "SELECT POLOZENIE_POZIOME FROM lokal WHERE ID_LOKALU = $idlokalu";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik = $a['POLOZENIE_POZIOME'];
        }
        return $wynik;
    }

    public function DajRozeWiatrow($idlokalu){
        $sql = "SELECT ID_ROZY FROM lokal WHERE ID_LOKALU = $idlokalu";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik = $a['ID_ROZY'];
        }
        return $wynik;
    }

    public function DajRodzajLokalu($idlokalu){
        $sql = "SELECT NUMER_RODZAJU FROM lokal WHERE ID_LOKALU = $idlokalu";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik = $a['NUMER_RODZAJU'];
        }
        return $wynik;
    }

    public function DajNieruchomosci(){
        $wynik = "";
        $sql = "SELECT Id_nieruchomosci, CONCAT_WS(' ', ulice.NAZWA_ULICY, Nr_budynku, Symbol_nieruchomosci) Nazwa
        FROM nieruchomosci, ulice, miejscowosci  WHERE nieruchomosci.ID_ULICY = ulice.ID_ULICY
        AND nieruchomosci.ID_MIEJSCOWOSCI = miejscowosci.ID_MIEJSCOWOSCI ORDER By ulice.NAZWA_ULICY, Cast(Nr_budynku as int)";
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf ($this->cselect, $a['Id_nieruchomosci'], $a['Nazwa']);
        }
        return $wynik;
    }

    public function DajIdNieruch($idlokalu){
        $wynik = 0;
        $sql = "SELECT Id_nieruchomosci FROM lokal WHERE lokal.ID_LOKALU = $idlokalu";
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik = $a['Id_nieruchomosci'];
        }
        return $wynik;
    }

    public function ZmienRMI($rmi, $idlokalu, $idsezonu){
        $db = new myBaza();
        $idrmi = $db->Sprawdz("wspolczynniki_dla_lokalu", "ID_LOKALU", "ID_LOKALU = $idlokalu");
        if ($idrmi != 0)
            $sql = "UPDATE wspolczynniki_dla_lokalu SET Rmi_dla_lokalu = $rmi WHERE ID_LOKALU = $idlokalu";
        else {
            $sql = "INSERT INTO wspolczynniki_dla_lokalu (ID_LOKALU, ID_SEZONU, Rmi_dla_lokalu)
                   VALUES ($idlokalu, $idsezonu, $rmi)";
        }
        $db->Execute($sql);
        return 0;
    }

    public function ZmienLokal($idbudynku, $idlokalu, $pionowe, $poziome, $roza, $idrodzaju, $numer, $powierzchnia,
                               $kodsm, $kodlok, $imie, $nazwisko, $nazwa,  $rmi, $idnieruchomosci, $idsezonu){
        $db = new myBaza;
        $wynik = $idlokalu;
        if (strlen($pionowe) < 1)
            $pionowe = "null";
        if (strlen($poziome) < 1)
            $poziome = "null";
        if (strlen($idrodzaju) < 1)
            $idrodzaju = "null";
        if (strlen($numer) < 1)
            $numer = "null";
        else $numer = "'$numer'";
        if (strlen($imie) < 1)
            $imie = "null";
        else $imie = "'$imie'";
        if (strlen($nazwisko) < 1)
            $nazwisko = "null";
        else $nazwisko = "'$nazwisko'";
        if (strlen($nazwa) < 1)
            $nazwa = "null";
        else $nazwa = "'$nazwa'";
        $powierzchnia = str_replace(',', '.', $powierzchnia);
        if (strlen($powierzchnia) < 1)
            $powierzchnia = "null";
        if (strlen($roza) < 1)
            $roza = "null";
        if (strlen($kodsm) < 1)
            $kodsm = "null";
        else $kodsm = "'$kodsm'";
        if (strlen($kodlok) < 1)
            $kodlok = "null";
        else $kodlok = "'$kodlok'";
        $rmi = str_replace(',', '.', $rmi);
        if (strlen($rmi) < 1)
            $rmi = "1";
        if ($idlokalu != 0){
            $sql = "UPDATE lokal SET POLOZENIE_POZIOME = $poziome, NUMER_PIONOWY = $pionowe, NUMER_RODZAJU = $idrodzaju,
                    ID_ROZY = $roza, NR_LOKALU = $numer, POWIERZCHNIA_LOKALU = $powierzchnia, kodsm = $kodsm, KodLok = $kodlok,
                    Id_nieruchomosci = $idnieruchomosci WHERE ID_LOKALU = $idlokalu";
            $db->Execute($sql);
            if ($imie !== 'null' || $nazwisko !== 'null' || $nazwa !== 'null'){
                $sql = "UPDATE uzytkownik, jednostka_uzytkowa SET uzytkownik.IMIE = $imie, uzytkownik.NAZWISKO = $nazwisko,
                        uzytkownik.NAZWA = $nazwa WHERE uzytkownik.ID_UZYTKOWNIKA = jednostka_uzytkowa.ID_UZYTKOWNIKA
                        AND jednostka_uzytkowa.ID_LOKALU = $idlokalu";
                $db->Execute($sql);
            }
        }
        else {
            $sql = "INSERT INTO lokal (POLOZENIE_POZIOME,NUMER_PIONOWY,NUMER_RODZAJU,ID_ROZY,NR_LOKALU,POWIERZCHNIA_LOKALU, kodsm, KodLok, Id_nieruchomosci)
              VALUES($poziome, $pionowe, $idrodzaju, $roza, $numer, $powierzchnia, $kodsm, $kodlok, $idnieruchomosci)";
            $idlokalu = $db->Insert($sql);
            $wynik = $idlokalu;
            if ($imie !== 'null' || $nazwisko !== 'null' || $nazwa !== 'null'){
                $sql = "INSERT INTO uzytkownik (IDENTYFIKATOR_LOKATORA, IMIE, NAZWISKO, NAZWA)
                SELECT CONCAT(REPEAT ('0', Length(MAX(IDENTYFIKATOR_LOKATORA)+1)), MAX(IDENTYFIKATOR_LOKATORA)+1), $imie, $nazwisko, $nazwa FROM uzytkownik";
                $iduzytkownika = $db->Insert($sql);
            }
            else $iduzytkownika = 'null';
            $sql = "INSERT INTO jednostka_uzytkowa (ID_LOKALU, ID_UZYTKOWNIKA, ID_JEDNOSTKI_ROZLICZENIOWEJ, DATA_UTWORZENIA_JU)
                    VALUES ($idlokalu, $iduzytkownika, $idbudynku, CURRENT_DATE)";
            $idjednostkiuzytkowej =  $db->Insert($sql);
            if ($idjednostkiuzytkowej !== 0){
                $sql = "UPDATE lokal SET ID_JEDNOSTKI_UZYTKOWEJ = $idjednostkiuzytkowej WHERE ID_LOKALU = $idlokalu";
                $db->Execute($sql);
            }
        }
        $this->ZmienRMI($rmi, $idlokalu, $idsezonu);
        return $wynik;
    }

    public function CzyMoznaUsunac($idlokalu){
        $sql = "ID_LOKALU = $idlokalu";
        $db = new myBaza();
        $wynik = $db->Sprawdz("pomieszczenie", "ID_LOKALU", $sql);
        if ($wynik == 0){
            if ($wynik == 0){
                $wynik = $db->Sprawdz("zaliczki_bonifikaty_kary_sezon", "ID_LOKALU", "$sql AND Zaliczka != 0");
                if ($wynik == 0){
                    $wynik = $db->Sprawdz("rozliczenie_lokalu", "ID_LOKALU", $sql);
                    if ($wynik == 0){
                        $wynik = $db->Sprawdz("wynik_rozliczenia_ju", "ID_LOKALU", $sql);
                        if ($wynik != 0)
                            $wynik = -5;
                    }
                    else
                        $wynik = -4;
                }
                else
                    $wynik = -3;
            }
            else
                $wynik = -2;
        }
        else
            $wynik = -1;
        return $wynik;
    }

    public function UsunLokal($idlokalu){
        $db = new myBaza();
        $sql = "DELETE FROM zaliczki_bonifikaty_kary_sezon WHERE ID_LOKALU = $idlokalu AND Zaliczka = 0";
        $db->Execute($sql);
        $sql = "DELETE FROM jednostka_uzytkowa WHERE ID_LOKALU = $idlokalu";
        $db->Execute($sql);
        $sql = "DELETE FROM lokal WHERE ID_LOKALU = $idlokalu";
        $db->Execute($sql);
        return 0;
    }

    public function SprawdKodLokalu($kodlok, $idlokalu){
        $db = new myBaza();
        $wynik = $db->Sprawdz("lokal", "ID_LOKALU", "kodlok = '$kodlok' and ID_LOKALU != $idlokalu");
        return $wynik;
    }

}

?>