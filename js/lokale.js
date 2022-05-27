//******************************  LOAKALE ******************************/

function PokazWidokLokali(budynek, filtr = '') {
	if (filtr === '')
	   filtr = $('#filtrlokali').val();
	$('#widokLokali').html('');
	$('#idbudynku').val(budynek);
	$.ajax({
		beforeSend: SpinnerShow(),
		type: 'POST',
		url: 'ajax/lokalecontrol.php',
		data: {
			typ: 'widok',
			budynek: budynek,
			filtr: filtr
		},
		success: function(dane) {
			SpinnerHide();
			$('#widokLokali').html(dane);
			ZaznaczLokal($('#idlokalu').val());
		},
		error: function(response){
			SpinnerHide();
		}
	});
}

function ZaznaczLokal(lokal) {
	var old = $('#idlokalu').val();
	if ($('#szczeglok'+lokal).is(':hidden')){
		$('#szczeglok'+old).hide();
		ZaznaczRekord($('#widokLokali tbody tr td a'), 'lok' + lokal);
		if ($('#pozycjaLokalu').val() != 0)
			UstawNaWierszuLok('#widokLokali', 'vlokid', lokal, 'lok');
		$('#idlokalu').val(lokal);
		$('#szczeglok'+lokal).show();
		$.ajax({
			type: "POST",
			url: "ajax/lokalecontrol.php",
			data: {
				typ: 'widokpokoi',
				idlokalu: lokal
			},
			success: function (response) {
				$('#pozycjaLokalu').val(0);
				if (response[0].length > 0){
					$('#szczeglok'+response[0]).html(response[1]);
				}
				else {
					$('#szczeglok'+$('#idlokalu').val()).hide();
				}
			}
		});
	}
	else $('#szczeglok'+lokal).hide();
}

function ZmienGrzejnik(idpomieszczenia){
	var lokal = $('#idlokalu').val();
	$('#idgrzejnika').val(0);
	$('#idpomieszczenia').val(idpomieszczenia);
	$('#pozycjaLokalu').val($('#vlokid'+lokal).position().top);
	UkryjOkna();
	$('#grzejnikWroc').text('Wróc do lokalu');
	ZainstalujGrzejnikLubPodzielnik('G', idpomieszczenia);
	$('#btnNowyGrzejnik').prop({disabled: false});
	$('#btnNowyPodzielnik').prop({disabled: false});
  }

function ZmienPodzielnik(idgrzejnika){
	var lokal = $('#idlokalu').val();
	$('#idgrzejnika').val(idgrzejnika);
	$('#idpomieszczenia').val(0);
	$('#pozycjaLokalu').val($('#vlokid'+lokal).position().top);
	UkryjOkna();
	$('#grzejnikWroc').text('Wróc do lokalu');
	ZainstalujGrzejnikLubPodzielnik('P', idgrzejnika);
	$('#btnNowyGrzejnik').prop({disabled: true});
	$('#btnNowyPodzielnik').prop({disabled: true});
}

function DodajLokal() {
	var idbudynku = $('#idbudynku').val();
	$.ajax({
		type: "POST",
		url: "ajax/sezonycontrol.php",
		data: {
			typ: 'czyjestsezondlabudynku',
			idbudynku: idbudynku
		},
		success: function (response) {
			if (response == 0)
				KomunikatBladPokaz ('Budynek nie jest powiązany z sezonem. Najpierw powiąż budynek z sezonem bo nie będzie można dodać wartość RMI!');
			else DajKatalogiDlaLokalu(0);
		}
	});

}

function UsunLokal(){
	var idlokalu = $('#idlokalu').val();
	$.ajax({
		type: "POST",
		url: "ajax/lokalecontrol.php",
		data: {
			typ: 'czymnoznausunaclokal',
			idlokalu: idlokalu
		},
		success: function (response) {
			if (response[0] != 0){
				var komunikat = '';
				switch (response[0]){
					case -1:
						komunikat = 'Najpierw należy usunąć pomieszczenia w lokalu!';
						break;
					case -2:
						komunikat = 'Lokal posiada niezerowe zaliczki!';
						break;
					default:
						komunikat = 'Lokal był już rozliczany!</br>Takiego lokalu nie można usnąć.';
				}
				KomunikatBladPokaz(' Błąd ('+response[0]+') usuwania lokalu!<br>'+komunikat);
			}
			else {
				MoznaUsunacLokal(response[1]);
			}
		}
	});
}

function MoznaUsunacLokal(kodlokalu){
	if (kodlokalu == null)
	   kodlokalu = 'nie zdefiniowano kodu lokalu';
	var idlokalu = $('#idlokalu').val();
	if (idlokalu > 0){
        $('#confirmationBody').empty();
		$('#confirmationFooter').empty();
		$('#confirmationBody').removeClass('form-group mb-2');
		$('#confirmationLongTitle').text('Usuwanie lokalu '+kodlokalu);
		$('#confirmationBody').addClass('form-group mb-2');
		$('#confirmationBody').append('<label class="row-form-label" for="confirmationInput" id="confirmationLabel">Czy chcesz usunąć lokal: '+kodlokalu+'</label>');
		$('#confirmationFooter').append(
			'<button type="button" class="btn btn-success" data-dismiss="modal">Anuluj</button>' +
				'<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="DeleteLokal(' +
				idlokalu +
				')">Usuń</button>'
		);
		$('#confirmation').modal();
	}
}

function DeleteLokal(){
    var idlokalu = $('#idlokalu').val();
	$.ajax({
		type: "POST",
		url: "ajax/lokalecontrol.php",
		data: {
			typ: 'usunlokal',
			idlokalu: idlokalu
		},
		success: function (response) {
			PokazWidokLokali($('#budynekWWezle').val());
		}
	});
}

function ZmienLokal(idlokalu){
	var idlokalu = $('#idlokalu').val();
	if (idlokalu != undefined && idlokalu != 0)
		DajKatalogiDlaLokalu(idlokalu);
	else KomunikatBladPokaz('Należy wskazać lokal do zmiany');
}

function DajKatalogiDlaLokalu(idlokalu){
	$.ajax({
		type: "POST",
		url: "ajax/lokalecontrol.php",
		data: {
			typ: 'katalogidlalokalu',
			idlokalu: idlokalu
		},
		success: function (response) {
			PokazOknoZmian(response[0],response[1],response[2],response[3],response[4],response[5],
							response[6],response[7],response[8],response[9], response[10],response[11],
							response[12], response[13]);
		}
	});
}

function SprawdzCzyMoznaDodacRMI(idlokalu){
	$.ajax({
		type: "POST",
		url: "ajax/sezonycontrol.php",
		data: {
			typ: 'dajsezondlalokalu',
			idlokalu: idlokalu
		},
		success: function (response) {
			if (response[0] == 0 || response[0] == undefined)
				$('#edycjaPodzKomunikatBlad').text('Budynek nie jest powiązany z sezonem. Wartość RMI nie zostanie dodana!');
		}
	});
}

function PokazOknoZmian(idlokalu, pionowe, idpionowe, poziome, idpoziome, roza, idrozy, rodzaj,
						idrodzaju, imie, nazwisko, nazwa, nieruchomosci, idnieruchomosci){
	var tabwiersz = $('#vlokid'+$('#idlokalu').val()).parent();
	var kodlok = '';
	var kodsm = '';
	var numer = '';
	var powierzchnia = '';
	var rmi = '';
	CzyscKlasyModalEdit();
	if (idlokalu != 0){
		SprawdzCzyMoznaDodacRMI(idlokalu);
		$('#edycjaLongTitle').text('Zmiana lokalu');
		kodlok = tabwiersz.find('td')[0].innerText;
		kodsm = tabwiersz.find('td')[1].innerText;
		numer = tabwiersz.find('td')[2].innerText;
		powierzchnia = tabwiersz.find('td')[3].innerText;
		rmi = tabwiersz.find('td')[6].innerText;
	}
	else $('#edycjaLongTitle').text('Dodawanie lokalu');
	$('#edycjaBody').addClass('form-group mb-2');
	$('#modalEdycjaMain').addClass('pam-duzy-dialog');
		$('#edycjaBody').append(
			'<div class="row">'+
				'<div class="col-md-4">'+
					'<label class="row-form-label" for="edycjaPionowe" id="edycjaPionoweLabel">Położenie pionowe</label>' +
					'<select class="form-control" id="edycjaPionowe">'+pionowe+'</select>' +
				'</div>'+
				'<div class="col-md-4">'+
					'<label class="row-form-label" for="edycjaPoziome" id="edycjaPoziomeLabel">Położenie poziome</label>' +
					'<select class="form-control" id="edycjaPoziome">'+poziome+'</select>' +
				'</div>'+
				'<div class="col-md-4">'+
					'<label class="row-form-label" for="edycjaRoza" id="edycjaRozaLabel">Róża wiatrów</label>' +
					'<select class="form-control" id="edycjaRoza">'+roza+'</select>' +
				'</div>'+
			'</div>'+
			'<label class="row-form-label" for="edycjaRodzajLok" id="edycjaRodzajLokLabel">Rodzaj lokalu</label>' +
			'<select class="form-control" id="edycjaRodzajLok">'+rodzaj+'</select>' +
			'<label class="row-form-label" for="edycjaNrLokalu" id="edycjaNrLokaluLabel">Numer lokalu</label>' +
			'<input class="form-control" type="text" id="edycjaNrLokalu" value="'+numer+'"></input>'+
			'<label class="row-form-label" for="edycjaPowierzniaLokalu" id="edycjaPowierzniaLokaluLabel">Powierznia lokalu</label>' +
			'<input class="form-control" type="text" id="edycjaPowierzniaLokalu" value="'+powierzchnia+'"></input>'+
			'<div class="row">'+
				'<div class="col-md-6">'+
					'<label class="row-form-label" for="edycjaKodSMLokalu" id="edycjaKodSMLokaluLabel">Kod SM</label>' +
					'<input class="form-control" type="text" id="edycjaKodSMLokalu" value="'+kodsm+'"></input>'+
				'</div>'+
				'<div class="col-md-6">'+
					'<label class="row-form-label" for="edycjaKodLokalu" id="edycjaKodLokaluLabel">Kod lokalu</label>' +
					'<input class="form-control" type="text" id="edycjaKodLokalu" value="'+kodlok+'"></input>'+
				'</div>'+
			'</div>'+
			'<div class="row">'+
				'<div class="col-md-5">'+
					'<label class="row-form-label" for="edycjaLokatorImie" id="edycjaLokatorImieLabel">Lokator imię</label>' +
					'<input class="form-control" type="text" id="edycjaLokatorImie" value="'+imie+'"></input>'+
				'</div>'+
				'<div class="col-md-7">'+
					'<label class="row-form-label" for="edycjaLokatorNazwisko" id="edycjaLokatorNazwiskoLabel">Lokator nazwisko</label>' +
					'<input class="form-control" type="text" id="edycjaLokatorNazwisko" value="'+nazwisko+'"></input>'+
				'</div>'+
			'</div>'+
			'<label class="row-form-label" for="edycjaLokatorNazwa" id="edycjaLokatorNazwaLabel">Lokator nazwa</label>' +
			'<input class="form-control" type="text" id="edycjaLokatorNazwa" value="'+nazwa+'"></input>'+
			'<label class="row-form-label" for="edycjaRMILokalu" id="edycjaRMILokaluLabel">Współczynnik Rmi</label>' +
			'<input class="form-control" type="text" id="edycjaRMILokalu" value="'+rmi+'"></input>'+
			'<label class="row-form-label" for="edycjaNieruchomoscLokalu" id="edycjaNieruchomoscLokaluLabel">Nieruchomość (dla adresu)</label>' +
			'<select class="form-control" id="edycjaNieruchomoscLokalu">'+nieruchomosci+'</select>' +
			'<span class="row-form-blad spanError" id="edycjaPodzKomunikatBlad"></span>'
		);
		$('#edycjaFooter').append(
			'<button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>' +
			'<button type="button" class="btn btn-success" onclick="SprawdzZapisLokalu('+idlokalu+')">Zapisz</button>'
	);
	$('#edycjaPionowe').val(idpionowe);
	$('#edycjaPoziome').val(idpoziome);
	$('#edycjaRoza').val(idrozy);
	$('#edycjaRodzajLok').val(idrodzaju);
	$('#edycjaNieruchomoscLokalu').val(idnieruchomosci);
	$('#edycja').on('shown.bs.modal', function(){
		$('#edycjaPionowe').focus();
	});
	$('#edycja').modal();
}

function SprawdzZapisLokalu(idlokalu){
	var idbudynku = $('#idbudynku').val();
	var numer = $('#edycjaNrLokalu').val();
	var idnieruchomosci = $('#edycjaNieruchomoscLokalu').val();
	if (idnieruchomosci === undefined || idnieruchomosci == null){
		$('#edycjaPodzKomunikatBlad').text('Należy wskazać nieruchomość');
		return false;
	}
	if (($('#edycjaLokatorNazwisko').val().length < 1) && ($('#edycjaLokatorImie').val().length < 1) && ($('#edycjaLokatorNazwa').val().length < 1)){
		$('#edycjaPodzKomunikatBlad').text('Należy wpisać imie, nazwisko lub nazwę lokatora');
		return false;
	}
    if (idbudynku != 0 && numer.length > 0)
		SprawdzKodLokalu(idlokalu);
	else {
		if (idbudynku != 0)
			$('#edycjaPodzKomunikatBlad').text('Numer lokalu nie może być pusty!');
		else $('#edycjaPodzKomunikatBlad').text('Należy wskazać węzeł i budynek!');
	}

}

function SprawdzKodLokalu(idlokalu){
	var kodlok = $('#edycjaKodLokalu').val();
	if (kodlok.length > 0){
		$.ajax({
			type: "POST",
			url: "ajax/lokalecontrol.php",
			data: {
				typ: 'sprawdzkodlokalu',
				idlokalu: idlokalu,
				kodlok: kodlok
			},
			success: function (response) {
				if (response[0] == 0){
					SprawdzCzyBudynekWSezonie(response[1]);
				}
				else {
					$('#edycjaPodzKomunikatBlad').text('Lokal o kodzie: '+response[2]+' już istnieje!');
				}
			}
		});
	}
	else{
		SprawdzCzyBudynekWSezonie(idlokalu);
	}
}

function SprawdzCzyBudynekWSezonie (idlokalu){
	var idbudynku = $('#idbudynku').val();
	$.ajax({
		type: "POST",
		url: "ajax/sezonycontrol.php",
		data: {
			typ: 'dajsezondlabudynku',
			idbudynku: idbudynku,
			idlokalu: idlokalu
		},
		success: function (response) {
			if (response[0] != 0)
				ZapiszModyfLokalu(response[1], response[0]);
			else $('#edycjaPodzKomunikatBlad').text('Zacznij od powiazania budyneku z sezonem!');
		}
	});
}

function ZapiszModyfLokalu(idlokalu, idsezonu){
	var idbudynku = $('#idbudynku').val();
	var pionowe = $('#edycjaPionowe').val();
	var poziome = $('#edycjaPoziome').val();
	var roza = $('#edycjaRoza').val();
	var idrodzaju = $('#edycjaRodzajLok').val();
	var numer = $('#edycjaNrLokalu').val();
	var powierzchnia = $('#edycjaPowierzniaLokalu').val();
	var kodsm = $('#edycjaKodSMLokalu').val();
	var kodlok = $('#edycjaKodLokalu').val();
	var imie = $('#edycjaLokatorImie').val();
	var nazwisko = $('#edycjaLokatorNazwisko').val();
	var nazwa = $('#edycjaLokatorNazwa').val();
	var rmi = $('#edycjaRMILokalu').val();
	var idnieruchomosci = $('#edycjaNieruchomoscLokalu').val();
    $('#edycja').modal('hide');
	$.ajax({
		type: "POST",
		url: "ajax/lokalecontrol.php",
		data: {
			typ: 'zmienlokal',
			idbudynku: idbudynku,
			idlokalu: idlokalu,
			pionowe: pionowe,
			poziome: poziome,
			roza: roza,
			idrodzaju: idrodzaju,
			numer: numer,
			powierzchnia: powierzchnia,
			kodsm: kodsm,
			kodlok: kodlok,
			imie: imie,
			nazwisko: nazwisko,
			nazwa: nazwa,
			rmi: rmi,
			idnieruchomosci: idnieruchomosci,
			idsezonu: idsezonu
		},
		success: function (response) {
            PokazWidokLokali (response);
		}
	});
}

$('#budynekWWezle').click(function() {
	PokazWidokLokali($('#budynekWWezle').val());
});

$('#filtrlokali').keyup(function (e) {
	if ((e.which < 33 || e.which > 40) && e.which != 27 && (e.which < 112 || e.which > 123) && e.which != 45){
		PokazWidokLokali($('#budynekWWezle').val(), $('#filtrlokali').val());
		return true;
	}
	if (e.which == 27){
		$('#filtrlokali').val('');
		PokazWidokLokali($('#budynekWWezle').val());
		return true;
    }
	if (e.which == undefined && $('#filtrlokali').val() == ''){
		PokazWidokLokali($('#budynekWWezle').val());
		return true;
	}
});

function DrukujRozliczenie(idlokalu){
	window.open('wydruki/rozliczenie.php?idlokalu='+idlokalu);
}

function WyslijRozliczenie(idlokalu){
	if (idlokalu != 0){
		$.ajax({
			type: "POST",
			url: "ajax/wysylkacontrol.php",
			data: {
				typ: 'czymaemail',
				idlokalu: idlokalu
			},
			success: function (response) {
                if (response[2] != '')
					MoznaWyslacRozliczenie(response[0], response[1], response[2], response[3]);
				else
					KomunikatBladPokaz('Lokator '+response[3]+' nie posiada zdefiniowanego adresu email!');
			}
		});
	}
	else
		KomunikatBladPokaz('Należy wybrać lokal!');
}

function MoznaWyslacRozliczenie(idlokalu, iduzytkownika, email, kto){
	if (idlokalu > 0){
		kto = kto.replaceAll('"','');
		let wysylka = `<button type="button" class="btn btn-success" data-dismiss="modal">Anuluj</button><button type="button" class="btn btn-danger" data-dismiss="modal" onclick="WyslijEmailDo(${idlokalu},${iduzytkownika},'${email}','${kto}')">Wyślij</button>`
		console.log(wysylka)
        $('#confirmationBody').empty();
		$('#confirmationFooter').empty();
		$('#confirmationBody').removeClass('form-group mb-2');
		$('#confirmationLongTitle').text('Wysyłanie rozliczenia email-em ');
		$('#confirmationBody').addClass('form-group mb-2');
		$('#confirmationBody').append('<label class="row-form-label" for="confirmationInput" id="confirmationLabel">Czy chcesz wysłać rozliczenie do '+kto+'?</label>');
		$('#confirmationFooter').append(wysylka);
		$('#confirmation').modal();
	}
}

function WyslijEmailDo(idlokalu, iduzytkownika, email, kto){
	$.ajax({
		type: "POST",
		url: "ajax/wysylkacontrol.php",
		data: {
			typ: 'wyslijjedenemail',
			idlokalu: idlokalu,
			iduzytkownika: iduzytkownika,
			email: email,
			kto: kto
		},
		success: function (response) {
            if (response > 0)
				KomunikatInfoPokaz ('Email został wysłany.');
			else
				KomunikatBladPokaz('Bład wysyałnia poczty email!');
		}
	});
}