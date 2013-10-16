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
 * @version     CVS: $Id: Filter_DIContainer.class.php,v 1.7 2006/08/30 12:48:26 hawkring Exp $
 */

/**
 * DIContainer�ν�����Ԥ�Filter
 *
 * @package     Maple.filter
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Filter_DIContainer extends Filter
{
    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function Filter_DIContainer()
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
        $log->trace("Filter_DIContainer�����������¹Ԥ���ޤ���", "Filter_DIContainer#execute");

        //
        // ����ե���������äƤ����ͤ�����
        //
        $container =& DIContainerFactory::getContainer();
        $actionChain =& $container->getComponent("ActionChain");
        $actionName = $actionChain->getCurActionName();

        $attributes = $this->getAttributes();

        foreach ($attributes as $key => $value) {
            if (preg_match("|^filename|", $key)) {
                $this->_createContainer($actionName, $value);
            }
        }

        $filterChain =& $container->getComponent("FilterChain");
        $filterChain->execute();

        $log->trace("Filter_DIContainer�θ�������¹Ԥ���ޤ���", "Filter_DIContainer#execute");
    }

    /**
     * DIContainer����������
     *
     * @param   string  $name   ���ߤ�action��̾��
     * @param   string  $filename   ����ե�����̾
     * @access  private
     * @since   3.0.0
     */
    function _createContainer($name, $filename) {
        if (!$name || !$filename) {
            return;
        }

        if (preg_match("|^/|", $filename)) {
            $filename   = WEBAPP_DIR . $filename;
        } else {
            $pathList = explode("_", $name);

            $actionPath = join("/", $pathList);
            $filename   = MODULE_DIR . "/${actionPath}/${filename}";
        }

        //
        // DIContainer������
        //
        $log =& LogFactory::getLog();

        $container =& DIContainerFactory::create($filename);
        if (!$container) {
            $log->error("DIConainer�������˼��Ԥ��ޤ���", "Filter_DIContainer#_createContainer");
        }
    }
}
?>
