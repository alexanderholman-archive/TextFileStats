<?php

error_reporting(E_ALL);

require_once "../text-file-stats.php";

$Options = [];

foreach( $_REQUEST as $Key => $Value ) {

    if ( property_exists( 'TextFileStats', $Key ) ) $Options[$Key] = $Value;

}

$TextFileStats = new TextFileStats( './test5.txt', false, null, $Options );

print $TextFileStats->GetJSON();

exit;