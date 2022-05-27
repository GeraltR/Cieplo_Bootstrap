<?php
require_once "baza.php";
require_once "kontrolki.php";

class Wydruk {

    private $cMain = '<div class="page">
                        %s
                    </div>';
    private $cprotokol = '<div class="content">
                            %s
                            <div class="row protOdczytWystawca">
                                %s
                            </div>
                            <div class="row protOdczytTytul">
                                %s
                            </div>
                            <div class="row protOdczytOdbiorcaNaglowek">
                                <div class="protImieNazwL">
                                    Imię i nazwisko / Nazwa
                                </div>
                                <div class="protImieNazwR">
                                    Adres
                                </div>
                            </div>
                            <div class="row protOdczytOdbiorca">
                                %s
                            </div>
                            <div class="row protOdczytPodzielniki">
                                %s
                            </div>
                            <div class="row protstopka">
                                <div class="protStopka3">
                                    Imię i nazwisko pracownika
                                </div>
                                <div class="protStopka3">
                                    Data i podpis
                                </div>
                                <div class="protStopka6">
                                    Potwierdzam wykonanie odczytu
                                    <div class="row protStopka6">
                                        <div class="col protStopka2">
                                            Data:
                                        </div>
                                        <div class="col protStopka2">
                                            Podpis:
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';

    private $cNagWystawca = '<div class="col-sm-6">
                                <h6><b>%s</b></br>ul. %s %s<br/>%s %s<br/>tel. %s</h6>
                                </div>
                                <div class="col-sm-6" style="text-align:right;">
                                    <b>Kod lokalu:</b>
                            </div>';

    private $cTytul = '<div class="col-md-12">
                            <b style="font-size: 1.2rem;">PROTOKÓŁ</b> odczytu
                            <div class="odsepWPoziom5"> </div>
                            <div class="checkbox"></div>
                            <div style="display: inline-block;">Odczyt rozliczeniowy</div>
                            <div class="odsepWPoziom5"> </div>
                            <div class="checkbox"></div>
                            <div style="display: inline-block;">Odczyt kontrolny</div>
                        </div>';

    private $cOdbiorca =    '<div class="col-md-6 protOdczytOdbiorca50L">
                                %s
                            </div>
                            <div class="col-md-6 protOdczytOdbiorca50R">
                                %s %s/%s, %s %s
                            </div>';

    private $cTabelaOdczytyNaglowk = '<tr>
                                        <th class="tableOdczytyLP">Lp.</th>
                                        <th class="tableOdczytyPom">Pom.</th>
                                        <th class="tableOdczytyNrFabryczny">Nr podzielnika</th>
                                        <th class="tableOdczytyOdczyt">Wskazane zużycia</th>
                                        <th class="tableOdczytyLastOdczyt">Ostatni odczyt</th>
                                        <th class="tableOdczytyUwagi">UWAGI</th>
                                    </tr>';
    private $cTabelaOdczyty = '<tr>
                                    <td class="tableOdczytyLP">%d</td>
                                    <td class="tableOdczytyPom">%s</td>
                                    <td class="tableOdczytyNrFabryczny">%s</td>
                                    <td class="tableOdczytyOdczyt"> </td>
                                    <td class="tableOdczytyLastOdczyt">%s</td>
                                    <td class="tableOdczytyUwagi"> </td>
                                </tr>';
    //private $cPrzerwa = '<div class="row protOdczytOdstepPion5">  </div>';


    public function TabelaPodzielnikow($idlokalu){
        $db = new myBaza;
        $sql = "SELECT zainstalowany_podzielnik.ID_ZP,
            (SELECT WSKAZANIE_PODZIELNIKA FROM stan_podzielnika WHERE stan_podzielnika.ID_ZP = zainstalowany_podzielnik.ID_ZP
            ORDER BY stan_podzielnika.DATA_ODCZYTU DESC LIMIT 1) LastOdczyt,
            jednostka_uzytkowa.ID_LOKALU, zainstalowany_podzielnik.ID_ZP, rodzaj_pomieszczenia.KOD_POMIESZCZENIA,
            zainstalowany_podzielnik.NUMER_FABRYCZNY
            FROM zainstalowany_podzielnik, zainstalowany_grzejnik, pomieszczenie, rodzaj_pomieszczenia, jednostka_uzytkowa
            WHERE zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
            AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
            AND pomieszczenie.ID_RODZAJU_POMIESZCZENIA = rodzaj_pomieszczenia.ID_RODZAJU_POMIESZCZENIA
            AND jednostka_uzytkowa.ID_LOKALU = pomieszczenie.ID_LOKALU
            AND zainstalowany_grzejnik.DATA_ZDJECIA_GRZEJNIKA is null AND zainstalowany_podzielnik.DATA_ZDJECIA_PODZIELNIKA is null
            AND jednostka_uzytkowa.ID_LOKALU = $idlokalu
            ORDER BY rodzaj_pomieszczenia.KOD_POMIESZCZENIA";
        $wynik = "";
        $lp = 1;
        $tabela = '<table class="tableOdczyty"><thead>'.$this->cTabelaOdczytyNaglowk.'</thead><tbody>%s</tbody></table>';
        $listapodzielnikow = $db->query($sql);
        foreach ($listapodzielnikow as $a) {
            $wynik.=sprintf($this->cTabelaOdczyty, $lp, $a['KOD_POMIESZCZENIA'], $a['NUMER_FABRYCZNY'], FloatToStr($a['LastOdczyt']));
            $lp = $lp + 1;
        }
        $wynik=sprintf($tabela, $wynik);
        return array($wynik, $lp);
    }

    public function ProtokolyOdczytu($idbudynku = 0, $idlokalu = 0){
        $db = new myBaza;
        if ($idlokalu != 0)
            $lokal = " AND lokal.ID_LOKALU = $idlokalu ";
        else $lokal = "";
        $sql = "SELECT IFNULL(lokal.KodLok, CONCAT_WS(' ',kodsm,'(SM)')) KodLokalu, lokal.ID_LOKALU,
                (SELECT IFNULL (uzytkownik.NAZWA, Concat_Ws (' ', uzytkownik.IMIE, uzytkownik.NAZWISKO)) FROM uzytkownik
                WHERE uzytkownik.ID_UZYTKOWNIKA = jednostka_uzytkowa.ID_UZYTKOWNIKA) kto,
                nieruchomosci.Kod_pocztowy,
                IFNULL(nieruchomosci.Nr_budynku, '') numer, miejscowosci.MIEJSCOWOSC, ulice.NAZWA_ULICY, lokal.NR_LOKALU
                FROM lokal, jednostka_uzytkowa, ulice, miejscowosci, nieruchomosci
                WHERE miejscowosci.ID_MIEJSCOWOSCI = nieruchomosci.ID_MIEJSCOWOSCI
                AND nieruchomosci.ID_ULICY = ulice.ID_ULICY AND lokal.Id_nieruchomosci = nieruchomosci.Id_nieruchomosci
                AND lokal.ID_JEDNOSTKI_UZYTKOWEJ = jednostka_uzytkowa.ID_JEDNOSTKI_UZYTKOWEJ
                AND jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku
                $lokal ORDER BY KodLokalu";
        $gornyprotokol = '';
        $dolnyprotokol = '';
        $wynik = '';
        $i = 1; //licznik lokali do określenia kiedy koniec strony razem z $k
        $j = 0; //numer strony
        $k = 0; //ilość pomieszczeń na jednej stronie
        $odbiorca = "";
        $wiersze = "";
        $wystawca = DajWystawce();
        $wydruk = $db->query($sql);
        foreach ($wydruk as $a) {
            $odbiorca = sprintf($this->cOdbiorca, $a['kto'], $a['NAZWA_ULICY'], $a['numer'], $a['NR_LOKALU'],
                $a['Kod_pocztowy'], $a['MIEJSCOWOSC']);
            $wystawcanag = sprintf($this->cNagWystawca, $wystawca[0], $wystawca[1], $wystawca[2], $wystawca[3], $wystawca[4], $wystawca[5]);
            $wystawcanag = str_replace('Kod lokalu:', 'Kod lokalu: '.$a['KodLokalu'], $wystawcanag);
            $wiersze = $this->TabelaPodzielnikow($a['ID_LOKALU']);
            $j = $j + 1;
            $strony = '<div class="cols=sm-2 mystrona">Strona '.$j.' z @PAMstron@</div>';
            $gornyprotokol = sprintf($this->cprotokol, $strony, $wystawcanag, $this->cTytul, $odbiorca, $wiersze[0]);
            $wynik.= sprintf($this->cMain, $gornyprotokol, $dolnyprotokol);
            $k = $wiersze[1];
            if (($k > 0) && ($k <= 7))
                $odstep = 'protOdczytOdstepPion10';
            elseif (($k > 7) && ($k < 10))
                $odstep = 'protOdczytOdstepPion8';
            elseif (($k >= 10) && ($k <=25))
                $odstep = 'protOdczytOdstepPion5';
            elseif ($k > 25)
                $odstep = 'protOdczytOdstepPion2';
            $wynik = str_replace('protOdczytOdstepPionN', $odstep, $wynik);

            if (($k > 25) && ($dolnyprotokol === '')) //jeżeli wyliczyłem że mam już 25 pokoi to bezwzględnie robię podział strony
            {
                $i = 1;
                $k = 0;
                $wynik.= sprintf($this->cMain, $gornyprotokol, $dolnyprotokol);
            }
            else {
                $i = $i + 1;
            }

        }
        $wynik = str_replace('@PAMstron@', $j, $wynik);
        return $wynik;
    }
}

?>