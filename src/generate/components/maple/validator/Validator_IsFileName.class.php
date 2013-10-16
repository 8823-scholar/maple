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
 * @package     Maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Validator_IsFileName.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

/**
 * Ŭ�ڤ�FileName���ɤ���Ĵ�٤�
 * ex.) foo-bar_zoo.hoge
 *
 * @package     Maple.validator
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 */
class Validator_IsFileName extends Validator
{
    /**
     * Ŭ�ڤ�FileName���ɤ���Ĵ�٤�
     *
     * @param   mixed   $attributes �����å�������
     * @param   string  $errStr     ���顼ʸ����
     * @param   array   $params     ���ץ�������
     * @return  string  ���顼ʸ����(���顼�ξ��)
     * @access  public
     */
    function validate($attributes, $errStr, $params)
    {
        if (preg_match('/^[-0-9a-zA-Z._]+$/', $attributes)) {
            return;
        } else {
            return $errStr;
        }
    }
}

?>
