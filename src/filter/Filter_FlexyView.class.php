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
 * @package     Maple.filter
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Filter_FlexyView.class.php,v 1.2 2006/08/30 12:48:26 hawkring Exp $
 */

require_once(MAPLE_DIR .'/flexy/Flexy_Flexy4Maple.class.php');
require_once(MAPLE_DIR .'/flexy/Flexy_ViewBase.class.php');
require_once(MAPLE_DIR .'/flexy/Flexy_FormElementFilter.class.php');
require_once(MAPLE_DIR .'/flexy/Flexy_ComponentElementFilter.class.php');

/**
 * �ƥ�ץ졼�ȥ��󥸥�Ȥ��� HTML_Template_Flexy ���Ѥ��뤿���View�ե��륿
 * 
 * @package     Maple.filter
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.1.0
 */
class Filter_FlexyView extends Filter
{
    var $_container;

    var $_log;

    var $_filterChain;
    
    var $_viewClassRoot = COMPONENT_DIR;

    /**
     * Constructor
     * 
     * 
     */
    function Filter_FlexyView()
    {
        parent::Filter();
    }
    
    /**
     * HTML_Template_Flexy�ˤ�������󥰤�Ԥ�
     * 
     * @access  private
     * @param   string  View's type (e.g "success")
     * @param   string  path/to/template.html
     * @param   DIContainer
     * @return  string
     */
    function _renderByFlexy($viewType, $template, &$container)
    {
        $actionChain =& $container->getComponent("ActionChain");
        $action      =& $actionChain->getCurAction();
        $errorList   =& $actionChain->getCurErrorList();
        $token       =& $container->getComponent("Token");
        $session     =& $container->getComponent("Session");
        
        $actionName  = $actionChain->getCurActionName();
        $viewClass = $this->_getFlexyControllerClass($actionName, $viewType, 'Flexy_ViewBase');

        $flexy =& new Flexy_Flexy4Maple();
        $obj =& new $viewClass();

        $flexy->addFilter(new Flexy_FormElementFilter($action));
        $flexy->addFilter(new Flexy_ComponentElementFilter($container, $flexy, $obj));
        $ret = $flexy->compile($template);
        if (is_a($ret,'PEAR_Error')) {
            $this->_log->error("�ƥ�ץ졼�ȥե�����Υ���ѥ���˼��Ԥ��ޤ���", get_class($this)."#_renderByFlexy");
            return "";
        }
        
        $obj->setAction($action);
        $obj->setErrorList($errorList);
        if(is_object($token)) {
            $obj->setToken($token);
        }
        if(is_object($session)) {
            $obj->setSession($session);
        }
        $obj->prepare();
        
        return $flexy->bufferedOutputObject($obj);
    }
    
    /**
     * View���饹�򸡺�����
     * 
     * 1. View_{ucfirst($viewType)}
     * 2. View_Default
     * 
     * �ν�Ǹ������Ԥ���
     * �Ȥ��¸�ߤ��ʤ���� $defaultClassName ���ֵѤ����
     * ʣ���ʸ����롼����ѻߤ��졢$actionName���Ѥ����ʤ�
     * 
     * @param   string  $actionName
     * @param   string  $viewType
     * @param   string  $defaultClassName
     * @return  string  View���饹��̾��
     */
    function _getFlexyControllerClass($actionName, $viewType, $defaultClassName)
    {
        $classRoot = $this->_viewClassRoot;
        $viewTypeSp = "View_". ucfirst($viewType);
        $userDefault= "View_Default";
        
        $className = "";
        
        $classPath = "view/{$viewTypeSp}.class.php";
        if(@include_once $classPath) {
            $className = $viewTypeSp;
        } else {
            $classPath = "/view/{$userDefault}.class.php";
            if(@include_once $classPath) {
                $className = $userDefault;
            }
        }
        if($className != "") {
            return $className;
        }
        return $defaultClassName;
    }

    function _postfilter()
    {
        $log =& $this->_log;
        $container =& $this->_container;
        
        $response =& $container->getComponent("Response");
        $view = $response->getView();

        if ($view == "") {
            $this->_sendResponse($response);
            return ;
        }

        $template = $this->getAttribute($view);
        if ($template == "") {
            $log->error("�ƥ�ץ졼�ȥե�����μ����˼��Ԥ��ޤ���[${view}:${template}]", "Filter_FlexyView#_postfilter");
            exit;
        }

        if(preg_match('/action:(.+)/', $template, $matches)) {
            $actionName = trim($matches[1]);
            if(preg_match('/,\s*clear$/', $actionName)) {
                /* �ꥯ�����ȥѥ�᡼����ꥻ�å� */
                $actionName = preg_replace('/,\s*clear$/', '', $actionName);
                $req =& $container->getComponent('Request');
                $req->_params = array();
            }

            $actionChain =& $container->getComponent("ActionChain");
            $actionChain->add($actionName);
            $log->trace("Filter_FlexyView�����forward���¹Ԥ���ޤ���", "Filter_FlexyView#_postfilter");
            return ;
        }elseif (preg_match("/location:/", $template)) {
            $url = preg_replace("/location:/", "", $template);
            $url = trim($url);
            $response->setRedirect($url);
        } else {
            $result = $this->_renderByFlexy($view, $template, $container);
            
            if ($result != "") {
                if(OUTPUT_CODE != INTERNAL_CODE) {
                    $result = mb_convert_encoding($result, OUTPUT_CODE, INTERNAL_CODE);
                }
                $response->setResult($result);
            }
        }
        
        $this->_sendResponse($response);
    }
    
    function _sendResponse(&$response)
    {
        $contentDisposition = $response->getContentDisposition();
        $contentType        = $response->getContentType();
        $result             = $response->getResult();
        $redirect           = $response->getRedirect();

        if ($redirect) {
            header("Location: ${redirect}");
        } else {
            if ($contentDisposition != "") {
                header("Content-disposition: ${contentDisposition}");
            }
            if ($contentType != "") {
                header("Content-type: ${contentType}");
            }

            echo $result;
        }
    }

    function _prefilter()
    {
        
    }
    
    /**
     * �ե��륿������¹�
     * 
     * @access  public
     */
    function execute()
    {
        $this->_container =& DIContainerFactory::getContainer();
        $this->_log =& LogFactory::getLog();
        $this->_filterChain =& $this->_container->getComponent("FilterChain");
        $className = get_class($this);

    
        $this->_log->trace("{$className}�����������¹Ԥ���ޤ���", "{$className}#execute");
        $this->_prefilter();

        $this->_filterChain->execute();

        $this->_postfilter();
        $this->_log->trace("{$className}�θ�������¹Ԥ���ޤ���", "{$className}#execute");
    }
}
?>
