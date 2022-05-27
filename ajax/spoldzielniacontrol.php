<?php
    error_reporting(0);
    session_start();
    require_once "../lib/spoldzielnia.php";


    $db = new myBaza();
    if ($db->CzySesja() != 0){
        if (!isset($_POST['typ']))
            $typ = '';
        else
            $typ = $_POST['typ'];
        $spoldzielnia = new Spoldzielnia;
        if ($typ === 'widokspoldzieni'){
            header('Content-Type: application/json');
            $sp = $spoldzielnia->WidokSpodzelni();
            $wystawca = $spoldzielnia->WidokWystawcy();
            echo json_encode(array($sp, $wystawca));
        }
        elseif ($typ === 'zapiszSpoldzielnie'){
            $nazwa = $_POST['nazwa'];
			$iduli = $_POST['iduli'];
			$idmia = $_POST['idmia'];
			$numer = $_POST['numer'];
			$kodpoczt = $_POST['kodpoczt'];
            $wynik = $spoldzielnia->ZapiszSpodzelnie($nazwa, $iduli, $idmia, $numer, $kodpoczt);
            echo $wynik;
        }
        elseif ($typ === 'zapiszWystawce'){
            $nazwa = $_POST['nazwa'];
			$iduli = $_POST['iduli'];
			$idmia = $_POST['idmia'];
			$numer = $_POST['numer'];
			$kodpoczt = $_POST['kodpoczt'];
			$smtp = $_POST['smtp'];
			$port = $_POST['port'];
			$user = $_POST['user'];
			$pass = $_POST['pass'];
            $wynik = $spoldzielnia->ZapiszWystawce($nazwa, $iduli, $numer, $idmia, $kodpoczt, $smtp, $port, $user, $pass);
            echo $wynik;
        }
        elseif ($typ === ''){
            $wystawca = $spoldzielnia->WidokWystawcy();
            $oldplik = $wystawca[7];
            $wynik = '';
            $filename = $_FILES['inputpodpis']['name'];
            $pozycja = strrpos($filename, '.');
            if ($pozycja > 0)
                $extention = substr($filename, $pozycja, 4);
            else
                $extention = '.jpg';
            $newfile = "podpis".md5(rand()*rand()+rand()).$extention;
            $imageFileType = pathinfo($newfile,PATHINFO_EXTENSION);
            $imageFileType = strtolower($imageFileType);
            if ($_FILES['inputpodpis']['type'] == "image/jpeg" || $_FILES['inputpodpis']['type'] == "image/gif" || $_FILES['inputpodpis']['type'] == "image/png") {
                if(move_uploaded_file($_FILES['inputpodpis']['tmp_name'],'../img/'.$newfile)){
                    $wynik = $newfile;
                }
            }
            $zapisz = $spoldzielnia->UstawPodpis($newfile);
            if ($zapisz == 0)
                unlink('../img/'.$oldplik);
            echo $wynik;
        }
        elseif ($typ === 'parametryemail'){
            header('Content-Type: application/json');
            $wynik = $spoldzielnia->DajParamEmail();
            echo json_encode($wynik);
        }
        elseif ($typ === 'zapiszparememaili'){
            $tytul = $_POST['tytul'];
			$tresc = $_POST['tresc'];
            $wynik = $spoldzielnia->ZapiszParamEmail($tytul, $tresc);
            echo $wynik;
        }
        elseif ($typ === 'widokloga'){
            $filtr = $_POST['filtr'];
            $nadzien = $_POST['nadzien'];
            $wynik = $spoldzielnia->WidokLogaEmail($filtr, $nadzien);
            echo $wynik;
        }
    }

?>