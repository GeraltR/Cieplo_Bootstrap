<?php

$plik = $_GET['dbf'];
if (isset($plik)){
    if (strpos($plik, '.dbf') > 0){
        $plik = "../dbf/$plik";
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: 0");
        header('Content-Disposition: attachment; filename="'.basename($plik).'"');
        header('Content-Length: ' . filesize($plik));
        header('Pragma: public');
        flush();
        readfile($plik);
        unlink($plik);
    }
}
exit(0);

?>