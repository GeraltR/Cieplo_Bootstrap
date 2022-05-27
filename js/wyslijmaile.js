function WyslijRozliczenia(){
    var idbudynku = $('#rozliczenieIdBudynku').val();
	var dataWykonania =  $('#dataRozliczenia').val();
    if (idbudynku != 0 && idbudynku != ''){
        $.ajax({
            type: "POST",
            url: "ajax/rozliczeniecontrol.php",
            data: {
                typ: 'sprawdz',
                idbudynku: idbudynku,
                dataWykonania: dataWykonania
            },
            success: function (response) {
                if (response[0] != 0){
                    WysylakaPrepare(response[0]);
                }
                else
                    KomunikatBladPokaz('Należy wskazać budynek z aktualnym rozliczeniem!');
            }
        });
    }
    else
        KomunikatBladPokaz ('Nalieży wskazać budynek!');
}

function WysylakaPrepare(idkos){
    $.ajax({
        type: "POST",
        url: "ajax/wysylkacontrol.php",
        data: {
            typ: 'prepare',
            idkos: idkos
        },
        success: function (response) {
            if (response[1] != 0){
                CzyMoznaWyslac(response[0], response[1], response[2], response[3], response[4], response[5]);
            }
            else
                KomunikatInfoPokaz('W wybranym budynku żaden lokator nie podał adresu email.');
        }
    });
}

function CzyMoznaWyslac(idkos, idlokalu, ilemaili, kto, adres, iduzytkownika){
    if (idlokalu > 0){
        var koncowka = 'jeden lokal';
        var posiada = ' posiada';
        var niego = 'niego';
        if (ilemaili > 1){
            niego = 'nich';
            var tmp = ilemaili.toString();
            var ostatek = parseInt(tmp.substr(tmp.length-1));
            if (ostatek > 1 && ostatek < 5){
                koncowka = LiczbaNaSlowo(ilemaili) + ' lokale ';
                posiada = ' posiadają';
            }
            else {
                koncowka = LiczbaNaSlowo(ilemaili) + ' lokali ';
            }
        }
        koncowka = koncowka.trim();
        koncowka = koncowka[0].toUpperCase() + koncowka.substr(1);
        $('#confirmationBody').empty();
		$('#confirmationFooter').empty();
		$('#confirmationBody').removeClass('form-group mb-2');
		$('#confirmationLongTitle').text('Wysyłanie rozliczenia email-em ');
		$('#confirmationBody').addClass('form-group mb-2');
		$('#confirmationBody').append('<label class="row-form-label" for="confirmationInput" id="confirmationLabel">'+koncowka+posiada+' zdefiniowany adres email.<br>Czy chcesz wysłać do '+niego+' rozliczenie?</label>');
		$('#confirmationFooter').append(
			'<button type="button" class="btn btn-success" data-dismiss="modal">Anuluj</button>' +
			'<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="WysylkaStart(' +
			idkos+','+idlokalu + ',' + ilemaili + ',\'' + kto + '\',\'' + adres + '\','+iduzytkownika+')">Wyślij</button>'
		);
		$('#confirmation').modal();
	}
}

function WysylkaStart(idkos, idlokalu, ilemaili, kto, adres, iduzytkownika){
    $('#pleaseWaitDialog').modal();
    $('#emaildialogtekst').text(kto);
    Wysylka(idkos, idlokalu, ilemaili, kto, adres, iduzytkownika);
}

function Wysylka(idkos, idlokalu, ilemaili, kto, adres, iduzytkownika){
    SetProgress(ilemaili, kto);
    $.ajax({
        type: "POST",
        url: "ajax/wysylkacontrol.php",
        data: {
            typ: 'wyslijlokal',
            idkos: idkos,
            idlokalu: idlokalu,
            ilemaili: ilemaili,
            kto: kto,
            adres: adres,
            iduzytkownika: iduzytkownika
        },
        success: function (response) {
            if (response[1] > 0){
                Wysylka(response[0], response[1], response[2], response[3], response[4], response[5]);
            }
            else{
                $('#pleaseWaitDialog').modal('hide');
                $('#emailprogress').attr('aria-valuenow', 0);
                $("#emailprogress").css("width", "0%").text("0 %");
                $('#emaildialogtekst').text('...');
                if (response[1] == 0)
                    KomunikatInfoPokaz('Wysyłka poczty email zakończona.');
                else
                    KomunikatBladPokaz('Bład serwera poczty!');
            }
        },
        error: function (){
            $('#pleaseWaitDialog').modal('hide');
            KomunikatBladPokaz('Bład serwera poczty!');
        }
    });
}

function SetProgress(maksymalna, kto){
    var pozycja = parseInt($('#emailprogress').attr('aria-valuenow'));
    if (pozycja <= Math.round(maksymalna*(100/maksymalna))){
        pozycja = pozycja+ Math.round(100 / maksymalna);
        $('#emailprogress').attr('aria-valuenow', pozycja);
        $("#emailprogress").css("width", pozycja+ "%").text(pozycja+ " %");
        $('#emaildialogtekst').text(kto);
    }
}