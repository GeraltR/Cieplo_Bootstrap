function ListaSezonow() {
	$.ajax({
		type: 'POST',
		url: 'ajax/sezonycontrol.php',
		data: {
			typ: 'lista',
			idbudynku: 0
		},
		success: function(response) {
			$('#listaSezonow').html(response[0]);
			$('#listaSezonow').val(response[1]);
			OdczytyZmienSezon();
		}
	});
}

function WidokSezonow(idsezonu = 0) {
	SpinnerShow();
	$.ajax({
		type: 'POST',
		url: 'ajax/sezonycontrol.php',
		data: {
			typ: 'widok',
			idsezonu: idsezonu
		},
		success: function(response) {
			SpinnerHide();
			$('#widokSezonow').html(response[0]);
			ZaznaczSezon(response[1]);
		}
	});
}

function ZaznaczSezon(idsezonu) {
	ZaznaczRekord($('#widokSezonow tbody tr td a'), 'sez' + idsezonu);
	SpinnerShow();
	$('#idSezonu').val(idsezonu);
	$.ajax({
		type: 'POST',
		url: 'ajax/budynkicontrol.php',
		data: {
			typ: 'widokbudynkowwsezonie',
			idsezonu: idsezonu
		},
		success: function(response) {
			SpinnerHide();
			$('#widokBudynkowWSezonie').html(response);
		}
	});
}

function PokazOkonoEdycji(idsezonu=0){
	var nazwa = '';
	var poczatek = DzisiajSQLStr();
	var koniec = DzisiajSQLStr();
	if (idsezonu != 0){
		nazwa = $('#vsezon' + idsezonu).text();
		poczatek = ConvertFormatDate($('#vsezpoczatek' + idsezonu).text());
		koniec = ConvertFormatDate($('#vsezkoniec' + idsezonu).text());
    }
	CzyscKlasyModalEdit();
	$('#edycjaLongTitle').text('Zmiana sezonu');
	$('#edycjaBody').addClass('form-group mb-2');
	$('#modalEdycjaMain').addClass('pam-duzy-dialog');
	$('#edycjaBody').append(
		'<label class="row-form-label" for="edycjaNazwaSezonu" id="edycjaNazwaSezonuLabel">Nazwa sezonu</label>' +
		'<input class="form-control" type="text" id="edycjaNazwaSezonu" value="'+nazwa+'"></input>' +
		'<label class="row-form-label" for="edycjaPoczatek" id="edycjaPoczatekLabel">Początek</label>' +
		'<input class="form-control" type="date" id="edycjaPoczatek" value="'+poczatek+'"></input>' +
		'<label class="row-form-label" for="edycjaKoniec" id="edycjaKoniecLabel">Koniec</label>' +
		'<input class="form-control" type="date" id="edycjaKoniec" value="'+koniec+'"></input>' +
		'<span class="row-form-blad" id="edycjaPodzKomunikatBlad"></span>'
	);
	$('#edycjaFooter').append(
		'<button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>' +
		'<button type="button" class="btn btn-success" onclick="ZapiszZmianeSezonu('+idsezonu+')">Zapisz</button>'
	);
	$('#edycja').modal();
}


function UpdateSezon() {
	var idsezonu = $('#idSezonu').val();
	PokazOkonoEdycji(idsezonu);
}

function ZapiszZmianeSezonu(idsezonu){
	var nazwa = $('#edycjaNazwaSezonu').val();
	var poczatek = $('#edycjaPoczatek').val();
	var koniec = $('#edycjaKoniec').val();
	$('#edycja').modal('hide');
	$.ajax({
		type: "POST",
		url: "ajax/sezonycontrol.php",
		data: {
			typ: 'update',
			idsezonu: idsezonu,
			nazwa: nazwa,
			poczatek: poczatek,
			koniec: koniec
		},
		success: function (response) {
			WidokSezonow(response);
		}
	});
}

function DodajSezon(){
   PokazOkonoEdycji(0);
}

function UsunSezon(){
    var idsezonu = $('#idSezonu').val();
	if (idsezonu > 0){
		var nazwa = $('#vsezon' + idsezonu).text();;
        $('#confirmationBody').empty();
		$('#confirmationFooter').empty();
		$('#confirmationBody').removeClass('form-group mb-2');
		$('#confirmationLongTitle').text('Usuwanie sezonu: ' + nazwa);
		$('#confirmationBody').addClass('form-group mb-2');
		$('#confirmationBody').append('<label class="row-form-label" for="confirmationInput" id="confirmationLabel">Czy chcesz usunąć węzeł: '+nazwa+'</label>');
		$('#confirmationFooter').append(
			'<button type="button" class="btn btn-success" data-dismiss="modal">Anuluj</button>' +
				'<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="CzyMoznaUsunacSezon(' +
				idsezonu +
				')">Usuń</button>'
		);
		$('#confirmation').modal();
	}
}

function CzyMoznaUsunacSezon(idsezonu){
	$.ajax({
		type: "POST",
		url: "ajax/sezonycontrol.php",
		data: {
			typ: 'czymoznausunac',
			idsezonu: idsezonu
		},
		dataType:"json",
		success: function (response) {
			if (response[0].length > 0){
				KomunikatBladPokaz(' Błąd usuwania sezonu!<br>'+response[0]);
			}
			else MoznaUsunacSezon(response[1]);
		},
		error: function (response, komunikat){
			KomunikatBladPokaz(' Błąd usuwania sezonu!<br>');
		}
	});
}

function MoznaUsunacSezon(idsezonu){
	$.ajax({
		type: "POST",
		url: "ajax/sezonycontrol.php",
		data: {
		typ: 'usunsezon',
		idsezonu: idsezonu
		},
		success: function (response) {
			WidokSezonow(0);
		},
		error: function (response, komunikat){
		KomunikatBladPokaz(' Błąd usuwania sezonu!<br>'+responsevent.statusText);
	}
	});

}

function UsunBudynekZSezonu(idbudynku){
	$('#confirmationBody').empty();
	$('#confirmationFooter').empty();
	$('#confirmationBody').removeClass('form-group mb-2');
	$('#confirmationLongTitle').text('Usuwanie budynku z sezonu');
	$('#confirmationBody').addClass('form-group mb-2');
	$('#confirmationBody').append('<label class="row-form-label" for="confirmationInput" id="confirmationLabel">Czy chcesz usunąć budynek z sezonu?</label>');
	$('#confirmationFooter').append(
		'<button type="button" class="btn btn-success" data-dismiss="modal">Anuluj</button>' +
		'<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="UsuwanieBudynkuZSezonu('+idbudynku+')">Usuń</button>'
	);
	$('#confirmation').modal();
}

function UsuwanieBudynkuZSezonu(idbudynku){
    $.ajax({
		type: "POST",
		url: "ajax/sezonycontrol.php",
		data: {
			typ: 'usunbudynekzsezonu',
			idbudynku: idbudynku
		},
		success: function (response) {
			WidokSezonow($('#idSezonu').val());
		}
	});
}

function DodajBudynekDoSezonu(){
	$.ajax({
		type: "POST",
		url: "ajax/budynkicontrol.php",
		data: {
			typ: 'listabudynkowbezsezonu'
		},
		success: function (response) {
			PokazOknoDodajBudynekDoSezonu(response)
		}
	});
}

function PokazOknoDodajBudynekDoSezonu(listabudynkow){
	var idsezonu = $('#idSezonu').val();
	CzyscKlasyModalEdit();
	$('#edycjaLongTitle').text('Dodanie budynku do sezonu');
	$('#edycjaBody').addClass('form-group mb-2');
	$('#modalEdycjaMain').addClass('pam-duzy-dialog');
	$('#edycjaBody').append(
		'<label class="row-form-label" for="edycjaNazwaBudynku" id="edycjaNazwaBudynkuLabel">Nazwa sezonu</label>' +
		'<select class="form-control" id="edycjaBudynkiBezSezonu">'+listabudynkow+'</select>'+
		'<span class="row-form-blad spanError" id="edycjaPodzKomunikatBlad"></span>'
	);
	$('#edycjaFooter').append(
		'<button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>' +
		'<button type="button" class="btn btn-success" onclick="ZapiszDodanieBudynkuDoSezonu('+idsezonu+')">Zapisz</button>'
	);
	$('#edycja').modal();
}

function ZapiszDodanieBudynkuDoSezonu(idsezonu){
	var idbudynku = $('#edycjaBudynkiBezSezonu').val();
	$('#edycja').modal('hide');
	$.ajax({
		type: "POST",
		url: "ajax/sezonycontrol.php",
		data: {
			typ: 'dodajbudynekdosezonu',
			idsezonu: idsezonu,
			idbudynku: idbudynku
		},
		success: function (response) {
			WidokSezonow(response);
		}
	});
}

function OdczytyZmienSezon(){
	var idsezonu = $('#listaSezonow').val();
	$.ajax({
		type: "POST",
		url: "ajax/budynkicontrol.php",
		data: {
			typ: 'widokbudynkowodczyty',
			idsezonu: idsezonu
		},
		success: function (response) {
			$('#widokBudynkowDoProtokolow').html(response[0]);
			$('#widokDoProtokolow').html('');
		}
	});
}

function DataOdczytu(event){
	$('#dataOdczytu').prop({disabled: true});
	PokazFormularz($('#odczytyIdBudynku').val(), $('#dataOdczytu').val());
}

function WyczyscOdczyty(){
	$('#widokDoProtokolow').html('');
}

function ZaznaczBudynekWSezonie(skad, idbudynku){
	var tabela = 0;
	if (skad === 'N'){
		tabela = $('#widokBudynkowDoProtokolow tbody tr td a');
		$('#sezonreturn').val($('#listaSezonow').val());
		$('#budynekreturn').val(idbudynku)
	}
	else if (skad === 'R') tabela = $('#widokBudynkowDoRozliczenia tbody tr td a');
		else if (skad === 'Z') tabela = $('#widokBudynkowDlaZaliczek tbody tr td a');
			else tabela = $('#widokBudynkowWSezonie tbody tr td a');
	ZaznaczRekord(tabela, 'budWSez'+skad+idbudynku);
	if (skad === 'N' || skad === 'n'){
		if (skad === 'N')
			$('#pozycjabtnOdzczytyLok').val(0);
		SpinnerShow();
		$('#odczytyIdBudynku').val(idbudynku);
		$.ajax({
			type: "POST",
			url: "ajax/sezonycontrol.php",
			data: {
				typ: 'dataodczytu',
				idbudynku: idbudynku
			},
			success: function (response) {
				$('#dataOdczytu').val(response[0]);
				PokazFormularz(response[1], response[0]);
			}
		});
	}
	if (skad === 'R'){
		PokazRozliczenie(idbudynku);
	}
	if (skad === 'Z'){
		PokazZaliczki(idbudynku);
	}
}

function ZaznaczBezOdczytu(przelaczniki){
	var ile = Object.keys(przelaczniki).length
	for (var i = 1; i <= ile; i++){
		if (przelaczniki[i] == 0)
			$('#odczC'+i).prop('checked', false);
		else $('#odczC'+i).prop('checked', true);
	}
}

function PokazFormularz(idbudynku, dataodczytu){
	$.ajax({
		type: "POST",
		url: "ajax/sezonycontrol.php",
		data: {
			typ: 'formularzodczytow',
			idbudynku: idbudynku,
			dataodczytu: dataodczytu
		},
		success: function (response) {
			SpinnerHide();
			$('#widokDoProtokolow').html(response[0]);
			ZaznaczBezOdczytu(response[1]);
			$('#dataOdczytu').prop({disabled: false});
			$('#selectdatyOdczytow').html(response[2]);
			var pozycja = $('#pozycjabtnOdzczytyLok').val();
			window.scrollTo(0, pozycja);
		}
	});
}

function ZgasIkony(numer){
	$('#odczF'+ numer).removeClass('bi-lightbulb-fill');
	$('#odczF'+ numer).addClass('bi-hourglass-split');
	$('#odczF'+ numer).hide();
}


function KlawiszUp(event, idpodz){
	var nastepny = 0;
	if (event.which === 38 || event.which === 40){
		var obecny = parseInt(event.target.id.replace('odczP', ''), 10);
		ZgasIkony(obecny);
		OdczytZapisz(idpodz, event.target.id);
		if (event.which === 38 && obecny > 1)
			nastepny = obecny - 1;
		else nastepny = obecny + 1;
		$('#odczP'+nastepny).focus();
	};
}

function OdcztyKey(event, idpodz){
	var nastepny = 0;
	var nazwa = $(event.target);
    event = (event) ? event : window.event;
	var charCode = (event.which) ? event.which : event.keyCode;
	var obecny = parseInt(event.target.id.replace('odczP', ''), 10);
	ZgasIkony(obecny);
	if (charCode === 13){
		OdczytZapisz(idpodz, event.target.id);
		nastepny = obecny + 1;
		$('#odczP'+nastepny).focus();
        return true;
	}
	if (charCode === 32){
		nazwa.val('');
	}
	if (charCode === 109){
		if ($('#odczC'+obecny).is(':checked'))
			DajMaxOdczyt(obecny, idpodz);
		return false;
	}
	if (charCode === 43){
		nastepny = parseInt(event.target.id.replace('odczP', ''), 10);
		if ($('#odczC'+nastepny).is(':checked')){
			$('#odczC'+nastepny).prop('checked', false);
		}
		else {
			$('#odczC'+nastepny).prop('checked', true);
		}
		nastepny = nastepny + 1;
		$('#odczP'+nastepny).focus();
		return false;
	}
    if ((charCode > 31) && (charCode < 48 || charCode > 57) && (charCode != 44)) {
        return false;
    }
    return true;
}

function OdczCheckClick(event, idpodz){
	var nastepny = event.target.id.replace('odczC', '');
	OdczytZapisz(idpodz, event.target.id);
}

function OdczytZapisz(idpodz, polewartosc){
	polewartosc = polewartosc.replace('odczC', 'odczP');
	var przelacznik = polewartosc.replace('odczP', 'odczC');
	var numerpola = polewartosc.replace('odczP', '');
	numerpola = numerpola.replace('odczC', '');
	var dataodczytu = $('#dataOdczytu').val();
	var wartosc = 0;
	var bezodczytu = 'N';
	if ($('#'+przelacznik).is(':checked'))
		bezodczytu = 'T';
	wartosc = $('#'+polewartosc).val();
	if (wartosc === undefined || wartosc === ''){
	   wartosc = 0;
	   $('#'+polewartosc).val(wartosc);
	}
	$('#odczC'+numerpola).prop({disabled: true});
	$('#odczP'+numerpola).prop({disabled: true});
	$('#odczF'+numerpola).show();
	$.ajax({
		type: "POST",
		url: "ajax/sezonycontrol.php",
		data: {
			typ: 'zapiswartosciodczytu',
			idpodz: idpodz,
			dataodczytu: dataodczytu,
			bezodczytu: bezodczytu,
			wartosc: wartosc,
			numerpola: numerpola
		},
		success: function (response) {
			$('#odczF'+response[1]).removeClass('bi-hourglass-split');
			$('#odczF'+response[1]).addClass('bi-lightbulb-fill');
			$('#odczC'+response[1]).prop({disabled: false});
			$('#odczP'+response[1]).prop({disabled: false});

		}
	});
}


function DajMaxOdczyt(numer, idpodz){
	var idbudynku = $('#odczytyIdBudynku').val();
	var idsezonu = $('#listaSezonow').val();
	if (idbudynku != 0 && idsezonu != 0){
		$.ajax({
			type: "POST",
			url: "ajax/rozliczeniecontrol.php",
			data: {
				typ: 'maxiloscjednostek',
				idbudynku: idbudynku,
				idsezonu: idsezonu,
				idpodz: idpodz,
				numer: numer
			},
			success: function (response) {
				$('#odczP'+response[2]).val(response[0]);
				OdczytZapisz(response[3], 'odczP'+response[2]);
				var nastepny = parseInt(response[2])+1;
				if (response[1] != '')
					KomunikatInfoPokaz('Lokal z największą ilością jednostek to: '+response[1], 'odczP'+nastepny,
					'Wyliczenia będą prawidłowe tylko jeżeli pomiszczenie będzie mieć podaną powierzchnię.');
				else
					KomunikatBladPokaz('Wynika, że dla wybranego sezonu nie ma wprowadzonych odczytów w tym budynku!');
			}
		});
	}
}

function ZmienDateOdczytow(){
	var idbudynku = $('#odczytyIdBudynku').val();
	CzyscKlasyModalEdit();
	$('#edycjaLongTitle').text('Zmiana daty odczytu');
	$('#edycjaBody').addClass('form-group mb-2');
	$('#edycjaBody').append(
		'<label class="row-form-label" for="edycjaData" id="edycjaDataLabel">Nowa data dla odczytów</label>' +
		'<input class="form-control" type="date" id="edycjaData" value="'+DzisiajSQLStr()+'">'+
		'<span class="row-form-blad spanError" id="edycjaDataOdczytuKomunikatBlad"></span>'
	);
	$('#edycjaFooter').append(
		'<button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>' +
		'<button type="button" class="btn btn-success" onclick="SprawdzDate('+idbudynku+')">Zapisz</button>'
	);
	$('#edycja').modal();
}

function SprawdzDate(idbudynku){
	$('#edycjaDataOdczytuKomunikatBlad').text('');
	try {
		var obecnadata = new Date($('#dataOdczytu').val())
		var nowadata = new Date($('#edycjaData').val());
		if (isNaN(nowadata) || isNaN(obecnadata))
			$('#edycjaDataOdczytuKomunikatBlad').text('Wprowadzona data nie jest prawidłowa');
		else {
			obecnadata = $('#dataOdczytu').val();
			nowadata = $('#edycjaData').val();
			$('#edycja').modal('hide');
			SpinnerShow();
			$.ajax({
				type: "POST",
				url: "ajax/rozliczeniecontrol.php",
				data: {
					typ: 'zmiendateodczytow',
					idbudynku: idbudynku,
					obecnadata: obecnadata,
					nowadata: nowadata
				},
				success: function (response) {
					ZaznaczBudynekWSezonie('N', response[0]);
				}
			});
		}
	}
	catch(e){
	 $('#edycjaDataOdczytuKomunikatBlad').text('Wprowadzona data nie jest prawidłowa');
	}
}