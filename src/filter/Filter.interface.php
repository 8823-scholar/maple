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
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Filter.interface.php,v 1.7 2006/08/30 12:48:26 hawkring Exp $
 */

/**
 * Filter�Υ��󥿥ե��������ꤹ�륯�饹
 *
 * @package     Maple.filter
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Filter
{
    /**
     * @var ɬ�פ˱�����°������Ĥ��Ȥ��Ǥ���
     *
     * @access  private
     * @since   3.0.0
     */
    var $_attributes;

    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function Filter()
    {
        $this->_attributes = array();
    }

    /**
     * Filter��ͭ�ν������������
     *
     * @access  public
     * @since   3.0.0
     */
    function execute()
    {
        $log =& LogFactory::getLog();
        $log->fatal("Filter��execute�ؿ�����������Ƥ��ޤ���", "Filter#execute");
        exit;
    }

    /**
     * °���ο����ֵ�
     *
     * @return  integer °���ο�
     * @access  public
     * @since   3.0.0
     */
    function getSize()
    {
        return count($this->_attributes);
    }

    /**
     * ���ꤵ�줿°�����ֵ�
     *
     * @param   string  $key    °��̾
     * @param   mixed  $default ���ꤵ�줿°�����ʤ����Υǥե������
     * @return  string  °������
     * @access  public
     * @since   3.0.0
     */
    function getAttribute($key, $default = null)
    {
        if (isset($this->_attributes[$key])) {
            return $this->_attributes[$key];
        } else {
            return $default;
        }
    }

    /**
     * ���ꤵ�줿°�����ͤ򥻥å�
     *
     * @param   string  $key    °��̾
     * @param   string  $value  °������
     * @access  public
     * @since   3.0.0
     */
    function setAttribute($key, $value)
    {
        $this->_attributes[$key] = $value;
    }

    /**
     * °����������ֵ�
     *
     * @return  array   °������(����)
     * @access  public
     * @since   3.0.0
     */
    function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * ���ꤵ�줿°�����ͤ򥻥å�(����ǤޤȤ�ƥ��å�)
     *
     * @param   array   $attributes °������(����)
     * @access  public
     * @since   3.0.0
     */
    function setAttributes($attributes)
    {
        $log =& LogFactory::getLog();

        if (!is_array($attributes) || (count($attributes) < 1)) {
            $log->warn("�����������Ǥ�", "Filter#setAttributes");
            return false;
        }

        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }
}
?>
