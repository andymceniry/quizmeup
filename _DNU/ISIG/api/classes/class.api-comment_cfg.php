<?php


class comment_cfg extends API {


	//  when a species compat is updated, automatically update its partner
	function hide( $aParams, $aaData = NULL )
	{
        //  check we have minimum amount of parameters
        if( count( $aParams ) < 2 ) {
            $this->errorMessage( 'Need a minimum of 2 parameters' );
        }

        //  put params into variables
        list( $comment_id, $hash ) = $aParams;
    
        //  check params are correct type
        if( !preg_match( '/^[0-9]+$/', $comment_id ) ) {
            $this->errorMessage( 'Parameter 1 must be an integer' );
        }

        //  check params are correct type
        if( !preg_match( '/^[0-9a-f]{8}$/', $hash ) ) {
            $this->errorMessage( 'Parameter 2 must be hash' );
        }

        //  create correct hash
        if( $hash != substr( md5( API_SALT . $comment_id ), 0, 8 ) ) {
            $this->invalidAuth( );
        }

        //  actually perform action now
        $sSQL = "
            UPDATE comment_cfg
            SET live = 'N'
            WHERE comment_id = $comment_id
            LIMIT 1
        ";
        
		$aaData = $this->oDB->upd($sSQL, '');

	}
	
		

}  //  end of class
	
?>