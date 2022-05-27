<?php

require_once "baza.php";
require_once "kontrolki.php";


class Zaliczka {
    private $cTable = '<thead><tr class="tabZalNaglowek">
                        <th>Kod lokalu</th>
                        <th>Lokator</th>
                        <th>Zaliczka</th>
                        <th class="tabZalInfo"></th>
                    </tr></thead>';
    private $cformularzOdczytow = '<tr class="tabFormularz" >
                                    <input type="hidden" id="zaliczLok%d" value="%d">
                                    <input type="hidden" id="zaliczUzyt%d" value="%d">
                                    <td>%s</td>
                                    <td>%s</td>
                                    <td><input class="inputCyfry wartOdczytu" type="text" id="zaliczP%d" value="%s"
                                        onkeypress=" return ZaliczkaKey(event, %d)" onfocusin="InputClick(event)"
                                        onclick="InputClick(event)" onkeyup="ZaliczkaKlawiszUp(event, %d)"  autocomplete="off"></td>
                                    <td class="tabZalInfo"><i class="bi bi-hourglass-split" id="zaliczF%d"></i></td>
                                </tr>';
    private $ctablefood = '<tfood><tr class="tabZalFood">
                           <td></td><td>Razem:</td><td id="widokSumaZaliczek"></td><td class="tabZalInfo"></td>
                            </tr></tfood>';

    public function DajFormularz($idbudynku) {
        $budynek = '';
        $sql = "SELECT IFNULL(KodLok, kodsm) kodlokalu, lokal.ID_LOKALU, uzytkownik.ID_UZYTKOWNIKA,
        IFNULL(uzytkownik.NAZWA, CONCAT_WS(' ', NAZWISKO, IMIE)) Nazwa,
        (SELECT Sum(Zaliczka) FROM zaliczki_bonifikaty_kary_sezon
            WHERE zaliczki_bonifikaty_kary_sezon.ID_LOKALU = jednostka_uzytkowa.ID_LOKALU) Zaliczka,
            (SELECT NAZWA_JEDNOSTKI_ROZLICZENIOWEJ FROM jednostka_rozliczeniowa
               WHERE jednostka_rozliczeniowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ) Budynek
        FROM jednostka_uzytkowa, lokal, uzytkownik
        WHERE jednostka_uzytkowa.ID_LOKALU = lokal.ID_LOKALU
        AND jednostka_uzytkowa.ID_UZYTKOWNIKA = uzytkownik.ID_UZYTKOWNIKA
        AND jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku
        ORDER BY kodlokalu";
        $wynik = '';
        $suma = 0;
        $i = 1;
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.= sprintf($this->cformularzOdczytow, $i, $a['ID_LOKALU'], $i, $a['ID_UZYTKOWNIKA'], $a['kodlokalu'],
                    $a['Nazwa'], $i, FloatToStr($a['Zaliczka']), $a['ID_LOKALU'], $a['ID_LOKALU'], $i);
                $i = $i + 1;
            $budynek = $a['Budynek'];
            $suma = $suma + $a['Zaliczka'];
        }
        $wynik = $this->cTable.'<tbody>'.$wynik.'</tbody>'.$this->ctablefood;
        return array($wynik, $budynek, FloatToStr($suma));
    }

    public function AktualizacjaNazw($idlokalu, $nazwa){
        $sql = "SELECT ID_UZYTKOWNIKA FROM jednostka_uzytkowa
        WHERE ID_LOKALU = $idlokalu";
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $iduzyt = $a['ID_UZYTKOWNIKA'];
        }
        if ($iduzyt != null){
            $sql = "UPDATE uzytkownik SET NAZWA = '$nazwa' WHERE ID_UZYTKOWNIKA = $iduzyt";
            $db->Execute($sql);
        }
        return 0;
    }

    public function WstawZaliczke($idsezonu, $kodLok, $kodsm, $zaliczka, $nazwa){
        $wynik = 0;
        $zaliczka = StrToFloat($zaliczka);
        $db = new myBaza();
        $idlokalu = $db->Sprawdz("lokal", "ID_LOKALU", "KodLok = '$kodLok'");
        if ($idlokalu == null) {
            $idlokalu = $db->Sprawdz("lokal", "ID_LOKALU", "KodLok = '$kodsm'");
        }
        if ($idlokalu != null) {
            $iduzyt = $db->Sprawdz("jednostka_uzytkowa", "ID_UZYTKOWNIKA", "ID_LOKALU = $idlokalu");
            $ile = $db->Sprawdz("zaliczki_bonifikaty_kary_sezon", "Count(*)", "ID_LOKALU = $idlokalu");
            if ($ile == null)
                $ile = 0;
            if ($ile > 1)
                $db->Execute("DELETE FROM zaliczki_bonifikaty_kary_sezon WHERE ID_LOKALU = $idlokalu");
            if ($ile == 0)
                $sql = "INSERT INTO zaliczki_bonifikaty_kary_sezon (ID_LOKALU, ID_SEZONU, ID_UZYTKOWNIKA, Zaliczka)
                       VALUES ($idlokalu, $idsezonu, $iduzyt, $zaliczka)";
            else
            $sql = "UPDATE zaliczki_bonifikaty_kary_sezon SET Zaliczka = $zaliczka, ID_SEZONU = $idsezonu, ID_UZYTKOWNIKA = $iduzyt
            WHERE ID_LOKALU = $idlokalu";
            $wynik = $db->Execute($sql);
            $this->AktualizacjaNazw($idlokalu, $nazwa);
        }
        else $wynik = 100;
        return $wynik;
    }

    public function UsunZaliczke($idsezonu){
        $sql = "DELETE FROM zaliczki_bonifikaty_kary_sezon WHERE ID_SEZONU = $idsezonu";
        $db = new myBaza();
        $db->Execute($sql);
        return 0;
    }

    public function ZapiszZaliczke($idsezonu, $idlokalu, $zaliczka){
        $zaliczka = StrToFloat($zaliczka);
        $db = new myBaza();
        $czyjest = $db->Sprawdz("zaliczki_bonifikaty_kary_sezon", "ID_LOKALU", "ID_LOKALU = $idlokalu");
        if ($czyjest != 0)
            $sql = "UPDATE zaliczki_bonifikaty_kary_sezon SET Zaliczka = $zaliczka, ID_SEZONU = $idsezonu WHERE ID_LOKALU = $idlokalu";
        else{
            $iduzyt = $db->Sprawdz("jednostka_uzytkowa", "ID_UZYTKOWNIKA", "ID_LOKALU = $idlokalu");
            $sql = "INSERT INTO zaliczki_bonifikaty_kary_sezon (ID_LOKALU, ID_SEZONU, ID_UZYTKOWNIKA, Zaliczka)
                    VALUES ($idlokalu, $idsezonu, $iduzyt, $zaliczka)";
        }
        $czyjest = $db->Execute($sql);
        if ($czyjest == 0)
            $wynik = $idlokalu;
        else
            $wynik = 0;
        return $wynik;
    }

    public function SumaZaliczek($idbudynku){
        $suma = 0;
        $sql = "SELECT Sum(Zaliczka) zaliczka FROM zaliczki_bonifikaty_kary_sezon, jednostka_uzytkowa
        WHERE zaliczki_bonifikaty_kary_sezon.ID_LOKALU = jednostka_uzytkowa.ID_LOKALU
        AND jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku";
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $suma = FloatToStr($a['zaliczka']);
        }
        return $suma;
    }
}

?>