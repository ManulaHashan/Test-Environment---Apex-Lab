<?php

use Dompdf\Dompdf;

 
//session_start();
if (!isset($_SESSION)) {
    session_start();
}
date_default_timezone_set('Asia/Colombo');

class DOMController extends Controller { 

    function domtest() {
        
        $dompdf = new Dompdf();
$dompdf->loadHtml('hello world'); 

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream();
        
    }

}

?>
    