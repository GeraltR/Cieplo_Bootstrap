<?php

require_once "baza.php";
require_once "kontrolki.php";


class Pracownicy {

    public function __construct()
    {
        return 0;
    }

    private $ctabWPrac = '<tr class="tabSelect" onClick=ZaznaczPrac("%d") ondblclick="ZmienPrac(%d)">
                        <td class="tabNazwaPrac" id="tdvwez%d">%s</td>
                        <td class="tabAdresPrac"><a href="#" id="vprac%d">%s</a></td>
                        <td class="tabAdresWez">%s</td>
                        </tr>';
    private $ctabWPracNag = '<thead>
                            <tr class="tabNaglowek">
                                <th>Nazwa użytkownika</th>
                                <th>Nazwa pracownika</th>
                                <th>Uprawnienia</th>
                            </tr>
                        </thead>';


    public function WidokPracownikow(){
        $sql = "SELECT USERID, USERNAME, CONCAT_WS(' ', IMIE_PRACOWNIKA, NAZWISKO_PRACOWNIKA) Nazwa,
            CASE admin WHEN 0 THEN 'Administrator' WHEN 1 THEN 'Kierownik' WHEN 2 THEN 'Operator' END Upraw
            FROM lista_pracownikow
            ORDER BY NAZWISKO_PRACOWNIKA, IMIE_PRACOWNIKA, USERNAME";
        $tmp = "";
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach($lista as $a){
            $tmp .= sprintf($this->ctabWPrac, $a['USERID'], $a['USERID'],
            $a['USERID'], $a['USERNAME'], $a['USERID'], $a['Nazwa'],$a['Upraw']);
        }
        $tmp = "$this->ctabWPracNag<tbody>$tmp</tbody>";
        return $tmp;
    }

    public function DajDanePracownika($idpracownika){
        $sql = "SELECT IMIE_PRACOWNIKA, NAZWISKO_PRACOWNIKA, admin FROM lista_pracownikow WHERE USERID = $idpracownika";
        $imie = "";
        $nazwisko = "";
        $admin = 0;
        $db = new myBaza;
        $lista = $db->query($sql);
        foreach($lista as $a){
            $imie = $a['IMIE_PRACOWNIKA'];
            $nazwisko = $a['NAZWISKO_PRACOWNIKA'];
            $admin = $a['admin'];
        }
        return array($idpracownika, $imie, $nazwisko, $admin);
    }

    public function SprawdzPracownika($login){
        $db = new myBaza();
        return $db->Sprawdz("lista_pracownikow", "USERID", "USERNAME = '$login'");
    }

    public function ZapiszPracownika($idpracownika, $login, $imie, $nazwisko, $haslo, $czyadmin){
        $db = new myBaza();
        $tmp = funkcja haszująca;
        if ($idpracownika == 0){
            $sql = "INSERT INTO lista_pracownikow (USERNAME, IMIE_PRACOWNIKA, NAZWISKO_PRACOWNIKA, haslo, admin)
                VALUES ('$login', '$imie', '$nazwisko', '$tmp', $czyadmin)";
            $wynik = $db->Insert($sql);
        }
        else {
            $sql = "UPDATE lista_pracownikow  SET IMIE_PRACOWNIKA = '$imie', NAZWISKO_PRACOWNIKA = '$nazwisko',
                    haslo = '$tmp', admin = $czyadmin WHERE  USERID = $idpracownika";
            $db->Execute($sql);
            $wynik = $idpracownika;
        }
        return $wynik;
    }

    public function UsunPracownika($idpracownika){
        $sql = "DELETE FROM lista_pracownikow WHERE  USERID = $idpracownika";
        $db = new myBaza();
        $db->Execute($sql);
        return 0;
    }
}

?>