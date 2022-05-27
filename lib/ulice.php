<?php

require_once "baza.php";

class Ulica {
    private $cselect = '<option value="%d">%s</option>';
    private $cNagWUli = '<thead>
                            <tr class="tabNaglowek">
                                <th>Nazwa</th>
                            </tr>
                        </thead>
                        <tbody>
                        %s
                        </tbody>';
    private $ctabWUli = '<tr class="tabSelect" onClick=ZaznaczUlice("%d") ondblclick="ZmienUlice(%d)">
                        <td class="tabNazwaUli" id="tdvUli%d"><a href="#" id="vUli%d">%s</a></td>
                        </tr>';

    public function ListaUlic(){
        $sql = "SELECT ID_ULICY, NAZWA_ULICY FROM ulice ORDER BY NAZWA_ULICY";
        $db = new myBaza;
        $listaUlic = $db->query($sql);
        $selectlista = "";
        foreach ($listaUlic as $a) {
            $selectlista .= sprintf($this->cselect, $a['ID_ULICY'], $a['NAZWA_ULICY']);
        }
        return $selectlista;
    }

    public function WidokUlic($filtr = ''){
        if (strlen($filtr) > 0)
            $filtr = " WHERE NAZWA_ULICY like '%$filtr%'";
        $sql = "SELECT ID_ULICY, NAZWA_ULICY FROM ulice $filtr ORDER BY NAZWA_ULICY";
        $db = new myBaza;
        $listaUlic = $db->query($sql);
        $wynik = "";
        foreach ($listaUlic as $a) {
            $wynik .= sprintf($this->ctabWUli, $a['ID_ULICY'], $a['ID_ULICY'], $a['ID_ULICY'],
                    $a['ID_ULICY'], $a['NAZWA_ULICY']);
        }
        $wynik = sprintf($this->cNagWUli, $wynik);
        return $wynik;
    }

    public function ZapiszUlice($idulicy, $nazwa){
        $db = new myBaza();
        if ($idulicy == 0){
            $sql = "INSERT INTO ulice (NAZWA_ULICY) VALUES ('$nazwa')";
            $wynik = $db->Insert($sql);
            $co = 'I';
        }
        else {
            $sql = "UPDATE ulice SET NAZWA_ULICY = '$nazwa' WHERE ID_ULICY = $idulicy";
            $db->Execute($sql);
            $wynik = $idulicy;
            $co = 'U';
        }
        return Array($wynik, $nazwa, $co);
    }


    public function CzyMoznaUsunacUlice($idulicy){
        $db = new myBaza();
        $wynik = $db->Sprawdz("jednostka_rozliczeniowa", "ID_ULICY", "ID_ULICY = $idulicy");
        return array($wynik, $idulicy);
    }

    public function UsunUlice($idulicy){
        $sql = "DELETE FROM ulice WHERE ID_ULICY = $idulicy";
        $db = new myBaza();
        $db->Execute($sql);
        return 0;
    }

    public function DajUliceWezla($idwezla){
        $db = new myBaza();
        $wynik = $db->Sprawdz("wezel_co_i_cw", "ID_ULICY", "IDENTYFIKATOR_WEZLA = $idwezla");
        return $wynik;
    }


}


?>