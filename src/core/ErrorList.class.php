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
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: ErrorList.class.php,v 1.6 2006/02/23 12:23:45 bobchin Exp $
 */

/**
 * �����ϥե�����ɤΥ��顼���ݻ����륯�饹
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class ErrorList
{
    /**
     * @var ���顼�μ�����ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_type;

    /**
     * @var ���顼ʸ������ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_list;

    /**
     * ���󥹥ȥ饯��
     *
     * @access  public
     * @since   3.0.0
     */
    function ErrorList()
    {
        $this->_type = NULL;
        $this->_list = array();
    }

    /**
     * ���顼ʸ������ɲ�
     *
     * @param   string  $key    ���顼��ȯ����������
     * @param   string  $str    ���顼ʸ����
     * @access  public
     * @since   3.0.0
     */
    function add($key, $value)
    {
        if (!isset($this->_list[$key])) {
            $this->_list[$key] = array();
        }
        $this->_list[$key][] = $value;
    }

    /**
     * ErrorList�򥯥ꥢ
     *
     * @access  public
     * @since   3.0.0
     */
    function clear()
    {
        $this->_list = array();
    }

    /**
     * ���ߥ��顼�����뤫�ɤ������ֵ�
     *
     * @return  boolean ���顼�����뤫�ɤ����ο�����(true/false)
     * @access  public
     * @since   3.0.0
     */
    function isExists()
    {
        return (count($this->_list) > 0);
    }

    /**
     * ���顼�μ�����ֵ�
     *
     * @return  string  ���顼�μ���
     * @access  public
     * @since   3.0.0
     */
    function getType()
    {
        return $this->_type;
    }

    /**
     * ���顼�μ���򥻥å�
     *
     * @param   string  $type   ���顼�μ���
     * @access  public
     * @since   3.0.0
     */
    function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * ���ꤵ�줿���ܤΥ��顼���ֵ�
     *
     * Smarty��°����Ϣ��������Ϥ���뤿�� $params["key"] �Ǽ������
     *
     * @param   string  $params ���顼��ȯ����������
     * @return  array   ���顼ʸ���������
     * @access  public
     * @since   3.0.0
     */
    function getMessage($params)
    {
        $key = $params["key"];

        $container =& DIContainerFactory::getContainer();
        $actionChain =& $container->getComponent("ActionChain");
        $errorList =& $actionChain->getCurErrorList();

        if (isset($errorList->_list[$key])) {
            return $this->_list[$key];
        } else {
            return array();
        }
    }

    /**
     * ��Ͽ����Ƥ��륨�顼ʸ�����������ֵ�
     *
     * @return  array   ���顼ʸ���������
     * @access  public
     * @since   3.0.0
     */
    function getMessages()
    {
        $container =& DIContainerFactory::getContainer();
        $actionChain =& $container->getComponent("ActionChain");
        $errorList =& $actionChain->getCurErrorList();

        $messages = $errorList->getSelfMessages();
        return $messages;
    }
    
    /**
     * ���Ȥ���Ͽ����Ƥ��륨�顼ʸ�����������ֵ�
     * 
     * ����Ϥ����餬"getMessages"�Ǥ��뵤�����뤬�������Ѥ��Ƥ���Τ�
     * ���̸ߴ��Τ���᥽�å�̾���Ѥ����ɲä��Ƥ���
     *
     * @return  array   ���顼ʸ���������
     * @access  public
     * @since   3.0.0
     */
    function getSelfMessages()
    {
        $messages = array();
        foreach ($this->_list as $k => $v) {
            $messages = array_merge($messages, $v);
        }
        return $messages;
    }
    
    /**
     * ��Ͽ����Ƥ��뤹�٤ƤΥ��顼ʸ�����������ֵ�
     * 
     * @return  array   ���顼ʸ���������
     * @access  public
     * @since   3.1.0
     */
    function getAllMessages()
    {
        $container =& DIContainerFactory::getContainer();
        $actionChain =& $container->getComponent("ActionChain");
        $actions = $actionChain->getAllActionName();
        
        $messages = array();
        foreach ($actions as $action) {
            $errorList = $actionChain->getErrorListByName($action);
            $mes = $errorList->getSelfMessages();
            $messages = array_merge($messages, $errorList->getSelfMessages());
        }
        return $messages;
    }
}

?>
