<?php


class status extends API {


	//  when a species compat is updated, automatically update its partner
	function post( $aParams, $aaData = NULL )
	{

        //  check we have minimum amount of parameters
        if( count( $aParams ) < 1 ) {
            $this->errorMessage( 'Need a minimum of 1 parameter' );
        }

        //  check we have data
        if( !array_key_exists('status',$_POST) OR !array_key_exists('status',$_POST) ){
            $this->errorMessage( 'Missing required data' );
        }

        //  get data
        $post_account_id = $_SESSION['authi']['user_settings']['active_pet_id']['setting_int'];
        $comment_account_name = $_SESSION['authi']['user_settings']['active_pet_id']['setting_varchar'];
        $post_type = 'Text';
        $post_text = $_POST['status'];
        $post_dt = date( 'Y-m-d H:i:s' );
        //  actually perform action now
        $sSQL = "
            INSERT INTO posts
            ( post_account_id, post_type, post_text, post_dt, post_live )
            VALUES
            ( $post_account_id, '$post_type', '$post_text', '$post_dt', 'Y' )
        ";
        #echo $sSQL;
		$aaData = $this->oDB->ins($sSQL, '');

	}
	
    //  user deletes a opst
	function delete( $aParams, $aaData = NULL )
	{
        //  check we have minimum amount of parameters
        if( count( $aParams ) < 1 ) {
            $this->errorMessage( 'Need a minimum of 1 parameter' );
        }

        //  get photo id and user id
        $post_id = $aParams[0];
        $iPetID = $_SESSION['authi']['user_settings']['active_pet_id']['setting_int'];
        
        //  check user did actually post the photo
        if( appFunctions::getPetIDofPost( $post_id ) != $iPetID ) {
            $this->errorMessage( 'Error #'.__LINE__.': You did not make this post' );
            echo '0';
        }

        //  update photo
        $comment_dt = date( 'Y-m-d H:i:s' );
        $sSQL = "
            UPDATE posts
            SET post_live = 'N', post_removed_dt = '$comment_dt'
            WHERE post_id = $post_id
        ";
        #echo $sSQL;
		$aaData = $this->oDB->upd($sSQL, '');

        echo '1';

    }
}  //  end of class
	
?>