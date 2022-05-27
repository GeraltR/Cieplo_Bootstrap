//******************************  WĘZŁY CO I CW ******************************/

function ListaWezlow(filtrWezlow) {
	var idwezla = $('#idWezla').val();
	$.ajax({
		type: 'POST',
		url: 'ajax/wezlycontrol.php',
		data: {
			filtrWezlow: filtrWezlow,
			typ: 'lista',
			idwezla: idwezla
		},
		success: function(response) {
			if (response[1].length > 1){
				$('#widokWezlow').html(response[1]);
				ZaznaczWezel(response[0], 1);
			}
		}
	});
}
function ZaznaczWezel(idwezla, skad) {
    $('#idWezla').val(idwezla)
	if (skad === 1) {
		SpinnerShow();
		$.ajax({
			type: 'POST',
			url: 'ajax/budynkicontrol.php',
			data: {
                typ: 'budynekWWezle',
                idwezla: idwezla
            },
			success: function(dane) {
				SpinnerHide();
				$('#budynekWWezle').html(dane);
				var idwezla = $('#idWezla').val();
				ZaznaczRekord($('#widokWezlow tbody tr td a'), 'wez' + idwezla);
				PokazWidokLokali($('#budynekWWezle').val());
			}
		});
	} else {
		$('#idlokalu').val(0);
		ZaznaczRekord($('#widokWezlowCOiCW tbody tr td a'), 'vwez' + idwezla);
	}
}
function PokazWidokWezlow(filtr) {
	$.ajax({
		type: 'POST',
		url: 'ajax/wezlycontrol.php',
		data: {
			filtrWezlow: filtr,
			typ: 'widok'
		},
		success: function(response) {
			$('#widokWezlowCOiCW').html(response);
			var wezel = $('#idWezla').val();
			UstawNaWierszu('#widokWezlowCOiCW', 'tdvwez', wezel, 'vwez');
		}
	});
}

function ZmienWezel(idwezla) {
	if (idwezla === 0)
		idwezla = $('#idWezla').val();
	if (idwezla > 0){
		$.ajax({
			type: "POST",
			url: "ajax/ulicecontrol.php",
			data: {
				typ: 'listazid',
				id: idwezla
			},
			success: function (response) {
				ZamienWezelOkno(response[1], response[0], response[2]);
			}
		});
	}
	else KomunikatInfoPokaz('Musisz wskazać węzeł, który chcesz zmienić.');
}

function ZamienWezelOkno(idwezla, ulice, idulicy){
	var nazwa = $('#tdvwez' + idwezla).text();
	CzyscKlasyModalEdit();
	$('#edycjaLongTitle').text('Zmiana nazwy węzła ');
	$('#edycjaBody').addClass('form-group mb-2');
	$('#edycjaBody').append(
		'<label class="row-form-label" for="edycjaInput" id="edycjaLabel">Nazwa węzła</label>' +
			'<input class="form-control" type="text" id="edycjaInput" value="' +nazwa +'"></input>'+
			'<label class="row-form-label" for="ulicaInput" id="ulicaLabel">Nazwa ulicy</label>' +
			'<select class="form-control" id="ulicaWezla">'+ulice+'</select>'
		);
	$('#edycjaFooter').append(
		'<button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>' +
			'<button type="button" class="btn btn-success" data-dismiss="modal" onclick="ZapiszWezel(' +
			idwezla +
			')">Zapisz</button>'
	);
	$('#edycja').on('shown.bs.modal', function(){
		$('#ulicaWezla').val(idulicy);
		$('#edycjaInput').select();
	});
	$('#edycja').modal();
}

function ZapiszWezel(idwezla) {
	var nazwa = $('#edycjaInput').val();
	var idulicy = $('#ulicaWezla').val();
	$.ajax({
		type: 'POST',
		url: 'ajax/wezlycontrol.php',
		data: {
            typ: "update",
			idwezla: idwezla,
            nazwa: nazwa,
			idulicy: idulicy
		},
		dataType:"json",
		success: function(response) {
			$('#tdvwez'+response[0]).text(response[1]);
			PokazWidokWezlow($('#filtrWidokuWezlow').val());
        }
	});
}

function DodajWezel(){
	$.ajax({
		type: "POST",
		url: "ajax/ulicecontrol.php",
		data: {
			typ: 'lista'
		},
		success: function (response) {
			DodajWezelOkno(response);
		}
	});
}

function DodajWezelOkno(ulice){
	CzyscKlasyModalEdit();
	$('#edycjaLongTitle').text('Dodawanie nowego węzła ');
	$('#edycjaBody').addClass('form-group mb-2');
	$('#edycjaBody').append(
		'<label class="row-form-label" for="edycjaInput" id="edycjaLabel">Nazwa węzła</label>' +
			'<input class="form-control" type="text" id="edycjaInput" value=""></input>'+
		'<label class="row-form-label" for="ulicaInput" id="ulicaLabel">Nazwa ulicy</label>' +
		'<select class="form-control" id="ulicaWezla">'+ulice+'</select>' +
		'<span class="row-form-blad" id="wstawWezelKomunikatBlad"></span>'
	);
	$('#edycjaFooter').append(
		'<button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>' +
			'<button type="button" class="btn btn-success" onclick="WstawNowyWezel()">Zapisz</button>'
	);
	$('#edycja').on('shown.bs.modal', function(){
		$('#edycjaInput').select();
	});
	$('#edycja').modal();
}

function WstawNowyWezel() {
	var nazwa = $('#edycjaInput').val();
	var idulicy = $('#ulicaWezla').val();
	if (nazwa == '') {
		$('#wstawWezelKomunikatBlad').text('Nazwa węzła nie może być pusta!');
		return;
	}
	$('#edycja').modal('hide');
	$.ajax({
		type: "POST",
		url: "ajax/wezlycontrol.php",
		data: {
			nazwa: nazwa,
			idulicy: idulicy,
			typ: 'insert'
		},
		success: function (response) {
			PoWstawieniuWezla(response);
		}
	});
}

function PoWstawieniuWezla (idwezel) {
	$('#idWezla').val(idwezel);
	PokazWidokWezlow('');
}

function CzyUsunacWezel(){
    var idwezla = $('#idWezla').val();
	if (idwezla > 0){
		var nazwa = $('#tdvwez'+idwezla).text();
        $('#confirmationBody').empty();
		$('#confirmationFooter').empty();
		$('#confirmationBody').removeClass('form-group mb-2');
		$('#confirmationLongTitle').text('Usuwanie węzła: ' + nazwa);
		$('#confirmationBody').addClass('form-group mb-2');
		$('#confirmationBody').append('<label class="row-form-label" for="confirmationInput" id="confirmationLabel">Czy chcesz usunąć węzeł: '+nazwa+'</label>');
		$('#confirmationFooter').append(
			'<button type="button" class="btn btn-success" data-dismiss="modal">Anuluj</button>' +
				'<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="UsunWezel(' +
				idwezla +
				')">Usuń</button>'
		);
		$('#confirmation').modal();
	}
}

function UsunWezel (idwezla){
	$.ajax({
		type: "POST",
		url: "ajax/wezlycontrol.php",
		data: {
			idwezla: idwezla,
			typ: 'czymoznausunac'
		},
		dataType:"json",
		success: function (response) {
			if (response[0].length > 0){
				KomunikatBladPokaz(' Błąd usuwania węzła!<br>'+response[0]);
			}
			else MoznaUsunacWezel(response[1]);
		}
	});

}

function MoznaUsunacWezel(idwezla){
	$.ajax({
		type: "POST",
		url: "ajax/wezlycontrol.php",
		data: {
			idwezla: idwezla,
			typ: 'delete'
		},
		success: function (response) {
			$('#idWezla').val(0);
			PokazWidokWezlow('');
		}
	});
}


function PodepnijBudynek(){
	var idwezla = $('#idWezla').val();
	if (idwezla > 0){
		$.ajax({
			type: "POST",
			url: "ajax/wezlycontrol.php",
			data: {
				typ: 'wolnebudynki',
				idwezla: idwezla
			},
			success: function (response) {
				if (response[0] != '')
					PokazDialogDodajBudynek(response[1], response[0]);
				else
					KomunikatInfoPokaz('Nie ma wolnych budynków.');
			}
		});
	}
	else
		KomunikatBladPokaz('Musisz wskazać węzeł.');
}

function PokazDialogDodajBudynek(idwezla, lista){
    CzyscKlasyModalEdit();
	$('#edycjaLongTitle').text('Odpinanie budynku z węzła ');
	$('#edycjaBody').addClass('form-group mb-2');
	$('#edycjaBody').append(
		'<label class="row-form-label" for="budynekInput" id="ulicaLabel">Nazwa ulicy</label>' +
		'<select class="form-control" id="budynekInput">'+lista+'</select>'
	);
	$('#edycjaFooter').append(
		'<button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>' +
			'<button type="button" class="btn btn-success" onclick="AddBudynek('+idwezla+')">Zapisz</button>'
	);
	$('#edycja').on('shown.bs.modal', function(){
		$('#budynekInput').select();
	});
	$('#edycja').modal();
}

function AddBudynek(idwezla){
	var idbudynku = $('#budynekInput').val();
	$('#edycja').modal('hide');
	$.ajax({
		type: "POST",
		url: "ajax/wezlycontrol.php",
		data: {
			typ: 'addbudynekdowezla',
			idwezla: idwezla,
			idbudynku: idbudynku
		},
		success: function (response) {
			if (response == 0)
				PokazWidokWezlow($('#filtrWidokuWezlow').val());
			else
				KomunikatBladPokaz('Błąd przy dodawaniu budynku do węzła.');
		},
		error: function(){
			KomunikatBladPokaz('Błąd przy dodawaniu budynku do węzła.');
		}
	});
}

function OdepnijBudynek(){
	var idwezla = $('#idWezla').val();
	if (idwezla > 0){
		$.ajax({
			type: "POST",
			url: "ajax/wezlycontrol.php",
			data: {
				typ: 'budynkiwwezle',
				idwezla: idwezla
			},
			success: function (response) {
				if (response[0] != '')
					PokazDialogOdepnijBudynek(response[1], response[0]);
				else
					KomunikatInfoPokaz('Nie ma podpiętych budynków.');
			}
		});
	}
	else
		KomunikatBladPokaz('Musisz wskazać węzeł.');
}

function PokazDialogOdepnijBudynek(idwezla, lista){
	CzyscKlasyModalEdit();
	$('#edycjaLongTitle').text('Odpinanie budynku z węzła ');
	$('#edycjaBody').addClass('form-group mb-2');
	$('#edycjaBody').append(
		'<label class="row-form-label" for="budynekInput" id="ulicaLabel">Nazwa ulicy</label>' +
		'<select class="form-control" id="budynekInput">'+lista+'</select>'
	);
	$('#edycjaFooter').append(
		'<button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>' +
			'<button type="button" class="btn btn-success" onclick="CutBudynek('+idwezla+')">Zapisz</button>'
	);
	$('#edycja').on('shown.bs.modal', function(){
		$('#budynekInput').select();
	});
	$('#edycja').modal();
}

function CutBudynek(idwezla){
	var idbudynku = $('#budynekInput').val();
	$('#edycja').modal('hide');
	$.ajax({
		type: "POST",
		url: "ajax/wezlycontrol.php",
		data: {
			typ: 'usunbudynekzwezla',
			idwezla: idwezla,
			idbudynku: idbudynku
		},
		success: function (response) {
			if (response == 0)
				PokazWidokWezlow($('#filtrWidokuWezlow').val());
			else
				KomunikatBladPokaz('Błąd przy odpinaniu budynku z węzła.');
		},
		error: function(){
			KomunikatBladPokaz('Błąd przy odpinaniu budynku z węzła.');
		}
	});
}

//******************************  KONIEC WĘZŁY CO I CW ******************************/
