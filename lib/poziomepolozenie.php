<?php

require_once "baza.php";

class PoziomePolozenie {

    private $cselect = '<option value="%d">%s</option>';

   public function ListaPoziomePolozenie(){
        $wynik = '';
        $sql = "SELECT POLOZENIE_POZIOME, IfNull(KOREKTA_POZIOM_DEF, 'brak definicji') wartosc FROM poziome_polozenie
              ORDER BY POLOZENIE_POZIOME";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf($this->cselect, $a['POLOZENIE_POZIOME'], $a['wartosc']);
        }
        return $wynik;
   }

}

?>