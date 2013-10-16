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
 * @package     Maple.core
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: ViewConvention.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */


/**
 * ViewConvention���饹
 * 
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 */
class ViewConvention
{
    /**
     * �б�����View�ե��륿̾������
     * 
     * @param  String    $type
     * @return String
     */
    function getFilterName($type)
    {
        if(strtolower($type) == 'smarty') {
            //B.C.
            return 'View';
        }
        return ucfirst($type) .'View';
    }

    /**
     * ���������̾��Ʊ�ե����ޥåȤ�ʸ���󤫤�
     * �ƥ�ץ졼�ȥե�����̾������
     * 
     * @param  String    $actionName
     * @return String
     */
    function getTemplate($actionName)
    {
        return str_replace('_', '/', $actionName) . '.html';
    }
}

?>
