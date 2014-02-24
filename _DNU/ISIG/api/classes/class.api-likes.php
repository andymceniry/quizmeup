<?php


class likes extends API {


	//  when a species compat is updated, automatically update its partner
	function like( $aParams, $aaData = NULL )
	{
        //  check we have minimum amount of parameters
        if( count( $aParams ) < 1 ) {
            $this->errorMessage( 'Need a minimum of 1 parameter' );
        }

        //  check we have data
        if( !array_key_exists('id',$_POST) OR intval($_POST['id']) == '' ){
            $this->errorMessage( 'Missing required data' );
        }
        if( !array_key_exists('type',$_POST) OR $_POST['type'] == '' ){
            $this->errorMessage( 'Missing required data' );
        }

        //  get variables ready
        $post_type = $_POST['type'];
        $post_id = $_POST['id'];
        $like_account_user_id = $_SESSION['authi']['user_settings']['active_pet_id']['setting_int'];
        $like_dt = date( 'Y-m-d H:i:s' );

        //  update relevant table
        switch( strtolower($post_type) ) {
            
            case 'text':
            case 'album':
            $sSQL = "
                UPDATE posts
                SET post_likes = post_likes + 1
                WHERE post_type = '$post_type'
                AND post_id = $post_id
                LIMIT 1
            ";
            break;

            case 'photo':
            $sSQL = "
                UPDATE photos
                SET photo_likes = photo_likes + 1
                WHERE photo_id = $post_id
                LIMIT 1
            ";
            break;

        }  //  end switch
               
        #echo $sSQL;
		$aaData = $this->oDB->upd($sSQL, '');


        //  update like table
        $sSQL = "
            INSERT INTO likes
            ( like_account_user_id, like_type, like_item_id, like_dt )
            VALUES
            ( $like_account_user_id, '$post_type', $post_id, '$like_dt' )
        ";
        #echo $sSQL;
		$aaData = $this->oDB->ins($sSQL, '');

        //  return like count
        $iLikeCount = appFunctions::getLikeCount( $post_type, $post_id );
        if( $iLikeCount == 1 ) {
            $aaResponse['mobile'] = "$iLikeCount pet likes this";
            $aaResponse['non-mobile'] = "$iLikeCount pet likes this";
        } else {
            $aaResponse['mobile'] = "$iLikeCount pets like this";
            $aaResponse['non-mobile'] = "$iLikeCount pets like this";
        }
        echo json_encode( $aaResponse );

	}


    function unlike( $aParams, $aaData = NULL )
	{
        //  check we have minimum amount of parameters
        if( count( $aParams ) < 1 ) {
            $this->errorMessage( 'Need a minimum of 1 parameter' );
        }

        //  check we have data
        if( !array_key_exists('id',$_POST) OR intval($_POST['id']) == '' ){
            $this->errorMessage( 'Missing required data' );
        }
        if( !array_key_exists('type',$_POST) OR $_POST['type'] == '' ){
            $this->errorMessage( 'Missing required data' );
        }

        //  get variables ready
        $post_type = $_POST['type'];
        $post_id = $_POST['id'];
        $like_account_user_id = $_SESSION['authi']['user_settings']['active_pet_id']['setting_int'];
        $like_dt = date( 'Y-m-d H:i:s' );

        //  update relevant table
        switch( strtolower($post_type) ) {
            
            case 'text':
            case 'album':
            $sSQL = "
                UPDATE posts
                SET post_likes = post_likes - 1
                WHERE post_type = '$post_type'
                AND post_id = $post_id
                LIMIT 1
            ";
            break;

            case 'photo':
            $sSQL = "
                UPDATE photos
                SET photo_likes = photo_likes - 1
                WHERE photo_id = $post_id
                LIMIT 1
            ";
            break;

        }  //  end switch

        #echo $sSQL;
		$aaData = $this->oDB->upd($sSQL, '');


        //  update like table
        $sSQL = "
            DELETE FROM likes
            WHERE like_type = '$post_type'
            AND like_item_id = $post_id
            AND like_account_user_id = $like_account_user_id
            LIMIT 1
        ";
        #echo $sSQL;
		$aaData = $this->oDB->del($sSQL, '');

        //  return like count
        $iLikeCount = appFunctions::getLikeCount( $post_type, $post_id );
        if( $iLikeCount == 1 ) {
            $aaResponse['mobile'] = "$iLikeCount pet likes this";
            $aaResponse['non-mobile'] = "$iLikeCount pet likes this";
        } else {
            $aaResponse['mobile'] = "$iLikeCount pets like this";
            $aaResponse['non-mobile'] = "$iLikeCount pets like this";
        }
        echo json_encode( $aaResponse );

	}
	
		

}  //  end of class
	
?>