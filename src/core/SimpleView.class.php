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
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: SimpleView.class.php,v 1.2 2006/10/17 12:17:36 hawkring Exp $
 */

/**
 * PHP�ν񼰤�ƥ�ץ졼�ȤǤ��Τޤ����Ѥ���ʰץƥ�ץ졼�ȥ��饹
 *
 * @package     Maple
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.2.0
 */
class SimpleView
{
    /**
     * @var array �ƥ�ץ졼�Ȥ˳�����Ƥ��ͤΥꥹ��
     */
    var $_assigns;

    /**
     * @var  String  $templateDir  
     */
    var $templateDir;

    /**
     * @var  String  $_templateEncoding  
     */
    var $templateEncoding;

    /**
     * @var  String  $_outputEncoding  
     */
    var $outputEncoding;
    
    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.2.0
     */
    function SimpleView()
    {
        $this->_assigns = array();
        $this->templateDir = defined('VIEW_TEMPLATE_DIR') ? VIEW_TEMPLATE_DIR : TEMPLATE_DIR;
        
        $this->templateEncoding = INTERNAL_CODE;
        $this->outputEncoding   = OUTPUT_CODE;
    }
    
    /**
     * �ƥ�ץ졼�Ȥ��ͤ������Ƥ�
     * 
     * @param string key �ƥ�ץ졼�Ȥǥ�����������ݤ˻��Ѥ��륭��̾
     * @param mixed $value ��
     * @access  public
     * @since   3.2.0
     */
    function assign($key, $value)
    {
        $this->_assigns[$key] = $value;
    }
    
    /**
     * ���Ȥǥƥ�ץ졼�Ȥ��ͤ������Ƥ�
     * 
     * @param string key �ƥ�ץ졼�Ȥǥ�����������ݤ˻��Ѥ��륭��̾
     * @param mixed $value ��
     * @access  public
     * @since   3.2.0
     */
    function assignByRef($key, &$value)
    {
        $this->_assigns[$key] =& $value;
    }

    /**
     * �ƥ�ץ졼�ȥե�����Υե�ѥ�������
     * 
     * @param  String    $template
     */
    function getTemplate($template)
    {
        return $this->templateDir . $template;
    }
    
    /**
     * �ƥ�ץ졼�Ȥ��ͤ������Ƥ���̤��������
     * 
     * @param string $template �ƥ�ץ졼�ȥե�����̾
     * @return string �ƥ�ץ졼�Ȥ�����
     * @access  public
     * @since   3.2.0
     */
    function fetch($template)
    {
        $template = $this->getTemplate($template);
        if (!file_exists($template)) {
            $_log =& LogFactory::getLog();
            $_log->error(
                "�ƥ�ץ졼�ȥե����뤬�ߤĤ���ޤ���($template)",
                __CLASS__.'#'.__FUNCTION__);
            exit;
        }

        extract($this->_assigns);

        ob_start();
        include $template;
        $result = ob_get_contents();
        ob_end_clean();
        
        $result = $this->_convertEncoding($result);
        return $result;
    }
    
    /**
     * �ƥ�ץ졼�Ȥ��ͤ������Ƥ���̤���Ϥ���
     * 
     * @param string $template �ƥ�ץ졼�ȥե�����̾
     * @return string �ƥ�ץ졼�Ȥ�����
     * @access  public
     * @since   3.2.0
     */
    function display($template)
    {
        $buf = $this->fetch($template);
        print $buf;
        return $buf;
    }

    /**
     * ���󥳡��ɤ���
     * 
     * @param string $string ���󥳡��ɤ���ʸ����
     * @return strign ���󥳡��ɸ��ʸ����
     * @access  public
     * @since   3.2.0
     */    
    function _convertEncoding($string)
    {
        if ($this->outputEncoding != $this->templateEncoding) {
            $string = mb_convert_encoding(
                $string,
                $this->outputEncoding,
                $this->templateEncoding);
        }
        return $string;
    }
}

?>
