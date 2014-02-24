<?php

#http://localhost/GitHub/quizmeup/html/
#http://local.isig.co.uk/api/quiz/questions/listupdatedsince/1389398300
#http://local.isig.co.uk/api/quiz/questions/addmultiple

class questions extends API {


    //  constructor
    function __construct() {
        //  get API constructor
        parent::__construct();
        //  db connection
        $this->oDB->db = 'isig';
        $this->oDB->host = 'localhost';
        $this->oDB->user = 'root';
        $this->oDB->pass = '';
        //  connect to DB
        $this->oDB->connect();
    }


	function add( $aParams, $aPost = NULL )
	{
        $aPost = $aPost;
        $aaData = $this->objectToArray(JSON_decode($aPost['data']));
        $iQuestionsAdded = 0;
        foreach($aaData as $aData) {
            $this->deletequestion($aData['T']);
            $iQuestionsAdded += $this->addquestion($aData['T'], $aData['Q'], $aData['A']);
        }

        $aResponse['result'] = 'success';
        $aResponse['message'] = "Added question count = $iQuestionsAdded";
        echo JSON_encode($aResponse);
    }




	function listupdatedsince( $aParams, $aaData = NULL )
	{
        //  prepare statement
        $SQL = "
            SELECT id AS T, question AS Q, answer AS A
            FROM quiz_questions 
            WHERE last_update > ?
        ";
        $this->oDB->prepare($SQL);

        //  bind parameters to statement
        $aSqlParams[] = date('Y-m-d H:i:s', $aParams[0]);
        $this->oDB->bindparams($aSqlParams);

        //  execute function
        $this->oDB->executeselect();

        //  output result as JSON
        $aResponse['result'] = 'success';
        $aResponse['data'] = $this->oDB->result;
        echo JSON_encode($aResponse);

    }


	function deletequestion( $id )
	{
        //  prepare statement
        $SQL = "
            DELETE
            FROM quiz_questions 
            WHERE id = ?
        ";
        $this->oDB->prepare($SQL);

        //  bind parameters to statement
        $aSqlParams[] = $id;
        $this->oDB->bindparams($aSqlParams);

        //  execute function
        $this->oDB->executedelete();

        //  return result
        return $this->oDB->result;

    }


	function addquestion( $id, $question, $answer )
	{
        //  prepare statement
        $SQL = "
            INSERT
            INTO quiz_questions 
            (id,question,answer,last_update)
            VALUES
            (?, ?, ?, ?)
        ";
        $this->oDB->prepare($SQL);

        //  bind parameters to statement
        $aSqlParams[] = $id;
        $aSqlParams[] = $question;
        $aSqlParams[] = $answer;
        $aSqlParams[] = $this->dt;
        #echo "<br/>".str_replace ('  ', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',nl2br(var_export($aSqlParams, TRUE)));
        $this->oDB->bindparams($aSqlParams);

        //  execute function
        $this->oDB->executeinsert();

        //  return result
        return $this->oDB->result;

    }


}  //  end of class
	
?>