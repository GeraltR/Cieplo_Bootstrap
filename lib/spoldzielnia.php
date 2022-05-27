<?php
require_once 'baza.php';
require_once 'pracownicy.php';

class Spoldzielnia {

    private $cLogEmailNag = '<thead>
                                    <tr class="tabNaglowek">
                                        <th>Kod lokalu</th>
                                        <th>Adresat</th>
                                        <th>Kiedy</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>';
    private $cLogEmail = '<tr class="tabFormularz" >
                                <td><button type="button" class="btn-xs btn-outline-secondary" onclick="SzukajWezlaZGrzej(%d)">%s</button></td>
                                <td>%s</td>
                                <td>%s</td>
                                <td %s>%s</td>
                            </tr>';

    public function WidokSpodzelni($idspoldzielni = 1){
        $sql = "SELECT spoldzielnia.ID_ULICY, spoldzielnia.ID_MIEJSCOWOSCI, NAZWA_SPOLDZIELNI,
        NAZWA_ULICY, NR_ULICY_SPOLDZIELNI,
        KOD_POCZTOWY_SPOLDZIELNI, MIEJSCOWOSC
        FROM spoldzielnia, ulice, miejscowosci
        WHERE spoldzielnia.ID_ULICY = ulice.ID_ULICY
        AND spoldzielnia.ID_MIEJSCOWOSCI = miejscowosci.ID_MIEJSCOWOSCI
        AND IDENTYFIKATOR_SPOLDZIELNI = $idspoldzielni";
        $wynik = array();
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            array_push($wynik, $a['NAZWA_SPOLDZIELNI'],$a['NAZWA_ULICY'], $a['NR_ULICY_SPOLDZIELNI'],
            $a['KOD_POCZTOWY_SPOLDZIELNI'], $a['MIEJSCOWOSC'], $a['ID_ULICY'], $a['ID_MIEJSCOWOSCI']);
        }
        return $wynik;
    }

    public function ZapiszSpodzelnie($nazwa, $idulicy, $idmiasta, $numerulicy, $kodpoczt,  $idspoldzielni = 1){
        $wynik = 1;
        if (!isset($nazwa))
            $nazwa = '';
        if (!isset($idulicy))
            $idulicy = null;
        if (!isset($numerulicy))
            $numerulicy = '';
        if (!isset($idmiasta))
            $idmiasta = null;
        if (!isset($kodpoczt))
            $kodpoczt = '';

        $sql = "UPDATE spoldzielnia SET ID_ULICY = $idulicy, ID_MIEJSCOWOSCI = $idmiasta, NAZWA_SPOLDZIELNI = '$nazwa',
            NR_ULICY_SPOLDZIELNI = '$numerulicy', KOD_POCZTOWY_SPOLDZIELNI = '$kodpoczt'
        WHERE IDENTYFIKATOR_SPOLDZIELNI = $idspoldzielni";
        $db = new myBaza();
        $wynik = $db->Execute($sql);
        return $wynik;
    }

    public function WidokWystawcy($idwystawcy = 1){
        $pracownik = new Pracownicy;
        $tmp = $pracownik->DajDanePracownika($_SESSION['idkto']);
        $sql = "SELECT NAZWA_WYSTAWCY, NAZWA_ULICY, NR_ULICY_WYSTAWCY,
        KOD_POCZTOWY_WYSTAWCY, MIEJSCOWOSC, wystawca.ID_MIEJSCOWOSCI,
        IFNULL(smtp, '') smtp, IFNULL(port, '') port, IFNULL(username, '') username,
        IFNULL(password, '') password, podpis, wystawca.ID_ULICY
        FROM wystawca, ulice, miejscowosci
        WHERE wystawca.ID_ULICY = ulice.ID_ULICY
        AND wystawca.ID_MIEJSCOWOSCI = miejscowosci.ID_MIEJSCOWOSCI
        AND ID_WYSTAWCY = $idwystawcy";
        $wynik = array();
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            if ($tmp[3] != 0)
                $haslo = '******';
            else
                $haslo = $a['password'];
            array_push($wynik, $a['NAZWA_WYSTAWCY'],$a['NAZWA_ULICY'],$a['MIEJSCOWOSC'], $a['smtp'], $a['port'],
            $a['username'], $haslo, $a['podpis'], $a['ID_ULICY'], $a['ID_MIEJSCOWOSCI'], $a['NR_ULICY_WYSTAWCY'],
            $a['KOD_POCZTOWY_WYSTAWCY']);
        }
        return $wynik;
    }

    public function UstawPodpis($podpis, $idwystawcy = 1){
        $wynik = 0;
        $sql = "UPDATE wystawca SET podpis = '$podpis' WHERE ID_WYSTAWCY = $idwystawcy";
        $db = new myBaza();
        $wynik = $db->Execute($sql);
        return $wynik;
    }

    public function ZapiszWystawce($nazwa, $idulicy, $numerulicy, $idmiasta, $kodpoczt, $smtp,
                                $port, $username, $pass, $idwystawcy=1){
        if (!isset($nazwa))
            $nazwa = '';
        if (!isset($idulicy))
            $idulicy = null;
        if (!isset($numerulicy))
            $numerulicy = '';
        if (!isset($idmiasta))
            $idmiasta = null;
        if (!isset($kodpoczt))
            $kodpoczt = '';
        if (!isset($smtp))
            $smtp = '';
        if (!isset($port))
            $port = 25;
        if (!isset($username))
            $username = '';
        if (!isset($pass))
            $pass = '';
        $sql = "UPDATE  wystawca SET NAZWA_WYSTAWCY='$nazwa', NR_ULICY_WYSTAWCY = '$numerulicy',
        KOD_POCZTOWY_WYSTAWCY = '$kodpoczt', ID_ULICY = $idulicy, ID_MIEJSCOWOSCI = $idmiasta,
        smtp = '$smtp', port = $port, username = '$username', password = '$pass'
        WHERE ID_WYSTAWCY = $idwystawcy";
        $db = new myBaza();
        $db->Execute($sql);
        return 0;
    }

    public function ZapiszParamEmail($tytul, $tresc){
        $db = new myBaza();
        $idpar = $db->Sprawdz('parememail', 'idpar');
        if ($idpar != 0){
            $sql = "UPDATE parememail SET Tytul = '$tytul', Tresc = '$tresc' WHERE idpar = $idpar";
        }
        else {
            $sql = "INSERT INTO parememail (Tytul, Tresc) VALUES ('$tytul','$tresc')";
        }
        $db->Execute($sql);
        return 0;
    }

    public function DajParamEmail(){
        $sql = "SELECT Tytul, Tresc FROM parememail LIMIT 1";
        $tytul = 'Rozliczenie CO';
        $tresc = 'Wynik rozliczenia centralnego ogrzewania.';
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            $tytul = $a['Tytul'];
            $tresc = $a['Tresc'];
        }
        return array($tytul, $tresc);
    }

    public function WidokLogaEmail($filtr = '', $data = ''){
        if ($data != '')
            $data = " AND CONVERT(kiedy, DATE) <= '$data'";
        if ($filtr != '')
            $filtr = " AND ((kodlok like '%$filtr%') OR (IFNULL(NAZWA, CONCAT_WS(' ', NAZWISKO, IMIE)) like '%$filtr%')) ";
        $sql = "SELECT Kiedy, logemail.ID_LOKALU, kodlok, IFNULL(NAZWA, CONCAT_WS(' ', NAZWISKO, IMIE)) nazwa, Status
        FROM logemail, lokal, uzytkownik
        WHERE logemail.ID_LOKALU = lokal.ID_LOKALU
        AND logemail.ID_UZYTKOWNIKA = uzytkownik.ID_UZYTKOWNIKA
        %s %s
        ORDER BY Kiedy DESC, nazwa ASC";
        $sql = sprintf($sql, $filtr, $data);
        $wynik = $this->cLogEmailNag;
        $db = new myBaza();
        $lista = $db->query($sql);
        foreach ($lista as $a) {
            if ($a['Status'] == 1){
                $status = 'Poprawny';
                $klasa = ' class = "logemailpoprawny" ';
            }
            else {
                $status = 'Błąd';
                $klasa = ' class = "logemailbledny" ';
            }
            $wynik.=sprintf($this->cLogEmail, $a['ID_LOKALU'], $a['kodlok'], $a['nazwa'], $a['Kiedy'], $klasa, $status);
        }
        return $wynik;
    }

}

?>