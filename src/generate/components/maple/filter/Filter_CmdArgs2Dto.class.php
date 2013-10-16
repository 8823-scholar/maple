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
 * @package     Maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Filter_CmdArgs2Dto.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

require_once(MAPLE_DIR .'/filter/Abstract.class.php');
require_once(MAPLE_DIR .'/core/CmdArgs.class.php');

/**
 * ���ޥ�ɥ饤�ѥ�᡼����DTO���Ѵ�����
 * ��̤� dto �Ȥ���̾����Request�˳�Ǽ�����
 * 
 * [CmdArgs2Dto]
 * actionName =
 * viewType = optional
 * d:webapp-dir = default
 *
 * @package     Maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 */
class Filter_CmdArgs2Dto extends Filter_Abstract
{
    var $componentKey = 'dto';

    var $errorType = 'usage';

    var $registerDummy = false;

    var $_errorList;
    
    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     */
    function Filter_CmdArgs2Dto()
    {
        parent::Filter_Abstract();
    }

    /**
     * command line parameters to dto
     *
     * @access  public
     */
    function execute()
    {
        $className = get_class($this);
        $container =& DIContainerFactory::getContainer();
        $actionChain =& $container->getComponent("ActionChain");
        $this->_errorList =& $actionChain->getCurErrorList();

        
        $log =& LogFactory::getLog();
        $log->trace("${className}�����������¹Ԥ���ޤ���", "{$className}#execute");

        $req =& $container->getComponent('Request');
        $cmdArgs = $req->getParameter('args');

        $dto =& $this->_createDto(
            $this->getAttributes(),
            is_array($cmdArgs) ? $cmdArgs : array());

        if(is_object($dto)) {
            $container->register($dto, $this->componentKey);

            //;�ä��ѥ�᡼����args�˾��
            $req->setParameter('args', $dto->args);
        } elseif($this->registerDummy) {
            $dummy = new stdClass;
            $req->setParameterRef($this->componentKey, $dummy);
            $container->register($dummy, $this->componentKey);
        }

        //
        // �����ǰ�ö���Υե��륿���������ܤ�
        //

        $filterChain =& $container->getComponent("FilterChain");
        $filterChain->execute();

        //
        // �����˸�����򵭽�
        //

        $log->trace("${className}�θ�������¹Ԥ���ޤ���", "${className}#execute");
    }

    /**
     * 
     * @since 06/07/16 21:27
     * @param  array    $config
     * @param  array    $args
     * @return Object
     */
    function &_createDto($config, $args)
    {
        $cmdArgs =& new CmdArgs();
        $cmdArgs->setErrorHandler(array(&$this, '_addError'));
        $dto =& $cmdArgs->args2Dto($config, $args);
        return $dto;
    }

    /**
     * error handler intended to be called in CmdArgs object.
     * 
     * @since 06/07/16 21:26
     * @param  String    $msg
     * @return String
     */
    function _addError($msg)
    {
        $this->_errorList->add('cmd2args', $msg);
        $this->_errorList->setType($this->errorType);
    }

}
?>
