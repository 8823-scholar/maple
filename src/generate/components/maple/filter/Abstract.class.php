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
 * @version     CVS: $Id: Abstract.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

/**
 * Filter�ζ��̵�ǽ���󶡤���
 * 
 * 
 * @abstract
 * @package     Maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @access      public
 */
class Filter_Abstract extends Filter
{
    /**
     * _error() ����ErrorList�˥��åȤ���륨�顼������
     * ���Υץ�ѥƥ���maple.ini����
     * 
     * [FilterName]
     * _errorType = error
     * 
     * �Ȥ�������������Ǥ���
     * 
     * @var  String  $errorType
     */
    var $errorType = 'error';

    /**
     * constructor
     * 
     * @since 06/07/19 20:59
     * @return String
     */
    function Filter_Abstract()
    {
        parent::Filter();
    }
    
    /**
     * _key
     * �Ȥ��������ϡ�key�Ȥ����ץ�ѥƥ���¸�ߤ�����
     * attributes�ˤ���¸���ʤ��ǡ��ץ�ѥƥ���񤭴�����
     * 
     * [Filter]
     * _property = value
     * 
     * $this->property = value
     * 
     * @since 06/07/18 17:49
     * @param  String    $key
     * @param  String    $value
     * @return String
     */
    function setAttribute($key, $value)
    {
        if(preg_match('/^_(.+)$/', $key, $m) && array_key_exists($m[1], $this)) {
            $this->{$m[1]} = $value;
        } else {
            $this->_attributes[$key] = $value;
        }
    }

    /**
     * Fatal�ʥ��顼
     * 
     * @since 06/07/18 16:45
     * @return String
     */
    function _fatalError($msg, $caller="")
    {
        $log =& LogFactory::getLog();
        $log->fatal($msg, $caller);
        exit(1);
    }

    /**
     * 
     * @since 06/07/19 21:07
     * @param  String    $msg
     * @return String
     */
    function _error($msg)
    {
        if(!$this->errorType) {
            return ;
        }
        
        $container =& DIContainerFactory::getContainer();
        $actionChain =& $container->getComponent('ActionChain');
        $errorList =& $actionChain->getCurErrorList();

        $errorList->setType($this->errorType);
        $errorList->add(get_class($this), $msg);
    }

}

?>
