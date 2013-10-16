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
 * @package     Maple.validator
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Validator_Numeric.class.php,v 1.4 2006/02/11 19:18:22 kunit Exp $
 */

/**
 * �����Τߤ��ɤ���������å�
 *
 * @package     Maple.validator
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Validator_Numeric extends Validator
{
    /**
     * �����Τߤ��ɤ���������å�
     *
     * @param   mixed   $attributes �����å�������
     * @param   string  $errStr     ���顼ʸ����
     * @param   array   $params     (���Ѥ��ʤ�)
     * @return  string  ���顼ʸ����(���顼�ξ��)
     * @access  public
     * @since   3.0.0
     */
    function validate($attributes, $errStr, $params)
    {
        if (is_numeric($attributes)) {
            return;
        } else {
            return $errStr;
        }
    }
}
?>