<?php

require_once "baza.php";
require_once "kontrolki.php";


class GrzePodzWPom {
    private $cGrzejnikiWPom = 'SELECT rodzaj_pomieszczenia.NAZWA_POMIESZCZENIA, zainstalowany_grzejnik.DATA_INSTALACJI_GRZEJNIKA,
    zainstalowany_grzejnik.DATA_ZDJECIA_GRZEJNIKA,  wspolczynnik_kq.LICZBA_ZEBEREK, rodzaj_grzejnika.WYMIAR_ABC,
    producent_grzejnika.NAZWA_PRODUCENTA_GRZEJNIKA, zainstalowany_grzejnik.ID_ZG
    FROM zainstalowany_grzejnik, pomieszczenie, rodzaj_pomieszczenia, wspolczynnik_kq, rodzaj_grzejnika, producent_grzejnika
    where zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
    AND pomieszczenie.ID_RODZAJU_POMIESZCZENIA = rodzaj_pomieszczenia.ID_RODZAJU_POMIESZCZENIA
    AND zainstalowany_grzejnik.ID_KQ = wspolczynnik_kq.ID_KQ AND wspolczynnik_kq.ID_RODZAJU_GRZEJNIKA = rodzaj_grzejnika.ID_RODZAJU_GRZEJNIKA
    AND rodzaj_grzejnika.ID_PRODUCENTA_GRZEJNIKA = producent_grzejnika.ID_PRODUCENTA_GRZEJNIKA AND pomieszczenie.ID_POMIESZCZENIA = %d
    ORDER BY DATA_INSTALACJI_GRZEJNIKA DESC';

    private $ctabWGrzeNag = '<thead>
                                <tr class="tabNaglowek">
                                    <th>Nazwa producenta</th>
                                    <th>Wymiar</th>
                                    <th>Ilość żeberek</th>
                                    <th class="tabRight">Data instalacji</th>
                                    <th class="tabRight">Data zdjęcia</th>
                                    <th class="tabRight">Parametry</th>
                                    <th>Montaż</th>
                                </tr>
                            </thead>';
    private $ctabWidGrze = '<tr class="tabSelect" onclick="PokazPodzelnikiGrzejnika(%d)">
                                <td><a href="#" id="grze%d"%s>%s</a></td>
                                <td class="tabKodLok">%s</td>
                                <td class="tabRight">%s</td>
                                <td class="tabRight" %s>%s</td>
                                <td class="tabRight">%s</td>
                                <td class="tabRight">%s</td>
                                <td class="tabKodLok">%s</td>
                            </tr>';
    private $cbtnGrzejZmien = '<button type="button" class="btn btn-outline-secondary" onclick="ModyfikacjaGrze(%d)">Zmień</button>';
    private $cbtnGrzejDemont = '<button type="button" class="btn btn-outline-danger" onclick="ZdemontujGrze(%d)">Demontuj</button>';

    private $ctabWPodzNag = '<thead>
                                <tr class="tabNaglowek">
                                    <th>Typ podzielnika</th>
                                    <th class="tabRight tabPB">Numer fabryczny</th>
                                    <th class="tabRight">Data odczytu</th>
                                    <th class="tabRight">Wskazanie</th>
                                    <th class="tabRight">Data instalacji</th>
                                    <th class="tabRight">Data zdjęcia</th>
                                    <th class="tabRight">Parametry</th>
                                    <th>Montaż</th>
                                </tr>
                            </thead>';
    private $ctabWidPodz = '<tr class="tabSelect">
                            <td class="tabNumer" %s><a href="#" id="podz%d">%s</a></td>
                            <td class="tabNumer tabPB%s" %s>%s</td>
                            <td class="tabRight" %s>%s</td>
                            <td class="tabRight%s" %s>%s</td>
                            <td class="tabRight" %s>%s</td>
                            <td class="tabRight">%s</td>
                            <td class="tabRight">%s</td>
                            <td class="tabKodLok">%s</td>
                        </tr>';
    private $cbtnPodzZmien = '<button type="button" class="btn btn-outline-secondary" onclick="ModyfikacjaPodzielnika(%d,%d)">Zmień</button>';
    private $cbtnStatusZmien = '<button type="button" class="btn btn-outline-secondary" onclick="ZmianaStatusuOdczytu(%d)">Zmień status</button>';
    private $cbtnPodzDemont = '<button type="button" class="btn btn-outline-danger" onclick="ZdemontujPodzielnik(%d,%d)">Demontuj</button>';

    private $cselect = '<option value="%d">%s</option>';


    public function WidokGrzejnikowWPom ($idpomieszczenia){
        $sql = sprintf($this->cGrzejnikiWPom, $idpomieszczenia);
        $pomieszczenie = "";
        $tmp = "";
        $tmp = "";
        $czyaktywny = "";
        $nzwdatainstalacji = "";
        $db = new myBaza;
        $listaGrzej = $db->query($sql);
        foreach ($listaGrzej as $a) {
            if (strlen($pomieszczenie) < 1)
                $pomieszczenie = $a['NAZWA_POMIESZCZENIA'];
            if ($a['DATA_ZDJECIA_GRZEJNIKA'] === null){
                $btnDemont =  sprintf ($this->cbtnGrzejDemont, $a['ID_ZG']);
                $btnZmien = sprintf ($this->cbtnGrzejZmien, $a['ID_ZG']);
                $nzwdatainstalacji = 'id="nzwdatainstalacjigrze"';
                $czyaktywny = ' class="aktywnyGrzej"';
            }
            else {
                $btnDemont = "";
                $btnZmien = "";
                $nzwdatainstalacji = '';
                $czyaktywny = '';
            }
            $tmp .= sprintf($this->ctabWidGrze, $a['ID_ZG'], $a['ID_ZG'], $czyaktywny, $a['NAZWA_PRODUCENTA_GRZEJNIKA'], $a['WYMIAR_ABC'],$a['LICZBA_ZEBEREK'],
            $nzwdatainstalacji, $db->PolskaData ($a['DATA_INSTALACJI_GRZEJNIKA']), $db->PolskaData($a['DATA_ZDJECIA_GRZEJNIKA']),
            $btnZmien, $btnDemont);
        }
        $wynik = array($idpomieszczenia, $pomieszczenie, $this->ctabWGrzeNag.$tmp);
        return $wynik;
    }

    public function WidokPodzielnikow($idgrzejnika){
        $sql = "SELECT stan_podzielnika.DATA_ODCZYTU, stan_podzielnika.WSKAZANIE_PODZIELNIKA, stan_podzielnika.STATUS_ODCZYTU_PODZIELNIKA,
        stan_podzielnika.ID_ODCZYTU_PODZIELNIKA, zainstalowany_podzielnik.NUMER_FABRYCZNY, zainstalowany_podzielnik.DATA_INSTALACJI_PODZIELNIKA,
        zainstalowany_podzielnik.DATA_ZDJECIA_PODZIELNIKA, rodzaj_podzielnika.TYP_PODZIELNIKA, rodzaj_podzielnika.Grupa_WHE,
        zainstalowany_podzielnik.ID_ZP
        FROM stan_podzielnika, zainstalowany_podzielnik, rodzaj_podzielnika
        where stan_podzielnika.ID_ZP = zainstalowany_podzielnik.ID_ZP
        AND rodzaj_podzielnika.ID_RODZAJU_PODZIELNIKA = zainstalowany_podzielnik.ID_RODZAJU_PODZIELNIKA
        AND zainstalowany_podzielnik.ID_ZG = $idgrzejnika
        ORDER BY DATA_INSTALACJI_PODZIELNIKA DESC, DATA_ODCZYTU DESC";
        $tmp = "";
        $numerfab = "";
        $nzwtyppodz="";
        $nzwnrfab = "";
        $nzwdataodcz = "";
        $nzwwskazanie = "";
        $nzwdatainstalacji = "";
        $klasared = "";
        $db = new myBaza;
        $listaPodz = $db->query($sql);
        foreach ($listaPodz as $a) {
            $nzwtyppodz = 'id="nzwtyppodz"';
            $nzwnrfab = sprintf('id="nzwnrfab%d"', $a['ID_ZP']);
            $nzwdataodcz = sprintf('id="nzwdataodcz%d"', $a['ID_ODCZYTU_PODZIELNIKA']);
            $nzwwskazanie =  sprintf('id="nzwwskazanie%d"', $a['ID_ODCZYTU_PODZIELNIKA']);
            $nzwdatainstalacji = 'id="nzwdatainstalacji"';
            $btnZmien = sprintf($this->cbtnPodzZmien, $a['ID_ZP'], $a['ID_ODCZYTU_PODZIELNIKA']);
            if($a['DATA_ZDJECIA_PODZIELNIKA'] === null && $numerfab != $a['NUMER_FABRYCZNY']){
                $btnDemont = sprintf($this->cbtnPodzDemont, $a['ID_ZP'], $a['ID_ODCZYTU_PODZIELNIKA']);
                $numerfab = $a['NUMER_FABRYCZNY'];
            }
            else
                $btnDemont = sprintf($this->cbtnStatusZmien, $a['ID_ODCZYTU_PODZIELNIKA']);
            if ($a['STATUS_ODCZYTU_PODZIELNIKA'] == 17)
                $klasared = ' kolorred';
            else
                $klasared = '';
            $tmp .= sprintf($this->ctabWidPodz, $nzwtyppodz, $a['ID_ZP'], $a['TYP_PODZIELNIKA'], $klasared, $nzwnrfab, $a['NUMER_FABRYCZNY'], $nzwdataodcz,
            $db->PolskaData($a['DATA_ODCZYTU']), $klasared, $nzwwskazanie,  FloatToStr($a['WSKAZANIE_PODZIELNIKA']), $nzwdatainstalacji,
            $db->PolskaData($a['DATA_INSTALACJI_PODZIELNIKA']), $db->PolskaData($a['DATA_ZDJECIA_PODZIELNIKA']),
            $btnZmien, $btnDemont);
        }
        $wynik = $this->ctabWPodzNag.$tmp;
        return array($wynik, $idgrzejnika);
    }

    public function DajListeRodzPodz($idpodzielnika = 0, $idodczytu = 0){
        $wynik = "";
        $db = new myBaza;
        if ($idpodzielnika != 0){
            $sql = "SELECT * FROM zainstalowany_podzielnik where zainstalowany_podzielnik.ID_ZP = $idpodzielnika";
            $rodzaj = $db->query($sql);
            foreach ($rodzaj as $a) {
                $idrodzaju = $a['ID_RODZAJU_PODZIELNIKA'];
            }
        }
        else $idrodzaju = 0;
        $sql = "SELECT * FROM rodzaj_podzielnika ORDER BY TYP_PODZIELNIKA";
        $listarodzaju = $db->query($sql);
        foreach ($listarodzaju as $a) {
            $wynik .= sprintf($this->cselect, $a['ID_RODZAJU_PODZIELNIKA'], $a['TYP_PODZIELNIKA']);
        }
        return array ($idpodzielnika, $wynik, $idrodzaju, $idodczytu);
    }

    public function ModyfikacjaPodzielnika($idgrzejnika, $idpodzielnika, $idodczytu, $typpodz, $numerfab, $wskazanie, $datainstalacji, $dataodczytu){
        $db = new myBaza;
        if ($idpodzielnika != 0 && $idpodzielnika != null){
            $sql = "UPDATE zainstalowany_podzielnik SET NUMER_FABRYCZNY = '$numerfab', ID_RODZAJU_PODZIELNIKA= $typpodz,
                    DATA_INSTALACJI_PODZIELNIKA = '$datainstalacji' WHERE ID_ZP = $idpodzielnika";
            $db->Execute($sql);
        }
        else {
            $sql = "INSERT INTO zainstalowany_podzielnik (ID_ZG, NUMER_FABRYCZNY, ID_RODZAJU_PODZIELNIKA, DATA_INSTALACJI_PODZIELNIKA, skala)
                VALUES ($idgrzejnika, '$numerfab', $typpodz, '$datainstalacji', 0)";
            $idpodzielnika = $db->Insert($sql);
        }

        $wskazanie = str_replace(' ', '', str_replace(',', '.', $wskazanie));
        if (strlen($wskazanie) < 1)
            $wskazanie = 0;
        if($idodczytu != 0 && $idodczytu != null){
            $sql = "UPDATE stan_podzielnika SET WSKAZANIE_PODZIELNIKA = $wskazanie, DATA_ODCZYTU = '$dataodczytu'
                    WHERE ID_ODCZYTU_PODZIELNIKA = $idodczytu AND ID_ZP = $idpodzielnika";
            $db->Execute($sql);
        }
        else {
            $sql = "INSERT INTO stan_podzielnika (ID_ZP, DATA_ODCZYTU, WSKAZANIE_PODZIELNIKA, STATUS_ODCZYTU_PODZIELNIKA) VALUES ($idpodzielnika, '$dataodczytu',
                    $wskazanie, 10)";
            $db->Insert($sql);
        }
        return $idgrzejnika;
    }

    public function ZdjeciePodzielnika($idpodzielnika, $wskazanie, $datazdjecia, $dataodczytu){
        $tmp = 0;
        $wskazanie = str_replace(' ', '', str_replace(',', '.', $wskazanie));
        $db = new myBaza;
        $tmp = $db->Sprawdz('stan_podzielnika', 'ID_ODCZYTU_PODZIELNIKA', "ID_ZP=$idpodzielnika and DATA_ODCZYTU = '$dataodczytu'");
        if ($tmp == 0){
            $sql = "INSERT INTO stan_podzielnika (ID_ZP, DATA_ODCZYTU, WSKAZANIE_PODZIELNIKA, STATUS_ODCZYTU_PODZIELNIKA)
                    VALUES ($idpodzielnika, '$dataodczytu', $wskazanie, 17)";
            $tmp = $db->Insert($sql);
        }
        else {
            $sql = "UPDATE stan_podzielnika set STATUS_ODCZYTU_PODZIELNIKA = 17, WSKAZANIE_PODZIELNIKA = $wskazanie WHERE ID_ODCZYTU_PODZIELNIKA = $tmp";
            $db->Execute($sql);
        }
        $sql = "UPDATE zainstalowany_podzielnik SET DATA_ZDJECIA_PODZIELNIKA = '$datazdjecia' WHERE ID_ZP = $idpodzielnika";
        $tmp = $db->Execute($sql);
        return $tmp;
    }

    public function DajIdZG($idpodzielnika){
        $db = new myBaza;
        $zdanie = "SELECT ID_ZG FROM zainstalowany_podzielnik WHERE zainstalowany_podzielnik.ID_ZP = $idpodzielnika";
        $listaPodz = $db->query($zdanie);
        foreach ($listaPodz as $a){
            $wynik = $a['ID_ZG'];
        }
        return $wynik;
    }

    public function ListaProducentowGrzejnika(){
        $wynik = "";
        $sql = "SELECT ID_PRODUCENTA_GRZEJNIKA, NAZWA_PRODUCENTA_GRZEJNIKA FROM producent_grzejnika";
        $db = new myBaza;
        $listaproducent = $db->query($sql);
        foreach ($listaproducent as $a) {
            $wynik .= sprintf($this->cselect, $a['ID_PRODUCENTA_GRZEJNIKA'], $a['NAZWA_PRODUCENTA_GRZEJNIKA']);
        }
        return $wynik;
    }

    public function DajIdProducenta($idgrzejnika){
        $wynik = 0;
        $sql = "SELECT rodzaj_grzejnika.ID_PRODUCENTA_GRZEJNIKA FROM `zainstalowany_grzejnik`, wspolczynnik_kq, rodzaj_grzejnika
        WHERE zainstalowany_grzejnik.ID_KQ = wspolczynnik_kq.ID_KQ AND rodzaj_grzejnika.ID_RODZAJU_GRZEJNIKA = wspolczynnik_kq.ID_RODZAJU_GRZEJNIKA
        AND zainstalowany_grzejnik.ID_ZG = $idgrzejnika LIMIT 1";
        $db = new myBaza;
        $listaprod = $db->query($sql);
        foreach ($listaprod as $a) {
            $wynik = $a['ID_PRODUCENTA_GRZEJNIKA'];
        }
        return $wynik;
    }

    public function ListaRodzajowGrzejnika($idproducenta){
        $wynik = "";
        $sql = "SELECT ID_RODZAJU_GRZEJNIKA, WYMIAR_ABC FROM rodzaj_grzejnika WHERE ID_PRODUCENTA_GRZEJNIKA = $idproducenta";
        $db = new myBaza;
        $listarodzajow = $db->query($sql);
        foreach ($listarodzajow as $a) {
            $wynik .= sprintf($this->cselect, $a['ID_RODZAJU_GRZEJNIKA'], $a['WYMIAR_ABC']);
        }
        return $wynik;
    }

    public function DajIdRodzajGrzej($idgrzejnika){
        $wynik = 0;
        $sql = "SELECT ID_RODZAJU_GRZEJNIKA FROM zainstalowany_grzejnik, wspolczynnik_kq
        WHERE zainstalowany_grzejnik.ID_KQ = wspolczynnik_kq.ID_KQ AND zainstalowany_grzejnik.ID_ZG = $idgrzejnika LIMIT 1";
        $db = new myBaza;
        $listarodzajow = $db->query($sql);
        foreach ($listarodzajow as $a) {
            $wynik = $a['ID_RODZAJU_GRZEJNIKA'];
        }
        return $wynik;
    }

    public function ListaIlosciZeberek($idrodzaju){
        $wynik = "";
        $sql = "SELECT ID_KQ, LICZBA_ZEBEREK FROM wspolczynnik_kq where ID_RODZAJU_GRZEJNIKA = $idrodzaju ORDER BY CONVERT (LICZBA_ZEBEREK, INTEGER)";
        $db = new myBaza;
        $listazeberek = $db->query($sql);
        foreach ($listazeberek as $a) {
            $wynik .= sprintf($this->cselect, $a['ID_KQ'], $a['LICZBA_ZEBEREK']);
        }
        return $wynik;
    }

    public function DajId_KQ($idgrzejnika){
        $wynik = 0;
        $sql = "SELECT ID_KQ FROM zainstalowany_grzejnik WHERE zainstalowany_grzejnik.ID_ZG = $idgrzejnika LIMIT 1";
        $db = new myBaza;
        $listakq = $db->query($sql);
        foreach ($listakq as $a) {
            $wynik = $a['ID_KQ'];
        }
        return $wynik;
    }

    public function ModyfikacjaGrzejnika($idpomieszczenia, $idgrzejnika, $id_kq, $datainstalacji){
        $db = new myBaza;
        if ($idgrzejnika == 0){
            $sql = "INSERT INTO zainstalowany_grzejnik (ID_POMIESZCZENIA, ID_KQ, DATA_INSTALACJI_GRZEJNIKA) VALUES ($idpomieszczenia, $id_kq, '$datainstalacji')";
            $wynik = $db->Insert($sql);
        }
        else {
            $sql = "UPDATE zainstalowany_grzejnik SET ID_KQ = $id_kq, DATA_INSTALACJI_GRZEJNIKA = '$datainstalacji' WHERE ID_ZG = $idgrzejnika";
            $db->Execute($sql);
            $wynik = $idgrzejnika;
        }
        return $wynik;
    }

   public function ZdemontujGrze($idpomieszczenia, $idgrzejnika, $datazdjecia){
        $sql = "UPDATE zainstalowany_grzejnik SET DATA_ZDJECIA_GRZEJNIKA = '$datazdjecia' WHERE ID_ZG = $idgrzejnika";
        $db = new myBaza;
        $db->Execute($sql);
        return $idpomieszczenia;
    }

    public function DajStatusOdczytu($idodczytu){
        $sql = "SELECT STATUS_ODCZYTU_PODZIELNIKA FROM stan_podzielnika WHERE ID_ODCZYTU_PODZIELNIKA = $idodczytu";
        $db = new myBaza;
        $status = $db->query($sql);
        foreach ($status as $a) {
            switch ($a['STATUS_ODCZYTU_PODZIELNIKA']){
                case 10: $wynik = 'Odczyt rozliczeniowy'; break;
                case 17: $wynik = 'Zmiana podzielnika'; break;
                default: $wynik = 'nieopomiarowane/ryczałt';
            }
        }
        return $wynik;
    }

    public function ZmianaStatusOdczytu($idodczytu){
        $sql = "UPDATE stan_podzielnika
        SET STATUS_ODCZYTU_PODZIELNIKA = IF (STATUS_ODCZYTU_PODZIELNIKA = 17, 10, 17)
        WHERE ID_ODCZYTU_PODZIELNIKA = $idodczytu";
        $db = new myBaza();
        $wynik = $db->Execute($sql);
        return $wynik;
    }
}



?>