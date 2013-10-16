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
 * @version     CVS: $Id: Converter_Tofullnum.class.php,v 1.5 2006/11/29 08:31:26 hawkring Exp $
 */

/**
 * Ⱦ�ѿ��������ѿ������Ѵ����륳��С�����
 *
 * @package     Maple.converter
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Converter_Tofullnum extends Converter
{
    /**
     * Ⱦ�ѿ��������ѿ����ˤ���
     *
     * @param   string  $attributes �Ѵ�����ʸ����
     * @return  string  �Ѵ����ʸ����
     * @access  public
     * @since   3.0.0
     */
    function convert($attributes)
    {
        if (is_array($attributes)) {
            foreach ($attributes as $key => $value) {
                $attributes[$key] = mb_convert_kana($value, "N", INTERNAL_CODE);
            }
        } else {
            $attributes = mb_convert_kana($attributes, "N", INTERNAL_CODE);
        }
        return $attributes;
    }
}
?>
