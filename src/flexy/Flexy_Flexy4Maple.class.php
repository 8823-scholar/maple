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
 * @version     CVS: $Id: Flexy_Flexy4Maple.class.php,v 1.3 2006/11/04 23:30:12 hawkring Exp $
 */

require_once 'HTML/Template/Flexy.php';
require_once 'HTML/Template/Flexy/Element.php';

/**
 * Maple�Ѥ˥������ޥ������줿Flexy
 * ���Τ� HTML_Template_Flexy ���ȼ���include�������ɲä���
 * include���ưŪ���Ǥ�����Ǥ���褦��ǽ��ĥ�������
 * �¤�Maple�Ȥ�ľ�ܴط����ʤ�
 * 
 * @package     Maple.flexy
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.1.0
 */
class Flexy_Flexy4Maple extends HTML_Template_Flexy
{
    /**
     * ����Flexy���֥������ȤΡɻҡɥ��֥�������
     * include�����ե�������˺����
     * 
     * @access private
     * @var array of HTML_Template_Flexy
     */
    var $_flexyChildren = array();
    
    /**
     * include���ޤ᤿���Ƥ�Elements
     * 
     * @access private
     * @var array of HTML_Template_Flexy_Element
     */
    var $_allElements = array();

    /**
     * include���ޤޤʤ�Elements
     * 
     * @access public
     * @var array of HTML_Template_Flexy_Element
     */
    var $_curTemplateElements = array();

    /**
     * include���줿�ƥ�ץ졼�Ȥ��֤�������������
     * 
     * @access private
     * @var array of HTML_Template_Flexy_Element
     */
    var $_includeElements = array();
    
    /**
     * Flexy_ElementFilters
     * 
     * @access private
     * @var array of Flexy_ElementFilter
     */
    var $_filters = array();
    
    /**
     * Constructor
     * 
     * @param public
     * @param array 
     */
    function Flexy_Flexy4Maple($cfg = array())
    {
        $options = Flexy_Flexy4Maple::globalOptions();

        $constants = array(
            'FLEXY_TEMPLATE_DIR'  => 'templateDir',
            'VIEW_TEMPLATE_DIR'   => 'templateDir',
            'FLEXY_COMPILE_DIR'   => 'compileDir',
            'VIEW_COMPILE_DIR'    => 'compileDir',
            'FLEXY_FORCE_COMPILE' => 'forceCompile',
            'FLEXY_DEBUG'         => 'debug',
            'FLEXY_LOCALE'        => 'locale',
            'FLEXY_COMPILER'      => 'compiler'
        );
        foreach($constants as $constName => $key) {
            if(defined($constName)) {
                $options[$key] = constant($constName);
            }
        }
        $options = $cfg + $options;
        parent::HTML_Template_Flexy($options);
    }
    
    /**
     * �����Х��Flexy���ץ��������ꤹ��
     * �����������setter���ʤ�����getter
     * 
     * @static
     * @access public
     * @param array options
     * @param bool 
     * @return array
     */
    function globalOptions($options=array(), $clear=false)
    {
        static $globalOptions = array();
        
        if($clear) {
            $globalOptions = $options;
        } else {
            $globalOptions = $options + $globalOptions;
        }

        return $globalOptions;
    }
    
    /**
     * ���ץ���������Ѥ���������Flexy���󥹥��󥹤��������
     * 
     * @access public
     * @return HTML_Template_Flexy
     */
    function &cloneFlexy()
    {
        $flexyClass = get_class($this);
        $flexy =& new $flexyClass($this->options);
        return $flexy;
    }
    
    function addFilter(&$filter)
    {
        $this->_filters[] =& $filter;
    }
    
    function _getFilteredElements()
    {
        $elms = array();
        $allElements = $this->getAllElements();
        foreach($this->_filters as $filter) {
            $elms = $elms + $filter->doFilter($allElements);
        }
        return $elms;
    }
    
    /**
     * 
     * @override
     * @access public
     */
    function compile($file)
    {
        $ret = parent::compile($file);
        
        if (is_a($ret,'PEAR_Error')) {
            return $ret;
        }
        
        foreach($this->getElements() as $name => $elm) {
            if(!is_object($elm)) {
                continue;
            }
            
            if($this->isIncludeElement($elm)) {
                $this->_includeElements[$name] = $elm;
                $filename = $this->getIncludeFile($elm);
                $this->_flexyChildren[$name] =& $this->cloneFlexy();
                $this->_flexyChildren[$name]->compile($filename);
            } else {
                $this->_curTemplateElements[$name] = $elm;
            }
        }
        
        return $ret;
    }

    /**
     * 
     * 
     * @override
     */
    function outputObject(&$t,$elements=array()) 
    {
        $filtered = $this->_getFilteredElements();
        $elements = $filtered + $elements;

        foreach($this->_flexyChildren as $name => $_flexy) {
            $this->_includeElements[$name]->override = $this->_flexyChildren[$name]->bufferedOutputObject($t, $elements);
        }

        /* ����Flexy�˴ط��������ǤΤߤ���� */
        $crrElements = array();
        if(function_exists('array_intersect_ukey')) {
            $crrElements = array_intersect_ukey($elements, $this->_curTemplateElements, 'strcmp');
        } else {
            $crrElements = array_intersect_assoc($elements, $this->_curTemplateElements);
        }
        $elms = $crrElements + $this->_includeElements;

        return parent::outputObject($t, $elms);
    }

    /**
     * 
     * 
     * @param HTML_Template_Flexy_Element
     * @return bool
     */
    function isIncludeElement(&$elm)
    {
        return isset($elm->attributes['maple:include']) && is_string($elm->attributes['maple:include']);
    }

    /**
     * 
     * 
     * @param HTML_Template_Flexy_Element
     * @return string
     */
    function getIncludeFile(&$elm)
    {
        return isset($elm->attributes['maple:include']) ? $elm->attributes['maple:include'] : "";
    }
    
    /**
     * include���ޤ����Ƥ�ưŪ���Ǥ��֤�
     * 
     * @access public
     * @return array of HTML_Template_Flexy_Element
     */
    function getAllElements()
    {
        if(count($this->_allElements) < 1) {
            $elms = $this->_curTemplateElements;
            foreach($this->_flexyChildren as $name => $_flexy) {
                $elms = $elms + $this->_flexyChildren[$name]->getAllElements();
            }
            $this->_allElements = $elms;
        }
        return $this->_allElements;
    }
}

?>
