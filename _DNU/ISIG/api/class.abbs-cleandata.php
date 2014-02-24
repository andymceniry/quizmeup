<?php
// Header {{{
/*
 * -File        $Id: $
 * -License     commercial, private
 * -Copyright   2006, Digital Marmalade Ltd. All Rights Reserved.
 * -Copyright   2003-2006, Dean Layton-James. All Rights Reserved.
 * -Path        <include dir>/ABBs
 * -Purpose    Routines for encoding / Decoding data
 */
// }}}


// {{{ Class CleanData

/*
 * Statically Called Data Cleansing Class
 *
 * @access  public
 */

 if( !defined( '_ABBS_APPLICATION_CHARSET_')) {
    define('_ABBS_APPLICATION_CHARSET_', 'UTF-8') ;
}


class ABBsCleanData
{

    // Constructor
    function ABBsCleanData()
    {
        return FALSE ;
    } // End Constructor

    // Destructor
    function _CleanData()
    {
    } // End Destructor

    function cleanInput($mData, $bConvertLineBreaks = FALSE)
    {
        return ABBsCleanData::Input($mData, $bConvertLineBreaks = FALSE) ;
    }


    // Clean data usually a data item frm _GET
    function Input($mData, $bConvertLineBreaks = FALSE)
    {
        if( !is_array($mData) ) {
            return ABBsCleanData::_cleanInValue($mData) ;
        } else {
            foreach( $mData as $key => $val) {
//                if( $key == 'pageid') {
//                    echo "<BR>\nEncoding :<BR>\n$val " ;
//                }
                if( is_array($val) ) {
                    $mData[$key] = ABBsCleanData::cleanInput($val, $bConvertLineBreaks);
                } else {
                    $mData[$key] = ABBsCleanData::_cleanInValue($val) ;
//                    if( $key == 'pageid') {
//                        echo "<BR>\nEncoded :<BR>\n ".$mData[$key] ;
//                    }
                }
            } // end foreach
            return $mData ;
        }
    } // end function clean

    // function to actually clean a value
    function _cleanInValue( $sValue )
    {
//        if (get_magic_quotes_gpc()) {
//            $sValue = stripslashes($sValue);
//        }
//        if (!get_magic_quotes_gpc()) {
//            $sValue = addslashes($sValue);
//        }

        // do own version of htmlspecialchars cos we don't want ampersands encoding
//        $sValue = htmlspecialchars($sValue, ENT_QUOTES) ;
//        str_replace("'", '&apos;', $sItem) ;
        $aTrans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES) ;
        $aTrans["'"] = '&#039;' ;
        foreach( $aTrans as $key => $val ) {
            // if( $val != '&amp;' ) {
            if( !in_array($val, array('&amp;', '&copy;', '&reg;') ) ) {
                $sValue = str_replace($key, $val, $sValue) ;
            }

        }
        $sValue = str_replace('&nbsp;', '&#160;', $sValue ) ;
        $sValue = str_replace("\\", '%5C', $sValue ) ;
        $sValue = str_replace('/', '%2f', $sValue ) ;
		$sValue = str_replace('/', '%2f', $sValue ) ;
		$sValue = str_replace('&Acirc;&pound;', '&pound;', $sValue ) ;
		$sValue = str_replace('&acirc;€œ', '"', $sValue ) ;
		$sValue = str_replace('&acirc;€˜', "'", $sValue ) ;
		$sValue = str_replace('&acirc;€™', "'", $sValue ) ;
		$sValue = str_replace('&acirc;€', '"', $sValue ) ;

        return $sValue ;
        //return htmlspecialchars($sValue, ENT_QUOTES) ;
    } // end function


    // clean up the output
    function cleanOutput($mData)
    {
        return ABBsCleanData::Output($mData) ;
    }

    function Output($mData)
    {
        $mTmp = $mData ;
        if( !is_array($mTmp) ) {
            // echo "\n<BR>String :: " ;
            return ABBsCleanData::_cleanOutValue($mTmp) ;
        } else {
            foreach( $mTmp as $key => $val) {
                if( is_array( $val ) ) {
                    $mTmp[$key] = ABBsCleanData::cleanOutput($val) ;
                } else {
//                    if( $key == 'pageid') {
//                        echo "<BR><BR>Key: $key<BR>$val " ;
//                    }
                    $mTmp[$key] = ABBsCleanData::_cleanOutValue($val) ;
//                    if( $key == 'pageid') {
//                        $mTmp['x'] = ABBsCleanData::_cleanOutValue($val) ;
//                    }
                }
            } // end foreach
            //print_r($mData) ;
            return $mTmp ;
        }
    } // end function clean

    // function top prepare a value for output
    function _cleanOutValue( $sItemIn )
    {
        $sItem = $sItemIn ;
//        if( is_array($sItem)) {
//            echo "<BR><BR>Array in Item<BR><BR>\n\n" ; print_r( $sItem ) ;
//            exit ;
//        }
        //$sItem = html_entity_decode($sItem, ENT_QUOTES) ;
        ### $sItem = html_entity_decode($sItem, ENT_QUOTES, _ABBS_APPLICATION_CHARSET_) ;
        $sItem = htmlspecialchars_decode($sItem, ENT_QUOTES) ;
        $sItem = str_replace('&apos;', "'", $sItem) ;
        $sItem = str_replace('%5C', "\\", $sItem ) ;
        $sItem = str_replace('%2f', '/', $sItem ) ;


        // $sItem = str_replace('\\', "\\", $sItem) ;
//        $aTrans = get_html_translation_table(HTML_ENTITIES) ;
//        $aTrans["'"] = '&apos;' ;
//        foreach( $aTrans as $key => $val ) {
//            if( $val != '&amp;' ) {
//                $sValue = str_replace( $val, $key, $sValue) ;
//            }
//        }
//        if(!get_magic_quotes_gpc()) {
//            $sItem = stripslashes($sItem);
//        }
        return $sItem ;
    } // end function cleanoutvalue

    function setEscapeScheme( $sScheme = 'MYSQL' )
    {
        // set the type of escape scheme
        $GLOBALS['_ABBs_CLEANDATA_ESCAPE_SCHEME_'] = $sScheme ;
    } // end function

    function getEscapeScheme()
    {
        if( !array_key_exists('_ABBs_CLEANDATA_ESCAPE_SCHEME_', $GLOBALS)) {
            ABBsCleanData::setEscapeScheme();
        }
        // set the type of escape scheme
        return $GLOBALS['_ABBs_CLEANDATA_ESCAPE_SCHEME_'] ;
    } // end function
}
// }}}

class CleanData extends ABBsCleanData
{

    // Constructor
    function CleanData()
    {
        return FALSE ;
    } // End Constructor

} // end class

// print_r( ABBsCleanData::Output('A really bad O&#039;flarherty %5Cname %% &#039;ad&#039; with `th%5C%5Cese` &quot;things&quot; and O&#039;&#039;Brien')) ;
//
//$x =Array (
//    '0' => Array
//        (
//            'pageid' => 'A really bad O&#039;flarherty %5Cname %% &#039;ad&#039; with `th%5C%5Cese` &quot;things&quot; and O&#039;&#039;Brien',
//            'layoutid' => 'Admin_Default' ,
//            'pagetitle' => '',
//            'pagetitleopt' => '',
//            'pageh1opt' => '',
//            'pageh1' => '',
//            'pagemetakeywords' => '',
//            'pagemetakeywordsopt' => '',
//            'pagemetadescription' => '',
//            'pagemetadescriptionopt' => '',
//            'pagemeta' => '',
//            'pagemetaopt' => '',
//            'pagejs' => '',
//            'pagejsopt' => '',
//            'pagecss' => '',
//            'pagecssopt' => '',
//            'pagebodyattrs' => '',
//            'pagebodyattrsopt' => '',
//            'pageisstatic' => '0',
//            'pagecontent' => 'A really bad O&#039;flarherty %5Cname %% &#039;ad&#039; with `th%5C%5Cese` &quot;things&quot; and O&#039;&#039;Brien',
//            'pagesearchtext' => '',
//            'pagelocked' => '',
//            'lastactionby' => 'abbsadmin',
//            'lastactiontimestamp' => '2008-02-13 18:29:23'
//        )) ;
//        print_r(ABBsCleanData::Output($x)) ;
//
?>