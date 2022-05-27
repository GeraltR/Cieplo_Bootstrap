<?php

    require_once '../../../vendor/autoload.php';
    require_once 'baza.php';
    require_once 'spoldzielnia.php';
    require_once 'rozliczeniewydruk.php';

    class Wysylka {
        private $SMTP = '';
        private $port = 25;
        private $username = '';
        private $pass = '';
        private $nazwawystawcy = '';
        private $tytul = '';
        private $tresc = '';

        function __construct(){
            $spo = new Spoldzielnia;
            $wystawca = $spo->WidokWystawcy();
            $this->nazwawystawcy = $wystawca[0];
            $this->SMTP = $wystawca[3];
            $this->port = $wystawca[4];
            $this->username = $wystawca[5];
            $this->pass = $wystawca[6];
            $parametry = $spo->DajParamEmail();
            $this->tytul= $parametry[0];
            $this->tresc= $parametry[1];
        }

        public function WyslijMail($idlokalu, $email, $kto, $iduzytkownika){
            //->setCc() lub setBcc() do wysłania kopii
            $result = -1;
            $rozliczenie = new RozliczenieWydruk;
            $content = $rozliczenie->ZrobPDF($idlokalu);
            $attachment = new Swift_Attachment($content, "Rozliczenie$kto.pdf", 'application/pdf');
            $transport = (new Swift_SmtpTransport($this->SMTP, $this->port, 'ssl'))
                            ->setUsername($this->username)
                            ->setPassword($this->pass)
                            ;

            $mailer = new Swift_Mailer($transport);
            $message = (new Swift_Message($this->tytul))
            ->setFrom([$this->username => $this->nazwawystawcy])
            ->setTo([$email, $email => $kto])
            ->setBody($this->tresc)
            ->attach($attachment)
            ;
            try {
                $result = $mailer->send($message);
            }
            catch (Exception $e) {
                $result = -1;
            }
            //zapisz do loga
            $db = new myBaza();
            $sql = "INSERT INTO LogEmail (ID_LOKALU, ID_UZYTKOWNIKA, Status) VALUES ($idlokalu, $iduzytkownika, $result)";
            $db->Execute($sql);
            return $result;
        }
    }

?>