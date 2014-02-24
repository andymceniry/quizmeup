<?php

class account extends API {

    function __construct()
    {
        parent::__construct();
        $this->db_table = 'app_accounts';
    }


    function pets( $aParams, $aaData = NULL )
    {
        //  adding
        if( array_key_exists('pid',$aaData) AND $aaData['pid'] == '0' ) {
            $this->pets_add( $aaData );
            $_SESSION['authi']['user_pets'] = appFunctions::getUserPets( $_SESSION['authi']['user_id'], true, 'account_name ASC' );            
        }

        //  updating
        if( array_key_exists('pid',$aaData) AND array_key_exists('id',$aaData) AND $aaData['pid'] == $aaData['id'] ) {
            $this->pets_edit( $aaData );
            appFunctions::createFolders( appFunctions::getPetFolders( $aaData['pid'] ) );
        }      

    }



    function pets_edit( $aaData = NULL )
    {

        if( !array_key_exists('authi',$_SESSION) OR $_SESSION['authi'] == NULL OR !array_key_exists('user_id',$_SESSION['authi']) OR $_SESSION['authi']['user_id'] == '' ) {
            $this->errorMessage( 'Request requires authentication' );
        }
        $aaData['user_id'] = $_SESSION['authi']['user_id'];

        //  friendly name
        $aaData['nameurl'] = $aaData['name'];

        $iUID = $aaData['id'];
        $sUserFolder = appFunctions::getUserFolder( $_SESSION['authi']['user_id'] );
        $sPetFolder = appFunctions::getPetFolder( $iUID );
        $sAvatarFolder = appFunctions::getPetFolder( $aaData['pid'] )._DS_.AVATAR_FOLDER._DS_;

        $sSQL = $this->createUpdateSQLForTable( 'app_accounts', $aaData );
        $this->oLog->log( 'pets', 'Updated Pet', 'Pet ID: '.$aaData['pid'], array( 'SQL'=>$sSQL ) );

        //  new image?
        if( array_key_exists('uploadedimage',$aaData) AND $aaData['uploadedimage'] != '' ) {
            
            //  clear existing images
            if( file_exists( $sAvatarFolder._DS_.'xl-avatar.jpg' ) ) {
                unlink( $sAvatarFolder._DS_.'xl-avatar.jpg' );
            }
            if( file_exists( $sAvatarFolder._DS_.'l-avatar.jpg' ) ) {
                unlink( $sAvatarFolder._DS_.'l-avatar.jpg' );
            }
            if( file_exists( $sAvatarFolder._DS_.'m-avatar.jpg' ) ) {
                unlink( $sAvatarFolder._DS_.'m-avatar.jpg' );
            }
            if( file_exists( $sAvatarFolder._DS_.'s-avatar.jpg' ) ) {
                unlink( $sAvatarFolder._DS_.'s-avatar.jpg' );
            }
            if( file_exists( $sAvatarFolder._DS_.'xs-avatar.jpg' ) ) {
                unlink( $sAvatarFolder._DS_.'xs-avatar.jpg' );
            }

            //  copy avatar across
            $sSrc = $sUserFolder._DS_.'_tmp'._DS_.$aaData['uploadedimage'];             
            $sAvatar = $sAvatarFolder.'xl-avatar.jpg'; 
            rename( $sSrc, $sAvatar );
            $this->oLog->log( 'pets', 'Copying avatar from tmp folder', '', array( 'src'=>$sSrc, 'dst'=>$sAvatar ) );
    
            //  copy avatar to various sizes
            $oImage = new imageManipulation();
            $oImage->resize( $sAvatar, -1, 50, $sAvatarFolder.'l-avatar.jpg' );
            $oImage->resize( $sAvatar, -1, 25, $sAvatarFolder.'m-avatar.jpg' );
            $oImage->resize( $sAvatar, -1, 10, $sAvatarFolder.'s-avatar.jpg' );
            $oImage->resize( $sAvatar, -1, 5, $sAvatarFolder.'xs-avatar.jpg' );
            $this->oLog->log( 'pets', 'Copying avatar to various sizes', '', array( 'src'=>$sAvatar, 'dst'=>$sAvatarFolder.'xs-avatar.jpg' ) );

        }
        
        $aaData = $this->oDB->upd($sSQL, ''); 
        echo $aaData;
        #$this->appOnly();        
        #$this->errorMessage( 'Action not understood' );

    }  //  end edit function



    function pets_add( $aaData = NULL )
    {
        #$this->appOnly();

        if( !array_key_exists('authi',$_SESSION) OR $_SESSION['authi'] == NULL OR !array_key_exists('user_id',$_SESSION['authi']) OR $_SESSION['authi']['user_id'] == '' ) {
            $this->errorMessage( 'Request requires authentication' );
        }
        $aaData['user_id'] = $_SESSION['authi']['user_id'];

        //  friendly name
        $aaData['nameurl'] = $aaData['name'];

        //  get users tmp folder
        $sUserFolder = appFunctions::getUserFolder( $_SESSION['authi']['user_id'] );
        
        //  save pet to database and get id
        $sSQL = $this->createInsertSQLForTable( 'app_accounts', $aaData );
        $oDBres = $this->oDB->ins($sSQL, ''); 
        $iPetID = mysql_insert_id();
        $this->oLog->log( 'pets', 'Added Pet', 'Pet ID: '.$iPetID, array( 'SQL'=>$sSQL ) );
        $_SESSION['authi']['user_settings']['active_pet_id']['setting_int'] = $iPetID;
        $_SESSION['authi']['user_settings']['active_pet_id']['setting_varchar'] = $aaData['name'];

        //  create folders
        $aPetFolders = appFunctions::getPetFolders( $iPetID);
        appFunctions::createFolders( $aPetFolders );
        $this->oLog->log( 'pets', 'Creating Pet Folders', '', $aPetFolders );
        $iAlbumID = $this->pets_add_album( $iPetID );
        $sPetFolder = appFunctions::getPetFolder( $iPetID)._DS_.ALBUMS_FOLDER._DS_.$iAlbumID;
        appFunctions::createFolders( $sPetFolder );
        $this->oLog->log( 'pets', 'Creating Pet Album Folder', 'Album ID: '.$iAlbumID, $sPetFolder );

        //  copy avatar across
        $sSrc = $sUserFolder._DS_.'_tmp'._DS_.$aaData['uploadedimage']; 
        $sAvatarFolder = appFunctions::getPetFolder( $iPetID )._DS_.AVATAR_FOLDER._DS_;
        $sAvatar = $sAvatarFolder.'xl-avatar.jpg'; 
        rename( $sSrc, $sAvatar );
        $this->oLog->log( 'pets', 'Copying avatar from tmp folder', '', array( 'src'=>$sSrc, 'dst'=>$sAvatar ) );

        //  copy avatar to various sizes
        $oImage = new imageManipulation();
        $oImage->resize( $sAvatar, -1, 50, $sAvatarFolder.'l-avatar.jpg' );
        $oImage->resize( $sAvatar, -1, 25, $sAvatarFolder.'m-avatar.jpg' );
        $oImage->resize( $sAvatar, -1, 10, $sAvatarFolder.'s-avatar.jpg' );
        $oImage->resize( $sAvatar, -1, 5, $sAvatarFolder.'xs-avatar.jpg' );
        $this->oLog->log( 'pets', 'Copying avatar to various sizes', '', array( 'src'=>$sAvatar, 'dst'=>$sAvatarFolder.'xs-avatar.jpg' ) );

        echo $oDBres;       

    }  //  end add function



    function pets_add_album( $iPetID )
    {
        $album_dt = date('Y:m:d H:i:s');
        $sSQL = "
            INSERT INTO albums 
            ( album_account_id, album_name, album_description, album_cover_photo_id, album_dt, album_live) 
            VALUES
            ( $iPetID, 'My Album', '', NULL, '$album_dt', 'Y')
        ";
        $aaData = $this->oDB->ins($sSQL, ''); 
        $iAlbumID = mysql_insert_id();
        $this->oLog->log( 'pets', 'Creating pet album DB entry for pet '.$iPetID, 'album id: '.$iAlbumID, array( 'SQL'=>$sSQL ) );

        return $iAlbumID; 
    }
		

}  //  end of class
	
?>