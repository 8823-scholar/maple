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
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Controller.class.php,v 1.5 2006/08/30 12:48:26 hawkring Exp $
 */

require_once MAPLE_DIR .'/core/LogFactory.class.php';
require_once MAPLE_DIR .'/core/DIContainerFactory.class.php';

/**
 * �ե졼������ư������礹�륯�饹
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Controller
{
    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function Controller()
    {
    }

    /**
     * �ե졼������ư������
     *
     * @access  public
     * @since   3.0.0
     */
    function execute()
    {
        $log =& LogFactory::getLog();

        //
        // DIContainer����������
        //
        $container =& $this->_createDIContainer();

        if (!is_object($container)) {
            $log->fatal("DIContainer�������˼��Ԥ��ޤ���", "Controller#execute");
            return;
        }

        //
        // �ꥯ�����ȥѥ�᡼������������¹Ԥ���Action�����
        //
        $request =& $container->getComponent("Request");
        $request->dispatchAction();
        $actionName = $request->getParameter(ACTION_KEY);

        //
        // ���Action��ActionChain�˥��å�
        //
        $actionChain =& $container->getComponent("ActionChain");
        $actionChain->add($actionName);

        //
        // �¹Ԥ��٤�Action������¤귫���֤�
        //
        while ($actionChain->hasNext()) {
            //
            // ����ե�������ɤ߹���
            //
            $configUtils =& $container->getComponent("ConfigUtils");
            $configUtils->execute();

            //
            // ����ե�����򸵤�FilterChain���Ȥ�Ω�Ƥơ��¹�
            //
            $filterChain =& $container->getComponent("FilterChain");
            $filterChain->build($configUtils);
            $filterChain->execute();
            $filterChain->clear();

            //
            // ���������Ӽ���Action��
            //
            $configUtils->clear();

            $actionChain->next();
        }
    }

    /**
     * DIContainer����������
     *
     * @access  public
     * @since   3.0.0
     */
    function &_createDIContainer()
    {
        $log =& LogFactory::getLog();

        if (!@file_exists(WEBAPP_DIR . BASE_INI)) {
            $log->fatal("����ե����뤬¸�ߤ��ޤ���", "Controller#_createDIContainer");
            return;
        }

        $config = parse_ini_file(WEBAPP_DIR . BASE_INI, TRUE);

        if (count($config) < 1) {
            $log->fatal("����ե����뤬�����Ǥ�", "Controller#_createDIContainer");
            return;
        }

        $container =& DIContainerFactory::getContainer();

        foreach ($config as $key => $value) {
            if (isset($config[$key]["name"])) {
                $className = $config[$key]["name"];
            }
            if (isset($config[$key]["path"])) {
                $filename = $config[$key]["path"];
            }

            if (!$className || !$filename) {
                $log->fatal("����ե����뤬�����Ǥ�", "Controller#_createDIContainer");
                return;
            }

            include_once($filename);

            $instance =& new $className();

            if (!is_object($instance)) {
                $log->fatal("���󥹥��󥹤������˼��Ԥ��ޤ���($className)", "Controller#_createDIContainer");
                return;
            }

            $container->register($instance, $key);
        }

        return $container;
    }
}
?>
