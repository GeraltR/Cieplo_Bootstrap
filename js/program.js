window.onload = function () {
    if (typeof history.pushState === "function") {
        history.pushState('nic', null, null);
        window.onpopstate = function () {
            history.pushState('gdzieś', null, null);
			if (($('#budynekreturn').val() != 0) && ($('#pozycjabtnOdzczytyLok').val() !=0 )){
				UkryjOkna();
				$('#odczytyPodzielnikow').show();
				$('#listaSezonow').val($('#sezonreturn').val());
				OdczytyZmienSezon();
				ZaznaczBudynekWSezonie('n', $('#budynekreturn').val());
			}
        };
    }
}

//Sterowanie widocznością gridów
function UkryjOkna() {
	//Rozliczenie
	$('#odczytyPodzielnikow').hide();
	$('#rozliczenie').hide();
	$('#wprowadzanieZaliczek').hide();
	//Katalogi
	$('#widokWezlow').empty();
	$('#widokLokali').empty();
	$('#lokalePomieszczenia').hide();
	$('#widokWezlowCOiCW').empty();
	$('#widokWezlowCoiCWIdWezla').val(0);
	$('#wezlycoicw').hide();
	$('#widokBudynkowIdbud').val(0);
	$('#widokBudynkowIdWez').val(0);
	$('#widokBudynkowIdUli').val(0);
	$('#widokBudynkowIdMia').val(0);
	$('#budynkiJednostkiRozliczeniowe').hide();
	$('#instalacjaGrzejnikowpodzielnikow').hide();
	$('#uzytkownicy').hide();
	$('#wPomieszczeniach').hide();
	//Słowniki
	$('#sezonGrzewczy').hide();
	$('#miastaUlice').hide();
    $('#grzejniki').hide();
	$('#rodzaje').hide();
	$('#adresybudynkow').hide();
	$('#spoldzielnia').hide();
	//Parametry
	$('#pracownicy').hide();
	$('#kopiaDanych').hide();
}


function ZaznaczRekord(tabela, wiersz) {
	if ($('#spinnerWait').is(':hidden')) {
		$(tabela).each(function(i) {
			if ($(this).attr('Id') === wiersz) $(this).parent().parent().addClass('zaznaczone');
			else $(this).parent().parent().removeClass('zaznaczone');
		});
	}
}

function WyczyscInput(poleInput) {
	$('#' + poleInput).val('');
	$('#' + poleInput).focus();
	$('#' + poleInput).keyup();
}

function CzyscKlasyModalEdit() {
	$('#edycjaBody').empty();
	$('#edycjaFooter').empty();
	$('#edycjaBody').removeClass('form-group mb-2');
	$('#modalEdycjaMain').removeClass('pam-duzy-dialog');
}

function KomunikatBladPokaz(komunikat, ustaw = '') {
	$('#konunikatTekstBlad-p').html(komunikat);
	$('#gdziefocuspoblad').val(ustaw);
	$('#komunikatBlad').modal();
}
$('#komunikatBlad').on('hidden.bs.modal', function(event){
	var nazwa = $('#gdziefocuspoblad').val();
	if (nazwa != '')
	  $('#'+nazwa).focus();
})


function KomunikatInfoPokaz(komunikat, ustaw = '', dodinfo = '') {
	$('#komunikatInfo-p').html(komunikat);
	$('#komunikatInfo-dod').html(dodinfo);
	$('#gdziefocuspoinfo').val(ustaw);
	$('#komunikatInfo').modal();
}

$('#komunikatInfo').on('hidden.bs.modal', function(event){
	var nazwa = $('#gdziefocuspoinfo').val();
	if (nazwa != '')
	  $('#'+nazwa).focus();
})

function SpinnerShow() {
	$('#spinnerWait').show();
	$('#spinnerwaitborder').show();
}

function SpinnerHide() {
	$('#spinnerWait').hide();
	$('#spinnerwaitborder').hide();
}

function UstawNaWierszu(gdzie, naczym, wartosc, nazwawiersza) {
	var i = 0;
	$(gdzie + ' > tbody  > tr >td').each(function(index, td) {
		if (td.id.length > 0) i++;
		if (td.id === naczym + wartosc) {
			var rowpos = $(gdzie + ' tr:eq(' + i + ')').position();
			$('#oknoglowne').scrollTop(rowpos.top);
			ZaznaczRekord(gdzie + ' tbody tr td a', nazwawiersza + wartosc);
		}
	});
}

function UstawNaWierszuLok(gdzie, naczym, wartosc, nazwawiersza) {
	$('#oknoglowne').scrollTop($('#pozycjaLokalu').val());
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

function TylkoCyfry(event){
    event = (event) ? event : window.event;
	var charCode = (event.which) ? event.which : event.keyCode;
    if ((charCode > 31) && (charCode < 48 || charCode > 57) && (charCode != 44)) {
        return false;
    }
    return true;
}

function StrToFloat(wartosc){
	return wartosc.replaceAll(',', '.');
}

function InputClick(event){
	$(event.target).select();
}


//******************************************************************************* */
//*                         Skrypty wewnątrz dokumentu                            */
//******************************************************************************* */
$(document).ready(function() {
	$.ajax({
		type: 'POST',
		url: 'ajax/kto.php',
		success: function(dane) {
			if (dane.admin.id != 0) {
				$('#admin_id').val(dane.admin.id);
				$('#admin_name').val(dane.admin.nazwa);
				$('#admin').val(dane.admin.upraw);
				$('#KtoNazwa').html('Zalogowany jako: ' + dane.admin.nazwa + '<b class="caret">');
				$('#dataserwis').val(DzisiajSQLStr());
			} else {
				window.location = '/index.php';
			}
		}
	});



	//-----------------------  Menu ----------------------------------
	//tylko jak spinner nie jest aktywny

	$('#mnuWyloguj').click(function() {
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#admin_id').val('');
			$('#admin_name').val('');
			$('#admin').val('');
			$('#KtoNazwa').html('Niezalogowany');
			$.ajax({
				url: 'ajax/wyloguj.php',
				success: function() {
					window.location = 'index.php';
				}
			});
		}
	});

	//------------------------ Rozliczenie -----------------------------------------
	$('#menuOdczytyPodzielnikow').click(function() {
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#odczytyPodzielnikow').show();
			ListaSezonow();
			$('#dataOdczytu').val(DzisiajSQLStr());
		}
	});
	$('#menuRozliczenie').click(function() {
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#rozliczenie').show();
			SelectSezonow();
			$('#dataRozliczenia').val(DzisiajSQLStr());

		}
	});
	$('#menuZaliczki').click(function () {
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			SelectSezonowZaliczki();
			$('#wprowadzanieZaliczek').show();
		}
	  })

	//------------------------- Katalogi --------------------------------------------
	$('#menuLokalePomiesz').click(function() {
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#lokalePomieszczenia').show();
			ListaWezlow($('#filtrWezlow').val());
		}
	});

	$('#menOdczyty').click(function() {
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
		}
	});

	$('#filtrWezlow').keyup(function() {
		if ($('#spinnerwaitborder').is(':hidden')) {
			$('#idWezla').val(0);
			$('#idlokalu').val(0);
			$('#idbudynku').val(0);
			$('#idpomieszczenia').val(0);
			$('#idgrzejnika').val(0);
			$('#pozycjaLokalu').val(0);
			ListaWezlow($('#filtrWezlow').val());
		}
	});

	$('#menuWezlyCOCW').click(function() {
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#filtrWidokuWezlow').val('');
			$('#wezlycoicw').show();
			PokazWidokWezlow('');
		}
	});

	$('#filtrWidokuWezlow').keyup(function() {
		if ($('#spinnerwaitborder').is(':hidden')) {
			PokazWidokWezlow($('#filtrWidokuWezlow').val());
		}
	});

	$('#menujednostkaRozliczeniowa').click(function() {
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#budynkiJednostkiRozliczeniowe').show();
			PokazWidokBudynkow('');
		}
	});

	$('#menuUzytkowncyLokali').click(function(){
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#uzytkownicy').show();
			var filtr = $('#filtrUzytkownikow').val();
			WidokUzytkownikow(filtr, 0);
		}
	});

	$('#filtrWidokuBudynkow').keyup(function() {
		if ($('#spinnerwaitborder').is(':hidden')) {
			PokazWidokBudynkow($('#filtrWidokuBudynkow').val());
		}
	});

	$('#menuWPomieszczeniach').click(function(){
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#wPomieszczeniach').show();
			WidokWPomieszczeniach();
		}
	});

	//---------------------------- Słowniki ----------------------------------

	$('#menuSezonGrzewczy').click(function() {
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#sezonGrzewczy').show();
			WidokSezonow(0);
		}
	});

	$('#menuMiastaUlice').click(function(){
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#miastaUlice').show();
			WidokMiastaUlice(0);
		}
	})

	$('#menuGrzejniki').click(function(){
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#grzejniki').show();
			WidokSlownikGrzejnikow();
		}
	})

	$('#menuRodzaje').click(function(){
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#rodzaje').show();
			WidokSlownikRodzaje();
		}
	})

	$('#menuAdresyBudynkow').click(function(){
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#adresybudynkow').show();
			WidokNieruchomosci();
		}
	})

	$('#menuspoldzielnia').click(function(){
		if ($('#spinnerwaitborder').is(':hidden')) {
			UkryjOkna();
			$('#spoldzielnia').show();
			PokazSpodzielnie();
		}
	})

	//---------------------------- Parametry ----------------------------------
	$('#menuZmienHaslo').click(function(){
		if ($('#spinnerwaitborder').is(':hidden')){
			UkryjOkna();
			PokazZmianeHasla();
		}
	});
	$('#menuPracownicy').click(function(){
		if ($('#spinnerwaitborder').is(':hidden')){
			if (CzyMaUprawnienia(0) === 1){
				UkryjOkna();
				$('#pracownicy').show();
				PokazWidokPracownikow();
			}
			else
				KomunikatBladPokaz('Brak uprawnień do tej operacji!');
		}
	})
	$('#menuKopiaDanych').click(function(){
		if ($('#spinnerwaitborder').is(':hidden')){
			UkryjOkna();
			$('#kopiaDanych').show();
			if (CzyMaUprawnienia(0) == 1){
				$('#wykonajzdanie').show();
				$('#btnWykonajZdanie').show();
			}
			else {
				$('#wykonajzdanie').hide();
				$('#btnWykonajZdanie').hide();
			}
		}
	})

});

function DrukujProtokoly() {
	var wybranyBudynek = $('#odczytyIdBudynku').val();
	window.open('wydruki/protokolyodczytu.php?idbudynku='+wybranyBudynek);
}

function StatSezon(){
	var idsezonu = $('#sezonRozliczenie').val();
	window.open('wydruki/statystyki.php?idsezonu='+idsezonu);
}

function StatAll(){
	window.open('wydruki/statystyki.php?idsezonu=0');
}

function LiczbaNaSlowo(liczba){
	var jednosci = ["", " jeden", " dwa", " trzy", " cztery", " pięć", " sześć", " siedem", " osiem", " dziewięć"];
	var nascie = ["", " jedenaście", " dwanaście", " trzynaście", " czternaście", " piętnaście", " szesnaście", " siedemnaście", " osiemnaście", " dziewietnaście"];
	var dziesiatki = ["", " dziesięć", " dwadzieścia", " trzydzieści", " czterdzieści", " pięćdziesiąt", " sześćdziesiąt", " siedemdziesiąt", " osiemdziesiąt", " dziewięćdziesiąt"];
	var setki = ["", " sto", " dwieście", " trzysta", " czterysta", " pięćset", " sześćset", " siedemset", " osiemset", " dziewięćset"];
	var grupy = [
	["" ,"" ,""],
	[" tysiąc" ," tysiące" ," tysięcy"],
	[" milion" ," miliony" ," milionów"],
	[" miliard"," miliardy"," miliardów"],
	[" bilion" ," biliony" ," bilionów"],
	[" biliard"," biliardy"," biliardów"],
	[" trylion"," tryliony"," trylionów"]];

	// jezeli pole zawiera poprawna wartosc calkowita
	if (liczba == liczba.toString()){
		var wynik = '';
		var znak = '';
		if (liczba == 0)
			wynik = "zero";
		if (liczba < 0) {
		znak = "minus";
			liczba = -liczba;
		}
		var g = 0;
		while (liczba > 0) {
			var s = Math.floor((liczba % 1000)/100);
			var n = 0;
			var d = Math.floor((liczba % 100)/10);
			var j = Math.floor(liczba % 10);

			if (d == 1 && j>0) {
				n = j;
				d = 0;
				j = 0;
			}

			var k = 2;
			if (j == 1 && s+d+n == 0)
			k = 0;
			if (j == 2 || j == 3 || j == 4)
				k = 1;
			if (s+d+n+j > 0)
				wynik = setki[s]+dziesiatki[d]+nascie[n]+jednosci[j]+grupy[g][k]+wynik;

			g++;
			liczba = Math.floor(liczba/1000);
		}
		return wynik;
	}
	else
	return '';
}

function WykonajZdanie() {
	CzyscKlasyModalEdit();
	$('#edycjaTitle').text('Zaloguj się');
	$('#edycjaBody').addClass('form-group mb-2');
	$('#edycjaBody').append(
		'<label class="row-form-label" for="edycjaSpecLogin" id="edycjaLabel">Hasło</label>' +
		'<input class="form-control" type="password" id="edycjaSpecLogin" value="" autocomplete="off"></input>'
	);
	$('#edycjaFooter').append(
		'<button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>' +
		'<button type="button" class="btn btn-success" data-dismiss="modal" onclick="SpecLogin()">Zaloguj</button>'
	);
	$('#edycja').modal();
}

function SpecLogin(){
	var haslo = $('#edycjaSpecLogin').val();
	$.ajax({
		type: "POST",
		url: "ajax/speclogin.php",
		data: {
			typ: 'login',
			haslo: haslo
		},
		success: function (response) {
			if (response != 0)
				PuscZdanie();
			else
					KomunikatBladPokaz('To nie jest prawidłowe hasło do tej operacji!');
		}
	});
}

function PuscZdanie(){
	var zdanie = $('#wykonajzdanie').val();
	$.ajax({
		type: "POST",
		url: "ajax/speclogin.php",
		data: {
			typ: 'wykonajzdanie',
            zdanie: zdanie
		},
		success: function (response) {
			if (response != 1)
				KomunikatInfoPokaz('Wykonane.')
			else
				KomunikatBladPokaz('Błąd zdania!');
		}
	});
}

function CzyMaUprawnienia(cowymagane){
	if ($('#admin').val() <= cowymagane)
		return 1;
	else
		return 0;
}
