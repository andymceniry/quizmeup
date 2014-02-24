<?php

class API {


    //  constructor
    function __construct() {
        $this->oDB = $GLOBALS['oDB'];
        $this->dt = date('Y-m-d H:i:s');
        $this->local = $_SERVER['REMOTE_ADDR'] == '127.0.0.1';
    }


    //  convert object to an array
    function objectToArray($d) {
		if (is_object($d)) {
			$d = get_object_vars($d);
		} 
		if (is_array($d)) {
            return array_map( array($this, __FUNCTION__), $d ); 
		}
		else {
			return $d;
		}
	}
	

}  //  end of class
	
?>