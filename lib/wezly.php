<?php

require_once "baza.php";

class Wezel {
    private $ctabVWez = '<tr class="tabSelect" onClick=ZaznaczWezel("%d",1)>
                        <td class="tabAdresWez"><a href="#" id="wez%d">%s</a></td>
                        </tr>';
    private $ctabWWez = '<tr class="tabSelect" onClick=ZaznaczWezel("%d",2) ondblclick="ZmienWezel(%d)">
                        <td class="tabNazwaWez" id="tdvwez%d">%s</td>
                        <td class="tabAdresWez"><a href="#" id="vwez%d">%s</a></td>
                        <td class="tabAdresWez">%s</td>
                        </tr>';
    private $ctabWWezNag = '<thead>
                            <tr class="tabNaglowek">
                                <th>Nazwa węzła</th>
                                <th>Nazwa ulicy</th>
                                <th>Posiada budynki</th>
                            </tr>
                        </thead>';
    private $cselect = '<option value="%d">%s</option>';

    function ListaWezlow ($szukaj, $idwezla) {
        if (strlen($szukaj) < 1)
            $filtr = "";
        else $filtr = "AND CONCAT_WS(' ', NAZWA_ULICY, NAZWA_WEZLA) like '%$szukaj%'";
        $sql="SELECT IDENTYFIKATOR_WEZLA, CONCAT_WS(' ', NAZWA_ULICY, NAZWA_WEZLA) Nazwa
                FROM wezel_co_i_cw, ulice WHERE wezel_co_i_cw.ID_ULICY = ulice.ID_ULICY
                $filtr
                ORDER BY ulice.NAZWA_ULICY LIMIT 30";
        $db = new myBaza;
        $lista = $db->query($sql);
        $tmp = "";
        $pocz = "";
        foreach ($lista as $a) {
            if (strlen($pocz) == 0 && $idwezla === 0)
                $idwezla = $a['IDENTYFIKATOR_WEZLA'];
            $tmp .= sprintf($this->ctabVWez, $a['IDENTYFIKATOR_WEZLA'], $a['IDENTYFIKATOR_WEZLA'], $a['Nazwa']);
        }
        $tmp =  "$pocz<tbody>$tmp</tbody>";
        return array($idwezla, $tmp);
    }

    function WidokWezlow($szukaj){
        if (strlen($szukaj) < 1)
            $filtr = "";
        else $filtr = " HAVING CONCAT_WS(' ', NAZWA_ULICY, NAZWA_WEZLA, Budynki) like '%$szukaj%'";
        $sql = "SELECT wezel_co_i_cw.IDENTYFIKATOR_WEZLA, NAZWA_ULICY, NAZWA_WEZLA,
        (SELECT GROUP_CONCAT(NAZWA_JEDNOSTKI_ROZLICZENIOWEJ) FROM jednostka_rozliczeniowa
            WHERE jednostka_rozliczeniowa.IDENTYFIKATOR_WEZLA = wezel_co_i_cw.IDENTYFIKATOR_WEZLA) Budynki
        FROM wezel_co_i_cw, ulice
        WHERE wezel_co_i_cw.ID_ULICY = ulice.ID_ULICY
         GROUP BY wezel_co_i_cw.IDENTYFIKATOR_WEZLA, NAZWA_ULICY, NAZWA_WEZLA $filtr ORDER BY ulice.NAZWA_ULICY";
        $tmp = "";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach($lista as $a){
            $tmp .= sprintf($this->ctabWWez, $a['IDENTYFIKATOR_WEZLA'], $a['IDENTYFIKATOR_WEZLA'],
            $a['IDENTYFIKATOR_WEZLA'], $a['NAZWA_WEZLA'], $a['IDENTYFIKATOR_WEZLA'], $a['NAZWA_ULICY'],$a['Budynki']);
        }
        $tmp = "$this->ctabWWezNag<tbody>$tmp</tbody>";
        return $tmp;
    }

    function ZmienNazweWezla($idwezla, $nazwa, $idulicy){
        $sql = "UPDATE wezel_co_i_cw SET NAZWA_WEZLA = '$nazwa', ID_ULICY = $idulicy WHERE IDENTYFIKATOR_WEZLA=$idwezla";
        $db = new myBaza;
        $wynik = $db->Execute($sql);
        return $wynik;
    }

    function WstawWezel($nazwa, $idulicy){
        $sql = "INSERT INTO wezel_co_i_cw (NAZWA_WEZLA, ID_ULICY, IDENTYFIKATOR_SPOLDZIELNI ) VALUES ('$nazwa', $idulicy, 1)";
        $db = new myBaza;
        $wynik = $db->Insert($sql);
        return $wynik;
    }

    function CzyMoznaUsunacWezel($idwezla){
        $sql = "SELECT IDENTYFIKATOR_WEZLA, NAZWA_JEDNOSTKI_ROZLICZENIOWEJ FROM jednostka_rozliczeniowa WHERE IDENTYFIKATOR_WEZLA=$idwezla";
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
                $wynik = "Węzeł jest powiązany z jednostką rozliczeniową: $wynik";
            else $wynik = "Węzeł jest powiązany z jednostkami rozliczeniowymi: $wynik";
        }
        return $wynik;
    }

    function UsunWezel($idwezla){
        $sql = "DELETE FROM wezel_co_i_cw WHERE IDENTYFIKATOR_WEZLA=$idwezla";
        $db = new myBaza;
        $db->Execute($sql);
        return 0;
    }

    public function WezlyDlaBudynku(){
        $sql = "SELECT IDENTYFIKATOR_WEZLA, NAZWA_ULICY, NAZWA_WEZLA
                FROM wezel_co_i_cw, ulice WHERE wezel_co_i_cw.ID_ULICY = ulice.ID_ULICY
                ORDER BY ulice.NAZWA_ULICY";
        $tmp = "";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach($lista as $a){
            $tmp .= sprintf($this->cselect, $a['IDENTYFIKATOR_WEZLA'], $a['NAZWA_WEZLA']);
        }
        return $tmp;
        ;
    }

    public function WolneBudynki(){
        $sql = "SELECT ID_JEDNOSTKI_ROZLICZENIOWEJ, NAZWA_JEDNOSTKI_ROZLICZENIOWEJ FROM jednostka_rozliczeniowa
        WHERE IFNULL(IDENTYFIKATOR_WEZLA, 0) = 0
        ORDER by NAZWA_JEDNOSTKI_ROZLICZENIOWEJ";
        $wynik = '';
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf($this->cselect, $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NAZWA_JEDNOSTKI_ROZLICZENIOWEJ']);
        }
        return $wynik;
    }

    public function BudynkiWezla($idwezla){
        $sql = "SELECT ID_JEDNOSTKI_ROZLICZENIOWEJ, NAZWA_JEDNOSTKI_ROZLICZENIOWEJ FROM jednostka_rozliczeniowa
        WHERE IDENTYFIKATOR_WEZLA = $idwezla
        ORDER by NAZWA_JEDNOSTKI_ROZLICZENIOWEJ";
        $wynik = '';
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf($this->cselect, $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['NAZWA_JEDNOSTKI_ROZLICZENIOWEJ']);
        }
        return $wynik;
    }

    public function UsunBudynekZWezla($idbudynku, $idwezla){
        $sql = "UPDATE jednostka_rozliczeniowa set IDENTYFIKATOR_WEZLA = null WHERE ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku";
        $db = new myBaza();
        $wynik = $db->Execute($sql);
        return $wynik;
    }

    public function DodajBudynekDoWezla($idbudynku, $idwezla){
        $sql = "UPDATE jednostka_rozliczeniowa set IDENTYFIKATOR_WEZLA = $idwezla WHERE ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku";
        $db = new myBaza();
        $wynik = $db->Execute($sql);
        return $wynik;
    }

}

?>