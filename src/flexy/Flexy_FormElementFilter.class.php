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
 * @package     Maple.flexy
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Flexy_FormElementFilter.class.php,v 1.1 2006/02/11 19:18:22 kunit Exp $
 */

require_once(dirname(__FILE__) .'/Flexy_ElementFilter.class.php');

/**
 * �ե��������Ǥ��Ф����ͤ�ư���ꤹ�뤿���ElementFilter
 * 
 * @package     Maple.flexy
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.1.0
 */
class Flexy_FormElementFilter extends Flexy_ElementFilter
{
    var $_action;
    
    /**
     * CONSTRUCTOR
     * 
     * @access public
     * @param object Maple's Action
     */
    function Flexy_FormElementFilter(&$action)
    {
        $this->_action =& $action;
    }

    /**
     * 
     * 
     * @override
     * @access public
     * @param array of HTML_Template_Flexy_Element
     * @return array of HTML_Template_Flexy_Element
     */
    function doFilter($allElements)
    {
        $action =& $this->_action;
        $filtered = array();

        $targets = array(&$action);

        foreach($allElements as $name => $elm) {
            $tag  = strtolower($elm->tag);
            $type = isset($elm->attributes['type']) ? $elm->attributes['type'] : "";
            $attr_value = isset($elm->attributes['value']) ? $elm->attributes['value'] : "";
            $attr_name  = isset($elm->attributes['name'])  ? $elm->attributes['name']  : "";

            if($tag == 'form') {
                $filtered[$name] = $elm;
                $targets = $this->_buildTargetChain($action, $name);
                continue;
            }
            
            if($tag != 'input' && $tag != 'select' && $tag != 'textarea') {
                continue;
            }

            if($tag == 'input') {
                
                /* input type="password" �ξ��ϥ��롼 */
                if($type == 'password') {
                    continue;
                }

                /* input type="radio" �ξ��ϸ��̤��ͤ�Ĵ�٤� */
                if($type == 'radio') {
                    if(($value = $this->_callGetter($targets, $attr_name)) !== null) {
                        $elm->setValue($value);
                    }
                    $filtered[$name] = $elm;
                    continue;
                }
                
                /*
                    input type="checkbox" �ξ��
                    1��������Τߥ��ݡ���
                */
                if($type == 'checkbox') {
                    /*
                        input type="checkbox" name="array[]" �ξ��
                        Flexy����ưŪ��ID���꿶�äƤ���
                    */
                    if(preg_match('/^([\d\w\-]+)\[\]$/', $attr_name, $m)) {
                        $value = $this->_callGetter($targets, $m[1]);
                        $elm->setValue($value);
                        $filtered[$name] = $elm;
                        continue;
                    }
                    
                    /*
                        input type="checkbox" name="array[key]" �ξ��
                        Flexy�ϲ��⤷�Ƥ��ʤ��Τ�
                        ���̤��ͤ�Ĵ�٤�
                    */
                    if(preg_match('/^([\d\w\-]+)\[([\w\d\-]+)\]$/', $attr_name, $m)) {
                        $value = $this->_callGetter($targets, $m[1]);
                        
                        if(is_array($value)) {
                            if(isset($value[$k = $m[2]])) {
                                $elm->setValue($value[$k]);
                            } elseif(isset($elm->attributes['checked'])) {
                                unset($elm->attributes['checked']);
                            }
                        }
                        $filtered[$name] = $elm;
                        continue;
                    }
                }
            }

            /* SELECT OPTION ������ */
            if($tag == 'select' && 
                is_array($opts = $this->_callGetter($targets, $name .'_options'))) {
                $elm->setOptions($opts);
            }
            
            if(($value = $this->_callGetter($targets, $name)) !== null) {
                $elm->setValue($value);
            }

            $filtered[$name] = $elm;
        }

        return $filtered;
    }
    
    function _buildTargetChain(&$action, $name)
    {
        $targets = array();
        $names = explode('-', $name);
        
        foreach($names as $n) {
            $t =& $this->_callGetterRef($action, $n);
            if($t !== null) {
                $targets[] =& $t;
            }
        }
        $targets[] =& $action;
        return $targets;
    }
    
    function &_callGetterRef(&$target, $prop)
    {
        $obj = null;
        $getter = "get". ucfirst($prop);
        if(method_exists($target, $getter)) {
            $obj =& $target->$getter();
        } elseif(isset($target->$prop)) {
            $obj =& $target->$prop;
        }
        return $obj;
    }
    
    function _callGetter(&$target, $prop)
    {
        if(is_array($target)) {
            $value = null;
            foreach($target as $t) {
                if(($value = $this->_callGetter($t, $prop)) !== null) {
                    return $value;
                }
            }
            return $value;
        } else {
            $value = null;
            $getter = "get". ucfirst($prop);
            if(method_exists($target, $getter)) {
                $value = $target->$getter();
            } elseif(isset($target->$prop)) {
                $value = $target->$prop;
            }
            return $value;
        }
    }

}

?>
