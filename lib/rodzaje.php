<?php

require_once "baza.php";
require_once "kontrolki.php";

class Rodzaj {
    private $cNagWLokal = '<thead>
                                <tr class="tabNaglowek">
                                    <th>Nazwa</th>
                                </tr>
                            </thead>
                            <tbody>
                            %s
                            </tbody>';
    private $ctabWLokal = '<tr class="tabSelect" onClick=ZaznaczRodzajLokalu("%d") ondblclick="ZmienRodzajLokalu(%d)">
                        <td class="tabNazwa"><a href="#" id="tdvRodzLok%d">%s</a></td>
                        </tr>';

    private $cNagWPomieszczenie = '<thead>
                                <tr class="tabNaglowek">
                                    <th>Kod</th>
                                    <th>Nazwa</th>
                                </tr>
                            </thead>
                            <tbody>
                            %s
                            </tbody>';
    private $ctabWPomieszczenie = '<tr class="tabSelect" onClick=ZaznaczRodzajPomieszczenia("%d") ondblclick="ZmienRodzajPomieszczenia(%d)">
                                    <td class="tabNazwa"><a href="#" id="tdvRodzPom%d">%s</a></td>
                                    <td class="tabNazwa" id="tdvRodzPomA%d">%s</td>
                                </tr>';

    private $cNagWPodzielnik = '<thead>
                                <tr class="tabNaglowek">
                                    <th>Nazwa</th>
                                    <th>Skala</th>
                                    <th>Grupa WHE</th>
                                </tr>
                            </thead>
                            <tbody>
                            %s
                            </tbody>';
    private $ctabWPodzielnik = '<tr class="tabSelect" onClick=ZaznaczRodzajPodzielnika("%d") ondblclick="ZmienRodzajPodzielnika(%d)">
                                    <td class="tabNazwa"><a href="#" id="tdvRodzPodz%d">%s</a></td>
                                    <td class="tabRodzPodzSkala" id="tdvRodzPodzA%d">%s</td>
                                    <td class="tabRodzPodzWHE" id="tdvRodzPodzB%d">%s</td>
                                </tr>';

    public function WidokRodzajLoakalu(){
        $wynik = '';
        $sql = "SELECT NUMER_RODZAJU, NAZWA_RODZAJU FROM rodzaj_lokalu ORDER BY NAZWA_RODZAJU";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf($this->ctabWLokal, $a['NUMER_RODZAJU'], $a['NUMER_RODZAJU'],
            $a['NUMER_RODZAJU'], $a['NAZWA_RODZAJU']);
        }
        $wynik = sprintf($this->cNagWLokal, $wynik);
        return $wynik;
    }

    public function ZapiszRodzajLokalu($idrodzaju, $nazwa){
        $db = new myBaza();
        if ($idrodzaju == 0){
            $sql = "INSERT INTO rodzaj_lokalu(NAZWA_RODZAJU) VALUES ('$nazwa')";
            $wynik = $db->Insert($sql);
        }
        else {
            $sql = "UPDATE rodzaj_lokalu SET NAZWA_RODZAJU = '$nazwa' WHERE NUMER_RODZAJU = $idrodzaju";
            $db->Execute($sql);
            $wynik = $idrodzaju;
        }
        return $wynik;
    }

    public function CzyMoznaUsunacRodzajLokalu($idrodzaju){
        $db = new myBaza();
        $wynik = $db->Sprawdz("lokal", "NUMER_RODZAJU", "NUMER_RODZAJU = $idrodzaju");
        return $wynik;
    }

    public function ListaLokaliDlaRodzaju($idrodzaju){
        $wynik = "";
        $i = 0;
        $sql = "SELECT IFNULL(KodLok, kodsm) Kodlokalu FROM lokal WHERE NUMER_RODZAJU = $idrodzaju ORDER BY IFNULL(KodLok, kodsm) LIMIT 3";
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            if (strlen ($wynik) > 0)
                $wynik.=',';
            $wynik.= $a['Kodlokalu'];
            $i = $i +1;
        }
        if ($i > 1)
            $wynik = substr($wynik, 0, strlen($wynik)).'...';
        return $wynik;
    }

    public function UsunRodzajLokalu($idrodzaju){
        $sql = "DELETE FROM rodzaj_lokalu WHERE NUMER_RODZAJU = $idrodzaju";
        $db = new myBaza();
        $db->Execute($sql);
        return 0;
    }

    public function WidokRodzajPomieszczenia(){
        $wynik = '';
        $sql = "SELECT ID_RODZAJU_POMIESZCZENIA, KOD_POMIESZCZENIA, NAZWA_POMIESZCZENIA FROM rodzaj_pomieszczenia
        ORDER BY KOD_POMIESZCZENIA";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf($this->ctabWPomieszczenie, $a['ID_RODZAJU_POMIESZCZENIA'], $a['ID_RODZAJU_POMIESZCZENIA'],
            $a['ID_RODZAJU_POMIESZCZENIA'], $a['KOD_POMIESZCZENIA'], $a['ID_RODZAJU_POMIESZCZENIA'], $a['NAZWA_POMIESZCZENIA']);
        }
        $wynik = sprintf($this->cNagWPomieszczenie, $wynik);
        return $wynik;
    }

    public function ZapiszRodzajPomieszczenia($idrodzaju, $kod, $nazwa){
        $db = new myBaza();
        if ($idrodzaju == 0){
            $sql = "INSERT INTO rodzaj_pomieszczenia(KOD_POMIESZCZENIA, NAZWA_POMIESZCZENIA) VALUES ('$kod', '$nazwa')";
            $wynik = $db->Insert($sql);
        }
        else {
            $sql = "UPDATE rodzaj_pomieszczenia SET KOD_POMIESZCZENIA = '$kod', NAZWA_POMIESZCZENIA = '$nazwa'
            WHERE ID_RODZAJU_POMIESZCZENIA = $idrodzaju";
            $db->Execute($sql);
            $wynik = $idrodzaju;
        }
        return $wynik;
    }

    public function CzyMoznaUsunacRodzajPomieszczenia($idrodzaju){
        $db = new myBaza();
        $wynik = $db->Sprawdz("pomieszczenie", "ID_RODZAJU_POMIESZCZENIA", "ID_RODZAJU_POMIESZCZENIA = $idrodzaju");
        return $wynik;
    }

    public function UsunRodzajPomieszczenia($idrodzaju){
        $sql = "DELETE FROM rodzaj_pomieszczenia WHERE ID_RODZAJU_POMIESZCZENIA = $idrodzaju";
        $db = new myBaza();
        $db->Execute($sql);
        return 0;
    }

    public function WidokRodzajPodzielnika(){
        $wynik = '';
        $sql = "SELECT ID_RODZAJU_PODZIELNIKA, TYP_PODZIELNIKA, SKALA_STANDARDOWA, Grupa_WHE FROM rodzaj_podzielnika ORDER BY TYP_PODZIELNIKA";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf($this->ctabWPodzielnik, $a['ID_RODZAJU_PODZIELNIKA'], $a['ID_RODZAJU_PODZIELNIKA'],
            $a['ID_RODZAJU_PODZIELNIKA'], $a['TYP_PODZIELNIKA'], $a['ID_RODZAJU_PODZIELNIKA'], $a['SKALA_STANDARDOWA'],
            $a['ID_RODZAJU_PODZIELNIKA'], $a['Grupa_WHE']);
        }
        $wynik = sprintf($this->cNagWPodzielnik, $wynik);
        return $wynik;
    }

    public function ZapiszRodzajPodzielnika($idrodzaju, $nazwa, $skala, $grupa){
        $db = new myBaza();
        if ($idrodzaju == 0){
            $sql = "INSERT INTO rodzaj_podzielnika(TYP_PODZIELNIKA, SKALA_STANDARDOWA, Grupa_WHE) VALUES ('$nazwa', $skala, $grupa)";
            $wynik = $db->Insert($sql);
        }
        else {
            $sql = "UPDATE rodzaj_podzielnika SET TYP_PODZIELNIKA = '$nazwa', SKALA_STANDARDOWA = $skala, Grupa_WHE = $grupa
            WHERE ID_RODZAJU_PODZIELNIKA = $idrodzaju";
            $db->Execute($sql);
            $wynik = $idrodzaju;
        }
        return $wynik;
    }

    public function CzyMoznaUsunacRodzajPodzielnika($idrodzaju){
        $db = new myBaza();
        $wynik = $db->Sprawdz("zainstalowany_podzielnik", "ID_RODZAJU_PODZIELNIKA", "ID_RODZAJU_PODZIELNIKA = $idrodzaju");
        return $wynik;
    }

    public function UsunRodzajPodzielnika($idrodzaju){
        $sql = "DELETE FROM rodzaj_podzielnika WHERE ID_RODZAJU_PODZIELNIKA = $idrodzaju";
        $db = new myBaza();
        $db->Execute($sql);
        return 0;
    }

}