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
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: LogFactory.class.php,v 1.6 2006/08/30 12:48:26 hawkring Exp $
 */

require_once LOGGER_DIR . '/Logger.interface.php';

/**
 * Logger��������뤿��Υ��饹
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class LogFactory
{
    /**
     * @var Logger���ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_list;

    /**
     * ���󥹥ȥ饯����
     *
     * LogFactory���饹��Singleton�Ȥ��ƻȤ��Τ�ľ��new���ƤϤ����ʤ�
     *
     * @access  private
     * @since   3.0.0
     */
    function LogFactory()
    {
        $this->_list = array();
    }

    /**
     * Request���饹��ͣ��Υ��󥹥��󥹤��ֵ�
     *
     * @return  Object  Request���饹�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function &getInstance()
    {
        static $instance;
        if ($instance === NULL) {
            $instance = new LogFactory();
        }
        return $instance;
    }

    /**
     * Logger���ֵ�
     *
     * @param   string  $name   Logger�Υ��饹̾
     * @return  Object  Logger�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function &getLog($name = DEFAULT_LOGGER)
    {
        //
        // Logger�Υ��饹̾���������ä���ǥե���Ȥ�Logger���ڤ��ؤ�
        //
        if (!preg_match("/^[0-9a-zA-Z_]+$/", $name)) {
            $name = DEFAULT_LOGGER;
        }

        //
        // ���˥��åȤ���Ƥ���Logger���ä��餽��򤽤Τޤ��ֵ�
        //
        $logFactory =& LogFactory::getInstance();

        if (isset($logFactory->_list[$name]) &&
            is_object($logFactory->_list[$name])) {
            return $logFactory->_list[$name];
        }

        //
        // �ե����뤬¸�ߤ��Ƥ��ʤ���Х��顼��ɽ��
        //
        $className = "Logger_" . ucfirst($name);
        $filename = LOGGER_DIR . "/${className}.class.php";

        if (!(@include_once $filename) or !class_exists($className)) {
            $error = "Logger���ɤ߹��ߤ˼��Ԥ��ޤ���($className@$filename)";
            trigger_error($error, E_USER_ERROR);
            exit;
        }

        //
        // ���֥������Ȥ������˼��Ԥ��Ƥ����饨�顼
        //
        $logger =& new $className();

        if (!is_object($logger)) {
            return false;
        }

        $logFactory->_list[$name] =& $logger;

        return $logger;
    }

    /**
     * LogFactory�˥��åȤ���Ƥ���Logger��������
     *
     * @access  public
     * @since   3.0.0
     */
    function clear()
    {
        $this->_list = array();
    }

    /**
     * ���ꤵ�줿Logger��������
     *
     * @param   string  $name   Logger��̾��
     * @access  public
     * @since   3.0.0
     */
    function delete($name)
    {
        unset($this->_list[$name]);
    }
}
?>
