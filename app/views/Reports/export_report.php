<?php

if (!isset($_SESSION)) {
    session_start();
}
header('Content-Type: application/vnd.ms-excel');
header('Content-disposition: attachment; filename=export.xls');
echo $_SESSION["exportdata"];
?> 