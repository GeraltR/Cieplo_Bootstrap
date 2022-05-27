<?php

require_once "baza.php";

class RodzajLokalu {

    private $cselect = '<option value="%d">%s</option>';

   public function ListaRodzajLoakalu(){
        $wynik = '';
        $sql = "SELECT NUMER_RODZAJU, NAZWA_RODZAJU FROM rodzaj_lokalu ORDER BY NUMER_RODZAJU";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf($this->cselect, $a['NUMER_RODZAJU'], $a['NAZWA_RODZAJU']);
        }
        return $wynik;
   }

}

?>