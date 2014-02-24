<?php


class DB {


    //  constructor
    function __construct() {
        $this->db = false;
        $this->host = false;
        $this->user = false;
        $this->pass = false;
        $this->connected = false;
    }


    function checkConnectionParams() {
        if ($this->db === false) {
            return false;
        }
        if ($this->host === false) {
            return false;
        }
        if ($this->user === false) {
            return false;
        }
        if ($this->pass === false) {
            return false;
        }
        return true;
    }

    
    function checkConnection() {
        if ($this->connected === false) {
            return false;
        }
        return true;
    }


    function connect() {
        if ($this->checkConnection()) {
            return false;
        }
        if (!$this->checkConnectionParams()) {
            die("<br/>".date("H:i:s").' => "'.__FILE__.'": Line '.__LINE__);
        }
        $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->db);
        if ($this->connection->connect_errno) {
            die( "Failed to connect to MySQL: (" . $this->connection->connect_errno . ") " . $this->connection->connect_error );
        } else {
            $this->connected = true;
        }
    }


    function prepare($SQL) {
        if (!$this->checkConnection()) {
            die("<br/>".date("H:i:s").' => "'.__FILE__.'": Line '.__LINE__);
        }
        if (!($this->statement = $this->connection->prepare($SQL))) {
            die( "Prepare failed: (" . $this->connection->errno . ") " . $this->connection->error );
        }
    }


    function bindparams( $aSqlParams ) {
        if (!$this->checkConnection()) {
            die("<br/>".date("H:i:s").' => "'.__FILE__.'": Line '.__LINE__);
        }
        $sBindTypes = '';
        foreach($aSqlParams as $param) {
            $sParamType = 's';
            if (is_integer($param)) {
                $sParamType = 'i';
            }
            $sBindTypes .= $sParamType;
        }

        $aBindParams[] = & $sBindTypes;         
        for($i = 0; $i < count($aSqlParams); $i++) {
          $aBindParams[] = & $aSqlParams[$i];
        }

        call_user_func_array(array($this->statement, 'bind_param'), $aBindParams);

    }


    function executeselect() {

        if (!$this->checkConnection()) {
            die("<br/>".date("H:i:s").' => "'.__FILE__.'": Line '.__LINE__);
        }

        $this->statement->execute();

        $meta = $this->statement->result_metadata();
        while ($field = $meta->fetch_field()) {
            $parameters[] = &$row[$field->name];
        }
        call_user_func_array(array($this->statement, 'bind_result'), $parameters);

        //  place result in array
        $results = array();
        while ($this->statement->fetch()) {
            foreach($row as $key => $val) {
                $x[$key] = $val;
            }
            $results[] = $x;
        }

        $this->result = $results;

    }


    function executedelete() {

        if (!$this->checkConnection()) {
            die("<br/>".date("H:i:s").' => "'.__FILE__.'": Line '.__LINE__);
        }

        $this->statement->execute();

        $this->result = mysqli_stmt_affected_rows($this->statement);

    }


    function executeinsert() {

        if (!$this->checkConnection()) {
            die("<br/>".date("H:i:s").' => "'.__FILE__.'": Line '.__LINE__);
        }

        $this->statement->execute();

        $this->result = mysqli_stmt_affected_rows($this->statement);

    }

}  //  end of class
	
?>