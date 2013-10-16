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
 * @package     Maple.core
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: GlobalConfig.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

/**
 * GlobalConfig
 * 
 * �ץ�������Τ��������򰷤�����Υ��饹
 * PHP�������Ʃ��Ū�˰������Ȥ������
 * 
 * preferConstant�ˤ�ä�PHP�����ͥ���̤��Ѥ��
 * true�ˤ����
 *   PHP��� > setValue�����ꤵ�줿�� > importSections�ǤޤȤ���ɤ߹������
 * false�ˤ����
 *   setValue�����ꤵ�줿�� > importSections�ǤޤȤ���ɤ߹������ > PHP���
 * 
 * @package     Maple.core
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.2.0
 */
class GlobalConfig
{
    /**
     * @var  array  importSections���ɤ߹��ޤ줿�����ͤ���¸�����
     */
    var $_values = array();

    /**
     * @var  String  setValue�����ꤵ�줿�ͤ���¸�����
     */
    var $_overwriteValues = array();
    
    /**
     * @var  String  importSections���ɤ߹�����ͤ򡢥��������ñ�̤ǤޤȤ�뤿�������
     */
    var $_sections = array();

    /**
     * @var  String  ����Ȥ��ƥ������ݡ��Ȳ�ǽ��̾����ɽ������ɽ��
     */
    var $_exportableConstPattern = '/^[A-Z_][A-Z0-9_]*$/';

    /**
     * @var  String  �����¾���ͤ��Ф���ͥ�褹�뤫�ݤ�
     */
    var $_preferConstant = false;

    /**
     * ���󥹥ȥ饯��
     * 
     * @access public
     * @param bool $preferConstant  [optional] if true, constants have priority over
     *                                         any other values.
     */
    function GlobalConfig($preferConstant=false)
    {
        $this->_preferConstant = $preferConstant;
    }

    /**
     * �����ͥ�褹�뤫
     * 
     * @access public
     * @param  boolean    $bool
     */
    function setPreferConstant($bool)
    {
        $this->_preferConstant = $bool;
    }

    /**
     * �ͤ�setValue�᥽�åɤˤ�äƾ�񤭤���Ƥ��뤫
     * �ɤ�����Ĵ�٤�
     * 
     * @access private
     * @param  String    $key
     * @return boolean
     * @see    setValue()
     */
    function _isOverwritten($key)
    {
        return array_key_exists($key, $this->_overwriteValues);
    }

    /**
     * ��������Ѳ�ǽ���ɤ�����Ĵ�٤�
     * 
     * @access private
     * @param  String    $key
     * @return boolean
     */
    function _canUseConstant($key)
    {
        return defined($key);
    }

    /**
     * ������礷�Ƽ�����
     * ��ưŪ��updateValues��ƤӽФ����ݻ�����Ƥ����ͤ򹹿�����
     * 
     * @access public
     * @param  array    $arr
     */
    function importSections($arr)
    {
        $this->_sections = $arr + $this->_sections;
        $this->updateValues();
    }


    /**
     * �ݻ�����Ƥ����ͤ򹹿�����
     * 
     * @access public
     */
    function updateValues()
    {
        foreach($this->_sections as $sec => $arr) {
            $prefix = $this->getValue($sec, "");
            
            foreach($arr as $k => $v) {
                $this->_values[$k] = $prefix . $v;
            }
        }
    }

    /**
     * �����ͤ�����
     * ����Ȥ����������Ƥ��뤫��
     * �����ͥ�褹�뤫��
     * ��񤭤���Ƥ��뤫��
     * �������Ǥ��θ���������ͤϷ�ޤ�
     * 
     * @access public
     * @param  String    $key
     * @param  mixed    $default
     * @return mixed
     */
    function getValue($key, $default=null)
    {
        if($this->_preferConstant && $this->_canUseConstant($key)) {
            return constant($key);
            
        } elseif($this->_isOverwritten($key)) {
            return $this->_overwriteValues[$key];
            
        } elseif(isset($this->_values[$key])) {
            return $this->_values[$key];
            
        } elseif(!$this->_preferConstant && $this->_canUseConstant($key)) {
            return constant($key);
            
        }
        return $default;
    }

    /**
     * �ͤ����ꤵ��Ƥ��뤫�ɤ�����Ĵ�٤�
     * 
     * @access public
     * @param  String    $key
     * @param  boolean    $checkConst [optional] ���������å����뤫
     * @return boolean
     */
    function hasValue($key, $checkConst=false)
    {
        return ($this->_isOverwritten($key) ||
           isset($this->_values[$key]) ||
           ($checkConst && $this->_canUseConstant($key)));
    }

    /**
     * �ͤ����ꤹ��
     * ���Υ᥽�åɤ����ꤷ���ͤ�
     * $_overwriteValues�Ȥ����ȼ��˴��������
     * 
     * @access public
     * @param  mixed    $key
     * @param  mixed    $value
     * @param  boolean  $autoUpdate [optional]
     *                  if ture, call updateValues() after overwriting
     */
    function setValue($key, $value, $autoUpdate=false)
    {
        $this->_overwriteValues[$key] = $value;
        if($autoUpdate) {
            $this->updateValues();
        }
    }

    /**
     * ���������ñ�̤������ͤ�����η�������
     * 
     * @access public
     * @param  String    $sec
     * @return array
     */
    function getSection($sec)
    {
        if(!isset($this->_sections[$sec])) {
            return null;
        }
        
        $result = array();
        foreach($this->_sections[$sec] as $k => $v) {
            if($this->_isOverwritten($k) ||
               ($this->_preferConstant && $this->_canUseConstant($k))) {
                //��񤭤���Ƥ��뤫��
                //����Ȥ����ȼ����������Ƥ�����ϡ�
                //�����������˴ޤ�ʤ�
                continue;
            } else {
                $result[$k] = $this->getValue($k);
            }
        }
        return $result;
    }

    /**
     * INI�ե����뤫��������ɤ߹���
     * 
     * @access public
     * @param  String    $filename
     * @return boolean
     */
    function loadFromFile($filename)
    {
        if(!($arr = @parse_ini_file($filename, true))) {
            return false;
        }
        $this->importSections($arr);
        return true;
    }

    /**
     * �����ͤ�����Ȥ��ƥ������ݡ��Ȥ���
     * 
     * @access public
     * @return String
     */
    function exportConstants()
    {
        $allValues = $this->_overwriteValues + $this->_values;
        foreach($allValues as $k => $v) {
            if(!defined($k) && $this->isConstName($k)) {
                define($k, $v);
            }
        }
    }

    /**
     * ����Ȥ��ƥ������ݡ��Ȳ�ǽ��̾�Τ�Ĵ�٤�
     * 
     * @access public
     * @param  String    $key
     * @return String
     */
    function isConstName($key)
    {
        return preg_match($this->_exportableConstPattern, $key);
    }


    /**
     * �ե����뤫��������ɤ߹��ߡ�
     * ����Ȥ��ƤΥ������ݡ��ȤޤǤ�Ԥ�static�᥽�å�
     * 
     * @static
     * @access public
     * @return boolean
     */
    function loadConstantsFromFile($filename)
    {
        $config =& new GlobalConfig(true);
        if(!$config->loadFromFile($filename)) {
            return false;
        }
        $config->exportConstants();
    }


}

?>
