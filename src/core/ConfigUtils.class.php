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
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: ConfigUtils.class.php,v 1.9 2006/11/06 07:29:25 hawkring Exp $
 */

/**
 * ����ե���������Ƥ��ݻ�����
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class ConfigUtils
{
    /**
     * @var �ƥ����������ͤ��ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_config;

    /**
     * ����������Ū����¸����
     * 
     * @var  String  $_configPool
     * @since 3.2.0
     */
    var $_configPool;

    /**
     * Action�ե��륿��̾��
     * 
     * @var  String  $_actionKey  
     * @since 3.2.0
     */
    var $_actionKey;

    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function ConfigUtils()
    {
        $this->clear();
    }

    /**
     * �������򥯥ꥢ
     *
     * @access  public
     * @since   3.0.0
     */
    function clear()
    {
        $this->_config     = array();
        $this->_configPool = array();
        $this->_actionKey  = "Action";
    }

    /**
     * �����¸���줿������������
     * ¸�ߤ��ʤ����϶���������֤�
     * 
     * @since  3.2.0
     * @param  String    $key
     * @return array
     */
    function _getPreserved($key)
    {
        return isset($this->_configPool[$key]) ? $this->_configPool[$key] : array();
    }

    /**
     * ����������Ū����¸����
     * 
     * @since  3.2.0
     * @param  String    $key
     * @param  array     $values
     */
    function _preserve($key, $values)
    {
        //To keep the order of keys, can't use array operator '+'.
        foreach($values as $k => $v) {
            $this->_configPool[$key][$k] = $v;
        }
    }

    /**
     * �����ɲä���Ƥ�����ϥޡ�����
     * �����Ǥʤ���а����¸
     * 
     * @since  3.2.0
     * @param  String    $key
     * @param  array     $values
     */
    function _mergeOrPreserve($key, $values)
    {
        if(isset($this->_config[$key])) {
            $this->_mergeOrAdd($key, $values);
        } else {
            $this->_preserve($key, $values);
        }
    }

    /**
     * �����ɲä���Ƥ�����ޡ�����
     * �����Ǥʤ���п����ɲ�
     * 
     * @since  3.2.0
     * @param  String    $key
     * @param  array     $values
     */
    function _mergeOrAdd($key, $values)
    {
        if(!isset($this->_config[$key])) {
            $this->_config[$key] = $this->_getPreserved($key);
        }
        //To keep the order of keys, can't use array operator '+'.
        foreach($values as $k => $v) {
            $this->_config[$key][$k] = $v;
        }
    }

    /**
     * ����ץ��������ɤ߹���
     * ���ץ������ɤ߹��७������ꤹ�뤳�Ȥ��Ǥ���
     * 
     * ���Υ᥽�åɤǤ�Action�ե��륿�ϰ����¸����������
     * ��Ͽ����ʤ�
     * 
     * @since  3.2.0
     * @param  array   $config
     * @param  array   $keys [optional]  keys to be read
     */
    function readSimpleConfig($config, $keys=null)
    {
        if(!is_array($keys)) {
            $keys = array_keys($config);
        }

        foreach($keys as $key) {
            if(!isset($config[$key])) {
                continue;
            }
            
            if($this->_isActionFilter($key)) {
                $this->_preserve($key, $config[$key]);
                $this->_actionKey = $key;
            } else {
                $this->_mergeOrAdd($key, $config[$key]);
            }
        }
    }

    /**
     * GlobalFilter�ν�����ޤᡢ
     * �ǲ��ؤ��ɤ������̣���ơ�������ɤ߹���
     * 
     * ���Υ᥽�åɤǤ�Action�ե��륿�ϰ����¸����������
     * ��Ͽ����ʤ�
     * 
     * @since  3.2.0
     * @param  array      $config
     * @param  boolean    $isDeepest
     */
    function readConfig($config, $isDeepest)
    {
        $globalFilter = null;
        if(isset($config['GlobalFilter'])) {
            $globalFilter = $config['GlobalFilter'];
            unset($config['GlobalFilter']);
        }

        if($globalFilter === null || $isDeepest) {
            //globalfilter��̵�����⤷���Ϻǲ���
            $this->readSimpleConfig($config);
            return;
        }

        //globalFilter����
        foreach($config as $key => $values) {
            //�����Ǥ�Action�ե��륿���ɤ�����Ĵ�٤ʤ����ɤ�
            if(!isset($globalFilter[$key])) {
                $this->_mergeOrPreserve($key, $values);
            }
        }
        $this->readSimpleConfig($config, array_keys($globalFilter));
    }

    /**
     * ����ե�������ɤ߹���
     * 
     * ���Υ᥽�åɤǤ�Action�ե��륿�ϰ����¸����������
     * ��Ͽ����ʤ�
     * 
     * @since  3.2.0
     * @param  String    $filename
     * @param  boolean    $isDeepest
     */
    function readConfigFile($filename, $isDeepest)
    {
        if(file_exists($filename) &&
           ($config = parse_ini_file($filename, true))) {
            
            if (CONFIG_CODE != INTERNAL_CODE) {
                mb_convert_variables(INTERNAL_CODE, CONFIG_CODE, $config);
            }
            $this->readConfig($config, $isDeepest);
        }
    }

    /**
     * �����������Ф������Ƥ�����ե�������ɤ߹���
     * Debug�ե��륿�Ϻǽ�ˡ�
     * Action�ե��륿�ϺǸ����Ͽ����
     * 
     * $readerFunc�ϥƥ����ӥ�ƥ��Τ����¸��
     * 
     * @since  3.2.0
     * @param  String    $actionName
     * @param  array or string     $readerFunc
     */
    function readConfigFiles($actionName, $readerFunc='readConfigFile')
    {
        $obj =& $this;
        $method = $readerFunc;
        if(is_array($readerFunc) && is_callable($readerFunc)) {
            $obj =& $readerFunc[0];
            $method =& $readerFunc[1];
        }

        $paths    = array_merge(array(""), explode('_', $actionName));
        $crrPath  = MODULE_DIR;
        $depth    = 0;
        $maxDepth = count($paths) - 1;

        foreach($paths as $p) {
            $crrPath .= "{$p}/";
            $configPath = "{$crrPath}". CONFIG_FILE;
            $obj->$method($configPath, ($maxDepth == $depth++));
        }

        $this->_mergeOrAdd($this->_actionKey, array());
    }
    
    /**
     * ����ե�������ɤ߹���
     *
     * @access  public
     * @since   3.0.0
     */
    function execute()
    {
        $container =& DIContainerFactory::getContainer();
        $actionChain =& $container->getComponent("ActionChain");
        
        $this->readConfigFiles($actionChain->getCurActionName());
    }

    /**
     * Action�ե��륿�ΰ�狼Ĵ�٤�
     * 
     * @since  3.2.0
     * @param  String    $key
     * @return boolean
     */
    function _isActionFilter($key)
    {
        return preg_match('/Action$/', $key);
    }
    
    /**
     * �������������������ֵ�
     *
     * @return  array   ����������������������
     * @access  public
     * @since   3.0.0
     */
    function &getConfig()
    {
        return $this->_config;
    }

    /**
     * ���ꤵ�줿�������������������ֵ�
     *
     * @param   string  $section    ���������̾
     * @return  array   ������������
     * @access  public
     * @since   3.0.0
     */
    function &getSectionConfig($section)
    {
        if (isset($this->_config[$section])) {
            return $this->_config[$section];
        }
    }
}
?>
