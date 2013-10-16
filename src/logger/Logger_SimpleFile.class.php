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
 * @version     CVS: $Id: Logger_SimpleFile.class.php,v 1.5 2006/08/30 12:48:26 hawkring Exp $
 */

/**
 * ���Ѥ�����ե�����̾
 *
 * @type    string
 * @since    3.0.0
 **/
define("LOG_FILENAME", "/maple.log");

/**
 * �ե�����˽��Ϥ���Logger
 *
 * @package     Maple.logger
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Logger_SimpleFile extends Logger
{
    /**
     * ���󥹥ȥ饯����
     *
     * @access  private
     * @since   3.0.0
     */
    function Logger_SimpleFile()
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
        $this->output(LEVEL_FATAL, $message, $caller);
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
        $this->output(LEVEL_ERROR, $message, $caller);
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
        $this->output(LEVEL_WARN, $message, $caller);
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
        $this->output(LEVEL_INFO, $message, $caller);
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
        $this->output(LEVEL_DEBUG, $message, $caller);
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
        $this->output(LEVEL_TRACE, $message, $caller);
    }

    /**
     * ������Ϥ���ؿ�
     *
     * @param   integer $logLevel   ����٥�
     * @param   string  $message    ���顼��å�����
     * @param   mixed   $caller �ƤӽФ���
     * @access  public
     * @since   3.0.0
     */
    function output($logLevel, $message, $caller)
    {
        if (LOG_LEVEL <= $logLevel) {
            $now = date("Y/m/d H:i:s");

            $levels = array(
                LEVEL_FATAL => 'fatal',
                LEVEL_ERROR => 'error',
                LEVEL_WARN  => 'warn',
                LEVEL_INFO  => 'info',
                LEVEL_DEBUG => 'debug',
                LEVEL_TRACE => 'trace',
            );

            $message = sprintf("[%s] [%s] %s - %s\n", $now, $levels[$logLevel], $message, $caller);

            @error_log($message, 3, LOG_DIR . LOG_FILENAME);
        }
    }
}
?>
