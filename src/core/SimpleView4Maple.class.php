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
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: SimpleView4Maple.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

require_once MAPLE_DIR .'/core/BeanUtils.class.php';
require_once MAPLE_DIR .'/core/SimpleView.class.php';

/**
 * PHP�ν񼰤�ƥ�ץ졼�ȤǤ��Τޤ����Ѥ���ʰץƥ�ץ졼�ȥ��饹
 *
 * @package     Maple
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.2.0
 */
class SimpleView4Maple extends SimpleView
{
    /**
     * @var array htmlspecialchars��Ŭ�Ѹ�Υ��������Υץ�ѥƥ�
     */
    var $_actionProps;

    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.2.0
     */
    function SimpleView4Maple()
    {
        parent::SimpleView();
    }

    /**
     * Simple4Maple���饹��ͣ��Υ��󥹥��󥹤��ֵ�
     *
     * @return  Object  Simple4Maple���饹�Υ��󥹥���
     * @access  public
     * @since   3.2.0
     */
    function &getInstance()
    {
        static $instance;
        if ($instance === null) {
            $instance = new SimpleView4Maple();
        }
        return $instance;
    }

    /**
     * ͣ��Υ��󥹥��󥹤��Ф��������Ԥ�
     * 
     * @static
     * @param  array    $opts
     * @since  3.2.0
     */
    function setOptions($opts)
    {
        $instance =& SimpleView4Maple::getInstance();

        if(isset($opts['aliasFuncName'])) {
            $instance->setAliasFuncName($opts['aliasFuncName']);
            unset($opts['aliasFuncName']);
        }

        foreach($opts as $attr => $value) {
            if(array_key_exists($attr, $instance)) {
                $instance->$attr = $value;
            }
        }
    }

    /**
     * Action�򥻥åȤ���
     *
     * @param   Object  $action Action�Υ��󥹥���
     * @access  public
     * @since   3.2.0
     */
    function setAction(&$action)
    {
        $this->_actionProps = array();
        $util =& new BeanUtils;
        $util->toArray(get_object_vars($action), $this->_actionProps, $action);
        
        $this->assign('h', $this->_actionProps);
        $this->assignByRef('action', $action);
    }
    
    /**
     * ErrorList�򥻥åȤ���
     *
     * @param   Object  $errorList  ErrorList�Υ��󥹥���
     * @access  public
     * @since   3.2.0
     */
    function setErrorList(&$errorList)
    {
        $this->assignByRef('errorList', $errorList);
    }
    
    /**
     * Token�򥻥åȤ���
     *
     * @param   Object  $token  Token�Υ��󥹥���
     * @access  public
     * @since   3.2.0
     */
    function setToken(&$token)
    {
        $this->assignByRef('token', $token);
    }
    
    /**
     * Session�򥻥åȤ���
     *
     * @param   Object  $session    Session�Υ��󥹥���
     * @access  public
     * @since   3.2.0
     */
    function setSession(&$session)
    {
        $this->assignByRef('session', $session);
    }

    /**
     * ScriptName�򥻥åȤ���
     *
     * @param   string  $scriptName ScriptName
     * @access  public
     * @since   3.2.0
     */
    function setScriptName($scriptName)
    {
        $this->assign('scriptName', $scriptName);
    }

    /**
     * htmlspecialchars�ؿ��Υ����ꥢ���ؿ�̾����ꤹ��
     * 
     * @param string $name �����ꥢ��̾
     * @access  public
     * @since   3.2.0
     */
    function setAliasFuncName($name)
    {
        static $alias;
        if ($alias !== null) {
            return;
        }
        if (function_exists($name)) {
            $_log =& LogFactory::getLog();
            $_log->error("���˴ؿ���¸�ߤ��ޤ�($name)", __CLASS__.'#'.__FUNCTION__);
            return;
        }
        
        $src = <<<SRC
            function $name(\$str, \$ref = true)
            {
                \$instance =& SimpleView4Maple::getInstance();
                return \$instance->h(\$str, \$ref);
            }
SRC;
        eval($src);
        $alias = $name;
    }

    /**
     * htmlspecialchars�ؿ��Υ����ꥢ���ؿ��μ���
     * 
     * @param string $str ���˥���������ʸ����
     * @param boolean $ref ���������Υץ�ѥƥ��򻲾Ȥ��뤫�ɤ���
     * @return string ���˥��������ʸ����
     * @access  public
     * @since   3.2.0
     */    
    function h($str, $ref = true)
    {
        $prop =& $this->_actionProps;
        if ($ref) {
            if (isset($prop[$str])) {
                return $prop[$str];
            } else {
                return htmlspecialchars($str, ENT_QUOTES);
            }
        } else {
            return htmlspecialchars($str, ENT_QUOTES);
        }
    }

}

?>
