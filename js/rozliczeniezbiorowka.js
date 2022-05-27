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
    var typ = 'zbiorowkalokale';
    var arch = 0;
    if (poczatek > 0){
        var tmp = adreswej.substr(poczatek+11, koniec-poczatek-10);
        var a = tmp.indexOf('?arch=');
        var idbudynku = 0;
        if (a > 0){
           idbudynku = tmp.substr(0, a);
           arch = tmp.substr(a+6,1);
        }
        else idbudynku = tmp;
        poczatek = adreswej.indexOf('?first=T');
        if (poczatek > 0)
           typ = 'zbiorowka';
        SpinnerShow();
        if (idbudynku != 0){
            $.ajax({
                type: "POST",
                url: "../ajax/rozliczeniecontrol.php",
                data: {
                    typ: typ,
                    idbudynku: idbudynku,
                    arch: arch
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
    else PokazBlad();
})

function PokazBlad(){
    $('#konunikatTekstBlad-p').text('Brak identyfikacji dla jakiego budynku wykonać wydruk!');
    $('#komunikatBlad').modal();
}