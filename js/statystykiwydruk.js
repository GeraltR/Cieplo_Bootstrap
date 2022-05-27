function SpinnerShow() {
	$('#spinnerWait').show();
	$('#spinnerwaitborder').show();
}

function SpinnerHide() {
	$('#spinnerWait').hide();
	$('#spinnerwaitborder').hide();
}

function ConvertFormatDate(data) {
	var parts = data.split('.');
	var wynik = parts[2] + '-' + parts[1] + '-' + parts[0];
	return wynik;
}

function DzisiajSQLStr() {
	var tda = new Date();
	var mc = tda.getMonth() + 1;
	if (mc < 10) mc = '0' + mc;
	var dzien = tda.getDate();
	if (dzien < 10) dzien = '0' + dzien;
	return tda.getFullYear() + '-' + mc + '-' + dzien;
}

$(document).ready(function () {
	$('#nadzien').val(DzisiajSQLStr());
    var adreswej = window.location.href;
    var poczatek = adreswej.indexOf('?idsezonu=');
    var koniec = adreswej.length;
    if (poczatek > 0){
		var tmp = adreswej.substr(poczatek+10, koniec-poczatek-9);
		$('#idsezonu').val(tmp);
		$('#edycja').on('shown.bs.modal', function(){
			$('#edycjaTekst').focus();
		});
		$('#edycja').modal();
	}
	else
		PokazBlad();
})


function Statystyka(){
	var idsezonu = $('#idsezonu').val();
	var nadzien = $('#nadzien').val();
	SpinnerShow();
	$.ajax({
		type: "POST",
		url: "../ajax/statystykiwydrukcontrol.php",
		data: {
			idsezonu: idsezonu,
			nadzien: nadzien
		},
		success: function (response) {
            SpinnerHide();
			$('#mainDiv').html(response);
		}
	});
}

function PokazBlad(){
    $('#konunikatTekstBlad-p').text('Błąd wykonywania wydruku!');
    $('#komunikatBlad').modal();
}