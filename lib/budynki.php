<?php

require_once "baza.php";

class Budynek {
    private $ctabWBudNag = '<thead>
                            <tr class="tabNaglowek" id="widokBudynkowTabela">
                                <th>Nazwa budynku (jednostki rozliczeniowej)</th>
                                <th>Węzeł</th>
                                <th>Ulica</th>
                                <th>Miejscowość</th>
                                <th>Index JR</th>
                            </tr>
                        </thead>';

    private $ctabbudkolumny = '<td class="tabNazwaBudynku" id="tdvbud%d">%s</td>
                                <td class="tabNazwaBudynku"><a href="#" id="vbud%d">%s</a></td>
                                <td class="tabNazwaBudynku"><input type="hidden" id="vbudnumer%d" value="%s">%s %s</td>
                                <td class="tabNazwaBudynku"><input type="hidden" id="vbudkodp%d" value="%s">%s %s</td>
                                <td class="tabIndexJR"><input type="hidden" id="vbudindx%d" value="%s">%s</td>';
    private $ctabWBud = '<tr class="tabSelect" id="trvbud%d" onClick=ZaznaczBudynek("%d",2) ondblclick="ZmienBudynek(%d)">';
    private $cselect = '<option value="%d">%s</option>';

    private $ctabBudSezonNag = '<thead>
                                    <tr class="tabNaglowek">
                                        <th colspan="2" >Nazwa budynku (jednostki rozliczeniowej)</th>
                                    </tr>
                                </thead>';
    private $ctabBudSezon = '<tr class="tabSelect" onclick="ZaznaczBudynekWSezonie(%s, %d)">
                                <td class="tabNazwaBud50"><a href="#" id="budWSez%s%d">%s</a></td>
                                <td class="tabNazwaBud50">%s</td>
                            </tr>';
    private $cbtnUsunBudynekZSezonu = '<button type="button" class="btn btn-outline-danger" onclick="UsunBudynekZSezonu(%d)">Usuń z sezonu</button>';
    private $cbudynki = "SELECT ID_JEDNOSTKI_ROZLICZENIOWEJ, jednostka_rozliczeniowa.IDENTYFIKATOR_WEZLA, NAZWA_JEDNOSTKI_ROZLICZENIOWEJ,
    jednostka_rozliczeniowa.ID_ULICY, NR_ULICY_NIERUCHOMOSCI, KOD_POCZTOWY_NIERUCHOMOSCI, jednostka_rozliczeniowa.ID_MIEJSCOWOSCI,
    Indeks_JR,  (SELECT IFNULL(NAZWA_WEZLA, '') FROM wezel_co_i_cw WHERE wezel_co_i_cw.IDENTYFIKATOR_WEZLA = jednostka_rozliczeniowa.IDENTYFIKATOR_WEZLA) NAZWA_WEZLA,
    NAZWA_ULICY, MIEJSCOWOSC
    FROM jednostka_rozliczeniowa, ulice, miejscowosci
    WHERE ulice.ID_ULICY = jednostka_rozliczeniowa.ID_ULICY
    AND miejscowosci.ID_MIEJSCOWOSCI = jednostka_rozliczeniowa.ID_MIEJSCOWOSCI %s
    ORDER BY NAZWA_JEDNOSTKI_ROZLICZENIOWEJ";


    public function ListaBudynkowWWezle ($idwezla) {
        $sql = "SELECT ID_JEDNOSTKI_ROZLICZENIOWEJ, NAZWA_JEDNOSTKI_ROZLICZENIOWEJ, Indeks_JR, jednostka_rozliczeniowa.IDENTYFIKATOR_WEZLA
        FROM jednostka_rozliczeniowa WHERE jednostka_rozliczeniowa.IDENTYFIKATOR_WEZLA = $idwezla
        order by jednostka_rozliczeniowa.NAZWA_JEDNOSTKI_ROZLICZENIOWEJ";
        $selectlista='<select class="form-control" id="budynekWWezle">';
        $db = new myBaza;
        $listaBud=$db->query($sql);
        foreach ($listaBud as $a) {
            $selectlista.= sprintf($this->cselect, $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NAZWA_JEDNOSTKI_ROZLICZENIOWEJ']);
        }
        $selectlista.='</select>';
        return $selectlista;
    }


    public function WidokBudynkow($szukaj){
        if (strlen($szukaj) > 1)
        $filtr = " AND CONCAT_WS(' ', NAZWA_ULICY, NAZWA_JEDNOSTKI_ROZLICZENIOWEJ) like '%$szukaj%'";
        else $filtr = "";
        $tmp = "";
        $sql = sprintf($this->cbudynki, $filtr);
        $wzor =  $this->ctabWBud.$this->ctabbudkolumny.'</tr>';
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $tmp .= sprintf($wzor, $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'],$a['ID_JEDNOSTKI_ROZLICZENIOWEJ'],$a['ID_JEDNOSTKI_ROZLICZENIOWEJ'],
                    $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NAZWA_JEDNOSTKI_ROZLICZENIOWEJ'],$a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NAZWA_WEZLA'],
                    $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NR_ULICY_NIERUCHOMOSCI'], $a['NAZWA_ULICY'], $a['NR_ULICY_NIERUCHOMOSCI'],
                    $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['KOD_POCZTOWY_NIERUCHOMOSCI'], $a['KOD_POCZTOWY_NIERUCHOMOSCI'], $a['MIEJSCOWOSC'],
                    $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['Indeks_JR'], $a['Indeks_JR']);
        }
        $tmp = "$this->ctabWBudNag<tbody>$tmp</tbody>";
        return $tmp;
    }

    public function BudynekWartosciObce($idbud){
        $sql = "SELECT IDENTYFIKATOR_WEZLA, ID_ULICY, ID_MIEJSCOWOSCI
        FROM jednostka_rozliczeniowa WHERE ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbud";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik = array ($a['IDENTYFIKATOR_WEZLA'], $a['ID_ULICY'], $a['ID_MIEJSCOWOSCI']);
        }
        return $wynik;

    }

    public function ZmienBudynek($idbud, $nazwa, $idwezla, $iduli, $numerulicy, $kodpocz, $idmia, $indexjr ){
        $sql = "UPDATE jednostka_rozliczeniowa SET NAZWA_JEDNOSTKI_ROZLICZENIOWEJ = '$nazwa', IDENTYFIKATOR_WEZLA=$idwezla, ID_ULICY=$iduli,
        NR_ULICY_NIERUCHOMOSCI='$numerulicy', KOD_POCZTOWY_NIERUCHOMOSCI='$kodpocz', ID_MIEJSCOWOSCI=$idmia, Indeks_JR='$indexjr'
        WHERE ID_JEDNOSTKI_ROZLICZENIOWEJ=$idbud";
        $budynek = "";
        $db = new myBaza;
        $wynik = $db->Execute($sql);
        if ($wynik === 0){
            $filtr = " AND jednostka_rozliczeniowa.ID_JEDNOSTKI_ROZLICZENIOWEJ=$idbud";
            $sql = sprintf($this->cbudynki, $filtr);
            $lista = $db->query($sql);
            foreach ($lista as $a) {
                $budynek = sprintf($this->ctabbudkolumny, $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NAZWA_JEDNOSTKI_ROZLICZENIOWEJ'],$a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NAZWA_WEZLA'],
                $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NR_ULICY_NIERUCHOMOSCI'], $a['NAZWA_ULICY'], $a['NR_ULICY_NIERUCHOMOSCI'],
                $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['KOD_POCZTOWY_NIERUCHOMOSCI'], $a['KOD_POCZTOWY_NIERUCHOMOSCI'], $a['MIEJSCOWOSC'],
                $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['Indeks_JR'], $a['Indeks_JR']);
            }
        }
        return array ($idbud, $budynek);
    }

    public function DodajBudynek($nazwa, $idwezla, $iduli, $numerulicy, $kodpocz, $idmia, $indexjr){
        $sql = "INSERT INTO jednostka_rozliczeniowa (NAZWA_JEDNOSTKI_ROZLICZENIOWEJ, IDENTYFIKATOR_WEZLA, ID_ULICY,  NR_ULICY_NIERUCHOMOSCI,
                 KOD_POCZTOWY_NIERUCHOMOSCI, ID_MIEJSCOWOSCI, Indeks_JR) VALUES ('$nazwa', $idwezla, $iduli, '$numerulicy', '$kodpocz', $idmia, '$indexjr')";
        $budynek = "";
        $db = new myBaza;
        $idbud = $db->Insert($sql);
        if ($idbud != 0){
            $filtr = " AND jednostka_rozliczeniowa.ID_JEDNOSTKI_ROZLICZENIOWEJ=$idbud";
            $sql = sprintf($this->cbudynki, $filtr);
            $lista = $db->query($sql);
            foreach ($lista as $a) {
                $budynek = sprintf($this->ctabbudkolumny, $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NAZWA_JEDNOSTKI_ROZLICZENIOWEJ'],$a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NAZWA_WEZLA'],
                $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NR_ULICY_NIERUCHOMOSCI'], $a['NAZWA_ULICY'], $a['NR_ULICY_NIERUCHOMOSCI'],
                $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['KOD_POCZTOWY_NIERUCHOMOSCI'], $a['KOD_POCZTOWY_NIERUCHOMOSCI'], $a['MIEJSCOWOSC'],
                $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['Indeks_JR'], $a['Indeks_JR']);
            }
        }
        return array ($idbud, $budynek);

    }

    public function CzyMoznaUsunacBudynek($idbud){
        $sql = "SELECT jednostka_uzytkowa.ID_JEDNOSTKI_UZYTKOWEJ, KodLok FROM jednostka_uzytkowa, lokal WHERE jednostka_uzytkowa.ID_JEDNOSTKI_UZYTKOWEJ
        AND lokal.ID_JEDNOSTKI_UZYTKOWEJ AND jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ=$idbud";
        $wynik = "";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a){
            $wynik .= $a['KodLok'].", ";
        }
        if (strlen($wynik) > 2) {
            $wynik = substr($wynik, 0, strlen($wynik) - 2);
            $tmp = strpos($wynik, ",");
            if ($tmp === false)
                $wynik = "Budynek jest powiązany z lokalem: $wynik";
            else $wynik = "Budynek jest powiązany z wieloma lokalami.";
        }
        return $wynik;
    }

    public function UsunBudynek($idbud){
        $sql = "DELETE FROM jednostka_rozliczeniowa WHERE ID_JEDNOSTKI_ROZLICZENIOWEJ=$idbud";
        $db = new myBaza;
        $db->Execute($sql);
        return 0;
    }

    public function WidokBudynkowWSezonie($idsezonu, $slownik='T'){
        $wynik = "";
        $idbudynku = 0;
        $sql = sprintf ($this->cbudynki, " AND jednostka_rozliczeniowa.ID_SEZONU = $idsezonu");
        $db = new myBaza;
        $listabudynkow = $db->query($sql);
        foreach ($listabudynkow as $a) {
            if ($idbudynku === 0)
               $idbudynku = $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'];
            if ($slownik === 'T')
               $tmp = sprintf($this->cbtnUsunBudynekZSezonu, $a['ID_JEDNOSTKI_ROZLICZENIOWEJ']);
            else $tmp = "";
            $wynik .= sprintf($this->ctabBudSezon, "'".$slownik."'", $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'],
            $slownik, $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NAZWA_JEDNOSTKI_ROZLICZENIOWEJ'],$tmp);
        }
        $wynik = $this->ctabBudSezonNag."<tbody>$wynik</tbody>";
        return array($wynik, $idbudynku);
    }

    public function ListaBudynkowBezSezonu () {
        $sql = "SELECT ID_JEDNOSTKI_ROZLICZENIOWEJ, NAZWA_JEDNOSTKI_ROZLICZENIOWEJ, Indeks_JR, jednostka_rozliczeniowa.IDENTYFIKATOR_WEZLA
        FROM jednostka_rozliczeniowa WHERE jednostka_rozliczeniowa.ID_SEZONU is null
        order by jednostka_rozliczeniowa.NAZWA_JEDNOSTKI_ROZLICZENIOWEJ";
        $selectlista = '';
        $db = new myBaza;
        $listaBud=$db->query($sql);
        foreach ($listaBud as $a) {
            $selectlista.= sprintf($this->cselect, $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NAZWA_JEDNOSTKI_ROZLICZENIOWEJ']);
        }
        return $selectlista;
    }

    public function DajPowierzchnieBudynku($idbudynku){
        $sql = "SELECT SUM(POWIERZCHNIA_LOKALU) powbud FROM jednostka_uzytkowa, lokal where jednostka_uzytkowa.ID_LOKALU = lokal.ID_LOKALU
        AND ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku";
        $db = new myBaza;
        $powierzchniabud = $db->query($sql);
        foreach ($powierzchniabud as $a) {
            $wynik = $a['powbud'];
        }
        return $wynik;
    }


    public function DajKosztyJednZmienne($idbudynku){
        $sql = "SELECT  (koszty_z_opom / zuzycie_opom) KosztyJednPodz
                FROM rozliczenie where ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku";
        $wynik = 0;
        $db = new myBaza;
        $budynek = $db->query($sql);
        foreach ($budynek as $a) {
            $wynik = $a['KosztyJednPodz'];
        }
        if ($wynik == null)
           $wynik = 0;
        return $wynik;
    }
}


?>