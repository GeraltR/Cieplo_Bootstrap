<?php
  session_start();
  if (isset($_POST['wynikLogowania']))
    $blad_login = $_POST['wynikLogowania'];
  else $blad_login = 0;
?>
<html lang="pl">

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ciepło logowanie do programu</title>
    <meta name="author" content="Paweł Mętelski and Bootstrap contributors">
    <!-- Bootstrap -->
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="bootstrap/css/navbar.css">
  <link href="css/signin.css" rel="stylesheet">


    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>

</head>

<body class="text-center">
    <nav class="navbar fixed-top navbar-expand-lg" onmousemove="PokazPrzycisk(event)" onmouseout="Ukryj()">
        <div class="collapse navbar-collapse flex-row-reverse" >
          <ul class="nav navbar-nav navbar"  id="menuKopia">
            <li class="dropdown">
              <a href="#" class="button" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">...<span class="caret"></span></a>
              <ul class="dropdown-menu dropdown-menu-right" id="ulmenuKopia">
                <li><a href="kopia/index.php">Odtwarzanie kopii danych</a></li>
              </ul>
            </li>
          </ul>
        </div>
    </nav>
    <form class="form-signin" id="logowanie_form">
        <img class="mb-4" src="img/logo.png" alt="" width="95" height="125">
        <h1 class="h3 mb-3 font-weight-normal">Zaloguj się jako:</h1>
        <label for="inputNazwaUzytkownika" class="sr-only">Nazwa użytkownika</label>
        <input type="text" name="inputNazwaUzytkownika" id="inputNazwaUzytkownika" class="form-control" placeholder="Nazwa pracownika" required autofocus>
        <label for="inputHaslo" class="sr-only">Hasło</label>
        <input type="password" name="inputHaslo" id="inputHaslo" class="form-control" placeholder="Hasło" required>
        <div class="checkbox mb-3">
            <p class="moj-error" id="error_login" style="display:none;">Błąd logowania</p>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit" id="btnLogin">Zaloguj</button>
        <p class="mt-5 mb-3 text-muted">&copy; Paweł M 2021</p>
    </form>


    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/ajax/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>

    <!--<script src="js/script.js"></script>-->

    <script>
        $('#logowanie_form').on('submit', function(event){
            event.preventDefault();
            $.ajax({
                type:'POST',
                url:'ajax/login.php',
                dataType:'json',
                data:$(this).serialize(),
                success:function(dane){
                    if (dane.error.exists==true){
                    $('#error_login').show(0);
                    } else {
                        $('#error_login').hide(0);
                        window.location.href='program.php';
                    }
                }
            })
        })
        function PokazPrzycisk(event){
          var x = event.clientX;
          var y = event.clientY;
          if (x + (document.body.clientWidth/10) >= document.body.clientWidth)
            $('#menuKopia').show();
          else
            $('#menuKopia').hide();
        }

      function Ukryj(){
        $('#menuKopia').hide();
      }
    </script>

</body>

</html>