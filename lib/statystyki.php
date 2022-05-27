<?php
    require_once "baza.php";
    require_once "kontrolki.php";
    require_once "spoldzielnia.php";

    class Statystyka {

        private $cNaglowek = '<table>
                                <tr><td><b>%s</b></td></tr>
                                <tr><td>%s %s</td></tr>
                                <tr><td>%s %s</td></tr>
                            </table>';
        private $cStatystyka = '<table class="lista">
                                <tr class="wiersze"><td><b>Całkowita liczba lokali</b></td><td> </td><td class="doprawej">%s</td></tr>
                                <tr class="wiersze"><td><b>Całkowita liczba użytkowników</b></td><td> </td><td class="doprawej">%s</td></tr>
                                <tr class="wiersze"><td><b>Aktualna liczba zainstalowanych grzejników</b></td><td> </td><td class="doprawej">%s</td></tr>
                                <tr class="wiersze"><td><b>Ilość zainstalowanych podzielników</b></td><td> </td><td class="doprawej">%s</td></tr>
                                <tr class="wiersze"><td><b>Całkowita liczna jednostek rozliczeniowych</b></td><td> </td><td class="doprawej">%s</td></tr>
                            </table>';

        public function DajNaglowek(){
            $sp = new Spoldzielnia();
            $wystwaca = $sp->WidokSpodzelni();
            $wynik = sprintf($this->cNaglowek, $wystwaca[0], $wystwaca[1], $wystwaca[2], $wystwaca[3] , $wystwaca[4]);
            return $wynik;
        }

        public function DajNazwaSeznu($idsezonu){
            $wynik = 'Brak nazwy';
            $sql = "SELECT NAZWA_SEZONU FROM sezon_grzewczy WHERE ID_SEZONU = $idsezonu";
            $db = new myBaza();
            $lista = $db->query($sql);
            foreach ($lista as $a) {
                $wynik = $a['NAZWA_SEZONU'];
            }
            return $wynik;
        }

        public function DajIloscLokali($idsezonu){
            $wynik = 0;
            if ($idsezonu != 0)
                $filtr = " AND jednostka_rozliczeniowa.ID_SEZONU = $idsezonu";
            else
                $filtr = "";
            $sql = "SELECT Count(*) Ile FROM jednostka_uzytkowa, jednostka_rozliczeniowa
            WHERE jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = jednostka_rozliczeniowa.ID_JEDNOSTKI_ROZLICZENIOWEJ $filtr";
            $db = new myBaza();
            $lista = $db->query($sql);
            foreach ($lista as $a) {
                $wynik = $a['Ile'];
            }
            return $wynik;
        }

        public function DajIloscUzytkownikow($idsezonu){
            $wynik = 0;
            if ($idsezonu != 0)
                $filtr = " AND jednostka_rozliczeniowa.ID_SEZONU = $idsezonu";
            else
                $filtr = "";
            $sql = "SELECT Count(*) Ile FROM jednostka_uzytkowa, jednostka_rozliczeniowa
            WHERE jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = jednostka_rozliczeniowa.ID_JEDNOSTKI_ROZLICZENIOWEJ
            AND jednostka_uzytkowa.ID_UZYTKOWNIKA IS NOT NULL $filtr";
            $db = new myBaza();
            $lista = $db->query($sql);
            foreach ($lista as $a) {
                $wynik = $a['Ile'];
            }
            return $wynik;
        }

        public function DajIloscBudynkow($idsezonu){
            $wynik = 0;
            if ($idsezonu != 0)
                $filtr = "WHERE jednostka_rozliczeniowa.ID_SEZONU = $idsezonu";
            else
                $filtr = "";
            $sql = "SELECT Count(*) Ile FROM jednostka_rozliczeniowa $filtr";
            $db = new myBaza();
            $lista = $db->query($sql);
            foreach ($lista as $a) {
                $wynik = $a['Ile'];
            }
            return $wynik;
        }

        public function DajIloscGrzejnikow($idsezonu, $nadzien){
            $wynik = 0;
            if ($idsezonu != 0)
                $filtr = " AND jednostka_rozliczeniowa.ID_SEZONU = $idsezonu";
            else
                $filtr = "";
            $sql = "SELECT Count(*) Ile FROM jednostka_uzytkowa, jednostka_rozliczeniowa, lokal, pomieszczenie, zainstalowany_grzejnik
            WHERE jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = jednostka_rozliczeniowa.ID_JEDNOSTKI_ROZLICZENIOWEJ
            AND jednostka_uzytkowa.ID_JEDNOSTKI_UZYTKOWEJ = lokal.ID_JEDNOSTKI_UZYTKOWEJ
            AND pomieszczenie.ID_LOKALU = lokal.ID_LOKALU
            AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
            AND IFNULL(zainstalowany_grzejnik.DATA_ZDJECIA_GRZEJNIKA, '$nadzien') >= '$nadzien'
            $filtr";
            $db = new myBaza();
            $lista = $db->query($sql);
            foreach ($lista as $a) {
                $wynik = $a['Ile'];
            }
            return $wynik;

        }
        public function DajIloscPodzielnikow($idsezonu, $nadzien){
            $wynik = 0;
            if ($idsezonu != 0)
                $filtr = " AND jednostka_rozliczeniowa.ID_SEZONU = $idsezonu";
            else
                $filtr = "";
            $sql = "SELECT COUNT(*) Ile
            FROM jednostka_uzytkowa, jednostka_rozliczeniowa, lokal, pomieszczenie, zainstalowany_grzejnik, zainstalowany_podzielnik
            WHERE jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = jednostka_rozliczeniowa.ID_JEDNOSTKI_ROZLICZENIOWEJ
            AND jednostka_uzytkowa.ID_JEDNOSTKI_UZYTKOWEJ = lokal.ID_JEDNOSTKI_UZYTKOWEJ
            AND pomieszczenie.ID_LOKALU = lokal.ID_LOKALU
            AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
            AND zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
            AND IFNULL(zainstalowany_podzielnik.DATA_ZDJECIA_PODZIELNIKA, '$nadzien' ) >= '$nadzien'
            $filtr";
            $db = new myBaza();
            $lista = $db->query($sql);
            foreach ($lista as $a) {
                $wynik = $a['Ile'];
            }
            return $wynik;
        }

        public function DajStatystyke($idsezonu, $nadzien) {
            $data = DajPolskaDate($nadzien);
            $wynik = '<br><br>'.$this->DajNaglowek();
            $wynik.= '<br><br><h4>EWIDENCJA SPÓŁDZIELNI na dzień '.$data.' <h4>';
            if ($idsezonu != 0){
                $tmp = $this->DajNazwaSeznu($idsezonu);
                $wynik.= "<br><h5>$tmp</h5><br>";
            }
            else
               $wynik.= '<br<h5></h5><br>';
            $ileLok = $this->DajIloscLokali($idsezonu);
            $ileuzytk = $this->DajIloscUzytkownikow($idsezonu);
            $ileBud = $this->DajIloscBudynkow($idsezonu);
            $ileGrzej = $this->DajIloscGrzejnikow($idsezonu, $nadzien);
            $ilePodz = $this->DajIloscPodzielnikow($idsezonu, $nadzien);
            $wynik.=sprintf($this->cStatystyka, $ileLok, $ileuzytk, $ileGrzej, $ilePodz, $ileBud);
            return $wynik;
        }


    }

?>