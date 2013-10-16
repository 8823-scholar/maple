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
 * @version     CVS: $Id: Token.class.php,v 1.5 2006/05/02 09:23:20 hawkring Exp $
 */

/**
 * Token������Ԥ�
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Token
{
    /**
     * @var Token��̾�����ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_name;

    /**
     * @var Session�Υ��󥹥��󥹤��ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_session;

    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function Token()
    {
        $this->_name = "";
        $this->_session = NULL;
    }

    /**
     * Session�Υ��󥹥��󥹤򥻥å�
     *
     * @param   Object  $session    Session�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function setSession(&$session)
    {
        $this->_session =& $session;
    }

    /**
     * Token��̾�����ֵ�
     *
     * @return  string  Token��̾��
     * @access  public
     * @since   3.0.0
     */
    function getName()
    {
        if ($this->_name == "") {
            $this->_name = "mapleToken";
        }

        return $this->_name;
    }

    /**
     * Token��̾��������
     *
     * @param   string  $name   Token��̾��
     * @access  public
     * @since   3.0.0
     */
    function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * Token���ͤ��ֵ�
     *
     * @return  string  Token���ͤ��ֵ�
     * @access  public
     * @since   3.0.0
     */
    function getValue()
    {
        return $this->_session->getParameter($this->getName());
    }

    /**
     * Token���ͤ�����
     *
     * @access  public
     * @since   3.0.0
     */
    function build()
    {
        $this->_session->setParameter($this->getName(), md5(uniqid(rand(),1)));
    }

    /**
     * Token���ͤ����
     *
     * @param   Object  $value  Request���饹�Υ��󥹥���
     * @return  boolean Token���ͤ����פ��뤫��
     * @access  public
     * @since   3.0.0
     */
    function check(&$request)
    {
        return (($this->getValue() != '') &&
                $this->getValue() == $request->getParameter($this->getName()));
    }

    /**
     * Token���ͤ���
     *
     * @access  public
     * @since   3.0.0
     */
    function remove()
    {
        $this->_session->removeParameter($this->getName());
    }
}
?>
