function WidokUlice(filtr = ''){
    $.ajax({
        type: "POST",
        url: "ajax/ulicecontrol.php",
        data: {
            typ: 'widokulice',
            filtr: filtr
        },
        success: function (response) {
            $('#widokUlic').html(response);
            var idulicy = $('#idulicy').val();
            UstawNaWierszu('#widokUlic', 'tdvUli', idulicy, 'vUli');
        }
    });
}

function ZaznaczUlice(iduli){
    $('#idulicy').val(iduli);
    ZaznaczRekord($('#widokUlic tbody tr td a'), 'vUli' + iduli);
}

function PokazOknoUlicy (iduli, nazwa){
    CzyscKlasyModalEdit();
    if (iduli != 0 && iduli != undefined)
        $('#edycjaLongTitle').text('Zmiana ulicy');
    else
        $('#edycjaLongTitle').text('Dodaj ulicę');
    $('#edycjaBody').addClass('form-group mb-2');
    $('#edycjaBody').append(
        '<label class="row-form-label" for="edycjaInput" id="edycjaLabel">Nazwa</label>' +
            '<input class="form-control" type="text" id="edycjaInput" value="' +
            nazwa +
            '"></input>'
    );
    $('#edycjaFooter').append(
        '<button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>' +
            '<button type="button" class="btn btn-success" data-dismiss="modal" onclick="ZapiszUlice(' +
            iduli +
            ')">Zapisz</button>'
    );
    $('#edycja').on('shown.bs.modal', function(){
        $('#edycjaInput').select();
    });
    $('#edycja').modal();
}

function ZapiszUlice(iduli){
    $('#edycja').modal('hide');
    var nazwa = $('#edycjaInput').val();
    $.ajax({
        type: "POST",
        url: "ajax/ulicecontrol.php",
        data: {
            typ: 'zmienulice',
            idulicy: iduli,
            nazwa: nazwa
        },
        success: function (response) {
            if (response[2] === 'I'){
                $('#idulicy').val(response[0]);
                WidokUlice();
            }
            else{
                $('#vUli'+response[0]).text(response[1]);
            }
            ZaznaczUlice(response[0]);
        }
    });
}

function ZmienUlice(iduli){
    if (iduli == 0)
        iduli = $('#idulicy').val();
    var nazwa = $('#vUli'+iduli).text();
    PokazOknoUlicy(iduli, nazwa);

}

function DodajUlice(){
    PokazOknoUlicy(0, '');
}

function CzyMoznaUsunacUlice(iduli){
    if (iduli == 0)
        iduli = $('#idulicy').val();
    $.ajax({
        type: "POST",
        url: "ajax/ulicecontrol.php",
        data: {
            typ: 'czymoznausunac',
            idulicy: iduli
        },
        success: function (response) {
            if (response[0] == 0)
                CzyUsunacUlice(response[1])
            else {
                var nazwa = $('#vUli'+response[1]).text();
                KomunikatBladPokaz('Ulica: ' + nazwa + ' jest powiązana z budynkiem!</br>Usunięcie nie jest możliwe.');
            }
        }
    });
}

function CzyUsunacUlice(iduli){
		var nazwa = $('#vUli'+iduli).text();
        $('#confirmationBody').empty();
		$('#confirmationFooter').empty();
		$('#confirmationBody').removeClass('form-group mb-2');
		$('#confirmationLongTitle').text('Usuwanie ulicy: ' + nazwa);
		$('#confirmationBody').addClass('form-group mb-2');
		$('#confirmationBody').append('<label class="row-form-label" for="confirmationInput" id="confirmationLabel">Czy chcesz usunąć ulicę: '+nazwa+'</label>');
		$('#confirmationFooter').append(
			'<button type="button" class="btn btn-success" data-dismiss="modal">Anuluj</button>' +
				'<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="UsunUlice(' +
				iduli +
				')">Usuń</button>'
		);
		$('#confirmation').modal();
}

function UsunUlice(iduli){
    $.ajax({
        type: "POST",
        url: "ajax/ulicecontrol.php",
        data: {
            typ: 'usun',
            idulicy: iduli
        },
        success: function (response) {
            WidokUlice();
        }
    });
}

$('#filtrUlic').keyup(function(e){
    WidokUlice($('#filtrUlic').val());
})