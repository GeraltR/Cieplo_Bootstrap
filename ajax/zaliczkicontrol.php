<?php
    error_reporting(0);
    session_start();

    require_once "../lib/baza.php";
    require_once "../lib/zaliczki.php";
    require_once '../import/excel_reader2.php';
    require_once '../import/spreadsheetreader.php';

    $db = new myBaza();
    if ($db->CzySesja() != 0){
        if (isset($_POST['typ'])){
            $zaliczka = new Zaliczka();
            $typ = $_POST['typ'];
            if ($typ === 'widokzaliczek'){
                header('Content-Type: application/json');
                $idbudynku = $_POST['idbudynku'];
                $wynik = $zaliczka->DajFormularz($idbudynku);
                echo json_encode($wynik);
            }
            elseif ($typ === 'zaliczkiimport'){
                header('Content-Type: application/json');
                if (isset($_POST['plik'])){
                    $Filepath = $_POST['plik'];
                    $idsezonu = $_POST['idsezonu'];
                    $idbudynku = $_POST['idbudynku'];
                    $czyusunac = $_POST['czyusunac'];
                    if (strlen($Filepath) > 1){
                        if ($czyusunac == 1 && $idsezonu != 0)
                        $zaliczka->UsunZaliczke($idsezonu);
                        $i = 0;
                        $wiersz = array();
                        $powod = array();
                        $StartMem = memory_get_usage();
                        try
                        {
                            $czyblad = 0;
                            $suma = 0;
                            $Spreadsheet = new SpreadsheetReader($Filepath);
                            $BaseMem = memory_get_usage();
                            $Index = 1; //index zakładki excel-a
                            $Spreadsheet -> ChangeSheet($Index);
                            foreach ($Spreadsheet as $Key => $Row){
                                if ($Row)
                                    if (substr_count($Row[1], "-") === 3){
                                        $i = $i +1;
                                        if ($Row[8] == '')
                                        $Row[8] = 0;
                                        $suma = $suma + $Row[8];
                                        $nazwa = Win2utf($Row[5]);
                                        $czyzapis = $zaliczka->WstawZaliczke ($idsezonu, $Row[1], $Row[11], $Row[8], $nazwa);
                                        if ($czyzapis != 0){
                                            $czyblad = 1;
                                            array_push($wiersz, $i);
                                            switch ($czyzapis) {
                                                case 100:
                                                    array_push($powod, 'nie znaleziono lokalu');
                                                    break;
                                                case 1:
                                                    array_push($powod, 'bład zapisu do tabeli');
                                                    break;
                                                default:
                                                    array_push($powod, 'nieokreślony błąd PHP');
                                            }
                                        }
                                    }
                                $CurrentMem = memory_get_usage();
                            }
                        }
                        catch (Exception $E)
                        {
                            echo $E -> getMessage();
                        }
                        unlink($Filepath);
                        $wynik='';
                        if ($czyblad != 0){
                            $wynik = '</br>';
                            foreach ($wiersz as $key => $a) {
                            $wynik.= "wiersz: $a => błąd: $powod[$key]</br>";
                            }
                        }
                        else {
                            $suma = FloatToStr($suma);
                            $wynik = "Zaimportowano $i wierszy na kwotę $suma";
                        }
                    }
                    echo json_encode(array($wynik, $idbudynku));
                }
            }
            elseif ($typ === 'zapiswartosczaliczki'){
                header('Content-Type: application/json');
                $idsezonu = $_POST['idsezonu'];
                $idlokalu = $_POST['idlokalu'];
                $wartosc = $_POST['wartosc'];
                $numerpola = $_POST['numerpola'];
                $idbudynku = $_POST['idbudynku'];
                $wynik = $zaliczka->ZapiszZaliczke($idsezonu,$idlokalu,$wartosc);
                $suma = $zaliczka->SumaZaliczek($idbudynku);
                echo json_encode(array($wynik, $numerpola, $suma));
            }

        }
        else {
            $filename = $_FILES['zaliczkifile']['name'];

            $location = "../upload/".$filename;
            $imageFileType = pathinfo($location,PATHINFO_EXTENSION);
            $imageFileType = strtolower($imageFileType);
            $valid_extensions = "xls";
            $response = 0;
            if($imageFileType === $valid_extensions) {
                if(move_uploaded_file($_FILES['zaliczkifile']['tmp_name'],$location)){
                    $response = $location;
                }
            }
            echo $response;
        }
    }
?>