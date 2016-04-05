<?php

/**
 * Class Count
 */
class Count {
    /**
     * @var int
     */
    public $Lines;
    /**
     * @var string
     */
    public $LineDelimiter = "\n";
    /**
     * @var int
     */
    public $Words;
    /**
     * @var array
     */
    public $WordsUsed = [];
    /**
     * @var array
     */
    public $WordsUsedUnique = [];
    /**
     * @var string
     */
    public $WordDelimiter = " ";
    /**
     * @var array
     */
    private $Errors = [];
    /**
     * @var string
     */
    private $Text = "";
    /**
     * Count constructor.
     * @param string|null $Text
     * @param array|null $Options
     */
    public function __construct( $Text = null, $Options = null ) {
        if ( !is_null($Options) && count($Options) ) {
            foreach( $Options as $Property => $Value ) {
                if ( property_exists( $this, $Property ) ) $this->{$Property} = $Value;
            }
        }
        if ( is_null( $Text ) ) {
            $this->Errors["no_text"] = "No \$Text argument was passed to the class constructor.";
            $this->Words = $this->Lines = 0;
        }
        if ( $Text === "" ) {
            $this->Errors["empty_text"] = "The \$Text argument passed to the class constructor was empty.";
            $this->Words = $this->Lines = 0;
        }
        $this->Text = $Text;
        $this->WordsUsed = explode( $this->WordDelimiter, str_replace( $this->LineDelimiter, $this->WordDelimiter, $this->Text ) );
        $this->Words = count( $this->WordsUsed );
        $this->Lines = count( explode( $this->LineDelimiter, str_replace( "\r\n", $this->LineDelimiter, $this->Text ) ) );
    }
    public function GetUniqueWords() {
        if ( count( $this->WordsUsedUnique ) ) return $this->WordsUsedUnique;
        foreach( $this->WordsUsed as $Word ) {
            if ( isset( $this->WordsUsedUnique[$Word] ) ) {
                $this->WordsUsedUnique[$Word]++;
            } else {
                $this->WordsUsedUnique[$Word] = 1;
            }
        }
        return $this->WordsUsedUnique;
    }
    /**
     * @return array
     */
    public function getErrors() {
        return $this->Errors;
    }
}

/**
 * Class Letter
 */
class Letter {
    /**
     * @var string
     */
    public $AllowedLetters = "a-z";
    /**
     * @var string||array
     */
    public $Common = "";
    /**
     * @var array
     */
    public $LettersUsed = [];
    /**
     * @var array
     */
    public $LettersCounted = [];
    /**
     * @var array
     */
    public $Mean = [];
    /**
     * @var array
     */
    public $Mode = [];
    /**
     * @var array
     */
    public $Median = [];
    /**
     * @var int
     */
    public $DecimalPoints = 1;
    /**
     * @var array
     */
    public $WordLetters = [];
    /**
     * @var array
     */
    private $Errors = [];

    /**
     * Letter constructor.
     * @param Count|null $Count
     * @param array|null $Options
     */
    public function __construct( Count $Count = null, $Options = null ) {
        if ( !is_null($Options) && count($Options) ) {
            foreach( $Options as $Property => $Value ) {
                if ( property_exists( $this, $Property ) ) $this->{$Property} = $Value;
            }
        }
        if ( is_null( $Count ) ) {
            $this->Errors["count_null"] = "The \$Count argument passed to the Letter constructor is null. It should be a Count object.";
            return false;
        }
        $this->SortLetters($Count->WordsUsed);
        $this->Common = $this->GetMostCommonLetter();
        $this->Mean = $this->GetMean();
        $this->Mode = $this->GetMode();
        $this->Median = $this->GetMedian();
    }
    /**
     * @return array
     */
    public function getErrors() {
        return $this->Errors;
    }
    public function SortLetters( $Words = null ) {
        if ( is_null( $Words ) ) {
            $this->Errors["count_null"] = "The \$Words argument passed to SortLetters is null. It should be an array.";
            return false;
        }
        foreach ( $Words as $Word ) {
            $Word = preg_replace("/[^". $this->AllowedLetters . "]/", "", strtolower( $Word ) );
            $Letters = str_split( $Word );
            $LetterCountWord = 0;
            foreach ( $Letters as $Letter ) {
                if ( $this->IsAllowedLetter( $Letter ) ) {
                    $LetterCountWord++;
                    if ( isset( $this->LettersUsed[$Letter] ) ) {
                        $this->LettersUsed[$Letter]++;
                    } else {
                        $this->LettersUsed[$Letter] = 1;
                    }
                }
            }
            if ( !isset( $this->WordLetters[$Word] ) ) {
                $this->WordLetters[$Word] = $LetterCountWord;
            }
        }
        rsort($this->WordLetters);
        foreach( $this->LettersUsed as $Letter => $Count ) {
            $this->LettersCounted[$Count][] = $Letter;
        }
        krsort( $this->LettersCounted );
    }
    public function GetMostCommonLetter() {
        $MostCommonLetter = reset( $this->LettersCounted );
        if ( count( $MostCommonLetter ) === 1 ) {
            $MostCommonLetter = $MostCommonLetter[0];
        }
        return $MostCommonLetter;
    }
    public function GetMean( $Values = null ) {
        if ( is_null($Values) ) $Values = $this->WordLetters;
        $Values = $this->RemoveEmptyWords( $Values );
        return number_format( array_sum($Values) / count($Values), $this->DecimalPoints );
    }
    public function GetMode( $Values = null ) {
        if ( is_null($Values) ) $Values = $this->WordLetters;
        $Values = $this->RemoveEmptyWords( $Values );
        $Values = array_count_values($Values);
        arsort($Values);
        $Counts = [];
        foreach($Values as $Key => $Value){
            $Counts[$Value][] = $Key;
        }
        krsort($Counts);
        $Modes = reset($Counts);
        foreach( $Modes as $Key => $Mode ) {
            $Modes[$Key] = number_format( $Mode, $this->DecimalPoints );
        }
        return $Modes;
    }
    public function GetMedian( $Values = null ) {
        if ( is_null($Values) ) $Values = $this->WordLetters;
        $Values = $this->RemoveEmptyWords( $Values );
        $ValueCount = count( $Values );
        if ( $ValueCount % 2 == 0 ) {
            $Median1Key = ( $ValueCount / 2 ) - 1;
            $Median2Key = $Median1Key + 1;
            $Median = $this->GetMean( [ $Values[$Median1Key], $Values[$Median2Key] ] );
        } else {
            $MedianKey = ( ( $ValueCount + 1 ) / 2 ) - 1;
            $Median = $Values[$MedianKey];
        }
        return number_format( $Median, $this->DecimalPoints );
    }
    public function RemoveEmptyWords( $Values ) {
        foreach ( $Values as $Key => $Value ) {
            if ( !$Value ) unset( $Values[$Key] );
        }
        return $Values;
    }
    public function IsAllowedLetter( $Letter ) {
        $Matches = [];
        preg_match( "/[". $this->AllowedLetters . "]/" , $Letter, $Matches );
        if ( count( $Matches ) ) return true;
        return false;
    }
}

/**
 * Class TextFileStats
 */
class TextFileStats {
    /**
     * @var array
     */
    public $AcceptedExtensions= [
        "asp",
        "css",
        "js",
        "acgi",
        "htm",
        "html",
        "htmls",
        "htx",
        "shtml",
        "js",
        "mcf",
        "pas",
        "c",
        "cc",
        "com",
        "def",
        "f",
        "for",
        "g",
        "h",
        "hh",
        "idc",
        "jav",
        "list",
        "log",
        "m",
        "mar",
        "pl",
        "c++",
        "conf",
        "cxx",
        "f90",
        "java",
        "lst",
        "sdml",
        "text",
        "txt",
        "rt",
        "rtf",
        "rtx",
        "wsc",
        "sgml",
        "sgm",
        "tsv",
        "uni",
        "unis",
        "uris",
        "uri",
        "abc",
        "flx",
        "rt",
        "wml",
        "wmls",
        "htt",
        "asm",
        "s",
        "aip",
        "cpp",
        "c",
        "cc",
        "htc",
        "f",
        "f77",
        "f90",
        "for",
        "h",
        "hh",
        "jav",
        "java",
        "lsx",
        "m",
        "p",
        "hlb",
        "csh",
        "el",
        "scm",
        "ksh",
        "lsp",
        "pl",
        "pm",
        "py",
        "rexx",
        "scm",
        "sh",
        "tcl",
        "tcsh",
        "zsh",
        "ssi",
        "shtml",
        "etx",
        "sgm",
        "sgml",
        "spc",
        "talk",
        "uil",
        "uue",
        "uu",
        "vcs",
        "xml"
    ];
    /**
     * @var array
     */
    public $AcceptedFileTypes = [
        'text/ecmascript',
        'text/html',
        'text/javascript',
        'text/mcf',
        'text/pascal',
        'text/plain',
        'text/richtext',
        'text/scriplet',
        'text/sgml',
        'text/tab-separated-values',
        'text/uri-list',
        'text/vnd.abc',
        'text/vnd.fmi.flexstor',
        'text/vnd.rn-realtext',
        'text/vnd.wap.wml',
        'text/vnd.wap.wmlscript',
        'text/webviewhtml',
        'text/x-asm',
        'text/x-audiosoft-intra',
        'text/x-c',
        'text/x-component',
        'text/x-fortran',
        'text/x-h',
        'text/x-java-source',
        'text/x-la-asf',
        'text/x-m',
        'text/x-pascal',
        'text/x-script',
        'text/x-script.csh',
        'text/x-script.elisp',
        'text/x-script.guile',
        'text/x-script.ksh',
        'text/x-script.lisp',
        'text/x-script.perl',
        'text/x-script.perl-module',
        'text/x-script.phyton',
        'text/x-script.rexx',
        'text/x-script.scheme',
        'text/x-script.sh',
        'text/x-script.tcl',
        'text/x-script.tcsh',
        'text/x-script.zsh',
        'text/x-server-parsed-html',
        'text/x-setext',
        'text/x-sgml',
        'text/x-speech',
        'text/x-uil',
        'text/x-uuencode',
        'text/x-vcalendar',
        'text/xml'
    ];
    /**
     * @var int
     */
    public $AcceptedFileSize = 2 * 1024 * 1024;
    /**
     * @var Count
     */
    public $Count;
    /**
     * @var array
     */
    public $CountOptions = [];
    /**
     * @var string
     */
    public $FileName = "";
    /**
     * @var Letter
     */
    public $Letter;
    /**
     * @var array
     */
    public $LetterOptions = [];
    /**
     * @var array
     */
    public $Path = [];
    /**
     * @var array
     */
    private $Errors = [];
    /**
     * @var string
     */
    private $JSON = "";
    /**
     * @var string
     */
    private $Text = "";

    /**
     * TextFileStats constructor.
     * @param string $FilePath
     * @param bool $Temp
     * @param string $RealFileName
     * @param array|null $Options
     */
    public function __construct( $FilePath, $Temp = false, $RealFileName = "", $Options = null ) {
        if ( !is_null($Options) && count($Options) ) {
            foreach( $Options as $Property => $Value ) {
                if ( property_exists( $this, $Property ) ) $this->{$Property} = $Value;
            }
        }
        if ( !file_exists( $FilePath ) ) {
            $this->Errors["exists"] = "The file \"$FilePath\" does not exist.";
            return $this->SetJSON();
        }
        if ( !is_readable( $FilePath) ) {
            $this->Errors["readable"] = "The file \"$FilePath\" is not readable";
            if ( chmod( $FilePath, 0644 ) ) {
                $this->Errors["readable"] .= ", but the server was able to change the file permissions to 644 in order to make the file readable.";
            } else {
                $this->Errors["readable"] .= ". The server was unable to change the file permissions to make the file readable.";
                return $this->SetJSON();
            }
        }
        $this->path = pathinfo( $FilePath );
        $this->FileName = !$Temp ? $this->path['filename'] . '.' . $this->path['extension'] : $RealFileName;
        if ( !in_array( ( !$Temp ? $this->path['extension'] : pathinfo( $RealFileName )['extension'] ), $this->AcceptedExtensions ) ) {
            $this->Errors["extension"] = "The file extension \"" . $this->path[PATHINFO_EXTENSION] . "\" is not in the list of allowed file extensions.";
            return $this->SetJSON();
        }
        if ( function_exists( 'mime_content_type' ) ) {
            if ( !in_array( mime_content_type( $FilePath ), $this->AcceptedFileTypes ) ) {
                $this->Errors["content_type"] = "The file content \"" . mime_content_type( $FilePath ) . "\" is not in the list of allowed file mime content types.";
                return $this->SetJSON();
            }
        }
        if ( filesize( $FilePath ) > $this->AcceptedFileSize ) {
            $this->Errors["file_size"] = "The maximum allowed filesize is " . number_format( $this->AcceptedFileSize / 1024 / 1024, 1 ) . "MB.";
            return $this->SetJSON();
        }
        $this->Text = strip_tags(utf8_encode(file_get_contents( $FilePath )));
        $this->Count = new Count( $this->Text, $this->CountOptions );
        $this->Errors = array_merge_recursive( $this->Errors, $this->Count->getErrors() );
        if ( !$this->Count->Words || !$this->Count->Lines ) {
            return $this->SetJSON();
        }
        $this->Letter = new Letter( $this->Count, $this->LetterOptions );
        $this->Errors = array_merge_recursive( $this->Errors, $this->Letter->getErrors() );
        if ( $this->Letter->Common == "" || !count($this->Letter->Mean) || !count($this->Letter->Mode) || !count($this->Letter->Mean) ) {
            return $this->SetJSON();
        }
        return $this->SetJSON(
            true,
            [
                "filename" => $this->FileName,
                "text" => $this->Text,
                "count" => [
                    'words' => $this->Count->Words,
                    'lines' => $this->Count->Lines
                ],
                "letter" => [
                    'mean' => $this->Letter->Mean,
                    'mode' => $this->Letter->Mode,
                    'median' => $this->Letter->Median,
                    'common' => $this->Letter->Common,
                ]
            ]
        );
    }

    /**
     * @param bool $Status
     * @param array $Data
     * @return string $Output
     */
    public function SetJSON( $Status = false, $Data = [] ) {
        $OutputArray = [
            'status' => $Status,
            'data' => array_merge_recursive( [ 'errors' => $this->Errors ], $Data )
        ];
        $this->JSON = json_encode( $OutputArray );
        return $Status;
    }

    public function GetJSON() {
        return $this->JSON;
    }

}