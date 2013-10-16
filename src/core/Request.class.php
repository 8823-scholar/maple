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
 * @version     CVS: $Id: Request.class.php,v 1.6 2006/03/20 02:31:39 bobchin Exp $
 */

/**
 * POST/GET�Ǽ�����ä��ͤ��Ǽ����
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Request
{
    /**
     * @var POT/GET�Ǽ�����ä��ͤ��ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_params;

    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function Request()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $request = $_POST;
        } else {
            $request = $_GET;
        }

        if (get_magic_quotes_gpc()) {
            $request = $this->_stripSlashesDeep($request);
        }

        if (!ini_get("mbstring.encoding_translation") &&
            (INPUT_CODE != INTERNAL_CODE)) {
             mb_convert_variables(INTERNAL_CODE, INPUT_CODE, $request);
        }

        $this->_params = $request;
    }
    
    /**
     * stripslashes() �ؿ���Ƶ�Ū�˼¹Ԥ���
     *
     * @param   mixed  $value  ���������ѿ�
     * @return  mixed  �������
     * @access  private
     * @see     http://www.php.net/manual/ja/function.stripslashes.php#AEN181588
     * @since   3.1.0
     */
    function _stripSlashesDeep($value)
    {
        if (is_array($value)) {
            $value = array_map(array($this, '_stripSlashesDeep'), $value);
        } else {
            $value = stripslashes($value);
        }
        return $value;
    }

    /**
     * REQUEST_METHOD���ͤ��ֵ�
     *
     * @return  string  REQUEST_METHOD����
     * @access  public
     * @since   3.1.0
     */
    function getMethod()
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    /**
     * POST/GET���ͤ��ֵ�
     *
     * @param   string  $key    �ѥ�᡼��̾
     * @return  string  �ѥ�᡼������
     * @access  public
     * @since   3.0.0
     */
    function getParameter($key)
    {
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }
    }

    /**
     * POST/GET���ͤ��ֵ�(���֥������Ȥ��ֵ�)
     *
     * @param   string  $key    �ѥ�᡼��̾
     * @return  Object  �ѥ�᡼������
     * @access  public
     * @since   3.0.0
     */
    function &getParameterRef($key)
    {
        if (isset($this->_params[$key])) {
            return $this->_params[$key];
        }
    }

    /**
     * POST/GET���ͤ򥻥å�
     *
     * @param   string  $key    �ѥ�᡼��̾
     * @param   string  $value  �ѥ�᡼������
     * @access  public
     * @since   3.0.0
     */
    function setParameter($key, $value)
    {
        $this->_params[$key] = $value;
    }

    /**
     * POST/GET���ͤ򥻥å�(���֥������Ȥ򥻥å�)
     *
     * @param   string  $key    �ѥ�᡼��̾
     * @param   Object  $value  �ѥ�᡼������
     * @access  public
     * @since   3.0.0
     */
    function setParameterRef($key, &$value)
    {
        $this->_params[$key] =& $value;
    }

    /**
     * POST/GET���ͤ��ֵ�(������ֵ�)
     *
     * @param   string  $key    �ѥ�᡼��̾
     * @return  string  �ѥ�᡼������(����)
     * @access  public
     * @since   3.0.0
     */
    function getParameters()
    {
        return $this->_params;
    }

    /**
     * �����������ڤ�ʬ����
     *
     * �ե��������submit�ܥ���2�Ĥ��äƤ���櫓������
     * �����������ڤ�ʬ��
     * <input type="submit" name="dispatch_A_B_C_D" value="OK">
     *   �� action �� A_B_C_D �ˤ��꤫��
     * <input type="submit" name="dispatch_E_F_G_H" value="OK">
     *   �� action �� E_F_G_H �ˤ��꤫��
     *
     * @access  public
     * @since   3.0.0
     */
    function dispatchAction()
    {
        $params = $this->getParameters();

        if (count($params) < 1) {
            return;
        }

        foreach ($params as $key => $value) {
            if (preg_match("/^dispatch_/", $key)) {
                $action = preg_replace("/^dispatch_/", "", $key);
                $action = preg_replace("/_x$/", "", $action);
                $action = preg_replace("/_y$/", "", $action);
                $this->setParameter(ACTION_KEY, $action);
                break;
            }
        }
    }
}
?>
