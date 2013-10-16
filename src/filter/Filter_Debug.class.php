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
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Filter_Debug.class.php,v 1.11 2006/11/06 07:29:25 hawkring Exp $
 */

/**
 * Debug Console�ν��Ϥ��Ԥ�줿���ɤ����򼨤��ե饰
 */
$GLOBALS['MAPLE_FILTER_DEBUG_EXECUTED'] = false;

require_once MAPLE_DIR .'/dBug/DumpHelper.class.php';

/**
 * �ǥХå������ɽ������Filter
 *
 * @package     Maple.filter
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.1.0
 */
class Filter_Debug extends Filter
{
    /** @var array ɽ����������  */
    var $_debugs = array();

    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.1.0
     */
    function Filter_Debug()
    {
        parent::Filter();
    }

    /**
     * �ǥХå������ɽ������
     *
     * @access  public
     * @since   3.1.0
     */
    function execute()
    {
        $log =& LogFactory::getLog();
        $log->trace("Filter_Debug�����������¹Ԥ���ޤ���", "Filter_Debug#execute");
        $this->_preFilter();
        
        $container =& DIContainerFactory::getContainer();
        $filterChain =& $container->getComponent("FilterChain");
        $filterChain->execute();

        $log->trace("Filter_Debug�θ�������¹Ԥ���ޤ���", "Filter_Debug#execute");

        if(DEBUG_MODE) {
            register_shutdown_function(array(&$this, '_postFilter'));
        }
    }

    /**
     * Response��HTML���ä����ɤ�����Ĵ�٤�
     * 
     * @access private
     * @param  Response $response
     * @return boolean
     */
    function _isHtmlResponse(&$response)
    {
        // Response���֥������Ȥ�Result���ͤ�����Ȥ��ϥӥ塼����Ϥ���Ȥߤʤ�
        $result = $response->getResult();
        $contentType = $response->getContentType();
        return ($result != '' &&
                ($contentType == '' || preg_match('|text/html|', $contentType)));
    }
    
    /**
     * �ץ�ե��륿��
     */
    function _preFilter()
    {
        // ���⤷�ʤ�
    }
    
    /**
     * �ݥ��ȥե��륿��
     */
    function _postFilter()
    {
        // Debug�ϥӥ塼��ɽ��������Τ߽��Ϥ���ɬ�פ���
        $container =& DIContainerFactory::getContainer();
        $response =& $container->getComponent("Response");
        
        if(!$this->_isHtmlResponse($response) ||
           $GLOBALS['MAPLE_FILTER_DEBUG_EXECUTED']) {
            return;
        }
        $GLOBALS['MAPLE_FILTER_DEBUG_EXECUTED'] = true;

        $NO = '�ʤ�';

        $var = (isset($_POST) && (0 < count($_POST)))? $_POST: $NO;
        $this->addParam('�ꥯ�����ȥѥ�᡼��($_POST)', $var);

        $var = (isset($_GET) && (0 < count($_GET)))? $_GET: $NO;
        $this->addParam('�ꥯ�����ȥѥ�᡼��($_GET)', $var);

        $var = (isset($_FILES) && (0 < count($_FILES)))? $_FILES: $NO;
        $this->addParam('�ꥯ�����ȥѥ�᡼��($_FILES)', $var);

        $var = (isset($_SESSION) && (0 < count($_SESSION)))? $_SESSION: $NO;
        $this->addParam('�ꥯ�����ȥѥ�᡼��($_SESSION)', $var);

        $container =& DIContainerFactory::getContainer();

        $actionChain =& $container->getComponent("ActionChain");
        foreach ($actionChain->_list as $name => $action) {
            $this->addParam("���������({$name})", $action);
            $this->addParam("���顼�ꥹ��({$name})", $actionChain->_errorList[$name]);
        }

        $dumpHelper =& new DumpHelper();
        $result = $dumpHelper->removeCircularReference($container->_components);
        $this->addParam("DIContainer", $result);

        $this->_printDebug();
    }

    /**
     * �ǥХå�������ɲä���
     * 
     * @param string $title �����ȥ�
     * @param mixed $var �ǥХå��оݤ��ѿ�
     */
    function addParam($title, $var)
    {
        $title = $this->_recursiveEncoding($title);
        $var = $this->_recursiveEncoding($var);
        $this->_debugs[$title] = $var;
    }
    
    /**
     * �Ƶ�Ū�˥��󥳡��ɤ��ѹ�����
     * 
     * @param mixed $var ���Ϥ�������
     */
    function _recursiveEncoding($var)
    {
        // ���֥������Ȥξ��
        if (is_object($var)) {
            foreach (array_keys(get_object_vars($var)) as $prop) {
                $var->$prop = $this->_recursiveEncoding($var->$prop);
            }
            $result = $var;
        
        // ����ξ��
        } else if (is_array($var)) {
            $result = array();
            foreach (array_keys($var) as $k) {
                $newkey = $this->_recursiveEncoding($k);
                $result[$newkey] = $this->_recursiveEncoding($var[$k]);
            }
        
        // �꥽�����ξ��
        } else if (is_resource($var)) {
            $result = $this->_detectEncoding('&resource(' . get_resource_type($var) . ')');
        
        // ʸ���󡢿��͡���������NULL�ξ��
        } else {
            if (!is_string($var)) {
                $var = (string)$var;
            }
            $result = $this->_detectEncoding($var);
        }
        return $result;
    }
    
    /**
     * ���ꤷ���ѿ��Υ��󥳡��ɤ�Ƚ�Ǥ��ƽ��ϥ��󥳡��ɤ��ѹ�����
     * 
     * @param string $var ����ʸ����
     */
    function _detectEncoding($var)
    {
        $var = mb_convert_encoding($var, OUTPUT_CODE, "auto");
        return $var;
    }
    
    /**
     * Javascript�ѤΥ��������׽���
     * 
     * @param string $src javascript������
     */
    function _escapeJavascript($src)
    {
        return strtr($src, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));
    }
    
    /**
     * �ǥХå���ɽ������
     */
    function _printDebug()
    {
        ob_start();
        require_once MAPLE_DIR .'/dBug/dBug.php';
        new dBug($this->_debugs, 'array');
        $debug = ob_get_contents();
        ob_end_clean();

        // ɽ������HTML�κ���
        $html = <<<HTML
<html>
<head>
<title>Maple Debug Console</title>
</head>
<body bgcolor="#ffffff">
<table border="0" width="100%"><tr bgcolor="#cccccc"><th colspan="2">Maple Debug Console</th></tr></table>
{$debug}
</body>
</html>
HTML;

        // Javascript�����Ѥ˥��������׽���
        $html = $this->_escapeJavascript($html);

        // Javascript����
        $JS = <<<JS
<script type="text/javascript">
    var title = 'Console';
    var _maple_debug_console = window.open("",title,"width=680,height=600,resizable,scrollbars=yes");
        _maple_debug_console.document.write("$html");
        _maple_debug_console.document.close();
</script>
JS;
        print $JS;
    }
}

?>
