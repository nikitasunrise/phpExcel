<?php
/**
 * Created by PhpStorm.
 * User: Никита
 * Date: 14.05.15
 * Time: 15:36
 */

class actionWord {
    private $objWord;
    public $wordVers;

    public function __construct($docType = string, $docTemplate = '\\Document\\Template.dot') {
        switch ($docType) {
            case 'word':
                try {
                    $word = new COM("word.application") or die("Unable to instantiate Word");
                    //echo "Loaded Word, version {$word->Version}\n\n";
                    $this->wordVers = $word->Version;
                    $word->Documents->Open(RQ_DIR . $docTemplate);
                    $word->Visible = 1;

                    $this->objWord = $word;

                } catch (Exception $ex) {
                    echo $ex;
                    $word = null;
                    exit();
                }
                break;
            default:
                // undefined document
                break;
        }
    }

    public function insertSomeText($bkmName = string, $insertText = string) {
        $word = $this->objWord;
        if ($word->ActiveDocument->Bookmarks->Exists($bkmName)) {
            $word->ActiveDocument->Bookmarks[$bkmName]->Select();
            $word->Selection->InsertAfter($insertText);
            //$word->Selection->TypeParagraph();
            return 1;
        } else {
            // bookmarks does not exist
            return 0;
        }
    }

    public function insertHeaderAndText($bkmName = string, $header = string, $text = string) {
        $word = $this->objWord;
        if ($word->ActiveDocument->Bookmarks->Exists($bkmName)) {
            $word->ActiveDocument->Bookmarks[$bkmName]->Select();
            $sel = $word->Selection;
            $sel->TypeText(iconv('utf-8', 'windows-1251', $header));
            $sel->Style = $word->ActiveDocument->Styles[18];
            $sel->TypeParagraph();
            $sel->TypeText(iconv('utf-8', 'windows-1251', $text));
            $sel->Style = $word->ActiveDocument->Styles[66];
            $sel->TypeParagraph();

            return 1;
        } else {
            // bookmarks does not exist
            return 0;
        }
    }

    public function insertTable($bkmName = string, $rowCnt = int, $colCnt = int, $visMode = bool) {
        $word = $this->objWord;
        if ($word->ActiveDocument->Bookmarks->Exists($bkmName)) {
            $word->ActiveDocument->Bookmarks[$bkmName]->Select();
            $range = $word->ActiveDocument->Bookmarks[$bkmName]->Range();
            $table = $word->ActiveDocument->Tables->Add($range, $rowCnt, $colCnt);

            if ($visMode == 1) $table->Borders->Enable = true;
            if ($visMode == 0) $table->Borders->Enable = false;

            if (!empty($table) && (isset($table))) return $table;
            else return null;
        }
    }

    public function insertTableText($objTable = object, $rowNum = int, $colNum = int, $insertText = string) {
        if (!empty($objTable) && isset($objTable)) {
            $objTable->Range->Font->Name = 'Times New Roman';
            $objTable->Range->Font->Size = 11;
            $tbl->Cell($rowNum, $colNum)->Range->InsertAfter($insertText);
            return 1;
        } else return 0;
    }

    public function insertBreakRow($bkmName = string) {
        $word = $this->objWord;
        if ($word->ActiveDocument->Bookmarks->Exists($bkmName)) {
            $word->ActiveDocument->Bookmarks[$bkmName]->Select();
            $word->Selection->TypeParagraph();
            return 1;
        } else {
            // bookmarks does not exist
            return 0;
        }
    }

    public function saveDocument($fileName) {
        $word = $this->objWord;
        $word->Documents[1]->SaveAs(RQ_DIR."\\Upload\\".$fileName);
        $word->Quit();
        $word = null;
        $this->objWord = null;
    }
}