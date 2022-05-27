<?php
    error_reporting(0);
    session_start();
    require_once "../lib/baza.php";
    require_once "../lib/kopia.php";
    require_once "../lib/stale.php";

    $db = new myBaza();
    $kopia = new Kopia();

    if ($db->CzySesja() != 0){
        $typ = $_POST['typ'];
        if ($typ === 'kopia'){
            $obecna = $_POST['obecna'];
            $tabela = $kopia->DajListeTabel($obecna);
            $kopia->KopiaTabeli($tabela[0], $tabela[1], $tabela[2]);
            echo $tabela[3];
        }
        elseif ($typ === 'zip'){
            $sciezka = "../kopia/tmp";
            $file = scandir($sciezka);
            $stale = new Stale();
            $zip = new ZipArchive();
            $dzien = date("Ymd_His");
            $filename = '../kopia/kopia'.$dzien.'.zip';
            if ($zip->open($filename, ZipArchive::CREATE&&ZipArchive::FL_NODIR)!==TRUE) {
                exit("cannot open <$filename>\n");
            }
            foreach ($file as $klucz => $wartosc){
                if (strpos($wartosc, '.sql') > 1){
                    if (file_exists("$sciezka/$wartosc")){
                        $zip->addFile("$sciezka/$wartosc", $wartosc);
                        $zip->setEncryptionName($wartosc, ZipArchive::EM_AES_256, $stale->tajnehaslo);
                    }
                }
            }
            $zip->close();
            echo basename($filename);
        }
        elseif ($typ === 'wysylka'){
            $stale = new Stale();
            $plik = $_POST['plik'];
            $plikftp = $plik;
            $plik = "../kopia/$plik";
            $ftp_server = 'adresServera.ftp';
            $ftp_user_name = 'userName';
            $ftp_user_pass = $stale->hasloftp;
            $conn_id = ftp_connect($ftp_server);
            $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
            if (ftp_put($conn_id, $plikftp, $plik,  FTP_BINARY)) {
                $wynik = "Plik $plik wysłany prawidłowo";
            } else {
                $wynik = "Błąd wysyłania pliku $plik\n";
            }
            ftp_close($conn_id);
            echo $wynik;
        }
    }


?>