<?php

require_once "baza.php";
require_once "kontrolki.php";

class WPomieszczeniach {
    private $cgrzejniksql = "SELECT %s FROM producent_grzejnika, rodzaj_grzejnika, wspolczynnik_kq, zainstalowany_grzejnik, pomieszczenie, lokal, rodzaj_pomieszczenia
    where producent_grzejnika.ID_PRODUCENTA_GRZEJNIKA = rodzaj_grzejnika.ID_PRODUCENTA_GRZEJNIKA
    AND rodzaj_grzejnika.ID_RODZAJU_GRZEJNIKA = wspolczynnik_kq.ID_RODZAJU_GRZEJNIKA
    AND wspolczynnik_kq.ID_KQ = zainstalowany_grzejnik.ID_KQ
    AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
    AND pomieszczenie.ID_LOKALU = lokal.ID_LOKALU
    AND rodzaj_pomieszczenia.ID_RODZAJU_POMIESZCZENIA = pomieszczenie.ID_RODZAJU_POMIESZCZENIA
    %s %s %s";

    private $cNagWGrzej = '<thead>
                                <tr class="tabNaglowek">
                                    <th>Producent</th>
                                    <th>Wymiar</th>
                                    <th>Pom.</th>
                                    <th>Lokal</th>
                                </tr>
                            </thead>
                            <tbody>
                                %s
                            </tbody>';
    private $ctabWGrzej = '<tr class="tabSelect">
                                <td class="tabZamGrzeA">%s</td>
                                <td class="tabZamGrzeB">%s</td>
                                <td class="tabZamGrzeC">%s</td>
                                <td class="tabZamGrzeD"><button type="button" class="btn-xs btn-outline-secondary" onclick="SzukajWezlaZGrzej(%d)">%s</button></td>
                            </tr>';

    private $cNagWPodz = '<thead>
                            <tr class="tabNaglowek">
                                <th>Numer fabryczny</th>
                                <th>Pom.</th>
                                <th>Lokal</th>
                            </tr>
                        </thead>
                        <tbody>
                            %s
                        </tbody>';
    private $ctabWPodz = '<tr class="tabSelect">
                            <td class="tabZamPodzA">%s</td>
                            <td class="tabZamPodzB">%s</td>
                            <td class="tabZamPodzC"><button type="button" class="btn-xs btn-outline-secondary" onclick="SzukajWezlaZGrzej(%d)">%s</button></td>
                        </tr>';

    public function WidokGrejnikow($filtrA = '', $filtrB = '', $poczatek = 0, $ilosc = 30){
        $wynik = '';
        $pola = "NAZWA_PRODUCENTA_GRZEJNIKA, WYMIAR_ABC, IFNULL(Kodlok, kodSM) kodlokalu, NAZWA_POMIESZCZENIA, lokal.ID_LOKALU";
        $order = "ORDER BY kodlokalu, NAZWA_POMIESZCZENIA LIMIT $poczatek, $ilosc";
        if (strlen($filtrA) > 0)
            $filtrA = "AND NAZWA_PRODUCENTA_GRZEJNIKA like '%$filtrA%'";
        if (strlen($filtrB) > 0)
            $filtrB = "AND WYMIAR_ABC like '%$filtrB%'";
        $sql = sprintf($this->cgrzejniksql, $pola, $filtrA, $filtrB, $order);
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf($this->ctabWGrzej, $a['NAZWA_PRODUCENTA_GRZEJNIKA'], $a['WYMIAR_ABC'], $a['NAZWA_POMIESZCZENIA'],
            $a['ID_LOKALU'], $a['kodlokalu']);
        }
        $wynik = sprintf($this->cNagWGrzej, $wynik);
        return $wynik;
    }

    public function IleGrzejnikow($filtrA = '', $filtrB = ''){
        $wynik = 0;
        $pola = "count(*) Ile";
        $order = "";
        if (strlen($filtrA) > 0)
            $filtrA = "AND NAZWA_PRODUCENTA_GRZEJNIKA like '%$filtrA%'";
        if (strlen($filtrB) > 0)
            $filtrB = "AND WYMIAR_ABC like '%$filtrB%'";
        $sql = sprintf($this->cgrzejniksql, $pola, $filtrA, $filtrB, $order);
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik = $a['Ile'];
        }
        return $wynik;
    }

    public function WidokPodzielnikow($filtr = '', $poczatek = 0, $ilosc = 30){
        $wynik = '';
        if (strlen($filtr) > 0)
            $filtr = "WHERE NUMER_FABRYCZNY like '%$filtr%'";
        $sql = "SELECT NUMER_FABRYCZNY,
        (SELECT NAZWA_POMIESZCZENIA FROM zainstalowany_grzejnik, pomieszczenie, rodzaj_pomieszczenia
        WHERE zainstalowany_podzielnik.ID_ZG = zainstalowany_grzejnik.ID_ZG
        AND zainstalowany_grzejnik.ID_POMIESZCZENIA = pomieszczenie.ID_POMIESZCZENIA
        AND pomieszczenie.ID_RODZAJU_POMIESZCZENIA = rodzaj_pomieszczenia.ID_RODZAJU_POMIESZCZENIA
        AND zainstalowany_grzejnik.ID_ZG = zainstalowany_podzielnik.ID_ZG LIMIT 1) NazwaPom,
        (SELECT IFNULL(kodlok, kodsm) kodlokalu FROM lokal, pomieszczenie, zainstalowany_grzejnik
        WHERE lokal.ID_LOKALU = pomieszczenie.ID_LOKALU AND pomieszczenie.ID_POMIESZCZENIA = zainstalowany_grzejnik.ID_POMIESZCZENIA
        AND zainstalowany_grzejnik.ID_ZG = zainstalowany_podzielnik.ID_ZG) Kodlokalu,
        (Select lokal.ID_LOKALU FROM lokal, pomieszczenie, zainstalowany_grzejnik
        WHERE lokal.ID_LOKALU = pomieszczenie.ID_LOKALU AND pomieszczenie.ID_POMIESZCZENIA = zainstalowany_grzejnik.ID_POMIESZCZENIA
        AND zainstalowany_grzejnik.ID_ZG = zainstalowany_podzielnik.ID_ZG) IdLok
        FROM zainstalowany_podzielnik $filtr ORDER BY kodlokalu, NazwaPom LIMIT $poczatek, $ilosc";
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik.=sprintf($this->ctabWPodz, $a['NUMER_FABRYCZNY'], $a['NazwaPom'],
            $a['IdLok'], $a['Kodlokalu']);
        }
        $wynik = sprintf($this->cNagWPodz, $wynik);
        return $wynik;
    }

    public function IlePodzielnikow($filtr = ''){
        $wynik = 0;
        if (strlen($filtr) > 0)
            $filtr = "WHERE NUMER_FABRYCZNY like '%$filtr%'";
        $sql = "SELECT count(*) Ile FROM zainstalowany_podzielnik $filtr";
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $wynik = $a['Ile'];
        }
        return $wynik;
    }

    public function SzukajWezla($idlokalu){
        $wynik = array();
        $sql = "SELECT wezel_co_i_cw.IDENTYFIKATOR_WEZLA, jednostka_rozliczeniowa.ID_JEDNOSTKI_ROZLICZENIOWEJ, NAZWA_WEZLA, IFNULL(KodLok, kodsm) kodlokalu
        FROM jednostka_rozliczeniowa, jednostka_uzytkowa, wezel_co_i_cw, lokal
        WHERE jednostka_rozliczeniowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ
        AND jednostka_rozliczeniowa.IDENTYFIKATOR_WEZLA = wezel_co_i_cw.IDENTYFIKATOR_WEZLA
        AND lokal.ID_LOKALU = jednostka_uzytkowa.ID_LOKALU
        AND jednostka_uzytkowa.ID_LOKALU = $idlokalu LIMIT 1";
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            array_push($wynik, $a['IDENTYFIKATOR_WEZLA'], $a['NAZWA_WEZLA'], $a['ID_JEDNOSTKI_ROZLICZENIOWEJ'], $a['kodlokalu']);
        }
        return $wynik;
    }
}

?>