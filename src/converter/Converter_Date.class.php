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
 * @package     Maple.converter
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Converter_Date.class.php,v 1.4 2006/02/11 19:18:22 kunit Exp $
 */

/**
 * ǯ������Υե�����ɤ�1�ĤˤޤȤ��
 *
 * @package     Maple.converter
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Converter_Date extends Converter
{
    /**
     * ǯ������Υե�����ɤ�1�ĤˤޤȤ��
     *
     * @param   string  $attributes �Ѵ�����ʸ����
     * @return  string  �Ѵ����ʸ����
     * @access  public
     * @since   3.0.0
     */
    function convert($attributes)
    {
        if (!is_array($attributes) || (count($attributes) != 3)) {
            return;
        }

        $values = array();
        foreach ($attributes as $key => $value) {
            $values[] = $value;
        }

        return sprintf("%04d%02d%02d", $values[0], $values[1], $values[2]);
    }
}
?>
