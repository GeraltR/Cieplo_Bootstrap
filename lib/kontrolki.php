<?php
require_once "baza.php";


function DajWystawce(){
    $sql = "SELECT NAZWA_WYSTAWCY, NR_ULICY_WYSTAWCY, KOD_POCZTOWY_WYSTAWCY, TELEFON_WYSTAWCY,
    ulice.NAZWA_ULICY, miejscowosci.MIEJSCOWOSC FROM wystawca, ulice, miejscowosci
    WHERE wystawca.ID_ULICY = ulice.ID_ULICY AND wystawca.ID_MIEJSCOWOSCI = miejscowosci.ID_MIEJSCOWOSCI
    AND ID_WYSTAWCY = 1";
    $db = new myBaza;
    $wystawca = $db->query($sql);
    foreach ($wystawca as $a) {
        $nazwa_wystawcy = $a['NAZWA_WYSTAWCY'];
        $ulica_wystawcy = $a['NAZWA_ULICY'];
        $nruli_wystawcy = $a['NR_ULICY_WYSTAWCY'];
        $kodpocz_wystawcy = $a['KOD_POCZTOWY_WYSTAWCY'];
        $miasto_wystawcy = $a['MIEJSCOWOSC'];
        $telef_wystawcy = $a['TELEFON_WYSTAWCY'];
    }
    return array($nazwa_wystawcy, $ulica_wystawcy, $nruli_wystawcy, $kodpocz_wystawcy, $miasto_wystawcy, $telef_wystawcy);
}

function DajPolskaDate($data){
    return date("d.m.Y", strtotime($data));
}

function FloatToStr($warotsc){
    $l = strlen($warotsc);
    $i = strpos($warotsc, '.');
    if ($i >= 1)
        $j = $l - $i -1;
    else $j = 2;
    if ($j <= 1)
        $j = 2;
    $wynik = number_format($warotsc, $j, ',', ' ');
    return $wynik;
}

function StrToFloat($warotsc){
    $warotsc = str_replace(' ', '', $warotsc);
    return str_replace(',', '.', $warotsc);
}

function Win2utf ($sText) {
    $wynik = '';
    $k = strlen($sText);
    for ($i = 0; $i < $k; $i++) {
        $znak = $sText[$i];
        switch (ord($znak)){
        case 261:
            $wynik.='ą';
            break;
        case 281:
            $wynik.='ę';
            break;
        case 347:
            $wynik.='ś';
            break;
        case 263:
            $wynik.='ć';
            break;
        case 243:
            $wynik.='ó';
            break;
        case 324:
            $wynik.='ń';
            break;
        case 380:
            $wynik.='ż';
            break;
        case 378:
            $wynik.='ź';
            break;
        case 322:
            $wynik.='ł';
            break;
        case 260:
            $wynik.='Ą';
            break;
        case 280:
            $wynik.='Ę';
            break;
        case 346:
            $wynik.='Ś';
            break;
        case 262:
            $wynik.='Ć';
            break;
        case 211:
            $wynik.='Ó';
            break;
        case 323:
            $wynik.='Ń';
            break;
        case 379:
            $wynik.='Ż';
            break;
        case 377:
            $wynik.='Ź';
            break;
        case 321:
            $wynik.='Ł';
            break;
        default: $wynik.= $znak;

        }
    }
    return $wynik;
}

?>