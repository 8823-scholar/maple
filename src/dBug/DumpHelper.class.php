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
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: DumpHelper.class.php,v 1.1 2006/03/08 05:37:06 hawkring Exp $
 */

/**
 * ���󡦥��֥������Ȥ���۴Ļ��Ȥ����������Υ��饹
 * 
 * @package     Maple
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.1.1
 */
class DumpHelper
{
    
    /**
     * @var  array  $_refStack  ���Ȥ��Ǽ���륹���å�
     */
    var $_refStack;
    
    /**
     * @var  boolean  $_isPHP5  PHP5���ݤ�
     */
    var $_isPHP5;
    

    /**
     * ���󥹥ȥ饯��
     * 
     * @access public
     */
    function DumpHelper()
    {
        $this->_isPHP5 = version_compare(phpversion(), '5', '>=');
        $this->reset();
    }
    
    /**
     * SimpleTest 
     * http://www.lastcraft.com/unit_test_documentation.php
     * 
     * assertReference ���������Ѥ��Ƥ���
     * SimpleTestCompatibility::isReference ���
     * 
     * @access public
     * @param mixed
     * @param mixed
     * @return boolean
     */
    function isReference(&$first, &$second)
    {
        if ($this->_isPHP5 && is_object($first)) {
            return ($first === $second);
        }
        $temp = $first;
        $first = uniqid("dumphelper");
        $is_ref = ($first === $second);
        $first = $temp;
        return $is_ref;
    }
    
    /**
     * $var ����۴Ļ��Ȥ����������Τ��֤�
     * 
     * @access public
     * @param mixed
     * @return mixed
     */
    function removeCircularReference(&$var)
    {
        $result = null;

        if(is_object($var)) {
            if($this->_push($var)) {
                $result = $this->_isPHP5 ? clone($var) : $var;
                
                foreach(array_keys(get_object_vars($var)) as $k) {
                    $tmp = $this->removeCircularReference($var->$k);
                    $result->$k =& $tmp;
                    unset($tmp);
                }
                $this->_pop();
                
            } else {
                $result = $this->_getSubstituteFor($var);
            }
        } elseif(is_array($var)) {
            if($this->_push($var)) {
                $result = array();
                
                foreach(array_keys($var) as $k) {
                    $result[$k] = $this->removeCircularReference($var[$k]);
                }
                $this->_pop();
            } else {
                $result = $this->_getSubstituteFor($var);
            }
        } else {
            $result = $var;
        }
        return $result;
    }
    
    /**
     * �۴Ĥ��Ƥ���object or array������ɽ�����֤�
     * 
     * @access protected
     * @param mixed
     * @return mixed
     */
    function _getSubstituteFor($var)
    {
        if(is_object($var)) {
            return '&object('. get_class($var) .')';
        } elseif(is_array($var)) {
            return '&array';
        }
        return "";
    }

    /**
     * �����å���ꥻ�åȤ���
     * ��̩�ˤϽ���������ä������ǥ����å��϶��ˤʤäƤ���Ϥ�����
     * ǰ�Τ���
     * 
     * @access public
     */
    function reset()
    {
        $this->_refStack = array();
    }
    

    /**
     * ����$var�򥹥��å����Ѥ�
     * ���˥����å����Ʊ��λ��Ȥ�¸�ߤ����硢
     * �����å��ˤ��Ѥޤ�false���֤�
     * 
     * @param  mixed $var
     * @return boolean
     */
    function _push(&$var)
    {
        $vartype = gettype($var);
        foreach($this->_refStack as $i => $v) {
            if($vartype == gettype($this->_refStack[$i]) &&
                $this->isReference($var, $this->_refStack[$i])) {
                return false;
            }
        }
        $this->_refStack[] =& $var;
        return true;
    }
    
    /**
     * �����å����黲�Ȥ������
     * 
     * access private
     */
    function _pop()
    {
        array_pop($this->_refStack);
    }
}

?>
