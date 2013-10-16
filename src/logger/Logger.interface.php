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
 * @version     CVS: $Id: Logger.interface.php,v 1.4 2006/02/11 19:18:22 kunit Exp $
 */

/**
 * �������Υ��󥿥ե��������ꤹ�륯�饹
 *
 * @package     Maple.logger
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Logger
{
    /**
     * fatal��٥�ʾ�Υ������
     *
     * @param   string  $message    ���顼��å�����
     * @access  public
     * @since   3.0.0
     */
    function fatal($message, $caller = null)
    {
        $error = 'Logger��fatal�ؿ�����������Ƥ��ޤ���';
        trigger_error($error, E_USER_ERROR);
        exit;
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
        $error = 'Logger��error�ؿ�����������Ƥ��ޤ���';
        trigger_error($error, E_USER_ERROR);
        exit;
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
        $error = 'Logger��warn�ؿ�����������Ƥ��ޤ���';
        trigger_error($error, E_USER_ERROR);
        exit;
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
        $error = 'Logger��info�ؿ�����������Ƥ��ޤ���';
        trigger_error($error, E_USER_ERROR);
        exit;
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
        $error = 'Logger��debug�ؿ�����������Ƥ��ޤ���';
        trigger_error($error, E_USER_ERROR);
        exit;
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
        $error = 'Logger��trace�ؿ�����������Ƥ��ޤ���';
        trigger_error($error, E_USER_ERROR);
        exit;
    }
}
?>
