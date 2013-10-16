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
 * @version     CVS: $Id: ActionChain.class.php,v 1.7 2006/08/30 12:48:26 hawkring Exp $
 */

require_once MAPLE_DIR .'/core/ErrorList.class.php';

/**
 * Action��������륯�饹
 *
 * ���Υ��饹��Ȥä�Action��Forward���б�����
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class ActionChain
{
    /**
     * @var Action���ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_list;

    /**
     * @var ErrorList���ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_errorList;

    /**
     * @var Action�ΰ��֤��ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_position;

    /**
     * @var ���߼¹Ԥ���Ƥ���Action�ΰ��֤��ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_index;

    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function ActionChain()
    {
        $this->_list      = array();
        $this->_errorList = array();
        $this->_position  = array();
        $this->_index     = 0;
    }

    /**
     * Action���饹�򥻥å�
     *
     * @param   string  $name   Action�Υ��饹̾
     * @access  public
     * @since   3.0.0
     */
    function add($name)
    {
        $log =& LogFactory::getLog();

        //
        // ������ꤵ��Ƥ��ʤ��ä���ǥե���Ȥ�Action���ڤ��ؤ�
        //
        //
        if ($name == "") {
            $name = DEFAULT_ACTION;
        }

        //
        // Action�Υ��饹̾���������ä���ǥե���Ȥ�Action���ڤ��ؤ�
        //
        if (!preg_match("/^[0-9a-zA-Z_]+$/", $name)) {
            $log->info("������Action�����ꤵ��Ƥ��ޤ�(${name})", "ActionChain#add");
            $name = DEFAULT_ACTION;
        }

        //
        // �ե����뤬¸�ߤ��Ƥ��ʤ���Хǥե���Ȥ�Action���ڤ��ؤ�
        //
        list ($className, $filename) = $this->makeNames($name, true);

        if (!$className) {
            $log->info("¸�ߤ��Ƥ��ʤ�Action�����ꤵ��Ƥ��ޤ�(${name})", "ActionChain#add");
            $name = DEFAULT_ACTION;
            list ($className, $filename) = $this->makeNames($name, true);
        }

        //
        // ����Ʊ̾��Action���ɲä���Ƥ����鲿�⤷�ʤ�
        //
        if (isset($this->_list[$name]) && is_object($this->_list[$name])) {
            $log->info("����Action�ϴ�����Ͽ����Ƥ��ޤ�(${name})", "ActionChain#add");
            return true;
        }

        //
        // ���֥������Ȥ������˼��Ԥ��Ƥ����饨�顼
        //
        include_once($filename);

        $action =& new $className();

        if (!is_object($action)) {
            $log->error("Action�������˼��Ԥ��ޤ���(${name})", "ActionChain#add");
            return false;
        }

        $this->_list[$name]      =& $action;
        $this->_errorList[$name] =& new ErrorList();
        $this->_position[]       =  $name;

        return true;
    }

    /**
     * Action�Υ��饹̾����ӥե�����ѥ����ֵѤ���
     *
     * @param   string  $name   Action̾
     * @param   boolean $check  �ե������¸�ߥ����å��򤹤뤫�ɤ���
     * @return  array   Action�Υ��饹̾�ȥե�����ѥ�
     * @access  public
     * @since   3.0.0
     */
    function makeNames($name, $check = false)
    {
        $pathList   = explode("_", $name);
        $ucPathList = array_map('ucfirst', $pathList);

        $basename = ucfirst($pathList[count($pathList) - 1]);

        $actionPath = join("/", $pathList);
        $className  = join("_", $ucPathList);
        $filename   = MODULE_DIR . "/${actionPath}/${basename}.class.php";

        if (!$check) {
            return array($className, $filename);
        }

        if (!@file_exists($filename)) {
            $filename = MODULE_DIR . "/${actionPath}/${className}.class.php";
            if (!@file_exists($filename)) {
                $className = null;
                $filename  = null;
            }
        }

        return array($className, $filename);
    }

    /**
     * ActionChain�򥯥ꥢ
     *
     * @access  public
     * @since   3.0.0
     */
    function clear()
    {
        $this->_list      = array();
        $this->_errorList = array();
        $this->_position  = array();
        $this->_index     = 0;
    }

    /**
     * ActionChain��Ĺ�����ֵ�
     *
     * @return  integer ActionChain��Ĺ��
     * @access  public
     * @since   3.0.0
     */
    function getSize()
    {
        return count($this->_list);
    }

    /**
     * ActionChain�򼡤˿ʤ�뤳�Ȥ��Ǥ��뤫���ֵ�
     *
     * @return  boolean ���˿ʤ�뤫�ɤ�����
     * @access  public
     * @since   3.0.0
     */
    function hasNext()
    {
        return ($this->_index < $this->getSize());
    }

    /**
     * ActionChain�򼡤˿ʤ��
     *
     * @access  public
     * @since   3.0.0
     */
    function next()
    {
        if ($this->_index < $this->getSize()) {
            $this->_index++;
        }
    }

    /**
     * ���ߤ�Action̾���ֵ�
     *
     * @return  string  Action��̾��
     * @access  public
     * @since   3.0.0
     */
    function getCurActionName()
    {
        if (isset($this->_position[$this->_index])) {
            return $this->_position[$this->_index];
        }
    }
    
    /**
     * ��Ͽ����Ƥ��뤹�٤Ƥ�Action̾���ֵ�
     * 
     * @return array Action��̾���Υꥹ��
     * @access  public
     * @since  3.1.0
     */
    function getAllActionName()
    {
        return array_values($this->_position);
    }

    /**
     * ���ꤵ�줿̾����Action���ֵ�
     *
     * @param   string  $name   Action̾
     * @return  Object  Action�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function &getActionByName($name)
    {
        $result = false;
        $log =& LogFactory::getLog();

        if ($name == "") {
            $log->warn("�����������Ǥ�", "ActionChain#getActionByName");
            return $result;
        }

        if (!isset($this->_list[$name]) || !is_object($this->_list[$name])) {
            $log->error("���ꤵ�줿Action����Ͽ����Ƥ��ޤ���(${name})", "ActionChain#getActionByName");
            return $result;
        }

        $action =& $this->_list[$name];

        if (!is_object($action)) {
            $log->error("Action�μ����˼��Ԥ��ޤ���(${name})", "ActionChain#getCurAction");
            return $result;
        }

        return $action;
    }

    /**
     * �ꥹ�Ȥ���Ƭ��Action�Υ��󥹥��󥹤��ֵ�
     *
     * @return  Object  Action�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function &getCurAction()
    {
        $name = $this->getCurActionName();
        return $this->getActionByName($name);
    }

    /**
     * ���ꤵ�줿̾����Action���Ф���ErrorList���ֵ�
     *
     * @param   string  $name   Action̾
     * @return  Object  ErrorList�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function &getErrorListByName($name)
    {
        $result = null;
        if (isset($this->_errorList[$name])) {
            $result =& $this->_errorList[$name];
        }
        return $result;
    }

    /**
     * �ꥹ�Ȥ���Ƭ��Action���Ф���ErrorList�Υ��󥹥��󥹤��ֵ�
     *
     * @return  Object  ErrorList�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function &getCurErrorList()
    {
        $name = $this->getCurActionName();
        return $this->getErrorListByName($name);
    }

    /**
     * Action��¹�
     *
     * @return  string  �¹Ԥ���Action���ֵ���
     * @access  public
     * @since   3.0.0
     */
    function execute()
    {
        $log =& LogFactory::getLog();

        if ($this->getSize() < 1) {
            $log->error("Action���ɲä���Ƥ��ޤ���", "ActionChain#execute");
            return false;
        }

        $action =& $this->getCurAction();

        if (!is_object($action)) {
            $log->error("Action�μ����˼��Ԥ��ޤ���(${name})", "ActionChain#execute");
            return false;
        }

        return $action->execute();
    }

    /**
     * Action���ɲ�
     *
     * ���Ѥ��䤹���褦�˥��饹�᥽�åɤ����
     *
     * @param   string  $name   Action�Υ��饹̾
     * @access  public
     * @since   3.0.0
     */
    function forward($name)
    {
        $container =& DIContainerFactory::getContainer();
        $actionChain =& $container->getComponent("ActionChain");
        return $actionChain->add($name);
    }
}
?>
