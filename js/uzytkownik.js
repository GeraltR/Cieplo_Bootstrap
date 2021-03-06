function WidokUzytkownikow(filtr = '', poczatek = 0){
    var czyemail = 0;
    if ($('#chbtylkozemailem').is(':checked'))
       czyemail = 1;
    var ile = $('#iluuzytkownikow').val();
    if (ile === undefined || ile < 1 || ile > 100){
       ile = 30;
       $('#iluuzytkownikow').val(ile);
    }
    $.ajax({
        type: "POST",
        url: "ajax/uzytkownikcontrol.php",
        data: {
            typ: 'widok',
            filtr: filtr,
            poczatek: poczatek,
            ile: ile,
            czyemail: czyemail
        },
        success: function (response) {
            $('#widokUzytkownikow').html(response[0]);
            $('#pierwszyuzytkownik').val(response[1]);
            if ( $('#iduzytkownika').val() !== undefined &&  $('#iduzytkownika').val() != 0){
                ZaznaczUzytkownika($('#iduzytkownika').val());
            }
        }
    });
}

function TylkoZEmailem(){
    var filtr = $('#filtrUzytkownikow').val();
    if (filtr.length > 3 || filtr.length == 0){
        $('#iduzytkownika').val(0);
        WidokUzytkownikow(filtr, 0);
    }
}

$('#filtrUzytkownikow').keyup(function (e) {
    var filtr = $('#filtrUzytkownikow').val();
    if (filtr.length > 3 || filtr.length == 0){
        $('#iduzytkownika').val(0);
        WidokUzytkownikow(filtr, 0);
    }
});

function ZaznaczUzytkownika(iduzytkownika){
    $('#iduzytkownika').val(iduzytkownika);
    ZaznaczRekord($('#widokUzytkownikow tbody tr td a'), 'vuzyt' + iduzytkownika);
}

function PoprzNastUzytkownik(strona){
    var poczatek = parseInt($('#pierwszyuzytkownik').val());
    if (strona == 1){
        poczatek = poczatek + 30;
    }
    else {
        if (poczatek > 30)
            poczatek = poczatek - 30;
        else poczatek = 0;
    }
    $('#iduzytkownika').val(0);
    WidokUzytkownikow('', poczatek);
}

function ZmienUzytkownika(iduzytkownika){
    if (iduzytkownika == undefined || iduzytkownika == 0){
        iduzytkownika = $('#iduzytkownika').val();
    }
    if (iduzytkownika != undefined && iduzytkownika != 0){
        $.ajax({
            type: "POST",
            url: "ajax/uzytkownikcontrol.php",
            data: {
                typ: 'dajdane',
                iduzytkownika: iduzytkownika
            },
            success: function (response) {
                PokazDialogUzytkownika(response[0], response[1], response[2], response[3],response[4]);
            }
        });
    }
    else {
        KomunikatBladPokaz('Nale??y wybra?? u??ytkownika do zmiany!');
    }
}

function PokazDialogUzytkownika(iduzytkownika, imie, nazwisko, nazwa, email){
    SpinnerHide();
    CzyscKlasyModalEdit();
    $('#edycjaLongTitle').text('Zmiana u??ytkwonika');
    $('#edycjaBody').addClass('form-group mb-2');
    $('#modalEdycjaMain').addClass('pam-dialog');
        $('#edycjaBody').append(
        '<label class="row-form-label" for="edycjaUzytkImie" id="edycjaUzytkImieLabel">Imi??</label>' +
        '<input class="form-control" type="text" id="edycjaUzytkImie" value="'+imie+'"</input>'+
        '<label class="row-form-label" for="edycjaUzytkNazwisko" id="edycjaUzytkNazwiskoLabel">Nazwisko</label>' +
        '<input class="form-control" type="text" id="edycjaUzytkNazwisko" value="'+nazwisko+'"</input>'+
        '<label class="row-form-label" for="edycjaUzytkNazwa" id="edycjaUzytkNazwaLabel">Nazwa</label>' +
        '<input class="form-control" type="text" id="edycjaUzytkNazwa" value=\''+nazwa+'\'</input>'+
        '<label class="row-form-label" for="edycjaUzytkEmail" id="edycjaUzytkEmailLabel">email</label>' +
        '<input class="form-control" type="email" id="edycjaUzytkEmail" value="'+email+'"</input>'+
        '<span class="row-form-blad spanError" id="edycjaUzytkKomunikatBlad"></span>'
        );
        $('#edycjaFooter').append(
            '<button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>' +
            '<button type="button" class="btn btn-success" onclick="ZapiszUzytkownika('+iduzytkownika+')">Zapisz</button>'
    );
    $('#edycja').on('shown.bs.modal', function(){
        $('#edycjaUzytkownik').select();
    });
    $('#edycja').modal();

}

function ZapiszUzytkownika(iduzytkownika){
    $('#edycja').modal('hide');
    SpinnerShow();
    var imie = $('#edycjaUzytkImie').val();
    var nazwisko = $('#edycjaUzytkNazwisko').val();
    var nazwa = $('#edycjaUzytkNazwa').val();
    var email = $('#edycjaUzytkEmail').val();
    $.ajax({
        type: "POST",
        url: "ajax/uzytkownikcontrol.php",
        data: {
            typ: 'zapisz',
            iduzytkownika: iduzytkownika,
            imie: imie,
            nazwisko: nazwisko,
            nazwa: nazwa,
            email: email
        },
        success: function (response) {
            SpinnerHide();
            var filtr = $('#filtrUzytkownikow').val();
            var poczatek = $('#pierwszyuzytkownik').val()
            WidokUzytkownikow(filtr, poczatek);
        },
        error: function(){
            SpinnerHide();
        }
    });
}

function UsunUzytkownika(iduzytkownika){
    if (iduzytkownika == undefined || iduzytkownika == 0){
        iduzytkownika = $('#iduzytkownika').val();
    }
    if (iduzytkownika != undefined && iduzytkownika != 0){
        $.ajax({
            type: "POST",
            url: "ajax/uzytkownikcontrol.php",
            data: {
                typ: 'dajdane',
                iduzytkownika: iduzytkownika
            },
            success: function (response) {
                SprawdzCzyMoznaUsunac(response[0], response[1], response[2], response[3]);
            }
        });
    }
    else {
        KomunikatBladPokaz('Nale??y wybra?? u??ytkownika do usuni??cia!');
    }
}


function SprawdzCzyMoznaUsunac(iduzytkownika, imie, nazwisko, nazwa){
    SpinnerShow();
    $.ajax({
        type: "POST",
        url: "ajax/uzytkownikcontrol.php",
        data: {
            typ: 'sprawdz',
            iduzytkownika: iduzytkownika,
            imie: imie,
            nazwisko: nazwisko,
            nazwa: nazwa
        },
        success: function (response) {
            SpinnerHide();
            if (response[0] != 0){
                if (response[0] == 1)
                    KomunikatBladPokaz('U??ytkownik by?? u??yty do rozliczenia w starym programie. Nie mo??na go usuni????!');
                if (response[0] == 2)
                    KomunikatBladPokaz('U??ytkownik by?? u??yty do rozliczenia. Nie mo??na go usuni????!');
                if (response[0] > 2)
                    KomunikatBladPokaz('U??ytkownik jest po????czony z lokalem. Nie mo??na go usuni????!');
            }
            else
                PytanieOUsuniecieUzytkownika(response[1], response[2], response[3], response[4]);
        },
        error: function () {
            SpinnerHide();
        }
    });
}

function PytanieOUsuniecieUzytkownika(iduzytkownika, imie, nazwisko, nazwa){
    var inform = imie + ' ' + nazwisko + ' ' + nazwa;
	if (iduzytkownika > 0){
        $('#confirmationBody').empty();
		$('#confirmationFooter').empty();
		$('#confirmationBody').removeClass('form-group mb-2');
		$('#confirmationLongTitle').text('Usuwanie u??ytkownika');
		$('#confirmationBody').addClass('form-group mb-2');
		$('#confirmationBody').append('<label class="row-form-label" for="confirmationInput" id="confirmationLabel">Czy checsz usun???? u??ytkownika: '+inform+'</label>');
		$('#confirmationFooter').append(
			'<button type="button" class="btn btn-success" data-dismiss="modal">Anuluj</button>' +
				'<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="DeleteUzytkownika(' +
				iduzytkownika +
				')">Usu??</button>'
		);
		$('#confirmation').modal();
	}
}

function DeleteUzytkownika(iduzytkownika){
    SpinnerShow();
    $.ajax({
        type: "POST",
        url: "ajax/uzytkownikcontrol.php",
        data: {
            typ: 'usun',
            iduzytkownika: iduzytkownika
        },
        success: function (response) {
            SpinnerHide();
            var filtr = $('#filtrUzytkownikow').val();
            var poczatek = $('#pierwszyuzytkownik').val();
            $('#iduzytkownika').val(0);
            WidokUzytkownikow(filtr, poczatek);
        }
    });
}

function SzukajWezla(iduzytkownika, kodlokalu){
    if (iduzytkownika != 0 && iduzytkownika != undefined){
        if (kodlokalu != 'brak'){
            $.ajax({
                type: "POST",
                url: "ajax/uzytkownikcontrol.php",
                data: {
                    typ: 'szukajwezla',
                    iduzytkownika: iduzytkownika,
                    kodlokalu: kodlokalu
                },
                success: function (response) {
                    if (response[0] != 0)
                        IdzDoLokaluUzyt(response[0], response[1], response[2], response[3]);
                    else
                        KomunikatBladPokaz('Nie znaleziono w??z??a dla wskazanego u??ytkownika i jego lokalu!');
                }
            });
        }
        else
            KomunikatBladPokaz('Wskazany u??ytkownik nie ma przypisanego lokalu!');
    }
    else
        KomunikatBladPokaz('Nale??y wskaza?? u??ytkownika by m??c przej???? do jego lokalu!');
}

function IdzDoLokaluUzyt(idwezla, nazwawezla, idbudynku, kodlokalu){
    UkryjOkna();
	$('#lokalePomieszczenia').show();
    $('#filtrWezlow').val(nazwawezla)
    $('#idWezla').val(idwezla);
	ListaWezlow($('#filtrWezlow').val());
    $('#budynekWWezle').val(idbudynku);
    $('#filtrlokali').val(kodlokalu);
    $('#oknoglowne').scrollTop(0);
}

