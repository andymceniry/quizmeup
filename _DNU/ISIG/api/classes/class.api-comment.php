<?php


class comment extends API {


	//  when a species compat is updated, automatically update its partner
	function post( $aParams, $aaData = NULL )
	{

        //  check we have minimum amount of parameters
        if( count( $aParams ) < 1 ) {
            $this->errorMessage( 'Need a minimum of 1 parameter' );
        }

        //  check we have data
        if( !array_key_exists('post_id',$aaData) OR !array_key_exists('comment',$aaData) ){
            $this->errorMessage( 'Missing required data' );
        }

        //  get data
        $comment_account_id = $_SESSION['authi']['user_settings']['active_pet_id']['setting_int'];
        $comment_account_name = $_SESSION['authi']['user_settings']['active_pet_id']['setting_varchar'];
        $comment_post_id = $aaData['post_id'];
        $comment_item_type = $aaData['post_type'];
        $comment_text = $aaData['comment'];
        $comment_dt = date( 'Y-m-d H:i:s' );
        //  actually perform action now
        $sSQL = "
            INSERT INTO comments
            ( comment_account_id, comment_post_id, comment_item_type, comment_text, comment_dt, comment_live )
            VALUES
            ( $comment_account_id, $comment_post_id, '$comment_item_type', '$comment_text', '$comment_dt', 'Y' )
        ";
        #echo $sSQL;
		$aaData = $this->oDB->ins($sSQL, '');

        appFunctions::notifyOfComment( $comment_post_id, $comment_account_name );

    #echo $comment_item_type;

        $comment_width = 'normal';
        if( $comment_item_type == 'photo' ) {
            $comment_width = 'wide';
        }

        //  return comment HTML
        switch( $comment_width ) {
        
            case 'normal':
            echo '
                <div class="box W0432 MW0 H0"></div>
                <div class="box W0232 H0 C2">
                    <img src="/i/avatar/'.$comment_account_id.'"/>
                </div>
                <div class="box W2632 MW1432 H0 C2 comment">
                    <p><span class="name petname me">'.ABBsCleanData::Output($comment_account_name).':</span>'.ABBsCleanData::Output($comment_text).'</p>
                </div>
            ';
            break;

            case 'wide':
            echo '
                <div class="box W0232 H0 C2">
                    <img src="/i/avatar/'.$comment_account_id.'"/>
                </div>
                <div class="box W3032 MW1432 H0 C2 comment">
                    <p><span class="name petname me">'.ABBsCleanData::Output($comment_account_name).':</span>'.ABBsCleanData::Output($comment_text).'</p>
                </div>
            ';
            break;

        }

	}
	

    //  user deletes a comment
	function delete( $aParams, $aaData = NULL )
	{
        //  check we have minimum amount of parameters
        if( count( $aParams ) < 1 ) {
            $this->errorMessage( 'Need a minimum of 1 parameter' );
        }

        //  get comment id and user id
        $comment_id = $aParams[0];
        $iPetID = $_SESSION['authi']['user_settings']['active_pet_id']['setting_int'];
        
        //  check user did actually make the comment
        if( appFunctions::getPetIDofComment( $comment_id ) != $iPetID ) {
            $this->errorMessage( 'Error #'.__LINE__.': You did not make this comment' );
            echo '0';
        }

        //  update comment
        $comment_dt = date( 'Y-m-d H:i:s' );
        $sSQL = "
            UPDATE comments
            SET comment_live = 'N', comment_removed_dt = '$comment_dt'
            WHERE comment_id = $comment_id
        ";
        #echo $sSQL;
		$aaData = $this->oDB->upd($sSQL, '');
        echo '1';

    }

}  //  end of class
	
?>