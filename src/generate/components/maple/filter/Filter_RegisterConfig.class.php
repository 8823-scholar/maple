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
 * @version     CVS: $Id: Filter_RegisterConfig.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

require_once(MAPLE_DIR .'/filter/Abstract.class.php');

/**
 * generation���оݤȤʤ�webapp�ˤĤ��Ƥ�GlobalConfig��
 * DIContainer����Ͽ����
 * 
 * [RegisterConfig]
 * seach_try = 20
 * missing_ok = false
 * 
 * @package     Maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 */
class Filter_RegisterConfig extends Filter_Abstract
{
    var $componentKey = 'globalConfigDto';

    var $errorType = 'error';
    
    /**
     * CmdArgs2Dto���������줿DTO
     * 
     * @var  object  $dto  
     */
    var $dto;
    
    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     */
    function Filter_RegisterConfig()
    {
        parent::Filter_Abstract();
    }

    /**
     * 
     *
     * @access  public
     */
    function execute()
    {
        $container =& DIContainerFactory::getContainer();
        
        $actionChain =& $container->getComponent('ActionChain');
        $errorList =& $actionChain->getCurErrorList();
        $className = get_class($this);

        $log =& LogFactory::getLog();
        $log->trace("${className}�����������¹Ԥ���ޤ���", "{$className}#execute");

        $this->dto =& $container->getComponent($this->componentKey);
        $this->_prefilter();

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
     * @since 06/07/16 21:37
     */
    function _prefilter()
    {
        $container =& DIContainerFactory::getContainer();
        $log =& LogFactory::getLog();

        //�Ȥꤢ������Ͽ���Ƥ���
        $globalConfig =& new GlobalConfig(false);
        $container->register($globalConfig, 'TargetWebappConfig');

        if(!is_object($this->dto)) {
            $this->_fatalError(
                "{$this->componentKey} is not in the DIContainer",
                __CLASS__ ."#". __FUNCTION__);
            return false;
        }
        
        if(!($webappDir  = $this->_getWebappDir(WORKING_DIR)) or
           !($configPath = $this->_getConfigPath($webappDir)) or
           !($baseDir    = $this->_getBaseDir($webappDir))) {
            return false;
        }

        $globalConfig->setValue('WEBAPP_DIR',  $webappDir);
        $globalConfig->setValue('BASE_DIR',    $baseDir);
        $globalConfig->setValue('WORKING_DIR', WORKING_DIR);
        
        $globalConfig->loadFromFile($configPath);
    }

    /**
     * �ǥե���Ȥ�GLOBAL_CONFIG
     * 
     * @since 06/07/18 16:05
     * @return String
     */
    function _getDefaultConfig()
    {
        $container =& DIContainerFactory::getContainer();
        $fileUtil  =& $container->getComponent('fileUtil');
        
        return $fileUtil->findIncludableFile('maple/config/webapp/'. GLOBAL_CONFIG);
    }

    /**
     * 
     * @since 06/07/18 16:48
     * @param  String    $webappDir
     * @return String or false
     */
    function _getBaseDir($webappDir)
    {
        $dto =& $this->dto;
        
        if(isset($dto->baseDir) && $dto->baseDir) {
            if(!file_exists($dto->baseDir) || !is_dir($dto->baseDir)) {
                $this->_error(
                    "{$dto->baseDir} is not a valid BASE_DIR");
                return false;
            } else {
                return $dto->baseDir;
            }
        }
        return dirname(preg_replace('|[\\/]$|', '', $webappDir));
    }

    /**
     * 
     * @since 06/07/18 15:50
     * @param  String    $webappDir
     * @return String
     */
    function _getConfigPath($webappDir)
    {
        if(isset($this->dto->configPath) && $this->dto->configPath) {
            if(!file_exists($this->dto->configPath)) {
                $this->_error(
                    "{$this->dto->configPath} is not found");
                return false;
            } else {
                return $this->dto->configPath;
            }
        }
        
        if(file_exists($webappDir ."/config/". GLOBAL_CONFIG)) {
            return $webappDir ."/config/". GLOBAL_CONFIG;
        } else {
            return $this->_getDefaultConfig();
        }
    }

    /**
     * 
     * @since 06/07/18 16:05
     * @param  String    $wd  current working directory
     * @return String
     */
    function _getWebappDir($wd)
    {
        $webappDir = null;

        $dto =& $this->dto;
        if(isset($dto->webappDir) && $dto->webappDir) {
            //webappDir��ľ�ܻ��ꤵ��Ƥ�����
            $webappDir = $dto->webappDir;
            
        } elseif(isset($dto->baseDir) && $dto->baseDir &&
                 isset($dto->webappName)  && $dto->webappName) {
            //baseDir�����webapp�����ꤵ��Ƥ�����
            $webappDir = $dto->baseDir ."/". $dto->webappName;
            
        } elseif($this->getAttribute('workDir_is_baseDir') &&
                 isset($dto->webappName) && $dto->webappName) {
            //working directory��baseDir�Ȥ��ƻȤ���
            //����webappName�����ꤵ��Ƥ�����
            $webappDir = $wd ."/". $dto->webappName;

        } else {
            //����¾�ξ��
            $webappDir = $this->_searchWebappDir($wd);
        }
        $webappDir = preg_replace('|[\\/]$|', '', $webappDir);
        
        if(!$this->getAttribute('missing_ok') &&
           !file_exists($webappDir ."/config/". GLOBAL_CONFIG)) {
            
            $this->_error(
                "{$webappDir} is not a valid WEBAPP_DIR");
            return false;
        }
        return $webappDir;
    }

    /**
     * 
     * @since 06/07/18 16:05
     * @param  String    $wd  current working directory
     * @return String
     */
    function _searchWebappDir($wd)
    {
        $try = $this->getAttribute('search_try');

        if($try <= 0) {
            return $wd;
        }

        $prev = "";
        $crr = $wd;
        while($try > 0 && $crr && $crr != $prev) {
            if(file_exists($crr ."/config/". GLOBAL_CONFIG)) {
                return $crr;
            }
            
            $prev = $crr;
            $crr = realpath($crr ."/../");
            $try--;
        }
        return $wd;
    }
}

?>
