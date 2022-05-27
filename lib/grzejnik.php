<?php

require_once "baza.php";
require_once "kontrolki.php";

class Grzejnik {
    private $cNagWProdG = '<thead>
                                <tr class="tabNaglowek">
                                    <th>Nazwa</th>
                                </tr>
                            </thead>
                            <tbody>
                            %s
                            </tbody>';
    private $ctabWProdG = '<tr class="tabSelect" onClick=ZaznaczProdG("%d") ondblclick="ZmienProducentaGrzejnika(%d)">
                        <td class="tabNazwaProdG"><a href="#" id="tdvProdG%d">%s</a></td>
                        </tr>';

    private $cNagWRodzG = '<thead>
                                <tr class="tabNaglowek">
                                    <th>Wymiar</th>
                                    <th>KC</th>
                                    <th>KCHF</th>
                                </tr>
                            </thead>
                            <tbody>
                            %s
                            </tbody>';
    private $ctabWRodzG = '<tr class="tabSelect" onClick=ZaznaczRodzG("%d") ondblclick="ZmienRodzajGrzejnika(%d)">
                    <td class="tabNazwaRodzGCenter"><a href="#" id="tdvRodzG%d">%s</a></td>
                    <td class="tabNazwaRodzG" id="tdvRodzGA%d">%s</td>
                    <td class="tabNazwaRodzG"id="tdvRodzGB%d">%s</td>
                    </tr>';

    private $cNagWWspKQ = '<thead>
                            <tr class="tabNaglowek">
                                <th>Żeberka</th>
                                <th>KQ</th>
                            </tr>
                        </thead>
                        <tbody>
                        %s
                        </tbody>';
    private $ctabWWspKQ = '<tr class="tabSelect" onClick=ZaznaczWspKQ("%d") ondblclick="ZmienWspKQ(%d)">
                            <td class="tabNazwaWspKQ"><a href="#" id="tdvWspKQ%d">%s</a></td>
                            <td class="tabNazwaWspKQ" id="tdvWspKQA%d">%s</td>
                        </tr>';

    public function WidokProducentaGrzejnikow($filtr = '', $poczatek = 0, $ile = 30){
        if (strlen($filtr) > 0)
            $filtr = " WHERE NAZWA_PRODUCENTA_GRZEJNIKA like '%$filtr%' ";
        $sql = "SELECT ID_PRODUCENTA_GRZEJNIKA, NAZWA_PRODUCENTA_GRZEJNIKA FROM producent_grzejnika
        $filtr ORDER BY NAZWA_PRODUCENTA_GRZEJNIKA LIMIT $poczatek, $ile";
        $idproducenta = 0;
        $db = new myBaza;
        $listaProdG = $db->query($sql);
        $wynik = "";
        foreach ($listaProdG as $a) {
            if ($idproducenta == 0)
                $idproducenta = $a['ID_PRODUCENTA_GRZEJNIKA'];
            $wynik .= sprintf($this->ctabWProdG, $a['ID_PRODUCENTA_GRZEJNIKA'], $a['ID_PRODUCENTA_GRZEJNIKA'],
            $a['ID_PRODUCENTA_GRZEJNIKA'], $a['NAZWA_PRODUCENTA_GRZEJNIKA']);
        }
        $wynik = sprintf($this->cNagWProdG, $wynik);
        $ile = $this->IluProducentow();
        return array($wynik, $ile, $idproducenta);
    }

    public function IluProducentow(){
        $wynik = 0;
        $sql = "SELECT Count(*) Ile FROM producent_grzejnika";
        $db = new myBaza();
        $ile = $db->query($sql);
        foreach ($ile as $a) {
            $wynik = $a['Ile'];
        }
        return $wynik;
    }

    public function ZapiszProducentaGrzejnika($idproducenta, $nazwa){
        $wynik = 0;
        $db = new myBaza();
        if ($idproducenta == 0){
            $sql = "INSERT INTO producent_grzejnika (NAZWA_PRODUCENTA_GRZEJNIKA) VALUES ('$nazwa')";
            $wynik = $db->Insert($sql);
        }
        else {
            $sql = "UPDATE producent_grzejnika SET NAZWA_PRODUCENTA_GRZEJNIKA = '$nazwa' WHERE ID_PRODUCENTA_GRZEJNIKA = $idproducenta";
            $db->Execute($sql);
            $wynik = $idproducenta;
        }
        return $wynik;
    }

    public function CzyMoznaUsunacProducentaGrzejnika($idproducenta){
        $db = new myBaza();
        return $db->Sprawdz("rodzaj_grzejnika", "ID_PRODUCENTA_GRZEJNIKA", "ID_PRODUCENTA_GRZEJNIKA = $idproducenta");
    }

    public function UsunProducentaGrzejnika($idproducenta){
        $sql = "DELETE FROM producent_grzejnika WHERE ID_PRODUCENTA_GRZEJNIKA = $idproducenta";
        $db = new myBaza();
        $db->Execute($sql);
        return 0;
    }

    public function WidokRodzajowGrzejnikow($idproducenta, $filtr = '', $poczatek = 0, $ile = 30){
        if (strlen($filtr) > 0)
            $filtr = " WHERE ID_PRODUCENTA_GRZEJNIKA = $idproducenta AND WYMIAR_ABC like '%$filtr%' ";
        else $filtr = " WHERE ID_PRODUCENTA_GRZEJNIKA = $idproducenta ";
        $sql = "SELECT ID_RODZAJU_GRZEJNIKA, WYMIAR_ABC, WSPOLCZYNNIK_KC, WSPOLCZYNNIK_KCHF FROM rodzaj_grzejnika
        $filtr ORDER BY WYMIAR_ABC, WSPOLCZYNNIK_KC, WSPOLCZYNNIK_KCHF LIMIT $poczatek, $ile";
        $idrodzaju = 0;
        $db = new myBaza;
        $listaProdG = $db->query($sql);
        $wynik = "";
        foreach ($listaProdG as $a) {
            if ($idrodzaju == 0)
                $idrodzaju = $a['ID_RODZAJU_GRZEJNIKA'];
            $wynik .= sprintf($this->ctabWRodzG, $a['ID_RODZAJU_GRZEJNIKA'], $a['ID_RODZAJU_GRZEJNIKA'],
            $a['ID_RODZAJU_GRZEJNIKA'], $a['WYMIAR_ABC'], $a['ID_RODZAJU_GRZEJNIKA'], FloatToStr($a['WSPOLCZYNNIK_KC']),
            $a['ID_RODZAJU_GRZEJNIKA'], FloatToStr($a['WSPOLCZYNNIK_KCHF']));
        }
        $wynik = sprintf($this->cNagWRodzG, $wynik);
        $ile = $this->IloscRodzajow($idproducenta);
        return array($wynik, $ile, $idrodzaju);
    }

    public function IloscRodzajow($idproducenta){
        $wynik = 0;
        $sql = "SELECT Count(*) Ile FROM rodzaj_grzejnika WHERE ID_PRODUCENTA_GRZEJNIKA = $idproducenta";
        $db = new myBaza();
        $ile = $db->query($sql);
        foreach ($ile as $a) {
            $wynik = $a['Ile'];
        }
        return $wynik;
    }

    public function ZapiszRodzajGrzejnika($idrodzaju, $idproducenta, $wymiar, $kc, $kchf){
        $kc = StrToFloat($kc);
        $kchf = StrToFloat($kchf);
        $wynik = 0;
        $db = new myBaza();
        if ($idrodzaju == 0){
            $sql = "INSERT INTO rodzaj_grzejnika (ID_PRODUCENTA_GRZEJNIKA, WYMIAR_ABC, WSPOLCZYNNIK_KC, WSPOLCZYNNIK_KCHF)
            VALUES ($idproducenta, '$wymiar', $kc, $kchf)";
            $wynik = $db->Insert($sql);
        }
        else {
            $sql = "UPDATE rodzaj_grzejnika SET WYMIAR_ABC = '$wymiar', WSPOLCZYNNIK_KC = $kc, WSPOLCZYNNIK_KCHF = $kchf
            WHERE ID_RODZAJU_GRZEJNIKA = $idrodzaju";
            $db->Execute($sql);
            $wynik = $idrodzaju;
        }
        return $wynik;
    }

    public function CzyMoznaUsunacRodzajGrzejnika($idrodzaju){
        $db = new myBaza();
        return $db->Sprawdz("wspolczynnik_kq", "ID_RODZAJU_GRZEJNIKA", "ID_RODZAJU_GRZEJNIKA = $idrodzaju");
    }

    public function UsunRodzajGrzejnika($idrodzaju){
        $sql = "DELETE FROM rodzaj_grzejnika WHERE ID_RODZAJU_GRZEJNIKA = $idrodzaju";
        $db = new myBaza();
        $db->Execute($sql);
        return 0;
    }

    public function WidokWspKQ($idrodzaju){
        $sql = "SELECT ID_KQ, ID_RODZAJU_GRZEJNIKA, LICZBA_ZEBEREK, KQ FROM wspolczynnik_kq
        WHERE ID_RODZAJU_GRZEJNIKA = $idrodzaju ORDER BY Cast(LICZBA_ZEBEREK as int), KQ";
        $db = new myBaza;
        $lista = $db->query($sql);
        $wynik = "";
        foreach ($lista as $a) {
            $wynik .= sprintf($this->ctabWWspKQ, $a['ID_KQ'], $a['ID_KQ'],
            $a['ID_KQ'], $a['LICZBA_ZEBEREK'], $a['ID_KQ'], FloatToStr($a['KQ']));
        }
        $wynik = sprintf($this->cNagWWspKQ, $wynik);
        return $wynik;
    }

    public function ZapiszWspolczynnik($idwspolczynnika, $idrodzaju, $ilosczeberek, $wspolczynnik){
        $wynik = 0;
        $db = new myBaza();
        $wspolczynnik = StrToFloat($wspolczynnik);
        if ($idwspolczynnika == 0){
            $sql = "INSERT INTO wspolczynnik_kq (ID_RODZAJU_GRZEJNIKA, LICZBA_ZEBEREK, KQ)
            VALUES ($idrodzaju, '$ilosczeberek', $wspolczynnik)";
            $wynik = $db->Insert($sql);
        }
        else {
            $sql = "UPDATE wspolczynnik_kq SET LICZBA_ZEBEREK = '$ilosczeberek', KQ = $wspolczynnik WHERE ID_KQ = $idwspolczynnika";
            $db->Execute($sql);
            $wynik = $idwspolczynnika;
        }
        return $wynik;
    }

    public function CzyMoznaUsunacWspolczynnik($idwspolczynnika){
        $db = new myBaza();
        return $db->Sprawdz("zainstalowany_grzejnik", "ID_KQ", "ID_KQ = $idwspolczynnika");
    }

    public function UsunWspolczynnik($idwspolczynnika){
        $sql = "DELETE FROM wspolczynnik_kq WHERE ID_KQ = $idwspolczynnika";
        $db = new myBaza();
        $db->Execute($sql);
        return 0;
    }

}

?>