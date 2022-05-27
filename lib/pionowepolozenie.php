<?php

require_once "baza.php";

class PionowePolozenie {

    private $cselect = '<option value="%d">%s</option>';

   public function ListaPionowePolozenie(){
        $wynik = '';
        $sql = "SELECT NUMER_PIONOWY, IfNull(WARTOSC_RMI_STANDARD, 'brak definicji') wartosc FROM pionowe_polozenie
               ORDER BY NUMER_PIONOWY";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf($this->cselect, $a['NUMER_PIONOWY'], $a['wartosc']);
        }
        return $wynik;
   }

}

?>