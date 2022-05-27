function SpinnerShow() {
	$('#spinnerWait').show();
	$('#spinnerwaitborder').show();
}

function SpinnerHide() {
	$('#spinnerWait').hide();
	$('#spinnerwaitborder').hide();
}

$(document).ready(function () {
    var adreswej = window.location.href;
    var poczatek = adreswej.indexOf('?idbudynku=');
    var koniec = adreswej.length;
    if (poczatek > 0){
        var idbudynku = adreswej.substr(poczatek+11, koniec-poczatek-10);
        SpinnerShow();
        if (idbudynku != 0){
            $.ajax({
                type: "POST",
                url: "../ajax/wydrukicontrol.php",
                data: {
                    typ: 'protokolodczytu',
                    idbudynku: idbudynku
                },
                success: function (response) {
                    SpinnerHide();
                    $('#mainDiv').html(response);
                }
            });
        }
        else {
            SpinnerHide();
            PokazBlad();
        }
    }
    else {
        PokazBlad();
    }

});

function PokazBlad(){
    $('#konunikatTekstBlad-p').text('Brak identyfikacji dla jakiego budynku wykonać wydruk!');
    $('#komunikatBlad').modal();
}