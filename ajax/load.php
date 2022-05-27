<?php

    require_once "../lib/stale.php";
    require_once "../lib/baza.php";
    require_once "../lib/import_danych.php";

    if (isset($_POST['typ']))
        $typ = $_POST['typ'];
    else $typ = '';
    if ($typ === 'logowanie'){
        $response = true;
        $haslo = $_POST['haslo'];
        $stale = new Stale;
        if ($haslo === $stale->tajnehaslo){
            $response = false;
        }
        echo $response;
    }
    elseif ($typ === '') {
        $wynik = 0;
        $filename = $_FILES['inputplik']['name'];
        $sciezka = "../kopia/tmp";
        $file = scandir($sciezka);
        $location = "../upload/".$filename;
        $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
        $imageFileType = strtolower($imageFileType);
        $valid_extensions = "zip";
        if($imageFileType === $valid_extensions) {
            if(move_uploaded_file($_FILES['inputplik']['tmp_name'],$location)){
                $stale = new Stale();
                $zip = new ZipArchive();
                if ($zip->open($location) === true) {
                    if ($zip->setPassword($stale->tajnehaslo))
                        $zip->extractTo($sciezka);
                    $zip->close();
                    if (file_exists("$sciezka/uzytkownik.sql"))
                        $wynik = 1;
                }
            }
        }
        echo $wynik;
    }
    elseif ($typ === 'zakladaniebazy'){
        $start = microtime();
        $db = new myBaza();
        $db->Execute("DROP DATABASE IF EXISTS cefeusz");
        $db = null;
        $reload = new ImportDanych(true);
        $dalej = $reload->ZalozKlucze("../dane/kopia/CreateTable.sql");
        $koniec = microtime();
        $start = explode(' ', $start);
        $koniec = explode(' ', $koniec);
        $roznica = round(($koniec[0]+$koniec[1])-($start[0]+$start[1]),0);
        $wynik = "ZAKŁADANIE TABEL TRWAŁO: $roznica SEKUND.<br>".str_repeat("-", 200)."<br>";
        header('Content-Type: application/json');
        echo json_encode(array(0, $wynik));
    }
    elseif ($typ === 'ladowanietabeli'){
        $tabela = $_POST['tabela'];
        $postep = $_POST['postep'];
        $wynik = '';
        $start = microtime();
        $reload = new ImportDanych();
        $wynik = $reload->ZaladujTabele($tabela);
        if ($postep < 81)
            $postep = $postep + 2;
        else $postep = 100;
        $koniec = microtime();
        $start = explode(' ', $start);
        $koniec = explode(' ', $koniec);
        $roznica = round(($koniec[0]+$koniec[1])-($start[0]+$start[1]),0);
        header('Content-Type: application/json');
        echo json_encode(array($wynik, $postep));
    }
    elseif ($typ === 'klucze'){
        $start = microtime();
        $reload = new ImportDanych();
        $reload->ZalozKlucze("../dane/kopia/klucze_indeksy.sql");
        $koniec = microtime();
        $start = explode(' ', $start);
        $koniec = explode(' ', $koniec);
        $roznica = round(($koniec[0]+$koniec[1])-($start[0]+$start[1]),0);
        echo "ZAKŁADANIE KLUCZY I INDEKSÓW: $roznica SEKUND.<br>".str_repeat("-", 200)."<br>";
    }




?>