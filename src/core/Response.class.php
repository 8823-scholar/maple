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
 * @version     CVS: $Id: Response.class.php,v 1.4 2006/02/11 19:18:22 kunit Exp $
 */

/**
 * ���Ϥ�������륯�饹
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Response
{
    /**
     * @var Content-disposition���ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_contentDisposition;

    /**
     * @var Content-Type���ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_contentType;

    /**
     * @var Result���ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_result;

    /**
     * @var View���ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_view;

    /**
     * @var redirect����ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_redirect;

    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function Response()
    {
        $this->_contentDisposition = NULL;
        $this->_contentType        = NULL;
        $this->_result             = NULL;
        $this->_view               = NULL;
        $this->_redirect           = NULL;
    }

    /**
     * contentDisposition���ͤ��ֵ�
     *
     * @return  string  contentDisposition����
     * @access  public
     * @since   3.0.0
     */
    function getContentDisposition()
    {
        return $this->_contentDisposition;
    }

    /**
     * contentDisposition���ͤ򥻥å�
     *
     * @param   string  $contentDisposition contentDisposition����
     * @access  public
     * @since   3.0.0
     */
    function setContentDisposition($contentDisposition)
    {
        $this->_contentDisposition = $contentDisposition;
    }

    /**
     * contentType���ͤ��ֵ�
     *
     * @return  string  contentType����
     * @access  public
     * @since   3.0.0
     */
    function getContentType()
    {
        return $this->_contentType;
    }

    /**
     * contentType���ͤ򥻥å�
     *
     * @param   string  $contentType    contentType����
     * @access  public
     * @since   3.0.0
     */
    function setContentType($contentType)
    {
        $this->_contentType = $contentType;
    }

    /**
     * result���ͤ��ֵ�
     *
     * @return  string  result����
     * @access  public
     * @since   3.0.0
     */
    function getResult()
    {
        return $this->_result;
    }

    /**
     * result���ͤ򥻥å�
     *
     * @param   string  $result result����
     * @access  public
     * @since   3.0.0
     */
    function setResult($result)
    {
        $this->_result = $result;
    }

    /**
     * view���ͤ��ֵ�
     *
     * @return  string  view����
     * @access  public
     * @since   3.0.0
     */
    function getView()
    {
        return $this->_view;
    }

    /**
     * view���ͤ򥻥å�
     *
     * @param   string  $view   view����
     * @access  public
     * @since   3.0.0
     */
    function setView($view)
    {
        $this->_view = $view;
    }

    /**
     * Redirect���ͤ��ֵ�
     *
     * @return  string  redirect����
     * @access  public
     * @since   3.0.0
     */
    function getRedirect()
    {
        return $this->_redirect;
    }

    /**
     * Redirect���ͤ򥻥å�
     *
     * @param   string  $redirect   redirect����
     * @access  public
     * @since   3.0.0
     */
    function setRedirect($redirect)
    {
        $this->_redirect = $redirect;
    }

    /**
     * redirect��򥻥å�
     *
     * ���Υ᥽�åɤϥ��饹�᥽�å�
     *
     * @access  public
     * @since   3.0.0
     */
    function redirect($redirect)
    {
        $container =& DIContainerFactory::getContainer();
        $response =& $container->getComponent("Response");
        $response->setRedirect($redirect);
    }
}
?>
