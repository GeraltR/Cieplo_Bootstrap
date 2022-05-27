function WidokMiastaUlice(){
   WidokMiasta();
   WidokUlice();
}

function WidokMiasta(filtr = ''){
    $.ajax({
        type: "POST",
        url: "ajax/miejscowoscicontrol.php",
        data: {
            typ: 'widokmiasta',
            filtr: filtr
        },
        success: function (response) {
            $('#widokMiast').html(response);
        }
    });
}

function ZaznaczMiasto(idmia){
    $('#idmiasta').val(idmia);
    ZaznaczRekord($('#widokMiast tbody tr td a'), 'tdvMia' + idmia);
}


function PokazOknoMiasta (idmia, nazwa){
    CzyscKlasyModalEdit();
    if (idmia != 0 && idmia != undefined)
        $('#edycjaLongTitle').text('Zmiana miasta');
    else
        $('#edycjaLongTitle').text('Dodaj miasto');
    $('#edycjaBody').addClass('form-group mb-2');
    $('#edycjaBody').append(
        '<label class="row-form-label" for="edycjaInput" id="edycjaLabel">Nazwa</label>' +
            '<input class="form-control" type="text" id="edycjaInput" value="' +
            nazwa +
            '"></input>'
    );
    $('#edycjaFooter').append(
        '<button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>' +
            '<button type="button" class="btn btn-success" data-dismiss="modal" onclick="ZapiszMiasto(' +
            idmia +
            ')">Zapisz</button>'
    );
    $('#edycja').on('shown.bs.modal', function(){
        $('#edycjaInput').select();
    });
    $('#edycja').modal();
}

function ZapiszMiasto(idmia){
    $('#edycja').modal('hide');
    var nazwa = $('#edycjaInput').val();
    $.ajax({
        type: "POST",
        url: "ajax/miejscowoscicontrol.php",
        data: {
            typ: 'zmienmiasto',
            idmiasta: idmia,
            nazwa: nazwa
        },
        success: function (response) {
            if (response[2] === 'I'){
                $('#idmiasta').val(response[0]);
                WidokMiasta();
            }
            else{
                $('#tdvMia'+response[0]).text(response[1]);
            }
            ZaznaczMiasto(response[0]);
        }
    });
}

function ZmienMiasto(idmia){
    if (idmia == 0)
       idmia =  $('#idmiasta').val();
    var nazwa = $('#tdvMia'+idmia).text();
    PokazOknoMiasta(idmia, nazwa);
}

function DodajMiasto(){
    PokazOknoMiasta(0, '');
}

function CzyMoznaUsunacMiasto(idmia){
    if (idmia == 0)
        idmia = $('#idmiasta').val();
    if (idmia != 0){
        $.ajax({
            type: "POST",
            url: "ajax/miejscowoscicontrol.php",
            data: {
                typ: 'czymoznausunac',
                idmiasta: idmia
            },
            success: function (response) {
                if (response[0] == 0)
                    CzyUsunacMiasto(response[1])
                else {
                    var nazwa = $('#tdvMia'+response[1]).text();
                    KomunikatBladPokaz('Miasto: ' + nazwa + ' jest powiązane z budynkiem!</br>Usunięcie nie jest możliwe.');
                }
            }
        });
    }
    else {
        KomunikatBladPokaz('Wskaż miasto do usnięcia.');
    }
}

function CzyUsunacMiasto(idmia){
		var nazwa = $('#tdvMia'+idmia).text();
        $('#confirmationBody').empty();
		$('#confirmationFooter').empty();
		$('#confirmationBody').removeClass('form-group mb-2');
		$('#confirmationLongTitle').text('Usuwanie miasta: ' + nazwa);
		$('#confirmationBody').addClass('form-group mb-2');
		$('#confirmationBody').append('<label class="row-form-label" for="confirmationInput" id="confirmationLabel">Czy chcesz usunąć miasto: '+nazwa+'</label>');
		$('#confirmationFooter').append(
			'<button type="button" class="btn btn-success" data-dismiss="modal">Anuluj</button>' +
				'<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="UsunMiasto(' +
				idmia +
				')">Usuń</button>'
		);
		$('#confirmation').modal();
}

function UsunMiasto(idmia){
    $.ajax({
        type: "POST",
        url: "ajax/miejscowoscicontrol.php",
        data: {
            typ: 'usun',
            idmiasta: idmia
        },
        success: function (response) {
            WidokMiasta();
        }
    });
}

$('#filtrMiast').keyup(function(e){
    WidokMiasta($('#filtrMiast').val());
})