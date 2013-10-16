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
 * @version     CVS: $Id: Smarty4Maple.class.php,v 1.11 2006/08/30 12:48:26 hawkring Exp $
 */

require_once "Smarty.class.php";
require_once MAPLE_DIR .'/core/BeanUtils.class.php';

/**
 * Smarty���饹���ĥ���ƻ��Ѥ���
 *
 * @package     Maple.smarty
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Smarty4Maple extends Smarty
{
    /**
     * ���󥹥ȥ饯����
     *
     * Smarty4Maple���饹��Singleton�Ȥ��ƻȤ��Τ�ľ��new���ƤϤ����ʤ�
     *
     * @access  private
     * @since   3.0.0
     */
    function Smarty4Maple()
    {
        $this->Smarty();

        $constants = array(
            'VIEW_TEMPLATE_DIR'     => 'template_dir',
            'VIEW_COMPILE_DIR'      => 'compile_dir',
            'VIEW_CONFIG_DIR'       => 'config_dir',
            'VIEW_CACHE_DIR'        => 'cache_dir',
            
            'SMARTY_TEMPLATE_DIR'   => 'template_dir',
            'SMARTY_COMPILE_DIR'    => 'compile_dir',
            'SMARTY_CONFIG_DIR'     => 'config_dir',
            'SMARTY_CACHE_DIR'      => 'cache_dir',
            
            'SMARTY_CACHING'        => 'caching',
            'SMARTY_CACHE_LIFETIME' => 'cache_lifetime',
            'SMARTY_COMPILE_CHECK'  => 'compile_check',
            'SMARTY_FORCE_COMPILE'  => 'force_compile'
            );
        
        foreach($constants as $constName => $attr) {
            if(defined($constName)) {
                $this->$attr = constant($constName);
            }
        }
        
        if(defined('SMARTY_DEFAULT_MODIFIERS')) {
            $this->default_modifiers = array(SMARTY_DEFAULT_MODIFIERS);
        }

        $this->_registerFilters();
    }

    /**
     * Smarty4Maple���饹��ͣ��Υ��󥹥��󥹤��ֵ�
     *
     * @return  Object  Smarty4Maple���饹�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function &getInstance()
    {
        static $instance;
        if ($instance === NULL) {
            $instance = new Smarty4Maple();
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
        $instance =& Smarty4Maple::getInstance();

        foreach($opts as $attr => $value) {
            if(array_key_exists($attr, $instance)) {
                $instance->$attr = $value;
            }
        }
    }

    /**
     * filter����Ͽ����
     * 
     * @access private
     * @since  3.2.0
     */
    function _registerFilters()
    {
        if (TEMPLATE_CODE != INTERNAL_CODE) {
            // �ץ�ե��륿����Ͽ
            $this->register_prefilter('smarty4maple_prefilter');
        }
        if (OUTPUT_CODE != INTERNAL_CODE) {
            // �����ȥץåȥե��륿����Ͽ
            $this->register_outputfilter('smarty4maple_outputfilter');
        }
    }

    /**
     * ����ѥ���ǥ��쥯�ȥ����Ȥ������˴�����
     *
     * @access  public
     * @since   3.0.0
     */
    function clearTemplates_c()
    {
        $result = $this->clear_compiled_tpl();

        if ($result) {
            echo "Clear";
        } else {
            echo "NG";
        }

        return true;
    }

    /**
     * �ƥ�ץ졼�ȤΥ���å�����˴�����
     *
     * @param   string  $tpl    �ƥ�ץ졼��̾
     * @access  public
     * @since   3.0.0
     */
    function clearCache($tpl = "")
    {
        $result = $this->clear_cache($tpl);

        if ($result) {
            echo "Cache Clear";
        } else {
            echo "NG";
        }

        return true;
    }

    /**
     * Action�򥻥åȤ���
     *
     * @param   Object  $action Action�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function setAction(&$action)
    {
        $this->register_object("action", $action);

        //
        // default_modifiers��escape�������äƤ��뤫������å�
        //
        $needOfEscape = true;
        foreach ($this->default_modifiers as $modifier) {
            if (preg_match('|escape|', $modifier)) {
                $needOfEscape = false;
                break;
            }
        }

        //
        // �ץ�ѥƥ����������Τ����
        //
        $attributes = array();
        $util =& new BeanUtils;
        $util->toArray(get_object_vars($action), $attributes, $action, $needOfEscape);

        $this->assign('action', $attributes);
    }

    /**
     * ErrorList�򥻥åȤ���
     *
     * @param   Object  $errorList  ErrorList�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function setErrorList(&$errorList)
    {
        $this->register_object("errorList", $errorList);
    }

    /**
     * Token�򥻥åȤ���
     *
     * @param   Object  $token  Token�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function setToken(&$token)
    {
        $this->register_object("token", $token);

        $this->assign('token', array(
            'name'  => $token->getName(),
            'value' => $token->getValue(),
        ));
    }

    /**
     * Session�򥻥åȤ���
     *
     * @param   Object  $session    Session�Υ��󥹥���
     * @access  public
     * @since   3.0.1
     */
    function setSession(&$session)
    {
        $this->register_object("session", $session);
    }

    /**
     * ScriptName�򥻥åȤ���
     *
     * @param   string  $scriptName ScriptName
     * @access  public
     * @since   3.1.0
     */
    function setScriptName($scriptName)
    {
        $scriptName = htmlspecialchars($scriptName, ENT_QUOTES);
        $this->assign('scriptName', $scriptName);
    }
}

/**
 * �ץ�ե��륿
 */
function smarty4maple_prefilter($source, &$Smarty)
{
    return mb_convert_encoding($source, INTERNAL_CODE, TEMPLATE_CODE);
}

/**
 * �ݥ��ȥե��륿
 */
function smarty4maple_postfilter($source, &$Smarty)
{
    return mb_convert_encoding($source, OUTPUT_CODE, INTERNAL_CODE);
}

/**
 * �����ȥץåȥե��륿
 */
function smarty4maple_outputfilter($source, &$Smarty)
{
    return mb_convert_encoding($source, OUTPUT_CODE, INTERNAL_CODE);
}
?>
