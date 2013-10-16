<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Maple - PHP Web Application Framework
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @package     Maple.filter
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @author      KeyPoint
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Filter_FileUpload.class.php,v 1.7 2006/08/30 12:48:26 hawkring Exp $
 */

require_once MAPLE_DIR .'/core/FileUpload.class.php';

if (!defined('UPLOAD_ERR_OK')) {
    define('UPLOAD_ERR_OK',        0);
    define('UPLOAD_ERR_INI_SIZE',  1);
    define('UPLOAD_ERR_FORM_SIZE', 2);
    define('UPLOAD_ERR_PARTIAL',   3);
    define('UPLOAD_ERR_NO_FILE',   4);
}

/**
 * FileUpload処理を行うFilter
 *
 * @package     Maple.filter
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @author      KeyPoint
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.1.0
 */
class Filter_FileUpload extends Filter
{
    /**
     * コンストラクター
     *
     * @access  public
     * @since   3.1.0
     */
    function Filter_FileUpload()
    {
        parent::Filter();
    }
    
    /**
     * ファイルアップロード処理を行う
     *
     * @access  public
     * @since   3.1.0
     */
    function execute()
    {
        $log =& LogFactory::getLog();
        $log->trace("Filter_FileUploadの前処理が実行されました", "Filter_FileUpload#execute");
        
        $container =& DIContainerFactory::getContainer();
        $actionChain =& $container->getComponent("ActionChain");
        $errorList =& $actionChain->getCurErrorList();
        
        $fileUpload =& new FileUpload;
        $container->register($fileUpload, "FileUpload");
        
        $attributes = $this->getAttributes();
        
        if (isset($attributes["name"])) {
            $fileUpload->setName($attributes["name"]);
            
            if (isset($attributes["filemode"])) {
                $fileUpload->setFilemode($attributes["filemode"]);
            }
            
            //maple.iniを分析
            $maxsize_ini = array();
            $type_ini = array();
            $sizeError_ini = array();
            $typeError_ini = array();
            $noFileError_ini = array();
            foreach($attributes as $key => $value) {
                if (substr($key,0,7) == "maxsize") {
                    if (strlen($key) == 7) {
                        $maxsize_ini["default"] = $value;
                    } else if (is_numeric(substr($key,8,strlen($key)-9))) {
                        $maxsize_ini[substr($key,8,strlen($key)-9)] = $value;
                    }
                }
                
                if (substr($key,0,4) == "type") {
                    $typeArray = array();
                    if (strlen($key) == 4) {
                        $typeArray = explode(",", $value);
                        $type_ini["default"] = $typeArray;
                    } else if (is_numeric(substr($key,5,strlen($key)-6))) {
                        $typeArray = explode(",", $value);
                        $type_ini[substr($key,5,strlen($key)-6)] = $typeArray;
                    }
                }
                
                if (substr($key,0,9) == "sizeError") {
                    if (strlen($key) == 9) {
                        $sizeError_ini["default"] = $value;
                    } else if (is_numeric(substr($key,10,strlen($key)-11))) {
                        $sizeError_ini[substr($key,10,strlen($key)-11)] = $value;
                    }
                }
                
                if (substr($key,0,9) == "typeError") {
                    if (strlen($key) == 9) {
                        $typeError_ini["default"] = $value;
                    } else if (is_numeric(substr($key,10,strlen($key)-11))) {
                        $typeError_ini[substr($key,10,strlen($key)-11)] = $value;
                    }
                }
                
                if (substr($key,0,11) == "noFileError") {
                    if (strlen($key) == 11) {
                        $noFileError_ini["default"] = $value;
                    } else if (is_numeric(substr($key,12,strlen($key)-13))) {
                        $noFileError_ini[substr($key,12,strlen($key)-13)] = $value;
                    } else if (substr($key,12,7) == "whether") {
                        $noFileError_ini["whether"] = $value;
                        $noFileError_whether = 0;
                    }
                }
            }
            
            //関連配列
            $error = $fileUpload->getError();
            
            $mime_type_check = $fileUpload->checkMimeType($type_ini);
            
            $filesize_check = $fileUpload->checkFilesize($maxsize_ini);
            //以下はforeachで各ファイルでエラーチェックを行う
            foreach ($error as $key => $val) {
                if ($val != UPLOAD_ERR_OK) {// PHP自体が感知するエラーが発生した場合
                    if ($val == UPLOAD_ERR_INI_SIZE) {
                        $errorList->setType(UPLOAD_ERROR_TYPE);
                        if (isset($attributes["iniSizeError"])) {
                            $message = $attributes["iniSizeError"];
                        } else {
                            $message = "アップロードされたファイルは、php.ini の upload_max_filesize ディレクティブの値を超えています。";
                        }
                        $errorList->add($fileUpload->getName()."[".$key."]", $message);
                        break;
                    } else if ($val == UPLOAD_ERR_FORM_SIZE) {
                        $errorList->setType(UPLOAD_ERROR_TYPE);
                        if (isset($attributes["formSizeError"])) {
                            $message = $attributes["formSizeError"];
                        } else {
                            $message = "アップロードされたファイルは、HTMLフォームで指定された MAX_FILE_SIZE を超えています。";
                        }
                        $errorList->add($fileUpload->getName()."[".$key."]", $message);
                        break;
                    } else if ($val == UPLOAD_ERR_PARTIAL) {
                        $errorList->setType(UPLOAD_ERROR_TYPE);
                        if (isset($attributes["partialError"])) {
                            $message = $attributes["partialError"];
                        } else {
                            $message = "アップロードされたファイルは一部のみしかアップロードされていません。";
                        }
                        $errorList->add($fileUpload->getName()."[".$key."]", $message);
                        break;
                    } else if ($val == UPLOAD_ERR_NO_FILE) {
                        if (isset($noFileError_ini[$key])) {
                            $errorList->setType(UPLOAD_ERROR_TYPE);
                            $message = $noFileError_ini[$key];
                            $errorList->add($fileUpload->getName()."[".$key."]", $message);
                        }else if (isset($noFileError_ini["default"])) {
                            $errorList->setType(UPLOAD_ERROR_TYPE);
                            $message = $noFileError_ini["default"];
                            $errorList->add($fileUpload->getName()."[".$key."]", $message);
                            break;
                        } else if (isset($noFileError_ini["whether"])) {
                            $noFileError_whether = $noFileError_whether +1;
                        }
                    }
                }else {// PHP自体が感知するエラーは発生していない場合
                    //
                    // maple.iniで設定されたサイズを超えていた場合
                    //
                    if (count($maxsize_ini) > 0) {
                        if (!$filesize_check[$key]) {
                            $errorList->setType(UPLOAD_ERROR_TYPE);
                            if (isset($sizeError_ini[$key])) {
                                $message = $sizeError_ini[$key];
                            }else if (isset($sizeError_ini["default"])) {
                                $message = $sizeError_ini["default"];
                            } else {
                                $message = "ファイルはアップロードされませんでした。";
                            }
                            $errorList->add($fileUpload->getName()."[".$key."]", $message);
                        }
                    }
                    //
                    // maple.iniで設定されたMIME-Typeではなかった場合
                    //
                    if (count($type_ini) > 0) {
                        if (!$mime_type_check[$key]) {
                            $errorList->setType(UPLOAD_ERROR_TYPE);
                            if (isset($typeError_ini[$key])) {
                                $message = $typeError_ini[$key];
                            }else if (isset($typeError_ini["default"])) {
                                $message = $typeError_ini["default"];
                            } else {
                                $message = "指定されたファイル形式ではありません。";
                            }
                            $errorList->add($fileUpload->getName()."[".$key."]", $message);
                        }
                    }
                }
            }
            if (isset($noFileError_whether) && count($error) == $noFileError_whether) {
                $errorList->setType(UPLOAD_ERROR_TYPE);
                $message = $noFileError_ini["whether"];
                $errorList->add($fileUpload->getName(), $message);
            }
        } else {
            $log->trace("フィールド名が指定されていません", "Filter_FileUpload#execute");
        }
        
        $filterChain =& $container->getComponent("FilterChain");
        $filterChain->execute();
        
        $log->trace("Filter_FileUploadの後処理が実行されました", "Filter_FileUpload#execute");
    }
}
?>
