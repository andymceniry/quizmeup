<?php


class image extends API {


	//  when a species compat is updated, automatically update its partner
	function post( $aParams, $aaData = NULL )
	{

        //  check we have minimum amount of parameters
        if( count( $aParams ) < 1 ) {
            $this->errorMessage( 'Need a minimum of 1 parameter' );
        }

        //  check we have data
        if( !array_key_exists('photo-upload-input',$aaData) OR $aaData['photo-upload-input'] == '' ){
            $this->errorMessage( 'Missing required data1' );
        }

        if( !array_key_exists('photo-upload-album',$aaData) OR $aaData['photo-upload-album'] == '' ){
            $this->errorMessage( 'Missing required data2' );
        }

        //  get data
        $photo_account_id = $_SESSION['authi']['user_settings']['active_pet_id']['setting_int'];
        $photo_album_id = $aaData['photo-upload-album'];
        $photo_imagename = strtolower( $aaData['photo-upload-input'] );
        
        if( array_key_exists('photo-upload-caption',$aaData) AND $aaData['photo-upload-caption'] != '' ){
            $photo_caption = $aaData['photo-upload-caption'];
        } else {
            $photo_caption = '';
        }
        $photo_dt = date( 'Y-m-d H:i:s' );

        //  check if user has uploaded a photo in last 10 minutes
        $tenminsago = date( 'Y-m-d H:i:s', time()-600 );
        $sSQL = "
            SELECT *
            FROM posts
            WHERE post_account_id = $photo_account_id
            AND ( post_type = 'Album' OR post_type = 'Photo' )
            AND post_dt > '$tenminsago'
            AND post_live = 'Y'
        ";
        #echo $sSQL;
        $aaRows = $this->oDB->sel($sSQL, '');
        #echo "<br/>".str_replace ('  ', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',nl2br(var_export($aaRows, TRUE)));

        //  already uploaded a photo so turn that entry into an album post
        if( count($aaRows) > 0 ) {
            $photo_post_id = $aaRows[0]['post_id'];
            $photo_account_name = $_SESSION['authi']['user_settings']['active_pet_id']['setting_varchar'];
            $sSQL = "
                UPDATE posts
                SET post_dt = '$photo_dt', post_text = '$photo_account_name added some photos', post_type = 'Album'
                WHERE post_id = $photo_post_id
                ";
            $aaRows = $this->oDB->upd($sSQL, '');

        //  no recent photo so add this as a single photo
        } else {
            $sSQL = "
                INSERT INTO posts
                ( post_account_id, post_type, post_text, post_dt, post_live )
                VALUES
                ( $photo_account_id, 'Photo', '$photo_caption', '$photo_dt', 'Y' )
                ";
            $aaRows = $this->oDB->ins($sSQL, '');
            $photo_post_id = mysql_insert_id();
        }
        
        //  actually perform action now
        $sSQL = "
            INSERT INTO photos
            ( photo_account_id, photo_album_id, photo_post_id, photo_imagename, photo_caption, photo_dt, photo_live )
            VALUES
            ( $photo_account_id, $photo_album_id, $photo_post_id, '$photo_imagename', '$photo_caption', '$photo_dt', 'Y' )
        ";
        #echo $sSQL;
		$aaData = $this->oDB->ins($sSQL, '');

        //  get users tmp folder
        $sUserFolder = appFunctions::getUserFolder( $_SESSION['authi']['user_id'] );

        //  copy image across
        $sSrc = $sUserFolder._DS_.'_tmp'._DS_.$photo_imagename; 
        $sAlbumFolder = appFunctions::getPetFolder( $photo_account_id )._DS_.ALBUMS_FOLDER._DS_.$photo_album_id._DS_;
        $sImage = $sAlbumFolder.'xl-'.$photo_imagename; 
        rename( $sSrc, $sImage );

        //  copy image to various sizes
        $oImage = new imageManipulation();
        $oImage->resize( $sImage, -1, 50, $sAlbumFolder.'l-'.$photo_imagename );
        $oImage->resize( $sImage, -1, 25, $sAlbumFolder.'m-'.$photo_imagename );
        $oImage->resize( $sImage, -1, 10, $sAlbumFolder.'s-'.$photo_imagename );
        $oImage->resize( $sImage, -1, 5, $sAlbumFolder.'xs-'.$photo_imagename );


        echo mysql_insert_id();
	}
	
    //  user deletes an image
	function delete( $aParams, $aaData = NULL )
	{

        if( !array_key_exists('type',$aaData) OR $aaData['type'] == '' ) {
            $this->errorMessage( 'Error #'.__LINE__.': No post type specified' );
            echo '0';
        }

        switch( $aaData['type'] ) {

            case'Album':
                $this->deleteAlbum( $aParams, $aaData );
            break;

            case'Photo':
                $this->deletePhoto( $aParams, $aaData );
            break;
    
        }  //  end switch

    }


    //  user deletes a album
	function deleteAlbum( $aParams, $aaData = NULL )
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

        //  update post
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



    //  user deletes a photo
	function deletePhoto( $aParams, $aaData = NULL )
	{
        //  check we have minimum amount of parameters
        if( count( $aParams ) < 1 ) {
            $this->errorMessage( 'Need a minimum of 1 parameter' );
        }

        //  get photo id and user id
        $post_id = $aParams[0];
        $iPetID = $_SESSION['authi']['user_settings']['active_pet_id']['setting_int'];
        
        //  check user did actually post the photo
        if( appFunctions::getPetIDofPhotoPost( $post_id ) != $iPetID ) {
            $this->errorMessage( 'Error #'.__LINE__.': You did not make this post' );
            echo '0';
        }

        //  update photo
        $comment_dt = date( 'Y-m-d H:i:s' );
        $sSQL = "
            UPDATE photos
            SET photo_live = 'N', photo_removed_dt = '$comment_dt'
            WHERE photo_id = $post_id
        ";
        #echo $sSQL;
		$aaData = $this->oDB->upd($sSQL, '');

        //  update related post
        $photo_post_id = $this->oDB->getSingleRow( 'photos', 'photo_id', $post_id, 'photo_post_id' );
        $comment_dt = date( 'Y-m-d H:i:s' );
        $sSQL = "
            UPDATE posts
            SET post_live = 'N', post_removed_dt = '$comment_dt'
            WHERE post_id = $photo_post_id
        ";
        #echo $sSQL;
		$aaData = $this->oDB->upd($sSQL, '');
        echo '1';

    }

}  //  end of class
	
?>