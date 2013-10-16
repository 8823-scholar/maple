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
 * Smartyクラスを拡張して使用する
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
     * コンストラクター
     *
     * Smarty4MapleクラスはSingletonとして使うので直接newしてはいけない
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
     * Smarty4Mapleクラスの唯一のインスタンスを返却
     *
     * @return  Object  Smarty4Mapleクラスのインスタンス
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
     * 唯一のインスタンスに対して設定を行う
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
     * filterを登録する
     * 
     * @access private
     * @since  3.2.0
     */
    function _registerFilters()
    {
        if (TEMPLATE_CODE != INTERNAL_CODE) {
            // プリフィルタを登録
            $this->register_prefilter('smarty4maple_prefilter');
        }
        if (OUTPUT_CODE != INTERNAL_CODE) {
            // アウトプットフィルタを登録
            $this->register_outputfilter('smarty4maple_outputfilter');
        }
    }

    /**
     * コンパイルディレクトリの中身を全て破棄する
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
     * テンプレートのキャッシュを破棄する
     *
     * @param   string  $tpl    テンプレート名
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
     * Actionをセットする
     *
     * @param   Object  $action Actionのインスタンス
     * @access  public
     * @since   3.0.0
     */
    function setAction(&$action)
    {
        $this->register_object("action", $action);

        //
        // default_modifiersでescapeがかかっているかをチェック
        //
        $needOfEscape = true;
        foreach ($this->default_modifiers as $modifier) {
            if (preg_match('|escape|', $modifier)) {
                $needOfEscape = false;
                break;
            }
        }

        //
        // プロパティーがあるものを取得
        //
        $attributes = array();
        $util =& new BeanUtils;
        $util->toArray(get_object_vars($action), $attributes, $action, $needOfEscape);

        $this->assign('action', $attributes);
    }

    /**
     * ErrorListをセットする
     *
     * @param   Object  $errorList  ErrorListのインスタンス
     * @access  public
     * @since   3.0.0
     */
    function setErrorList(&$errorList)
    {
        $this->register_object("errorList", $errorList);
    }

    /**
     * Tokenをセットする
     *
     * @param   Object  $token  Tokenのインスタンス
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
     * Sessionをセットする
     *
     * @param   Object  $session    Sessionのインスタンス
     * @access  public
     * @since   3.0.1
     */
    function setSession(&$session)
    {
        $this->register_object("session", $session);
    }

    /**
     * ScriptNameをセットする
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
 * プリフィルタ
 */
function smarty4maple_prefilter($source, &$Smarty)
{
    return mb_convert_encoding($source, INTERNAL_CODE, TEMPLATE_CODE);
}

/**
 * ポストフィルタ
 */
function smarty4maple_postfilter($source, &$Smarty)
{
    return mb_convert_encoding($source, OUTPUT_CODE, INTERNAL_CODE);
}

/**
 * アウトプットフィルタ
 */
function smarty4maple_outputfilter($source, &$Smarty)
{
    return mb_convert_encoding($source, OUTPUT_CODE, INTERNAL_CODE);
}
?>
