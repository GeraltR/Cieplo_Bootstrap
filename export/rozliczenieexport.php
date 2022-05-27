<?php
require_once "xlsxwriter.class.php";
require_once "../lib/baza.php";
require_once "../lib/rozliczenie.php";

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

$idbudynku = $_GET['idbudynku'];
$arch = 0;
if (isset($_GET['arch'])){
    $arch = $_GET['arch'];
    if (!isset($arch))
        $arch = 0;
}

$rozliczenie = new Rozliczenie();
$kosztybudynku = $rozliczenie->DajKosztyDlaBudynku($idbudynku,$arch);
$nazwa = $kosztybudynku[6];
$dataodcz = $kosztybudynku[4];
$rows = array(array("koszty ogrzewania", "koszty stale", "koszty zmienne", "zuzycie", "data odczytu",
        "wspolczynnik", "nazwa budynku", "koszty rozliczenia", "koszty rozliczenia nieopomiartowanych"),
        $kosztybudynku);

$sql = "SELECT IdRoz, pow_total, pow_opom, pow_nopom,
zuzycie_opom, koszty_z_opom, kj_z_opom, koszty_s_nopom, kj_s, koszty_p_opom,
kj_p, kj_r_opom, kj_r_nopom, Data_rozliczenia FROM kosztybudynku, Rozliczenie
WHERE kosztybudynku.IdKos = rozliczenie.IdKos  AND kosztybudynku.Aktualne = $arch
 AND rozliczenie.ID_JEDNOSTKI_ROZLICZENIOWEJ = $idbudynku";

$db = new myBaza();
$rozliczbud = $db->query($sql);
foreach ($rozliczbud as $a) {
    $idroz = $a['IdRoz'];
    $kj_r_nopom = round($a['kj_r_nopom']/$a['pow_total'],6);
    $budynek = array ($a['pow_total'], $a['pow_opom'], $a['pow_nopom'], $a['zuzycie_opom'], $a['koszty_z_opom'],
                $a['kj_z_opom'], $a['koszty_s_nopom'], $a['kj_s'], $a['koszty_p_opom'], $a['kj_p'], $a['kj_r_opom'],
                $kj_r_nopom, $a['Data_rozliczenia']);
}
$budrows = array(array ('Całkowita powierzchnia', 'Powierzchnia opomiarowanych', 'Powierzchnia nieopomiarowanych',
                        'Całkowita ilość jednostek', 'Koszty wg podzielników', 'jpko', 'Koszty z nieopomiarowanych',
                        'Koszt jednostki', 'Koszty wg powierzchni lokali', 'Koszt jednostkowy wg powierzchni',
                        'Koszty jednostkowy rozliczenia wg podzielników', 'Koszt jednostkowy rozliczenia wg metrów',
                        'Data rozliczenia'),
                $budynek);

$sql = "SELECT IFNULL(kodLok, kodSM) KodLokalu, Data_Rozliczenia, IF (Sposob_Rozliczania = 0, 'ryczałt', 'odczyt') sposob,
K_Stale,K_Zuzycia, K_Przesylu, K_Rozliczenia, Liczba_Podzielnikow, Zuzycie_Zredukowane, Zaliczki,
IFNULL (NAZWA, CONCAT_WS(' ', NAZWISKO, IMIE)) Nazwa,
(SELECT Rmi_dla_lokalu FROM wspolczynniki_dla_lokalu WHERE wspolczynniki_dla_lokalu.ID_LOKALU = lokal.ID_LOKALU LIMIT 1) RMI
FROM rozliczenie_lokalu, lokal, uzytkownik
WHERE rozliczenie_lokalu.ID_LOKALU = lokal.ID_LOKALU AND rozliczenie_lokalu.ID_UZYTKOWNIKA = uzytkownik.ID_UZYTKOWNIKA
AND idroz = $idroz ORDER BY KodLokalu";
$lokal = array(array('Lokal', 'Lokator', 'Data rozliczenia', 'Sposób rozliczenia', 'Ryczałt', 'Koszty wg podzielników',
                    'Koszty wg powierzchni', 'Koszty rozliczenia', 'Liczba podzielników', 'Jednostki', 'Zaliczki', 'RMI',
                    'Ilosc jednostek'));
$rozliczlok = $db->query($sql);
foreach ($rozliczlok as $a) {
    if ($a['RMI'] != 0)
        $iloscjedn = round($a['Zuzycie_Zredukowane'] / $a['RMI']);
    else
        $iloscjedn = 0;
    array_push($lokal, array($a['KodLokalu'],$a['Nazwa'],$a['Data_Rozliczenia'],$a['sposob'],$a['K_Stale'],$a['K_Zuzycia'],
                            $a['K_Przesylu'],$a['K_Rozliczenia'],$a['Liczba_Podzielnikow'],$a['Zuzycie_Zredukowane'],
                            $a['Zaliczki'],$a['RMI'],$iloscjedn));
}

$filename = "$nazwa $dataodcz.xlsx";

header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$writer = new XLSXWriter();
$writer->setAuthor('Cieplo');
foreach($rows as $row)
    $writer->writeSheetRow($nazwa.' koszty', $row);
foreach($budrows as $row)
    $writer->writeSheetRow($nazwa.' rozliczenie', $row);
foreach($lokal as $row)
    $writer->writeSheetRow($nazwa.' lokale', $row);
$writer->writeToStdOut();

exit(0);


?>

