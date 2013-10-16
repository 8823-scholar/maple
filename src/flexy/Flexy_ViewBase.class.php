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
 * @package     Maple.flexy
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Flexy_ViewBase.class.php,v 1.1 2006/02/11 19:18:22 kunit Exp $
 */

require_once(dirname(__FILE__) .'/Flexy_ControllerBase.class.php');

/**
 * Mapleの"Viewクラス"として必要な機能を実装する
 * 
 * @package     Maple.flexy
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.1.0
 */
class Flexy_ViewBase extends Flexy_ControllerBase
{
    var $action;
    
    var $errorList;
    
    var $token;
    
    var $session;

    function FlexyViewBase()
    {

    }
    
    function setAction(&$action)
    {
        $this->action =& $action;
    }
    
    function setErrorList(&$errorList)
    {
        $this->errorList =& $errorList;
    }
    
    function setToken(&$token)
    {
        $this->token =& $token;
    }

    function setSession(&$session)
    {
        $this->session =& $session;
    }

    
    /**
     * 出力前に呼び出される
     * 
     * @access public
     */
    function prepare()
    {
        
    }
}

?>
