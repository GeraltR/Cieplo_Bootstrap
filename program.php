<?php
    session_start();
?>
<html lang="pl">

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="img/logo.ico">
    <title>Ciepło</title>
    <meta name="author" content="Paweł Mętelski and Bootstrap contributors">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-icons.css">
    <link rel="stylesheet" href="bootstrap/css/navbar.css">
    <link href="css/cefeusz.css" rel="stylesheet">
    <!--<link href="css/autocomplete.css" rel="stylesheet">-->

</head>

<body class="fill" id="oknoglowne" style="margin-bottom: 20px;">
    <!--SPINNER-->
    <div class="container">
        <nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light rounded" id="menuMain">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarmainMenu"
                aria-controls="navbarmainMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-md-center" id="navbarmainMenu">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="menuMainRozliczenia" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Rozliczenie</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown10">
                            <a class="dropdown-item" href="#" id="menuOdczytyPodzielnikow">Odczyty podzielników</a>
                            <a class="dropdown-item" href="#" id="menuRozliczenie">Rozliczenie</a>
                            <a class="dropdown-item" href="#" id="menuZaliczki">Zaliczki</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="menuKatalogi" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Katalogi</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown10">
                            <a class="dropdown-item" href="#" id="menuLokalePomiesz">Lokale i pomieszczenia</a>
                            <a class="dropdown-item" href="#" id="menuWezlyCOCW">Węzły CO i CW</a>
                            <a class="dropdown-item" href="#" id="menujednostkaRozliczeniowa">Budynki (jednostki rozliceniowe)</a>
                            <a class="dropdown-item" href="#" id="menuUzytkowncyLokali">Użytkownicy lokali</a>
                            <a class="dropdown-item" href="#"id ="menuWPomieszczeniach">Zainstalowane w pomieszczeniach</a>
                            <!-- <div class="dropdown-divider"></div> -->
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="menuSlowniki" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Słowniki</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown10">
                            <a class="dropdown-item" href="#" id="menuSezonGrzewczy">Sezony grzewcze</a>
                            <a class="dropdown-item" href="#" id="menuMiastaUlice">Miasta i Ulice</a>
                            <a class="dropdown-item" href="#" id="menuGrzejniki">Grzejniki</a>
                            <a class="dropdown-item" href="#" id="menuRodzaje">Rodzaj: lokalu, pomieszczenia i podzielnika</a>
                            <a class="dropdown-item" href="#" id="menuAdresyBudynkow">Adresy budynków</a>
                            <a class="dropdown-item" href="#" id="menuspoldzielnia">Spółdzielnia</a>
                            <!-- <div class="dropdown-divider"></div> -->
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="menuParametry" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">Parametry</a>
                        <div class="dropdown-menu" aria-labelledby="dropdown10">
                            <a class="dropdown-item" href="#" id="menuZmienHaslo">Zmień swoje hasło</a>
                            <a class="dropdown-item" href="#" id="menuPracownicy">Zarządzanie pracownikami</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" id="menuKopiaDanych">Wykonaj kopię</a>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" id="KtoNazwa" class="dropdown-toggle" data-toggle="dropdown">Zalogowano <b
                                class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a id="mnuWyloguj" href="#">Wyloguj</a></li>
                            <li role="separator" class="divider"></li>
                        </ul>
                    </li>
                </ul>
            </div>

        </nav>
        <header>
            <input type="hidden" id="admin_id" value=0 />
            <input type="hidden" id="admin_name" value="" />
            <input type="hidden" id="admin" value=0 />
            <input type="hidden" id="idusr" value="0" />
            <!-- **********  KOMUNIKAT BŁĄD *************-->
            <div class="modal fade" id="komunikatBlad" tabindex="-1" role="dialog" aria-labelledby="komunikatBladTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header modal-red" id="modalBlad">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title" id="komunikatBladLongTitle">Błąd</h4>
                        </div>
                        <div class="modal-body" id="komunikatTekstBody">
                            <p id="konunikatTekstBlad-p">Wystąpił błąd.</p>
                            <input type="hidden" id="gdziefocuspoblad" value=""/>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- **********  KOMUNIKAT SUKCES *************-->
            <div class="modal fade" id="komunikatDobry" tabindex="-1" role="dialog" aria-labelledby="komunikatDobryTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header modal-sukces" id="modalSukces">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title" id="komunikatDobryLongTitle">INFORMACJA</h5>
                        </div>
                        <div class="modal-body" id="komunikatSukcesBody">
                            <p id="komunikatDobry-p">Zatwierdzono poprawnie.</p>
                            <input type="hidden" id="gdziefocusposukces" value=""/>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- **********  KOMUNIKAT Informacja *************-->
            <div class="modal fade" id="komunikatInfo" tabindex="-1" role="dialog" aria-labelledby="komunikatInfoTitle"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header modal-info" id="modalInformacja">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title" id="komunikatInfoLongTitle">INFORMACJA</h5>
                        </div>
                        <div class="modal-body" id="komunikatInfoBody">
                            <p id="komunikatInfo-p">Zatwierdzono poprawnie.</p>
                            <input type="hidden" id="gdziefocuspoinfo" value=""/>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-6">
                                <p id="komunikatInfo-dod"></p>
                            </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- **********  EDYCJA TEMPLATE *************-->
            <div class="modal fade" id="edycja" tabindex="-1" role="dialog" aria-labelledby="edycjaTitle"
                aria-hidden="true">
                <div class="modal-dialog" role="document" id="modalEdycjaMain">
                    <div class="modal-content">
                        <div class="modal-header modal-info" id="modalEdycja">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title" id="edycjaLongTitle">INFORMACJA</h5>
                        </div>
                        <div class="modal-body" id="edycjaBody">

                        </div>
                        <div class="modal-footer" id="edycjaFooter">

                        </div>
                    </div>
                </div>
            </div>

            <!-- **********  PYTANIE TEMPLATE *************-->
            <div class="modal fade" id="confirmation" tabindex="-1" role="dialog" aria-labelledby="confirmationTitle"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header modal-info" id="modalconfirmation">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="modal-title" id="confirmationLongTitle">INFORMACJA</h5>
                        </div>
                        <div class="modal-body" id="confirmationBody">

                        </div>
                        <div class="modal-footer" id="confirmationFooter">

                        </div>
                    </div>
                </div>
            </div>


        </header>
        <main role="main" class="container">


            <div id="spinnerwaitborder" class="spinnerborder">

                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-light" role="status" id="spinnerWait">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>

            </div>
            <input type="hidden" id="dataserwis" value=""/>
            <input type="hidden" id="sezonreturn" value=""/>
            <input type="hidden" id="budynekreturn" value=""/>
            <input type="hidden" id="pozycjabtnOdzczytyLok" value=""/>

            <!-- LOKALE i POMIESZCZENIA-->
            <div class="container" id="lokalePomieszczenia">
                <h4 class="tytulOkna">Lokale i pomieszczenia</h4>
                <div class="w-100 listaWezlow">
                    <div class="row">
                        <div class="col-md-3">
                            <input class="form-control" id="filtrWezlow" type="text"
                                placeholder="wprowadź adres węzła">
                            <button type="button" class="btn btn-light btncenter" onclick="WyczyscInput('filtrWezlow')">Wyczyść filtr węzłów</button>
                            <div>
                                <input type="hidden" value="1" id="odWezla" />
                                <input type="hidden" value="30" id="iloscWezlow" />
                                <table class="table table-striped table-hover table-sm" id="widokWezlow">
                                    <input type="hidden" id="widokWezlowCoiCWIdWezla" value=0 />

                                </table>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <select class="form-control" id="budynekWWezle"></select>
                            <form class="form-inline">
                                <input class="form-control pam-inline" type="text" placeholder="wprowadź lokaltora"
                                    id="filtrlokali" value="">
                                <button type="button" class="btn btn-light pam-inline" onclick="WyczyscInput('filtrlokali')">Wyczyść filtr</button>
                                <button type="button" class="btn btn-light pam-inline" onclick="DodajLokal()"
                                    id="dodajLokal">Dodaj lokal</button>
                                <button type="button" class="btn btn-light pam-inline" onclick="ZmienLokal()"
                                    id="zmienLokal">Zmień</button>
                                <button type="button" class="btn btn-danger pam-inline" onclick="UsunLokal()"
                                    id="usunLokal">Usuń</button>
                            </form>
                            <div class="pb-3" Id="dTabelaLokali">
                                <input type="hidden" id="idWezla" value=0 />
                                <input type="hidden" id="idlokalu" value=0 />
                                <input type="hidden" id="idbudynku" value=0 />
                                <input type="hidden" id="idpomieszczenia" value=0 />
                                <input type="hidden" id="idgrzejnika" value=0 />
                                <input type="hidden" id="pozycjaLokalu" value=0 />

                                <table class="table table-striped table-hover table-sm" id="widokLokali">

                                </table>
                            </div>
                            <div class="pb-3" Id="dTabelaPomieszczen">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- WEZLY CO i CW-->
            <div class="container" id="wezlycoicw">
                <h4 class="tytulOkna">Węzły CO i CW</h4>
                <div class="w-100 listaWezlow">
                    <form class="form-inline">
                        <input class="form-control pam-inline" type="text" placeholder="szukaj wg węzła i ulicy"
                            id="filtrWidokuWezlow" value="">
                        <button type="button" class="btn btn-light pam-inline" onclick="WyczyscInput('filtrWidokuWezlow')">Wyczyść filtr</button>
                        <button type="button" class="btn btn-light pam-inline" onclick="ZmienWezel(0)">Zmień</button>
                        <button type="button" class="btn btn-light pam-inline" onclick="DodajWezel(0)">Dodaj nowy</button>
                        <button type="button" class="btn btn-danger pam-inline" onclick="CzyUsunacWezel(0)">Usuń</button>
                        <button type="button" class="btn btn-light pam-inline" onclick="PodepnijBudynek(0)">Podepnij budynek</button>
                        <button type="button" class="btn btn-danger pam-inline" onclick="OdepnijBudynek(0)">Odepnij budynek</button>
                    </form>
                    <div class="row">
                        <input type="hidden" id="widokWezlowCoiCWIdWezla" value=0 />
                        <table class="table table-striped table-hover table-sm" id="widokWezlowCOiCW">

                        </table>
                    </div>
                </div>
            </div>
            <!-- Jednostka rozliczeniowa -->
            <div class="container" id="budynkiJednostkiRozliczeniowe">
                <h4 class="tytulOkna">Budynki (jednostki rozliczeniowe)</h4>
                <div class="w-100 listaWezlow">
                    <form class="form-inline">
                        <input class="form-control pam-inline" type="text" placeholder="szukaj wg nazwy i ulicy"
                            id="filtrWidokuBudynkow" value="">
                        <button type="button" class="btn btn-light pam-inline" id="wyczyscFiltrWidokBudynkow"
                            onclick="WyczyscInput('filtrWidokuBudynkow')">Wyczyść filtr</button>
                        <button type="button" class="btn btn-light pam-inline" onclick="ZmienBudynek(0)">Zmień budynek</button>
                        <button type="button" class="btn btn-light pam-inline" onclick="DodajBudynek(0)">Dodaj nowy budynek</button>
                        <button type="button" class="btn btn-danger pam-inline" id="usunFiltrWidokBudynkow"
                            onclick="CzyUsunacBudynek(0)">Usuń budynek</button>
                    </form>
                    <div class="row">
                        <input type="hidden" id="widokBudynkowIdbud" value=0 />
                        <input type="hidden" id="widokBudynkowIdWez" value=0 />
                        <input type="hidden" id="widokBudynkowIdUli" value=0 />
                        <input type="hidden" id="widokBudynkowIdMia" value=0 />
                        <table class="table table-striped table-hover table-sm" id="widokBudynkow">

                        </table>
                    </div>
                </div>
            </div>
            <!-- Instalacja grzejnika lub podzielnika -->
            <div class="container" id="instalacjaGrzejnikowpodzielnikow">
                <h4 class="tytulOkna">Instalacja grzejnika lub podzielnika</h4>
                <div class="w-100 listaWezlow">
                    <form class="form-inline">
                        <button type="button" class="btn btn-light pam-inline" id="btnNowyGrzejnik" onclick="NowyGrzejnik()">Dodaj nowy grzejnik</button>
                        <button type="button" class="btn btn-light pam-inline" id="btnNowyPodzielnik" onclick="NowyPodzielnik()">Dodaj nowy podzielnik</button>
                        <button type="button" class="btn btn-light pam-inline" onclick="WrocDoLokali()">Przejdź do Budynków i lokali</button>
                    </form>
                    <div class="row">
                        <h4 id="pNazwaPomieszczenia"></h4>
                        <table class="table table-striped table-hover table-sm" id="widokGrzejnkow">
                        </table>
                        <table class="table table-striped table-hover table-sm" id="widokpodzielnikow">
                        </table>
                    </div>
                </div>
            </div>
            <!-- Odczyty podzielników -->
            <div class="container" id="odczytyPodzielnikow">
                <h4 class="tytulOkna">Wprowadzanie odczytów podzielników</h4>
                <div class="w-100 listaWezlow">
                    <form class="form-inline">
                        <select class="form-control" id="listaSezonow" onchange="OdczytyZmienSezon()"></select>
                        <button type="button" class="btn btn-light pam-inline" onclick="DrukujProtokoly()">Drukuj protokoły</button>
                        <label class="form-inline" for="dataOdczytu" id="dataOdczytuLabel"> Na dzień </label>
                        <input type="date" id="dataOdczytu" value="" onchange="WyczyscOdczyty()"/>
                        <button type="button" class="btn btn-light pam-inline leftmar" onclick="DataOdczytu()">Załaduj odczyty</button>
                        <select class="form-control" id="selectdatyOdczytow"></select>
                        <button type="button" class="btn btn-light pam-inline leftmar" onclick="ZmienDateOdczytow()">Zmień datę</button>
                        <input type="hidden" id="odczytyIdBudynku" value="0" />
                    </form>
                    <div class="row">
                        <div class="col-md-3">
                            <table class="table table-striped table-hover table-sm" id="widokBudynkowDoProtokolow">
                            </table>
                        </div>
                        <div class="col-md-9">
                            <table class="table table-sm" id="widokDoProtokolow">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Rozliczenie -->
            <div class="container" id="rozliczenie">
                <h4 class="tytulOkna">Rozliczenie ciepła </h4>
                <div class="w-100 listaWezlow">
                    <form class="form-inline">
                        <select class="form-control" id="sezonRozliczenie" onchange="RozliczenieZmienSezon()"></select>
                        <input  type="hidden" id="rozliczenieIdBudynku" value="0" />
                        <label class="rozliczenieSezon">Początek:</label><label style="margin-left: 1rem;" id="rozliczeniesezonpoczatek"></label>
                        <label class="rozliczenieSezon">Koniec:</label><label style="margin-left: 1rem;" id="rozliczeniesezonkoniec"></label>
                        <button type="button" class="btn btn-outline-secondary rozliczenieSezon" id="btnExportRCO" onclick="ExportDoRCO()">Eksport do RCO</button>
                    </form>
                    <div class="row">
                        <div class="col-md-3">
                            <table class="table table-striped table-hover table-sm" id="widokBudynkowDoRozliczenia">
                            </table>
                        </div>
                        <div class="col-md-9">
                                <div class="container rozliczenieForm">
                                    <div class="row" style="margin-left: 0.2rem;">
                                        <h4 id="rozliczenieNazwaBudynku">... </h4>
                                    </div>
                                    <div class="row">
                                            <div class="col-sm-4 form-inline">
                                                <select class="form-control" id="rozliczenieselectarchiwalne" onclick="RozliczenieArchiwalneChange()"></select>
                                                <div class="col-sm-3 form-inline">
                                                        <label class="form-check-label">
                                                            <input class="form-check-input" type="checkbox" id="rozliczeniearchiwalne" value="0" onchange="NoweRozliczenieClick()"/>
                                                            Nowe
                                                        </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="form-inline pam-leftmragin" for="dataRozliczenia" id="dataRozliczeniaLabel">Data
                                                    <input class= "form-inline inputRozliczenie" type="date" id="dataRozliczenia" value="" style="width: 9.2rem;"/>
                                                </label>
                                            </div>
                                            <div class="col-sm-4">
                                                <label class="form-inline pam-leftmragin" for="rozliczenieIloscGJBudynku">Ilość GJ
                                                    <input class="inputCyfry inputRozliczenie" type="text" id="rozliczenieIloscGJBudynku" value="" onkeypress=" return TylkoCyfry(event)">
                                                </label>
                                            </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label class="form-inline" for="rozliczenieKosztyBudynku">Koszty całkowite dostawy energii cieplnej dla budynku
                                                <input class="inputCyfry inputRozliczenie" type="text" id="rozliczenieKosztyBudynku" value=""  onkeypress=" return TylkoCyfry(event)" style="width: 6.8rem;">
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-inline" for="rozliczenieWspolczynnikKary" id="rozliczenieWspolczynnikKaryLabel" style="margin-left: 1.5rem;">Współczynnik karny
                                                <input class="inputCyfry inputRozliczenieBardzoWaskie" type="text" id="rozliczenieWspolczynnikKary" value="" onkeypress=" return TylkoCyfry(event)"/>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 form-inline">
                                            <label class="form-inline" for="rozliczenieKosztyStale" id="rozliczenieKosztyStaleLabel">Koszty stałe
                                                <input class="inputCyfry inputRozliczenieDuze" type="text" id="rozliczenieKosztyStale" value="" onkeypress=" return TylkoCyfry(event)"/>
                                                <select class="form-control form-inline" id="rodzajKosztowStalych" onchange="PokazKoszty()">
                                                    <option value="0">%</option>
                                                    <option value="1">zł</option>
                                                </select>
                                                <input type="hidden" id="kosztyStaleProcent" value=""/>
                                                <input type="hidden" id="kosztyStaleKwota" value=""/>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-inline" for="rozliczenieKosztyRozlicz" id="rozliczenieKosztyStaleLabel">Rozliczenie za podzielnik
                                                <input class="inputCyfry inputRozliczenieBardzoWaskie" type="text" id="rozliczenieKosztyRozlicz" value="" onkeypress=" return TylkoCyfry(event)"/>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-inline" for="rozliczenieKosztyRozliczMetr" id="rozliczenieKosztyStaleLabel">Rozliczenie nieopom.
                                                <input class="inputCyfry inputRozliczenieSred" type="text" id="rozliczenieKosztyRozliczMetr" value="" onkeypress=" return TylkoCyfry(event)"/>
                                            </label>
                                        </div>
                                        <input type="hidden" id="rozliczenieKosztyZmienne" value=""/>
                                    </div>
                                    <div class="row rozliczeniePrzyciski">
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-outline-secondary" id="btnRozliczBudynek" onclick="SprawdzCzyJestRozliczenie()">Oblicz</button>
                                        </div>
                                        <nav class="navbar navbar-expand" style="padding-top: 0px;">
                                            <div class="collapse navbar-collapse justify-content-md-center" id="navbarWydrukRozliczenia">
                                                <ul class="navbar-nav">
                                                    <li class="nav-item dropdown">
                                                        <button type="button" id="dropdownRozl1" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >Wydruki rozliczeń</button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownRozl1">
                                                            <a class="dropdown-item" href="#" onclick="WydrukZbiorowki()">Zbiorówka</a>
                                                            <a class="dropdown-item" href="#" onclick="WydrukZbiorowkiLokali()">Zbiorówka wg lokali</a>
                                                            <a class="dropdown-item" href="#" onclick="WydrukIndywDlaLokali()">Indywidualne rozliczenia dla lokali</a>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </nav>
                                        <nav class="navbar navbar-expand" style="padding-top: 0px;">
                                            <div class="collapse navbar-collapse justify-content-md-center" id="navbarStatystyki">
                                                <ul class="navbar-nav">
                                                    <li class="nav-item dropdown">
                                                        <button type="button" id="dropdownStat1" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >Statystyki</button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownStat1">
                                                            <a class="dropdown-item" href="#" onclick="StatAll()">Wszystkie budynki</a>
                                                            <a class="dropdown-item" href="#" onclick="StatSezon()">Budynki z sezonu</a>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </nav>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-outline-secondary" id="btnExcelEksport" onclick="ExportRozliczenia()">Eksport do Excela</button>
                                        </div>
                                    </div>

                                    <div id="pleaseWaitDialog" class="modal" data-backdrop="static" data-keyboard="false">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4>Wysłanie poczty do</h4>
                                                    <div class="w-100" id="emaildialogtekst">...</div>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="progress sndmail">
                                                        <div class="progress-bar" role="progressbar" id="emailprogress" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0 %</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row no-gutters rozliczeniePrzyciski">
                                            <div class="col-md-4 sndmailText">
                                            </div>
                                            <div class="col-md-6">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-outline-secondary" id="sendemailButton" onclick="WyslijRozliczenia()">Wyślij maile</button>
                                            </div>
                                        </div>
                                    </div>

                            <table class="table table-striped table-hover table-sm" id="widokWynikowRozliczenia">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Import zaliczek -->
            <div class="container" id="wprowadzanieZaliczek">
                <h4> Wprowadzanie zaliczek </h4>
                <div class="w-100 listaWezlow">
                    <form class="form-inline">
                        <select class="form-control" id="sezonZaliczki" onchange="ZaliczkiZmienSezon()"></select>
                        <input  type="hidden" id="zaliczkiIdBudynku" value="0" />
                        <label style="margin-left: 2rem; font-weight: bold;">Początek:</label><label style="margin-left: 1rem;" id="zaliczkisezonpoczatek"></label>
                        <label style="margin-left: 2rem; font-weight: bold;">Koniec:</label><label style="margin-left: 1rem;" id="zaliczkisezonkoniec"></label>
                    </form>
                </div>
                <div class="row listaWezlow">
                    <div class="col-md-3">
                        <table class="table table-striped table-hover table-sm" id="widokBudynkowDlaZaliczek">
                        </table>
                    </div>
                    <div class="col-md-9">
                        <div class="container zaliczkiForm">
                            <div class="row" style="margin-left: 0.2rem;">
                                <h4 id="zaliczkiNazwaBudynku">... </h4>
                            </div>
                            <div class="row" style="margin-bottom: 0.7rem;">
                                <div class="col-md-6">
                                    <input type="file" id="zaliczkifile" />
                                    <input type="hidden" id="zaliczkiimportfile" />
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-outline-secondary" onclick="ImportZaliczek()">Import zaliczek</button>
                                </div>
                            </div>
                        </div>
                        <div id="importzaliczek">
                        </div>
                        <table class="table table-striped table-sm" id="widokZaliczek">
                        </table>
                    </div>
                </div>
            </div>
            <!-- Sezon grzewczy -->
            <div class="container" id="sezonGrzewczy">
                <h4 class="tytulOkna">Sezony grzewcze</h4>
                <div class="w-100 listaWezlow">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="hidden" id="idSezonu" value=0 />
                            <form class="form-inline">
                                <button type="button" class="btn btn-light pam-inline" onclick="DodajSezon()">Dodaj nowy sezon</button>
                                <button type="button" class="btn btn-light pam-inline" onclick="UpdateSezon()">Zmień sezon</button>
                                <button type="button" class="btn btn-danger pam-inline" onclick="UsunSezon()">Usuń sezon</button>
                            </form>
                            <table class="table table-striped table-hover table-sm" id="widokSezonow">
                            </table>
                        </div>
                        <div class="col-md-6">
                            <form class="form-inline">
                                <button type="button" class="btn btn-light pam-inline" onclick="DodajBudynekDoSezonu()">Dodaj nowy budynek do sezonu</button>
                            </form>

                            <table class="table table-striped table-hover table-sm" id="widokBudynkowWSezonie">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pracownicy-->
            <div class="container" id="pracownicy">
                <h4 class="tytulOkna">Użytkownicy programu</h4>
                <div class="w-100 listaWezlow">
                    <form class="form-inline">
                        <button type="button" class="btn btn-light pam-inline" onclick="PokazDialogPracownika(0)">Dodaj pracownika</button>
                        <button type="button" class="btn btn-light pam-inline" onclick="ZmienPrac(0)">Zmień pracownika</button>
                        <button type="button" class="btn btn-danger pam-inline" onclick="UsunPrac(0)">Usuń Pracownika</button>
                    </form>
                    <div class="row">
                        <input type="hidden" id="idpracownika" value=0 />
                        <table class="table table-striped table-hover table-sm" id="widokpracownikow">

                        </table>
                    </div>
                </div>
            </div>
            <!-- Kopia danych-->
            <div class="container" id="kopiaDanych">
                <h4 class="tytulOkna">Wykonanie kopii danych</h4>
                <div class="w-100 listaWezlow">
                    <form class="form-inline">
                        <button type="button" class="btn btn-info pam-inline" id="btnKopiaDanych" onclick="Kopia()">Wykonaj kopię bazy danych</button>
                        <label class="form-check-label" style="margin: 0 1rem;">
                            <input class="form-check-input" type="checkbox" id="chbserwerpomocy" value="0"/>
                            Wyślij na serwer pomocy technicznej
                        </label>
                        <span id="kopialog"></span>
                    </form>
                </div>
                <div class="w-100">
                    <textarea class="form-control" rows="8" id="wykonajzdanie"></textarea>
                </div>
                <div class="w-100" style="margin-top: 1rem;">
                    <button type="button" class="btn btn-light pam-inline" id="btnWykonajZdanie" onclick="WykonajZdanie()">Wykonaj zdanie</button>
                </div>
            </div>
            <!-- Użytkownicy -->
            <div class="container" id="uzytkownicy">
                <h4 class="tytulOkna">Użytkownicy</h4>
                <div class="w-100 listaWezlow">
                    <form class="form-inline">
                        <input class="form-control pam-inline" type="text" placeholder="szukaj wg nazwy lub lokalu"
                            id="filtrUzytkownikow" value="">
                        <button type="button" class="btn btn-light pam-inline" id="wyczyscFiltrWidokUzytkownikow"
                            onclick="WyczyscInput('filtrUzytkownikow')">Wyczyść filtr</button>
                        <button type="button" class="btn btn-light pam-inline"
                            onclick="ZmienUzytkownika(0)">Zmień użytkownika</button>
                        <button type="button" class="btn btn-danger pam-inline"
                            onclick="UsunUzytkownika(0)">Usuń użytkownika</button>
                    </form>
                    <div class="row">
                        <input type="hidden" id="iduzytkownika" value=0 />
                        <input type="hidden" id="pierwszyuzytkownik" value=0 />
                        <div class="btn-group col-md-auto" role="group" aria-label="Kolejne strony">
                            <button type="button" class="btn btn-light pam-inline" onclick="PoprzNastUzytkownik(0)">Poprzedni</button>
                            <input class="form-control pam-inline pam-number" type="number" id="iluuzytkownikow" min="30" max="100" value="30" onkeypress="return TylkoCyfry(event)">
                            <button type="button" class="btn btn-light pam-inline" onclick="PoprzNastUzytkownik(1)">Następny</button>
                            <div class="col-mb-4 pam-leftmragin">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="checkbox" id="chbtylkozemailem" value="0" onchange="TylkoZEmailem()"/>
                                    Pokaż tylko mających email
                                </label>
                            </div>
                        </div>

                        <table class="table table-striped table-hover table-sm" id="widokUzytkownikow">

                        </table>
                    </div>
                </div>
            </div>
            <!-- Miasta i Ulice -->
            <div class="container" id="miastaUlice">
                <h4 class="tytulOkna">Miasta i ulice</h4>
                <div class="w-100 listaWezlow">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="row">
                                <form class="form-inline">
                                    <input class="form-control pam-inline" type="text" placeholder="szukaj wg nazwy miasta" id="filtrMiast" value="">
                                    <button type="button" class="btn btn-light pam-inline" onclick="WyczyscInput('filtrMiast')">Wyczyść filtr</button>
                                </form>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-light pam-inline" onclick="DodajMiasto()">Dodaj</button>
                                <button type="button" class="btn btn-light pam-inline" onclick="ZmienMiasto(0)">Zmień</button>
                                <button type="button" class="btn btn-danger pam-inline" onclick="CzyMoznaUsunacMiasto(0)">Usuń</button>
                            </div>
                            <div class="row" >
                                <input type="hidden" id="idmiasta" value=0 />
                                <table class="table table-striped table-hover table-sm" id="widokMiast">

                                </table>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="row">
                                <form class="form-inline">
                                    <input class="form-control pam-inline" type="text" placeholder="szukaj wg nazwy ulicy" id="filtrUlic" value="">
                                    <button type="button" class="btn btn-light pam-inline" onclick="WyczyscInput('filtrUlic')">Wyczyść filtr</button>
                                </form>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-light pam-inline" onclick="DodajUlice()">Dodaj</button>
                                <button type="button" class="btn btn-light pam-inline" onclick="ZmienUlice(0)">Zmień</button>
                                <button type="button" class="btn btn-danger pam-inline" onclick="CzyMoznaUsunacUlice(0)">Usuń</button>
                            </div>
                            <div class="row" >
                                <input type="hidden" id="idulicy" value=0 />
                                <table class="table table-striped table-hover table-sm" id="widokUlic">

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Grzejniki -->
            <div class="container" id="grzejniki">
                <h4 class="tytulOkna">Grzejniki</h4>
                <div class="w-100 listaWezlow">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row">
                                <form class="form-inline">
                                    <input class="form-control pam-inline" type="text" placeholder="szukaj wg nazwy producenta" id="filtrPoducGrzejnikow" value="" onKeyUp="KeyUpFiltrPodGrz(event)">
                                    <button type="button" class="btn btn-light pam-inline" onclick="WyczyscInput('filtrPoducGrzejnikow')">Wyczyść filtr</button>
                                </form>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-light btn-sm pam-inline" onclick="DodajProducentaGrzejnika()">Dodaj</button>
                                <button type="button" class="btn btn-light btn-sm pam-inline" onclick="ZmienProducentaGrzejnika(0)">Zmień</button>
                                <button type="button" class="btn btn-danger btn-sm pam-inline" onclick="CzyMoznaUsunacProducentaGrzejnika(0)">Usuń</button>
                            </div>
                            <div class="row" >
                                <input type="hidden" id="idproducenta" value=0 />
                                <input type="hidden" id="pierwszyProducent" value=0 />
                                <input type="hidden" id="iloscProducentow" value=0 />
                                <div class="btn-group col-md-auto" role="group" aria-label="Kolejne strony">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" id="btnPoprzProdGrz" onclick="PoprzNastProducentGrz(0)">Poprzedni</button>
                                    <input class="form-control pam-inline pam-number" type="number" id="ileProducentGrz" min="30" max="100" value="30" onkeypress="return TylkoCyfry(event)">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" id="btnNastProdGrz" onclick="PoprzNastProducentGrz(1)">Następny</button>
                                </div>
                                <table class="table table-striped table-hover table-sm" id="widokProducentowGrzejnikow">

                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <form class="form-inline">
                                    <input class="form-control pam-inline" type="text" placeholder="szukaj wg wymiaru" id="filtrRodzajuGrzejnikow" value="" onKeyUp="KeyUpFiltrRodzGrz(event)">
                                    <button type="button" class="btn btn-light  pam-inline" onclick="WyczyscInput('filtrRodzajuGrzejnikow')">Wyczyść filtr</button>
                                </form>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-light btn-sm pam-inline" onclick="DodajRodzajGrzejnika()">Dodaj</button>
                                <button type="button" class="btn btn-light btn-sm pam-inline" onclick="ZmienRodzajGrzejnika(0)">Zmień</button>
                                <button type="button" class="btn btn-danger btn-sm pam-inline" onclick="CzyMoznaUsunacRodzajGrzejnika(0)">Usuń</button>
                            </div>
                            <div class="row" >
                                <input type="hidden" id="idrodzajugrzejnika" value=0 />
                                <input type="hidden" id="pierwszyRodzGrz" value=0 />
                                <input type="hidden" id="iloscRodzajow" value=0 />
                                <div class="btn-group col-md-auto" role="group" aria-label="Kolejne strony">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" id="btnPoprzRodzGrz" onclick="PoprzNastRodzGrz(0)">Poprzedni</button>
                                    <input class="form-control pam-inline pam-number" type="number" id="ileRodzGrz" min="30" max="100" value="30" onkeypress="return TylkoCyfry(event)">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" id="btnNastRodzGrz" onclick="PoprzNastRodzGrz(1)">Następny</button>
                                </div>
                                <table class="table table-striped table-hover table-sm" id="widokRodzajuGrzejnika">

                                </table>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="row" style="background-color: darkseagreen;margin-bottom: 1rem;">
                                <h6>Ilość żeberek i współczynnik</h6>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-light btn-sm pam-inline" onclick="DodajWspKQ()">Dodaj</button>
                                <button type="button" class="btn btn-light btn-sm pam-inline" onclick="ZmienWspKQ(0)">Zmień</button>
                                <button type="button" class="btn btn-danger btn-sm pam-inline" onclick="CzyMoznaUsunacWspolczynnikKQ(0)">Usuń</button>
                            </div>
                            <div class="row" >
                                <input type="hidden" id="idwspolczynnikakq" value=0 />

                                <table class="table table-striped table-hover table-sm" id="widokWspolczynnikKQ">

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Rodzaje -->
            <div class="container" id="rodzaje">
                <div class="w-100 listaWezlow">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row kolumnatytul">
                                <h6>Rodzaje lokali</h6>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-4 smbuttoninline">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" onclick="DodajRodzajLokalu()">Dodaj</button>
                                </div>
                                <div class="col-md-4 smbuttoninline">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" onclick="ZmienRodzajLokalu(0)">Zmień</button>
                                </div>
                                <div class="col-md-4 smbuttoninline">
                                    <button type="button" class="btn btn-danger btn-sm pam-inline" onclick="CzyMoznaUsunacRodzajLokalu(0)">Usuń</button>
                                </div>
                            </div>
                            <div class="row" >
                                <input type="hidden" id="idrodzajlokalu" value=0 />
                                <table class="table table-striped table-hover table-sm" id="widokRodzajowLokali">
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row kolumnatytul">
                                <h6>Rodzaje pomieszczeń</h6>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-4 smbuttoninline">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" onclick="DodajRodzajPomieszczenia()">Dodaj</button>
                                </div>
                                <div class="col-md-4 smbuttoninline">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" onclick="ZmienRodzajPomieszczenia(0)">Zmień</button>
                                </div>
                                <div class="col-md-4 smbuttoninline">
                                    <button type="button" class="btn btn-danger btn-sm pam-inline" onclick="CzyMoznaUsunacRodzajPomieszcenia(0)">Usuń</button>
                                </div>
                            </div>
                            <div class="row" >
                                <input type="hidden" id="idrodzajupomieszczenia" value=0 />
                                <table class="table table-striped table-hover table-sm" id="widokRodzajuPomieszczenia">
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row kolumnatytul">
                                <h6>Rodzaje podzielników</h6>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-4 smbuttoninline">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" onclick="DodajRodzajPodzielnika()">Dodaj</button>
                                </div>
                                <div class="col-md-4 smbuttoninline">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" onclick="ZmienRodzajPodzielnika(0)">Zmień</button>
                                </div>
                                <div class="col-md-4 smbuttoninline">
                                    <button type="button" class="btn btn-danger btn-sm pam-inline" onclick="CzyMoznaUsunacRodzajPodzielnika(0)">Usuń</button>
                                </div>
                            </div>
                            <div class="row" >
                                <input type="hidden" id="idrodzajupodzielnika" value=0 />
                                <table class="table table-striped table-hover table-sm" id="widokRodzajuPodzielnika">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- W pomieszczenich -->
            <div class="container" id="wPomieszczeniach">
                <h4 class="tytulOkna">Zainstalowane w pomieszczeniach</h4>
                <div class="w-100 listaWezlow">
                    <div class="row">
                        <div class="col-md-5 colA">
                            <div class="row kolumnatytulzai">
                                <h6>Grzejniki</h6>
                            </div>
                            <div class="row">
                                <form class="form-inline formmaxrow">
                                    <input class="form-control pam-inline formmaxrow" type="text" placeholder="szukaj wg nazwy producenta" id="filtrZainstalGrzejProd" value="" onKeyUp="KeyUpFiltrZainstGrzej(event)">
                                    <button type="button" class="btn btn-light" onclick="WyczyscInput('filtrZainstalGrzejProd')">Wyczyść filtr</button>
                                </form>
                            </div>
                            <div class="row">
                                <form class="form-inline formmaxrow">
                                    <input class="form-control pam-inline formmaxrow" type="text" placeholder="szukaj wg wymiaru" id="filtrZainstalGrzejWymiar" value="" onKeyUp="KeyUpFiltrZainstGrzej(event)">
                                    <button type="button" class="btn btn-light" onclick="WyczyscInput('filtrZainstalGrzejWymiar')">Wyczyść filtr</button>
                                </form>
                            </div>
                            <input type="hidden" id="pierwszyZainstalGrzej" value=0 />
                            <input type="hidden" id="iloscZainstalGrzej" value=0 />
                            <div class="btn-group col-md-auto" role="group" aria-label="Kolejne strony">
                                <button type="button" class="btn btn-light btn-sm pam-inline" id="btnPoprzZainstalGrzej" onclick="PoprzZainstalGrzej()">Poprzedni</button>
                                <input class="form-control pam-inline pam-number" type="number" id="pokazIleZainstalGrzej" min="30" max="100" value="30" onkeypress="return TylkoCyfry(event)">
                                <button type="button" class="btn btn-light btn-sm pam-inline" id="btnNastZainstalGrzej" onclick="NastZainstalGrzej()">Następny</button>
                            </div>
                            <input type="hidden" id="idzainstalgrzej" value=0 />
                            <table class="table table-striped table-hover table-sm" id="widokZainstalGrzejnikow">
                            </table>
                        </div>
                        <div class="col-md-5 colB">
                            <div class="row kolumnatytulzai">
                                <h6>Podzielniki</h6>
                            </div>
                            <div class="row">
                                <form class="form-inline formmaxrow">
                                    <input class="form-control pam-inline formmaxrow" type="text" placeholder="szukaj wg numeru fabrycznego" id="filtrZainstalPodzielnik" value="" onKeyUp="KeyUpFiltrZainstPodz(event)">
                                    <button type="button" class="btn btn-light" onclick="WyczyscInput('filtrZainstalPodzielnik')">Wyczyść filtr</button>
                                </form>
                            </div>
                            <input type="hidden" id="pierwszyZainstalPodziel" value=0 />
                            <input type="hidden" id="iloscZainstalPodziel" value=0 />
                            <div class="btn-group col-md-auto" role="group" aria-label="Kolejne strony">
                                <button type="button" class="btn btn-light btn-sm pam-inline" id="btnPoprzZainstalPodziel" onclick="PoprzZainstalPodziel()">Poprzedni</button>
                                <input class="form-control pam-inline pam-number" type="number" id="pokazIleZainstalPodziel" min="30" max="100" value="30" onkeypress="return TylkoCyfry(event)">
                                <button type="button" class="btn btn-light btn-sm pam-inline" id="btnNastZainstalPodziel" onclick="NastZainstalPodziel()">Następny</button>
                            </div>
                            <input type="hidden" id="idzainstalpodzielnik" value=0 />
                            <table class="table table-striped table-hover table-sm" id="widokZainstalPodzielnik">
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Adresy budynków -->
            <div class="container" id="adresybudynkow">
                <h4 class="tytulOkna">Adresy dla budynków</h4>
                <div class="w-100 listaWezlow">
                    <form class="form-inline">
                        <input class="form-control pam-inline" type="text" placeholder="szukaj wg kodu i ulicy" onkeyup="KeyUpFiltrNieruch(event)"
                            id="filtrWidokuAdresyBudynkow" value="">
                        <button type="button" class="btn btn-light pam-inline" onclick="WyczyscInput('filtrWidokuAdresyBudynkow')">Wyczyść filtr</button>
                        <button type="button" class="btn btn-light pam-inline" id="edytujFiltrWidokBudynkow"
                            onclick="ZmienAdresBudynu(0)">Zmień adres budynu</button>
                        <button type="button" class="btn btn-light pam-inline"
                            onclick="DodajAdresBudynu()">Dodaj adres</button>
                        <button type="button" class="btn btn-danger pam-inline"
                            onclick="CzyUsunacAdresBudynu(0)">Usuń adres</button>
                    </form>
                    <div class="row">
                        <input type="hidden" id="idnier" value=0 />
                        <table class="table table-striped table-hover table-sm" id="widokAdresBudynu">
                        </table>
                    </div>
                </div>
            </div>
            <!-- Spółdzielnia -->
            <div class="container" id="spoldzielnia">
                <div class="w-100 listaWezlow">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="row kolumnatytul">
                                <h6>Spółdzielnia</h6>
                            </div>
                            <div class="col-md-12">
                                <input type="hidden" id="spoldzielniaNazwa" value=0 />
                                <input type="hidden" id="spoldzielniaidulicy" value="0" />
                                <input type="hidden" id="spoldzielniaidmiasta" value="0" />
                                <input type="hidden" id="spoldzielnianumerulicy" value="" />
                                <input type="hidden" id="spoldzielniakodpoczt" value="" />
                                <div class="row">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" id="btnZmienSpodzielnie" onclick="PokazDialogSpoldzielni()">Zmien spółdzielnie</button>
                                    <div class="w-100" id="spoldzielniawidok">
                                    </div>
                                </div>
                                <input type="hidden" id="wystawcaidulicy" value="0" />
                                <input type="hidden" id="wystawcaidmiasta" value="0" />
                                <input type="hidden" id="wystawcanumerulicy" value="" />
                                <input type="hidden" id="wystawcakodpoczt" value="" />
                                <input type="hidden" id="wystawcaNazwa" value="" />
                                <input type="hidden" id="wystawcaMiasto" value="" />
                                <input type="hidden" id="wystawcasmtp" value="" />
                                <input type="hidden" id="wystawcaport" value="25" />
                                <input type="hidden" id="wystawcauser" value="" />
                                <input type="hidden" id="wystawcapass" value="" />
                                <div class="row">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" id="btnZmienWystawce" onclick="PokazDialogWystawcy()">Zmień wystawce</button>
                                    <div class="w-100" id="wystawcawidok">
                                    </div>
                                </div>
                                <input type="file" id="inputpodpis">
                                <div class="row">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" onclick="WczytajPodpis()">Wczytaj plik</button>
                                    <div class="w-100">
                                    <img id="previewpodpis" src="img/podpis.jpg">
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row kolumnatytul">
                                <h6>Wysyłki emaili</h6>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-3 smbuttoninline">
                                    <button type="button" class="btn btn-light btn-sm pam-inline" onclick="ParamEmail()">Zapisz</button>
                                </div>
                                <div class="col-md-3 smbuttoninline">
                                    <button type="button" class="btn btn-danger btn-sm pam-inline" onclick="WyczyscLoga(0)">Wyczyść loga</button>
                                </div>
                            </div>
                            <div class="row" id="parametryemailwidok">
                                <div class="w-100">
                                    <label class="form-group pam-inline">Tytuł
                                    </label>
                                    <input class="form-control" type="text" id="tytulemaili" value=""/>
                                </div>
                                <div class="w-100">
                                    <label class="form-group">
                                        Treść
                                    </label>
                                    <textarea class="form-control" rows="8" id="tekstemaili"></textarea>
                                </div>
                            </div>
                            <div class="row" >
                                <form class="form-inline">
                                    <input class="form-control pam-inline" type="text" placeholder="szukaj wg kodu lub nazwy" onkeyup="KeyUpFiltrLoga(event)"
                                    id="filtrWidokuLogaEmail" value="">
                                    <button type="button" class="btn btn-light pam-inline" id="wyczyscFiltrWidokLogaEmail" onclick="WyczyscInput('filtrWidokuLogaEmail')">Wyczyść filtr</button>
                                    <label class="form-inline pam-inline" for="dataLoga" id="dataLogaLabel"> Pokaż do dnia </label>
                                    <input class="form-control pam-inline" type="date" id="dataLoga" value="" onchange="LogNaDzien()"/>
                                </form>
                                <table class="table table-striped table-hover table-sm" id="widoklogaemail">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
        <footer id="sticky-footer" class="py-4 bg-light text-grey-50">
            <div class="container text-center">
                <small>&copy; 2021 Paweł M</small>
            </div>
        </footer>
    </div>
    <a href="#" id="toTopBtn" class="cd-top text-replace js-cd-top cd-top--is-visible cd-top--fade-out"
        data-abc="true"></a>
    <script src="js/ajax/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="js/grafika.js"></script>
    <script src="js/wezly.js" type="text/javascript"></script>
    <script src="js/budynki.js" type="text/javascript"></script>
    <script src="js/lokale.js" type="text/javascript"></script>
    <script src="js/sezony.js" type="text/javascript"></script>
    <script src="js/grzejnikpozielnik.js" type="text/javascript"></script>
    <script src="js/pomieszczenie.js" type="text/javascript"></script>
    <script src="js/rozliczenie.js" type="text/javascript"></script>
    <script src="js/zaliczki.js" type="text/javascript"></script>
    <script src="js/pracownicy.js" type="text/javascript"></script>
    <script src="js/kopia.js" type="text/javascript"></script>
    <script src="js/uzytkownik.js" type="text/javascript"></script>
    <script src="js/miasta.js" type="text/javascript"></script>
    <script src="js/ulice.js" type="text/javascript"></script>
    <script src="js/grzejnik.js" type="text/javascript"></script>
    <script src="js/rodzaje.js" type="text/javascript"></script>
    <script src="js/wpomieszczeniach.js" type="text/javascript"></script>
    <script src="js/nieruchomosci.js" type="text/javascript"></script>
    <script src="js/spoldzielnia.js" type="text/javascript"></script>
    <script src="js/wyslijmaile.js" type="text/javascript"></script>
    <script src="js/program.js" type="text/javascript"></script>
</body>

</html>