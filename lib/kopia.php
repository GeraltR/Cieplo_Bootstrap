<?php

require_once "baza.php";
require_once "kontrolki.php";


class Kopia {
    private $cfolderKopii = "../kopia";

    private function DajParametryInserta($wiersz, $nastepna){
        $i = strpos($wiersz, '"', 2);
        if ($i > 0){
            $tabela = substr($wiersz, 1, $i-1);
            $tabela = trim($tabela);
            $k = strpos($wiersz, 'VALUES (');
            $pola = substr($wiersz, $i+1, $k-$i-1);
            $pola = trim($pola);
            $wartosci = substr($wiersz, $k+8);
            $wartosci = str_replace(')', '', $wartosci);
            $wartosci = trim($wartosci);
        }
        else {
            $tabela = '';
            $pola = '';
            $wartosci = '';

        }
        return array($tabela, $pola, $wartosci, $nastepna);
    }

    public function DajListeTabel($tabela){
        $wynik = '';
        $wzor = '';
        $czynastepna = 1;
        $tabfile = fopen("../dane/kopia/tabele.dat", 'r');
        rewind($tabfile);
        if ($tabfile){
            while(!feof($tabfile) && ($czynastepna != 3)){
                $wiersz = fgets($tabfile);
                if (strlen($wiersz) > 1){
                    $i = strpos($wiersz, '"', 2);
                    if ($i > 0){
                        $tmp = substr($wiersz, 1, $i-1);
                        if ($czynastepna === 2){
                            $wynik = $tmp;
                            $insert = $this->DajParametryInserta($wzor, $wynik);
                            $czynastepna = 3;
                        }
                        if ($tmp === $tabela){
                            $wzor = $wiersz;
                            $czynastepna = 2;
                        }
                    }
                }
                else
                    $czynastepna = 3;
            }
            if (($wynik === '')){
                $insert = $this->DajParametryInserta($wzor, '');
            }
        }
        fclose($tabfile);
        return $insert;
    }

    public function KopiaTabeli($tabela, $pola, $wartosci){
        if (is_dir($this->cfolderKopii));
            mkdir($this->cfolderKopii);
        if (is_dir($this->cfolderKopii.'/tmp'));
            mkdir($this->cfolderKopii.'/tmp');
        if (file_exists("$this->cfolderKopii/tmp/$tabela.sql"))
            unlink("$this->cfolderKopii/tmp/$tabela.sql");
        $tabfile = fopen("$this->cfolderKopii/tmp/$tabela.sql", 'w');
        $tmp = "";
        $k = substr_count($wartosci, ',');
        $db = new myBaza();
        $sql = "SELECT $pola FROM $tabela ORDER BY 1";
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $tablicapol = array($a[0]);
            for ($i=1; ($i<=$k); $i++){
                if ($a[$i] === null)
                    $p = 'null';
                else $p = str_replace ("'", "''", $a[$i]);
                array_push($tablicapol, $p);
            }

            if (strlen($tmp) > 1)
                $tmp="\r\n";
            else
                $tmp = "";
            $tmp.=  "INSERT INTO $tabela ($pola) VALUES (".vsprintf($wartosci, $tablicapol).");";
            $tmp = str_replace("'null'", "null", $tmp);
            fwrite($tabfile, $tmp);
        }
        fclose($tabfile);
        return 0;
    }



}

?>