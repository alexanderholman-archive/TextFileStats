<?php

error_reporting(0);

require_once "../text-file-stats.php";

$Options = [];

foreach( $_REQUEST as $Key => $Value ) {

    if ( property_exists( 'TextFileStats', $Key ) ) $Options[$Key] = $Value;

}

if ( isset( $_FILES[0] ) ) {

    $File = $_FILES[0];

    $TextFileStats = new TextFileStats( $File['tmp_name'], true, $File['name'], $Options );

    print $TextFileStats->GetJSON();

}

exit;