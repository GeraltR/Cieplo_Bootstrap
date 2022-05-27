<?php
    error_reporting(0);
    session_start();
    require_once "../lib/baza.php";
    require_once "../lib/rodzaje.php";

    $db = new myBaza();
    if ($db->CzySesja() != 0){
        $typ = $_POST['typ'];
        $rodzaj = new Rodzaj;
        if ($typ === 'widok'){
            header('Content-Type: application/json');
            $rodzajwidoku = $_POST['rodzaj'];
            if ($rodzajwidoku === 'widokRodzajowLokali'){
                $wynik = $rodzaj->WidokRodzajLoakalu();
            }
            elseif ($rodzajwidoku === 'widokRodzajuPomieszczenia'){
                $wynik = $rodzaj->WidokRodzajPomieszczenia();
            }
            elseif ($rodzajwidoku === 'widokRodzajuPodzielnika'){
                $wynik = $rodzaj->WidokRodzajPodzielnika();
            }
            echo json_encode(array($rodzajwidoku, $wynik));
        }
        elseif ($typ === 'zapiszrodzajlokalu'){
            $idrodzajulokalu = $_POST['idrodzajulokalu'];
            $nazwa = $_POST['nazwa'];
            $wynik = $rodzaj->ZapiszRodzajLokalu($idrodzajulokalu, $nazwa);
            echo $wynik;
        }
        elseif ($typ === 'czymoznausunacrodzajlokalu'){
            header('Content-Type: application/json');
            $idrodzajulokalu = $_POST['idrodzajulokalu'];
            $wynik = $rodzaj->CzyMoznaUsunacRodzajLokalu($idrodzajulokalu);
            if ($wynik != 0)
                $kodlok = $rodzaj->ListaLokaliDlaRodzaju($idrodzajulokalu);
            else $kodlok = '';
            echo json_encode(array($wynik, $idrodzajulokalu, $kodlok));
        }
        elseif ($typ === 'usuwanierodzajulokalu'){
            $idrodzajulokalu = $_POST['idrodzajulokalu'];
            $wynik = $rodzaj->UsunRodzajLokalu($idrodzajulokalu);
            echo 0;
        }
        elseif ($typ === 'zapiszrodzajpomieszczenia'){
            $idrodzajupomieszczenia = $_POST['idrodzajupomieszczenia'];
            $kod = $_POST['kod'];
            $nazwa = $_POST['nazwa'];
            $wynik = $rodzaj->ZapiszRodzajPomieszczenia($idrodzajupomieszczenia, $kod, $nazwa);
            echo $wynik;
        }
        elseif ($typ === 'czymoznausunacrodzajlpomieszczenia'){
            header('Content-Type: application/json');
            $idrodzajupomieszczenia = $_POST['idrodzajupomieszczenia'];
            $wynik = $rodzaj->CzyMoznaUsunacRodzajPomieszczenia($idrodzajupomieszczenia);
            echo json_encode(array($wynik, $idrodzajupomieszczenia));
        }
        elseif ($typ === 'usuwanierodzajupomieszczenia'){
            $idrodzajupomieszczenia = $_POST['idrodzajupomieszczenia'];
            $wynik = $rodzaj->UsunRodzajPomieszczenia($idrodzajupomieszczenia);
            echo 0;
        }
        elseif ($typ === 'zapiszrodzajpodzielnika'){
            $idrodzajupodzielnika = $_POST['idrodzajupodzielnika'];
            $nazwa = $_POST['nazwa'];
            $skala = $_POST['skala'];
            $grupa = $_POST['grupa'];
            $wynik = $rodzaj->ZapiszRodzajPodzielnika($idrodzajupodzielnika, $nazwa, $skala, $grupa);
            echo $wynik;
        }
        elseif ($typ === 'czymoznausunacrodzajpodzielnika'){
            header('Content-Type: application/json');
            $idrodzajupodzielnika = $_POST['idrodzajupodzielnika'];
            $wynik = $rodzaj->CzyMoznaUsunacRodzajPodzielnika($idrodzajupodzielnika);
            echo json_encode(array($wynik, $idrodzajupodzielnika));
        }
        elseif ($typ === 'usuwanierodzajupodzielnika'){
            $idrodzajupodzielnika = $_POST['idrodzajupodzielnika'];
            $wynik = $rodzaj->UsunRodzajPodzielnika($idrodzajupodzielnika);
            echo 0;
        }

    }

?>