<?php
    error_reporting(0);
    session_start();
    require_once('../lib/baza.php');
    $response = array(array());
    $userpass = $_POST['inputHaslo'];
    $username = $_POST['inputNazwaUzytkownika'];
    $my_session_id = session_id();

    $db = new myBaza;
    $wynik = $db->Zaloguj ($username, $userpass);
    if (!isset ($wynik)) {
        $response['error']['exists'] = true;
    }
    else {
        if ($wynik != 0) {
            $db->NowaSesja($wynik, $my_session_id);
            $kto = $db->DajNazweUzytkownika($wynik);
            $uprawnienia = $db->DajUprawnienia($wynik);
            $response['error']['exists'] = false;
            $_SESSION['uprawnienia'] = $uprawnienia;
            $_SESSION['kto'] = $kto;
            $_SESSION['idkto'] = $wynik;
            $_SESSION['pracownik'] = $username;
        }
        else {
            $response['error']['exists'] = true;
        }
    }
    header( 'Content-Type: application/json' );
    echo json_encode($response);
?>