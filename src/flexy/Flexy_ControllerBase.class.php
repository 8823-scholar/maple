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
 * @version     CVS: $Id: Flexy_ControllerBase.class.php,v 1.1 2006/02/11 19:18:22 kunit Exp $
 */

/**
 * HTML_Template_Flexy のコントローラ・オブジェクト
 * としての基本的なHelper-Methodを提供するためのクラス
 * 
 * @package     Maple.flexy
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.1.0
 */
class Flexy_ControllerBase
{
    /**
     * Flexyのテンプレートでは条件式などが表現できないので
     * 
     * {if:op(value1, #==#, value2)} ok {end:}
     * 
     * のように用いる
     * 
     * @param mixed
     * @param string Operator
     * @param mixed
     * @return mixed
     */
    function op($a, $op, $b)
    {
        $returnValue = null;
        switch($op)
        {
            case '==':
            case 'eq':
                $returnValue = ($a == $b);
                break;

            case '!=':
            case 'ne':
                $returnValue = ($a != $b);
                break;

            case '>':
            case 'gt':
                $returnValue = ($a > $b);
                break;

            case '<':
            case 'lt':
                $returnValue = ($a < $b);
                break;
                
            case '+':
                $returnValue = ($a + $b);
                break;
            
            case '-':
                $returnValue = ($a - $b);
                break;

            case '/':
                $returnValue = ($a / $b);
                break;

            case '*':
                $returnValue = ($a * $b);
                break;
            
            case '%':
                $returnValue = ($a % $b);
                break;
        }
        return $returnValue;
    }

}

?>
