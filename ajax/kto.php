<?php
    require_once '../lib/pracownicy.php';

    session_start();
    $pracownik = new Pracownicy;

    $response = array(array());
    $response['admin']['id']=0;
    $response['admin']['nazwa']='';
    $response['admin']['upraw']= 3;
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        if (isset($_SESSION['idkto']))
        $response['admin']['id']=$_SESSION['idkto'];
        if (isset($_SESSION['kto']))
        $response['admin']['nazwa']=$_SESSION['kto'];
        $user = $pracownik->DajDanePracownika($_SESSION['idkto']);
        $response['admin']['upraw']= $user[3];
    }
    header( 'Content-Type: application/json' );
	echo json_encode($response);
?>