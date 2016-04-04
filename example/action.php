<?php

error_reporting(E_ALL);

require_once "../text-file-stats.php";

foreach( $_FILES as $File ) {

    $TextFileStats = new TextFileStats( $File['tmp_name'], true, $File['name'] );

}

$TextFileStats = new TextFileStats( './test4.txt' );

exit;