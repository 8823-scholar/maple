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
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Filter_View.class.php,v 1.7 2006/08/30 12:48:26 hawkring Exp $
 */

require_once MAPLE_DIR .'/core/Smarty4Maple.class.php';

/**
 * View�μ¹Խ�������Ӽ¹Ԥ�Ԥ�Filter
 *
 * @package     Maple.filter
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Filter_View extends Filter
{
    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function Filter_View()
    {
        parent::Filter();
    }

    /**
     * View�ν�����¹�
     *
     * @access  public
     * @since   3.0.0
     **/
    function execute()
    {
        $log =& LogFactory::getLog();
        $log->trace("Filter_View�����������¹Ԥ���ޤ���", "Filter_View#execute");

        $container =& DIContainerFactory::getContainer();
        $filterChain =& $container->getComponent("FilterChain");
        $filterChain->execute();

        $response =& $container->getComponent("Response");
        $view = $response->getView();

        if ($view != "") {
            $template = $this->getAttribute($view);
            if ($template == "") {
                $log->error("�ƥ�ץ졼�ȥե�����μ����˼��Ԥ��ޤ���", "Filter_View#execute");
                exit;
            }

            if (preg_match("/location:/", $template)) {
                $url = preg_replace("/location:/", "", $template);
                $url = trim($url);
                $response->setRedirect($url);
            } else if (preg_match("/action:/", $template)) {
                $action = preg_replace("/action:/", "", $template);
                $action = trim($action);
                $actionChain =& $container->getComponent("ActionChain");
                $actionChain->add($action);
            } else {
                $renderer =& Smarty4Maple::getInstance();

                $actionChain =& $container->getComponent("ActionChain");
                $action =& $actionChain->getCurAction();
                $renderer->setAction($action);

                $errorList =& $actionChain->getCurErrorList();
                $renderer->setErrorList($errorList);

                $token =& $container->getComponent("Token");
                if (is_object($token)) {
                    $renderer->setToken($token);
                }

                $session =& $container->getComponent("Session");
                if (is_object($session)) {
                    $renderer->setSession($session);
                }

                $renderer->setScriptName($_SERVER['SCRIPT_NAME']);

                $result = $renderer->fetch($template);

                if ($result != "") {
                    $response->setResult($result);
                }
            }
        }

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

            print $result;
        }

        $log->trace("Filter_View�θ�������¹Ԥ���ޤ���", "Filter_View#execute");
    }
}
?>
