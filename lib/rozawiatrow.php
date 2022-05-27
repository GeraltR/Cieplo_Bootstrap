<?php

require_once "baza.php";

class RozaWiatrow {

    private $cselect = '<option value="%d">%s</option>';

   public function ListaRozaWiatrow(){
        $wynik = '';
        $sql = "SELECT ID_ROZY, CONCAT_WS(' ', KIERUNEK_SWIATA, KOREKTA_ROZA_DEF) wartosc FROM roza_wiatrow
                ORDER BY ID_ROZY";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf($this->cselect, $a['ID_ROZY'], $a['wartosc']);
        }
        return $wynik;
   }

}

?>