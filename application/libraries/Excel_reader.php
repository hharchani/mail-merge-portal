<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . '/third_party/vendor/nuovo/spreadsheet-reader/php-excel-reader/excel_reader2.php';

require_once APPPATH . '/third_party/vendor/nuovo/spreadsheet-reader/SpreadsheetReader.php';

class Excel_reader {

    public function read($file) {
        $Reader = new My_excel_reader($file);
        return $Reader;
    }
}

class My_excel_reader extends SpreadsheetReader {
    private $reader;
    private $set_ref = true;
    private $fields = null;

    public function __construct($file) {
        parent::__construct($file);
        foreach($this as $row) {
            $this->fields = $row;
            break;
        }
    }

    public function get_fields() {
        return $this->fields;
    }

    public function each($cb) {
        $b = null;
        foreach($this as $row) {
            if ($this->set_ref) {
                $ref = array();
                foreach($row as $i => $name) {
                    $ref[$name] = $i;
                }
                $reflectedClass = new \ReflectionClass('Row');
                $reflectedClass->setStaticPropertyValue('r', $ref);
                $this->set_ref = false;
            }
            else {
                $b = $cb( new Row($row) );
                if ($b === false) {
                    break;
                }
            }
        }
        $this->set_ref = true;
    }
}

class Row {
    public static $r = null;
    private $d;

    public function __construct($row) {
        $this->d = $row;
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function get($name) {
        if (array_key_exists($name, self::$r)) {
            return $this->d[ self::$r[ $name ] ];
        }
        return null;
    }
}
