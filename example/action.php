<?php

//error_reporting(0);

require_once "../text-file-stats.php";

foreach( $_FILES as $File ) {

    $TextFileStats = new TextFileStats( $File['tmp_name'], true, $File['name'] );

}

exit;