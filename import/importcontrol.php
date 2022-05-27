<?php

require_once "../lib/import_danych.php";

$create = $_POST['create'];

$reload = new ImportDanych($create);

$typ = $_POST['typ'];
if ($typ === 'tabele'){
    header('Content-Type: application/json');
    $corobic = $_POST['corobic'];
    $start = microtime();
    if ($corobic == 1)
        $wynik = $reload->ZalozKlucze("../dane/CreateTable.sql");
    else
        $wynik = $reload->ZalozKlucze("../dane/kopia/CreateTable.sql");
    $koniec = microtime();
    $start = explode(' ', $start);
    $koniec = explode(' ', $koniec);
    $roznica = round(($koniec[0]+$koniec[1])-($start[0]+$start[1]),0);
    $wynik = "ZAKŁADANIE TABEL TRWAŁO: $roznica SEKUND.<br>".str_repeat("-", 200)."<br>";
    echo json_encode(array($wynik, $corobic));
}
elseif ($typ === 'import'){
    header('Content-Type: application/json');
    $start = microtime();
    $wynik = $reload->PrzygotujZdanniaPHP("../dane/reload_dane.SQL");
    $koniec = microtime();
    $start = explode(' ', $start);
    $koniec = explode(' ', $koniec);
    $roznica = round(($koniec[0]+$koniec[1])-($start[0]+$start[1]),0);
    $wynik = "IMPORT DANYCH: $roznica SEKUND.<br>".str_repeat("-", 200)."<br>";
    echo json_encode(array($wynik, "1"));
}
elseif ($typ === 'wczytajdane'){
    header('Content-Type: application/json');
    $start = microtime();
    $reload->OdtworzDane();
    $koniec = microtime();
    $start = explode(' ', $start);
    $koniec = explode(' ', $koniec);
    $roznica = round(($koniec[0]+$koniec[1])-($start[0]+$start[1]),0);
    $wynik = "Wczytanie danych: $roznica SEKUND.<br>".str_repeat("-", 200)."<br>";
    echo json_encode(array($wynik, "0"));
}
elseif ($typ === 'klucze'){
    header('Content-Type: application/json');
    $start = microtime();
    $reload->ZalozKlucze("../dane/klucze_indeksy.sql");
    $corobic = $_POST['corobic'];
    $koniec = microtime();
    $start = explode(' ', $start);
    $koniec = explode(' ', $koniec);
    $roznica = round(($koniec[0]+$koniec[1])-($start[0]+$start[1]),0);
    echo json_encode(array($corobic, "ZAKŁADANIE KLUCZY I INDEKSÓW: $roznica SEKUND.<br>".str_repeat("-", 200)."<br>"));
}
elseif ($typ === 'dodatki'){
    $start = microtime();
    set_time_limit(240);
    $reload->ZalozKlucze("lokale.sql");
    set_time_limit(120);
    $koniec = microtime();
    $start = explode(' ', $start);
    $koniec = explode(' ', $koniec);
    $roznica = round(($koniec[0]+$koniec[1])-($start[0]+$start[1]),0);
    echo "IMPORT Z PLIKU LOKALE.SQL: $roznica SEKUND.<br>".str_repeat("-", 200)."<br>";
}

?>
