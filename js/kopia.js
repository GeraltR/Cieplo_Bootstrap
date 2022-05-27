function Kopia(){
    $('#kopialog').text('Start');
    $.ajax({
        type: "POST",
        url: "ajax/kopiacontrol.php",
        data: {
            typ: 'kopia',
            obecna: 'bufor_grzejnikow'
        },
        success: function (response) {
            $('#chbserwerpomocy').prop({disabled: true});
            $('#btnKopiaDanych').prop({disabled: true});
            $('#kopialog').text('Kopiowanie tabeli: '+response);
            Nastepna(response);
        }
    });
}

function Nastepna(tabela){
    if (tabela.length > 0)
        $.ajax({
            type: "POST",
            url: "ajax/kopiacontrol.php",
            data: {
                    typ: 'kopia',
                    obecna: tabela
            },
            success: function (response) {
                $('#kopialog').text('Kopiowanie tabeli: '+response);
                    Nastepna(response);
            }
        });
    else {
        $('#kopialog').text('Pakowanie...');
        Spakuj();
    }
}

function Spakuj(){
    $.ajax({
        type: "POST",
        url: "ajax/kopiacontrol.php",
        data: {
            typ: 'zip'
        },
        success: function (response) {
            if ($('#chbserwerpomocy').is(':checked')){
                $('#kopialog').text('Wysyłanie pliku '+response+' na ftp...');
                Wyslij(response);
            }
            else {
                $('#kopialog').text('Gotowe. ' + response);
                $('#chbserwerpomocy').prop({disabled: false});
                $('#btnKopiaDanych').prop({disabled: false});
            }
        }
    });
}

function Wyslij(plik){
    $.ajax({
        type: "POST",
        url: "ajax/kopiacontrol.php",
        data: {
            typ: 'wysylka',
            plik: plik
        },
        success: function (response) {
            $('#kopialog').text('Gotowe. ' + response);
            $('#chbserwerpomocy').prop({disabled: false});
            $('#btnKopiaDanych').prop({disabled: false});
        }
    });
}