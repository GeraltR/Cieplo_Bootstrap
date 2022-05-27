<?php
require_once "baza.php";
require_once "kontrolki.php";

class ImportDanych {

    private $plikReload;
    private $sciezkaPlikowDAT;
    private $dbcon;

    function __construct($create = false){
        $this->dbcon = new myBaza(true, $create);
    }
    function __destruct(){
    }


    private function DajIloscWierszy ($plik){
        $i=0;
        rewind($plik);
        while(!feof($plik)){
            $i+= substr_count(fread($plik, 8192), "\n");
        }
        return $i;
    }

    private function CzyJestPoczRok($wiersz, $pocz=0){
        $wiersz = substr($wiersz, $pocz);
        $i = strpos($wiersz, ",20");
        if ($i !== false){
            $k = false;
            while (!$k) {
                $tmp = substr($wiersz, $i, 5);
                $tmp = str_replace(array(",", "'"), "", $tmp);
                if (substr($wiersz, $i-1, 1) !== "'" && substr($wiersz, $i+4, 1) === "/" && strlen($tmp) === 5 && substr($wiersz, $i+13, 1) === ":")
                    $k = true;
                else if ($i < strlen($wiersz) - 10)
                        $i++;
                    else {
                        $k = true;
                        $i = false;
                    }
            }
        }
        return $i+$pocz;
    }
    //Dostosowane do wsadowych plików danych format tekstowy zbliżony do SQL
    //Wyszukanie pól z datą o dodanie na początku i na końcu apostrofa
    private function PoleDaty($wiersz){
       //pierwsza pętla działa dopóki w wierszu jest 00:00, lub 00:00\r\n
       //druga pętla sprawdza czy w wierszu jest ,20 lub 20 jest na początku
       // trzecia pętla leci po latach od 2004 do 2021 i jak znajdę ,rok/
       //a nasępnie na 13-tym znaku od znalezionego jest dwukropek to mam pole daty i wychodzimy
        $tmp = $wiersz;
        $i = 2004;
        $j = $this->CzyJestPoczRok($tmp);
        while ($j !== false) {
            if (substr($tmp, $j, 5) == $i."/"){
                $tmp = str_replace(",$i/", ",'$i/", $tmp);
                if ($j == 0)
                if ((substr($tmp, 0, 4) == $i) && (substr($tmp, 13, 1) == ":"))
                    $tmp = "'".$tmp;
                $tmp = str_replace("00:00,", "00:00',", $tmp);
                $tmp = str_replace("00:00\r\n", "00:00'\r\n", $tmp);
                if (strrpos($tmp, "00:00")+5 == strlen($tmp))
                $tmp = $tmp."'";
                $i = 2004; //reset i bo jak coś zmienił to inne pola mogą mieć ten sam rok
            }
            if ($i < 2022){
                $i++;
                $j = $this->CzyJestPoczRok($tmp, ($j>0)?$j-1:$j);
            }
            else $j = false;
        }
        return $tmp;
    }


    //funkcja zwraca wiersz uzupełniony o brakująe null-e i nawiasy
    function GetWierszDanych($wiersz){
        str_replace(chr(0), "", $wiersz);
        if (strlen($wiersz) > 17)
            $wiersz = $this->PoleDaty($wiersz);
        $i = strpos($wiersz, ",,");
        while ($i !== false){
            $wiersz = str_replace(",,", ",null,", $wiersz);
            $i = strpos($wiersz, ",,");
        }
        $wiersz = str_replace(",\r\n",",null", $wiersz);
        $wiersz = str_replace("\r\n","", $wiersz);
        $i = strlen($wiersz);
        if ($i > 0){
            if ($wiersz[$i-1] === ","){
                $wiersz = $wiersz."null";
                $i = $i + 1;
            }
            $wiersz = $wiersz."\r\n";
        }
        return $wiersz;
    }


    //funkcja otwiera plik z danymi, przekazuje do uzupełniania i oddaje do inserta
    private function PrepareData($plikDat, $tabela="", $poczatek="", $czyDobazy = false){
        $tmp="";
        $i = 0;
        ob_start();
        $start = microtime();
        if (strlen($tabela)>0){
            echo "Rozpoczęto import do: $tabela.<br>";
            // $zdanie = "TRUNCATE TABLE $tabela";
            // $this->dbcon->UruchomZdanie($zdanie);
        }
        else
        echo "Rozpoczęto import do: $poczatek.<br>";
        ob_end_flush();
        flush();
        $j = $this->DajIloscWierszy ($plikDat);
        rewind($plikDat);
        if ($plikDat){
            while(!feof($plikDat)){
                set_time_limit(20);
                $a =  fgets($plikDat);
                $a = iconv( "Windows-1250", "UTF-8", $a);
                $a = $this->GetWierszDanych ($a);
                $k = strlen($a);
                if (!$czyDobazy){
                    if ($k > 0){
                        $a = "(".$a;
                        $k = $k + 1;
                    }
                    if ($i < $j && $k > 2){
                        $tmp = $tmp.$a."),<br>";
                        $i++;
                    }
                    else if ($k > 2)
                            $tmp = $tmp.$a.");<br>";
                        else {
                            $k = strrpos($tmp, ",<br>");
                            if ($k !== false)
                                $tmp = substr ($tmp, 0, $k-1).");<br>";
                            else $tmp = $tmp.");<br>";
                        }
                }
                else {
                    $a = str_replace("\r\n", "", $a);
                    $k = strlen($a);
                    if ($k > 0){
                        $tmp = "($a);";
                        if (strlen($poczatek) > 0){
                            $zdanie = "$poczatek $tmp";
                            $this->dbcon->UruchomZdanie($zdanie);
                        }
                    }
                }
            }
        }
        $koniec = microtime();
        $start = explode(' ', $start);
        $koniec = explode(' ', $koniec);
        $roznica = ($koniec[0]+$koniec[1])-($start[0]+$start[1]);
        if (strlen($tabela) > 0)
            echo "Import $tabela : $roznica sekund <br>".str_repeat("-", 200)."<br>";
        else  echo "$poczatek : $roznica sekund <br>".str_repeat("-", 200)."<br>";
        return $tmp;
    }

    //funkcja wyciągająca z AValue nazwę tabeli musi być między Insert into a nawiasem otwierającym
    private function DajNazweTabeli($AValue){
        $i=strpos($AValue, "INSERT INTO");
        $k=strpos($AValue, "(");
        if (($i !== false) && ($k !== false)){
            return str_replace('"', '', substr($AValue, $i+11, $k-11));
        }
        else return "";
    }

    private function PrepareInsertIntoPHP($CzyDoBazy = false){
        $nazwaTabeli = "";
        $wiersze = "";
        if ($this->plikReload){
            $i = 1;
            $j = $this->DajIloscWierszy($this->plikReload);
            rewind($this->plikReload);
            while(!feof($this->plikReload)){
                $tmp = fgets($this->plikReload);
                $pozycja = strpos($tmp, "INSERT INTO");
                if ($pozycja !== false){
                    $wiersze = str_replace('"', "", $tmp)." VALUES ";
                    $nazwaTabeli = $this->DajNazweTabeli($tmp);
                }
                else {
                    $pozycja = strpos($tmp, "UNLOAD");
                    if ($pozycja !== false){
                        $plikDat = substr($tmp, $pozycja);
                        $plikDat = str_replace(array("\r\n", "\r", "\n", "'"), "", $plikDat);
                        $plikDat = str_replace("\\\\", "/", $plikDat);
                        if (file_exists($this->sciezkaPlikowDAT.$plikDat)){
                            if (filesize($this->sciezkaPlikowDAT.$plikDat) > 0){
                                $tmpplikDat = @fopen($this->sciezkaPlikowDAT.$plikDat, "r");
                                if (!$CzyDoBazy)
                                    $wiersze = $wiersze.$this->PrepareData($tmpplikDat)."<br>";
                                else
                                    $this->PrepareData($tmpplikDat, $nazwaTabeli, $wiersze, true);
                                @fclose($tmpplikDat);
                            }
                        }
                    }
                }
                $i++;
            }
        }
        set_time_limit(120);
        return $wiersze."<br>";
    }

    public function ZalozKlucze($Plik){
        $plikKluczy = @fopen($Plik, "r");
        $klucz = "";
        $i = 0;
        $linia= "";
        while(!feof($plikKluczy)){
            $linia = fgets($plikKluczy);
            $i = strpos($linia, ";");
            if (($i !== false) && (strlen($klucz) > 0)) {
                $this->dbcon->UruchomZdanie($klucz);
                $klucz = "";
            }
            else $klucz = $klucz." ".$linia;
        }
        @fclose($plikKluczy);

    }

    public function WczytajLinie($Plik){
        $plikKluczy = @fopen($Plik, "r");
        $klucz = "";
        $linia= "";
        while(!feof($plikKluczy)){
            $linia .= fgets($plikKluczy);
            $j = strlen($linia);
            $i = strpos($linia, ');');
            if ((strlen($linia) > 1) && ($j >= $i+2) && ($i != 0)) {
                $this->dbcon->UruchomZdanie($linia);
                $linia = '';
            }
        }
        @fclose($plikKluczy);

    }

    public function PrzygotujZdanniaPHP($AReload){
        $this->plikReload = @fopen($AReload, "r");
        $this->sciezkaPlikowDAT = "../dane/";
        $zdania = $this->PrepareInsertIntoPHP(true);
        @fclose($this->plikReload);

        return $zdania;
    }

    public function OdtworzDane(){
        $kopia = @fopen("../dane/kopia/tabele.dat", "r");
        $klucz = "";
        $i = 0;
        $linia= "";
        while(!feof($kopia)){
            $linia = fgets($kopia);
            $i = strpos($linia, '"', 2);
            $tabela = substr($linia, 1, $i-1);
            set_time_limit(1000);
            $this->WczytajLinie("../dane/nic/$tabela.sql");
        }
        @fclose($kopia);

    }

    public function ZaladujTabele($tabela){
        $kopia = @fopen("../dane/kopia/tabele.dat", "r");
        $klucz = "";
        $wynik = "";
        $i = 0;
        $linia= "";
        $nastepny = false;
        $koniec = false;
        while(!$koniec){
            $linia = fgets($kopia);
            $i = strpos($linia, '"', 2);
            $tmp = substr($linia, 1, $i-1);
            if ($tmp == $tabela && $nastepny == false){
                set_time_limit(1000);
                $this->WczytajLinie("../kopia/tmp/$tmp.sql");
                $nastepny = true;
            }
            else {
                if ($nastepny){
                    $koniec = true;
                    $wynik = $tmp;
                }
            }
            if (feof($kopia))
                $koniec = true;
        }
        @fclose($kopia);
        return $wynik;
    }


}


?>