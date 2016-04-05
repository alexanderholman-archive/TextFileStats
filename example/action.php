<?php

error_reporting(0);

require_once "../text-file-stats.php";

$Options = [
    /*'AcceptedExtensions' => [
        "text",
        "txt"
    ],
    'AcceptedFileTypes' => [
        'text/plain'
    ]*/
];

foreach( $_REQUEST as $Key => $Value ) {

    if ( property_exists( 'TextFileStats', $Key ) ) $Options[$Key] = $Value;

}

if ( isset( $_REQUEST[ 'GetOptions' ] ) ) {

    $TextFileStats = new TextFileStats( 'test1.txt', false, null, $Options );

    if ( property_exists( $TextFileStats, $_REQUEST[ 'GetOptions' ] ) ) {

        $TextFileStats->SetJSON( true, [ $_REQUEST[ 'GetOptions' ] => $TextFileStats->{$_REQUEST[ 'GetOptions' ]} ] );

    } else {

        $TextFileStats->SetJSON( false, [ 'errors', 'The option ' . $_REQUEST[ 'GetOptions' ] . ' does not exit within TextFileStats' ] );

    }

    die( "json:" . $TextFileStats->GetJSON() );

}

if ( isset( $_FILES['fileUpload'] ) ) {

    $File = isset( $_FILES['fileUpload'][0] ) ? $_FILES['fileUpload'][0] : $_FILES['fileUpload'];

    $TextFileStats = new TextFileStats( $File['tmp_name'], true, $File['name'], $Options );

    print $TextFileStats->GetJSON();

}

exit;