function RozpakujDane(){
	var fd = new FormData();
	var files = $('#inputplik')[0].files[0];
	fd.append('inputplik', files);
	$.ajax({
		type: "POST",
		url: "../ajax/load.php",
        data: fd,
		contentType: false,
        processData: false,
		success: function (response) {
			if (response == 1)
				UsunBaze();
			else {
				$('#konunikatTekstBlad-p').html('Wykonanie operacji nie jest możliwe!');
				$('#komunikatBlad').modal();
			}
		}
	});
}

function UsunBaze(){
	$('#confirmationBody').empty();
	$('#confirmationFooter').empty();
	$('#confirmationBody').removeClass('form-group mb-2');
	$('#confirmationLongTitle').text('Odtwarzanie danych');
	$('#confirmationBody').addClass('form-group mb-2');
	$('#confirmationBody').append('<label class="row-form-label" for="confirmationInput" id="confirmationLabel">Wykonanie tej operacji spowoduje utratę obecnych danych!<br>Czy chcesz kontynuować?</label>');
	$('#confirmationFooter').append(
		'<button type="button" class="btn btn-success" data-dismiss="modal" >Nie</button>' +
		'<button type="button" class="btn btn-danger" data-dismiss="modal" onclick="DropDataBase()">Tak</button>'
	);
	$('#confirmation').modal();
}

function DropDataBase(){
	$.ajax({
		type: "POST",
		url: "../ajax/load.php",
		data: {
			typ: 'zakladaniebazy'
		},
		success: function (response) {
            if (response[0] == 0){
				$('#fileprogress').css('display', 'block');
				$('#informacja').css('display', 'block');
				$('#corobi').css('display', 'block');
				Importuj('bufor_grzejnikow', 1);
				//Importuj('ParemEmail', 1);
			}
		}
	});
}

function Importuj(tabela, postep){
    $.ajax({
		type: "POST",
		url: "../ajax/load.php",
		data: {
			typ: 'ladowanietabeli',
			tabela: tabela,
			postep: postep
		},
		success: function (response) {
			if (response[0] != ''){
				$('#informacja').text(response[0]);
				$('#fileprogress').val(response[1]);
				$('#fileprogress').text(response[1]+'%');
				Importuj(response[0], response[1]);
			}
			else{
				$('#informacja').text('Zakładanie kluczy i indeksów');
                ZakladanieKluczyInedksow();
			}
		}
	});
}

function ZakladanieKluczyInedksow(){
	$.ajax({
		type: "POST",
		url: "../ajax/load.php",
		data: {
			typ: 'klucze'
		},
		success: function (response) {
			window.open('../index.php', "_self");
		}
	});
}