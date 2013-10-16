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
 * @package     Maple.logger
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Logger_NoOp.class.php,v 1.4 2006/02/11 19:18:22 kunit Exp $
 */

/**
 * ������Ϥ��ʤ�Logger
 *
 * @package     Maple.logger
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Logger_NoOp extends Logger
{
    /**
     * ���󥹥ȥ饯����
     *
     * @access  private
     * @since   3.0.0
     */
    function Logger_NoOp()
    {
    }

    /**
     * fatal��٥�ʾ�Υ������
     *
     * @param   string  $message    ���顼��å�����
     * @access  public
     * @since   3.0.0
     */
    function fatal($message, $caller = null)
    {
    }

    /**
     * error��٥�ʾ�Υ������
     *
     * @param   string  $message    ���顼��å�����
     * @access  public
     * @since   3.0.0
     */
    function error($message, $caller = null)
    {
    }

    /**
     * warn��٥�ʾ�Υ������
     *
     * @param   string  $message    ���顼��å�����
     * @access  public
     * @since   3.0.0
     */
    function warn($message, $caller = null)
    {
    }

    /**
     * info��٥�ʾ�Υ������
     *
     * @param   string  $message    ���顼��å�����
     * @access  public
     * @since   3.0.0
     */
    function info($message, $caller = null)
    {
    }

    /**
     * debug��٥�ʾ�Υ������
     *
     * @param   string  $message    ���顼��å�����
     * @access  public
     * @since   3.0.0
     */
    function debug($message, $caller = null)
    {
    }

    /**
     * trace��٥�ʾ�Υ������
     *
     * @param   string  $message    ���顼��å�����
     * @access  public
     * @since   3.0.0
     */
    function trace($message, $caller = null)
    {
    }
}
?>
