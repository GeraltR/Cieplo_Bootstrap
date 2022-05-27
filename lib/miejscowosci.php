<?php

require_once "baza.php";

class Miasto {
    private $cselect = '<option value="%d">%s</option>';

    private $cNagWMia = '<thead>
                            <tr class="tabNaglowek">
                                <th>Nazwa</th>
                            </tr>
                        </thead>
                        <tbody>
                        %s
                        </tbody>';
    private $ctabWMia = '<tr class="tabSelect" onClick=ZaznaczMiasto("%d") ondblclick="ZmienMiasto(%d)">
                        <td class="tabNazwaMia"><a href="#" id="tdvMia%d">%s</a></td>
                        </tr>';

    public function ListaMiast(){
        $sql = "SELECT ID_MIEJSCOWOSCI, MIEJSCOWOSC FROM miejscowosci ORDER BY MIEJSCOWOSC";
        $db = new myBaza;
        $listamiast = $db->query($sql);
        $selectlista = "";
        foreach ($listamiast as $a) {
            $selectlista .= sprintf($this->cselect, $a['ID_MIEJSCOWOSCI'], $a['MIEJSCOWOSC']);
        }
        return $selectlista;
    }

    public function WidokMiast($filtr = ''){
        if (strlen($filtr) > 0)
            $filtr = " WHERE MIEJSCOWOSC like '%$filtr%' ";
        $sql = "SELECT ID_MIEJSCOWOSCI, MIEJSCOWOSC FROM miejscowosci $filtr ORDER BY MIEJSCOWOSC";
        $db = new myBaza;
        $listamiast = $db->query($sql);
        $wynik = "";
        foreach ($listamiast as $a) {
            $wynik .= sprintf($this->ctabWMia, $a['ID_MIEJSCOWOSCI'], $a['ID_MIEJSCOWOSCI'], $a['ID_MIEJSCOWOSCI'], $a['MIEJSCOWOSC']);
        }
        $wynik = sprintf($this->cNagWMia, $wynik);
        return $wynik;
    }

    public function ZapiszMiasto($idmiasta, $nazwa){
        $db = new myBaza();
        if ($idmiasta == 0){
            $sql = "INSERT INTO miejscowosci (MIEJSCOWOSC) VALUES ('$nazwa')";
            $wynik = $db->Insert($sql);
            $co = 'I';
        }
        else {
            $sql = "UPDATE miejscowosci SET MIEJSCOWOSC = '$nazwa' WHERE ID_MIEJSCOWOSCI = $idmiasta";
            $db->Execute($sql);
            $wynik = $idmiasta;
            $co = 'U';
        }
        return Array($wynik, $nazwa, $co);
    }


    public function CzyMoznaUsunacMiasto($idmiasta){
        $db = new myBaza();
        $wynik = $db->Sprawdz("jednostka_rozliczeniowa", "ID_MIEJSCOWOSCI", "ID_MIEJSCOWOSCI = $idmiasta");
        return array($wynik, $idmiasta);
    }

    public function UsunMiasto($idmiasta){
        $sql = "DELETE FROM miejscowosci WHERE ID_MIEJSCOWOSCI = $idmiasta";
        $db = new myBaza();
        $db->Execute($sql);
        return 0;
    }
}

