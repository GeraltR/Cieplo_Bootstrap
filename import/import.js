function SpinnerShow() {
	$('#spinnerWait').show();
	$('#spinnerwaitborder').show();
}

function SpinnerHide() {
	$('#spinnerWait').hide();
	$('#spinnerwaitborder').hide();
}

function ZalozTabele(corobic){
    SpinnerShow();
    $.ajax({
        type: "POST",
        url: "importcontrol.php",
        data: {
            typ:'tabele',
            create: true,
            corobic: corobic
        },
        success: function (response) {
            SpinnerHide();
            $('#tabele').html(response[0]);
            if (response[1] != 0)
                ImportDanych();
            else
                WczytajDane();
        }
    });
}

function ImportDanych(){
    SpinnerShow();
    $.ajax({
        type: "POST",
        url: "importcontrol.php",
        data: {
            typ:'import',
            create: false
        },
        success: function (response) {
            SpinnerHide();
            $('#import').html(response[0]);
            ZakladanieKluczy(response[1]);
        }
    });
}

function WczytajDane(){
    SpinnerShow();
    $.ajax({
        type: "POST",
        url: "importcontrol.php",
        data: {
            typ:'wczytajdane',
            create: false
        },
        success: function (response) {
            SpinnerHide();
            $('#import').html(response[0]);
            ZakladanieKluczy(response[1]);
        }
    });
}

function ZakladanieKluczy(corobic){
    SpinnerShow();
    $.ajax({
        type: "POST",
        url: "importcontrol.php",
        data: {
            typ:'klucze',
            create: false,
            corobic: corobic
        },
        success: function (response) {
            SpinnerHide();
            $('#klucze').html(response[1]);
            if (response[0] == 0)
                Dodatki();
            else
                $('#dodatki').html('Koniec odtwarzania kopii.');
        }
    });
}

function Dodatki(){
    SpinnerShow();
    $.ajax({
        type: "POST",
        url: "importcontrol.php",
        data: {
            typ:'dodatki',
            create: false
        },
        success: function (response) {
            SpinnerHide();
            $('#dodatki').html(response)
        }
    });
}

function start(corobic){
    ZalozTabele(corobic);
}

$(document).ready(function(){
    SpinnerHide();
    $('#tabele').html('oczekuje');
    $('#import').html('oczekuje');
    $('#klucze').html('oczekuje');
    $('#dodatki').html('oczekuje');
})