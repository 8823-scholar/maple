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
 * @version     CVS: $Id: Flexy_ComponentElementFilter.class.php,v 1.1 2006/02/11 19:18:22 kunit Exp $
 */

require_once(dirname(__FILE__) .'/Flexy_ElementFilter.class.php');

/**
 * 一般動的要素をコンポーネントで置換するElementFilter
 *
 * @package     Maple.flexy
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.1.0
 */
class Flexy_ComponentElementFilter extends Flexy_ElementFilter
{
    var $_container;
    
    var $_flexy;
    
    var $_view;
    
    /**
     * CONSTRUCTOR
     * 
     * @access public
     * @param DIContainer
     * @param Flexy_Flexy4Maple
     * @param Flexy_FlexyViewBase
     */
    function Flexy_ComponentElementFilter(&$container, &$flexy, &$view)
    {
        $this->_container =& $container;
        $this->_flexy =& $flexy;
        $this->_view =& $view;
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
        $container =& $this->_container;
        $filtered = array();
        
        foreach($allElements as $id => $elm) {
            $tag = strtolower($elm->tag);
            if($tag == 'form' || $tag == 'input' 
                || $tag == 'select' || $tag == 'textarea') {
                continue;
            }
            
            $componentId = ($c=$this->_getComponentId($elm)) ? $c : $id;
            
            if(is_object($obj =& $container->getComponent($componentId))) {
                if(method_exists($obj, 'acceptElement')) {
                    $obj->acceptElement($elm);
                }
                
                if($template = $this->_getInsertTemplate($elm)) {
                    $flexy =& $this->_flexy->cloneFlexy();
                    $obj->view =& $this->_view;
                    if(method_exists($obj, 'prepare')) {
                        $obj->prepare();
                    }
                    
                    $ret = $flexy->compile($template);
                    if (!is_a($ret,'PEAR_Error')) {
                        $elm->override = $flexy->bufferedOutputObject($obj);
                    }
                } elseif(method_exists($obj, 'toHtml')) {
                    $elm->override = $obj->toHtml();
                }
            }
            
            $filtered[$id] = $elm;
        }

        return $filtered;
    }

    function _getComponentId(&$elm)
    {
        if(isset($elm->attributes['maple:component'])){
            $id = $elm->attributes['maple:component'];
            unset($elm->attributes['maple:component']);
            return $id;
        } else {
            return "";
        }
    }

    function _getInsertTemplate(&$elm)
    {
        if(isset($elm->attributes['maple:insert'])){
            $template = $elm->attributes['maple:insert'];
            unset($elm->attributes['maple:insert']);
            return $template;
        } else {
            return "";
        }
    }

}

?>
