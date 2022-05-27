<?php

class Nieruchomosc {

    private $cNagWNier = '<thead>
                            <tr class="tabNaglowek">
                                <th>Symbol</th>
                                <th>Kod budynku</th>
                                <th>Ulica</th>
                                <th>Miasto</th>
                                <th>Nazwa budynku</th>
                            </tr>
                        </thead>
                        <tbody>
                        %s
                        </tbody>';
    private $ctabWNier = '<tr class="tabSelect" onClick=ZaznaczNier("%d") ondblclick="ZmienNier(%d)">
                            <td class="tabNierA"><a href="#" id="tdvNier%d">%s</a></td>
                            <td class="tabNierB">%s</td>
                            <td class="tabNierC">%s<input type="hidden" id="tinNierU%d" value="%d"/>
                                                    <input type="hidden" id="tinNierNr%d" value="%s"/></td>
                            <td class="tabNierD">%s<input type="hidden" id="tinNierM%d" value="%d"/>
                                                    <input type="hidden" id="tinNierKpocz%d" value="%s"/></td>
                            <td class="tabNierE">%s</td>
                        </tr>';

    public function WidokNieruchomosci($filtr = '', $poczate = 0, $ile = 30){
        if (strlen($filtr) > 0)
        $filtr = "AND (exists (SELECT 1 FROM lokal WHERE lokal.Id_nieruchomosci = nieruchomosci.Id_nieruchomosci AND kodlok like '%$filtr%')
        OR (NAZWA_ULICY like '%$filtr%') OR (Symbol_nieruchomosci like '%$filtr%')) ";
        $sql = "SELECT nieruchomosci.ID_MIEJSCOWOSCI, nieruchomosci.ID_ULICY, nieruchomosci.Id_nieruchomosci,
        Nr_budynku, Kod_pocztowy, Symbol_nieruchomosci,
        CONCAT_WS(' ', NAZWA_ULICY, Nr_budynku) ulicanr, CONCAT_WS(' ', Kod_pocztowy, MIEJSCOWOSC) miasto,
        (SELECT jednostka_rozliczeniowa.NAZWA_JEDNOSTKI_ROZLICZENIOWEJ
            FROM jednostka_rozliczeniowa, jednostka_uzytkowa, lokal
            WHERE jednostka_rozliczeniowa.ID_JEDNOSTKI_ROZLICZENIOWEJ = jednostka_uzytkowa.ID_JEDNOSTKI_ROZLICZENIOWEJ
            AND jednostka_uzytkowa.ID_LOKALU = lokal.ID_LOKALU
            AND lokal.Id_nieruchomosci = nieruchomosci.Id_nieruchomosci LIMIT 1) Budynek,
        (SELECT kodlok FROM lokal WHERE lokal.Id_nieruchomosci = nieruchomosci.Id_nieruchomosci LIMIT 1) KodBud
        FROM nieruchomosci, ulice, miejscowosci
        WHERE nieruchomosci.ID_ULICY = ulice.ID_ULICY
        AND nieruchomosci.ID_MIEJSCOWOSCI = miejscowosci.ID_MIEJSCOWOSCI
        $filtr
        ORDER BY Budynek LIMIT $poczate, $ile";
        $wynik = '';
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $kodbud = $a['KodBud'];
            $kodbud = substr($kodbud, 0, strrpos($kodbud, '-'));
            $kodbud = str_replace(' ', '', $kodbud);

            $wynik.=sprintf($this->ctabWNier, $a['Id_nieruchomosci'], $a['Id_nieruchomosci'],
            $a['Id_nieruchomosci'], $a['Symbol_nieruchomosci'],
            $kodbud,
            $a['ulicanr'], $a['Id_nieruchomosci'], $a['ID_ULICY'], $a['Id_nieruchomosci'], $a['Nr_budynku'],
            $a['miasto'], $a['Id_nieruchomosci'],  $a['ID_MIEJSCOWOSCI'], $a['Id_nieruchomosci'],  $a['Kod_pocztowy'],
            $a['Budynek']);
        }
        $wynik = sprintf($this->cNagWNier, $wynik);
        return $wynik;
    }

    public function ZapiszNieruchomosc($idnier, $symbol, $numer, $kodpoczt, $iduli, $idmia){
        $db = new myBaza();
        if ($idnier == 0){
            $sql = "INSERT INTO nieruchomosci (Symbol_nieruchomosci, Nr_budynku, Kod_pocztowy,
            ID_ULICY, ID_MIEJSCOWOSCI) VALUES ('$symbol', '$numer', '$kodpoczt', $iduli, $idmia)";
            $wynik = $db->Insert($sql);
        }
        else {
            $sql = "UPDATE nieruchomosci SET Symbol_nieruchomosci = '$symbol', Nr_budynku = '$numer',
            Kod_pocztowy = '$kodpoczt', ID_ULICY = $iduli, ID_MIEJSCOWOSCI = $idmia WHERE Id_nieruchomosci = $idnier";
            $db->Execute($sql);
            $wynik = $idnier;
        }
        return Array($wynik, $symbol);
    }

    public function CzyMoznaUsunac($idnier){
        $db = new myBaza();
        $wynik = $db->Sprawdz("lokal", "Id_nieruchomosci", "Id_nieruchomosci = $idnier");
        return $wynik;
    }

    public function UsunNieruchomosc($idnier){
        $sql = "DELETE FROM nieruchomosci WHERE Id_nieruchomosci = $idnier";
        $db = new myBaza();
        $db->Execute($sql);
        return 0;
    }

}

?>