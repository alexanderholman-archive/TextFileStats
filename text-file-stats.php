<?php
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
     */
    public function __construct( $Text = null ) {
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
     */
    public function __construct( Count $Count = null ) {
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
        return number_format( array_sum($Values) / count($Values), $this->DecimalPoints );
    }
    public function GetMode( $Values = null ) {
        if ( is_null($Values) ) $Values = $this->WordLetters;
        $Values = array_count_values($Values);
        arsort($Values);
        $Counts = [];
        foreach($Values as $Key => $Value){
            $Counts[$Value][] = $Key;
        }
        krsort($Counts);
        $Mode = $this->GetMean(reset($Counts));
        return number_format( $Mode, $this->DecimalPoints );
    }
    public function GetMedian( $Values = null ) {
        if ( is_null($Values) ) $Values = $this->WordLetters;
        $ValueCount = count( $Values );
        if ( $ValueCount % 2 == 0 ) {
            $Median1Key = $ValueCount / 2;
            $Median2Key = $Median1Key;
            $Median = $this->GetMean( [ $Values[$Median1Key], $Values[$Median2Key] ] );
        } else {
            $MedianKey = ( $ValueCount + 1 ) / 2;
            $Median = $Values[$MedianKey];
        }
        return number_format( $Median, $this->DecimalPoints );
    }
    public function IsAllowedLetter( $Letter ) {
        $Matches = [];
        preg_match( "/[". $this->AllowedLetters . "]/" , $Letter, $Matches );
        if ( count( $Matches ) ) return true;
        return false;
    }
}
class TextFileStats {
    /**
     * @var array
     */
    public $AcceptedExtensions= [ "txt" ];
    /**
     * @var array
     */
    public $AcceptedFileTypes = [ "text/plain" ];
    /**
     * @var Count
     */
    public $Count;
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
    public $Path = [];
    /**
     * @var array
     */
    private $Errors = [];
    /**
     * @var string
     */
    private $Text = "";
    /**
     * TextFileStats constructor.
     * @param string $FilePath
     */
    public function __construct( $FilePath, $Temp = false, $RealFileName = "" ) {
        if ( !file_exists( $FilePath ) ) {
            $this->Errors["exists"] = "The file \"$FilePath\" does not exist.";
            $this->__return();
        }
        if ( !is_readable( $FilePath) ) {
            $this->Errors["readable"] = "The file \"$FilePath\" is not readable";
            if ( chmod( $FilePath, 0644 ) ) {
                $this->Errors["readable"] .= ", but the server was able to change the file permissions to 644 in order to make the file readable.";
            } else {
                $this->Errors["readable"] .= ". The server was unable to change the file permissions to make the file readable.";
                $this->__return();
            }
        }
        $this->path = pathinfo( $FilePath );
        $this->FileName = !$Temp ? $this->path['filename'] . '.' . $this->path['extension'] : $RealFileName;
        if ( !in_array( ( !$Temp ? $this->path['extension'] : pathinfo( $RealFileName )['extension'] ), $this->AcceptedExtensions ) ) {
            $this->Errors["extension"] = "The file extension \"" . $this->path[PATHINFO_EXTENSION] . "\" is not in the list of allowed file extensions.";
            $this->__return();
        }
        if ( !in_array( mime_content_type( $FilePath ), $this->AcceptedFileTypes ) ) {
            $this->Errors["content_type"] = "The file content \"" . mime_content_type( $FilePath ) . "\" is not in the list of allowed file mime content types.";
            $this->__return();
        }
        $this->Text = file_get_contents( $FilePath );
        $this->Count = new Count( $this->Text );
        $this->Errors = array_merge_recursive( $this->Errors, $this->Count->getErrors() );
        if ( !$this->Count->Words || !$this->Count->Lines ) {
            $this->__return();
        }
        $this->Letter = new Letter( $this->Count );
        $this->Errors = array_merge_recursive( $this->Errors, $this->Letter->getErrors() );
        if ( $this->Letter->Common == "" || !count($this->Letter->Mean) || !count($this->Letter->Mode) || !count($this->Letter->Mean) ) {
            $this->__return();
        }
        $this->__return(
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
     */
    public function __return( $Status = false, $Data = [] ) {
        $OutputArray = [
            'status' => $Status,
            'data' => array_merge_recursive( [ 'errors' => $this->Errors ], $Data )
        ];
        $Output = json_encode( $OutputArray );
        die( $Output );
    }

}