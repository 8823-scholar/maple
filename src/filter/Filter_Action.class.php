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
 * @version     CVS: $Id: Filter_Action.class.php,v 1.7 2006/08/30 12:48:26 hawkring Exp $
 */

require_once MAPLE_DIR .'/core/BeanUtils.class.php';

/**
 * Action�μ¹Խ�������Ӽ¹Ԥ�Ԥ�Filter
 *
 * @package     Maple.filter
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Filter_Action extends Filter
{
    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function Filter_Action()
    {
        parent::Filter();
    }

    /**
     * Action��¹�
     *
     * @access  public
     * @since   3.0.0
     */
    function execute()
    {
        $log =& LogFactory::getLog();
        $log->trace("Filter_Action�����������¹Ԥ���ޤ���", "Filter_Action#execute");

        //
        // �����Ȥ�Action�����
        //
        $container =& DIContainerFactory::getContainer();
        $actionChain =& $container->getComponent("ActionChain");
        $action =& $actionChain->getCurAction();

        //
        // ����ե���������äƤ����ͤ�����
        //
        $request =& $container->getComponent("Request");
        $params = $request->getParameters();

        if ($this->getSize() > 0) {
            $attributes = $this->getAttributes();
            foreach ($attributes as $key => $value) {
                if (preg_match("/^ref:/", $value)) {
                    $value = preg_replace("/^ref:/", "", $value);
                    $component =& $container->getComponent($value);
                    if (is_object($component)) {
                        $attributes[$key] =& $component;
                    } else {
                        $log->error("�����ʥ���ݡ��ͥ�Ȥ�����ե�����ǻ��ꤵ��Ƥ��ޤ�($value)", "Filter_Action#execute");
                    }
                }

                //
                // DI�����ѥ�᡼���ϥꥯ�����ȥѥ�᡼����
                // ��񤭤���ʤ��褦�ˤ���
                //
                if (isset($params[$key])) {
                    unset($params[$key]);
                }
            }
            BeanUtils::setAttributes($action, $attributes);
        }

        //
        // Request���ͤ�Action�˰ܤ�
        //
        if (count($params) > 0) {
            BeanUtils::setAttributes($action, $params, true);
        }

        //
        // Filter_Action�ˤ��ɤ�Ĥ����˥��顼��ȯ�����Ƥ�����
        // Action�ϼ¹Ԥ��ʤ��ʤ��Τ���ꥨ�顼�����פ�View�μ���Ȥ����
        //
        $errorList =& $actionChain->getCurErrorList();
        $type = $errorList->getType();

        if ($type == "") {
            $view = $actionChain->execute();
        } else {
            $view = $type;
        }

        if ($view != "") {
            $response =& $container->getComponent("Response");
            $response->setView($view);
        }

        $filterChain =& $container->getComponent("FilterChain");
        $filterChain->execute();

        $log->trace("Filter_Action�θ�������¹Ԥ���ޤ���", "Filter_Action#execute");
    }
}
?>
