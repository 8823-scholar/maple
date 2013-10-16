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
 * @version     CVS: $Id: Session.class.php,v 1.8 2006/02/12 14:28:42 kunit Exp $
 */

/**
 * Session������Ԥ�
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class Session
{
    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function Session()
    {
    }

    /**
     * ���ꤵ��Ƥ����ͤ��ֵ�
     *
     * @param   string  $key    �ѥ�᡼��̾
     * @return  string  �ѥ�᡼������
     * @access  public
     * @since   3.0.0
     */
    function getParameter($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }

    /**
     * ���ꤵ��Ƥ����ͤ��ֵ�(���֥������Ȥ��ֵ�)
     *
     * @param   string  $key    �ѥ�᡼��̾
     * @return  Object  �ѥ�᡼������
     * @access  public
     * @since   3.0.0
     */
    function &getParameterRef($key)
    {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }

    /**
     * �ͤ򥻥å�
     *
     * @param   string  $key    �ѥ�᡼��̾
     * @param   string  $value  �ѥ�᡼������
     * @access  public
     * @since   3.0.0
     */
    function setParameter($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * �ͤ򥻥å�(���֥������Ȥ򥻥å�)
     *
     * @param   string  $key    �ѥ�᡼��̾
     * @param   Object  $value  �ѥ�᡼������
     * @access  public
     * @since   3.0.0
     */
    function setParameterRef($key, &$value)
    {
        $_SESSION[$key] =& $value;
    }

    /**
     * �ͤ��ֵ�(������ֵ�)
     *
     * @param   string  $key    �ѥ�᡼��̾
     * @return  string  �ѥ�᡼������(����)
     * @access  public
     * @since   3.0.0
     */
    function getParameters()
    {
        if (isset($_SESSION)) {
            return $_SESSION;
        }
    }

    /**
     * �ͤ�������
     *
     * @param   string  $key    �ѥ�᡼��̾
     * @access  public
     * @since   3.0.0
     */
    function removeParameter($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * ���å��������򳫻�
     *
     * @access  public
     * @since   3.0.0
     */
    function start()
    {
        @session_start();
    }

    /**
     * ���å���������λ
     *
     * @access  public
     * @since   3.0.0
     */
    function close()
    {
        $_SESSION = array();
        session_destroy();
    }

    /**
     * ���å����̾���ֵ�
     *
     * @return  string  ���å����̾
     * @access  public
     * @since   3.0.0
     */
    function getName()
    {
        return session_name();
    }

    /**
     * ���å����̾�򥻥å�
     *
     * @param   string  $name   ���å����̾
     * @access  public
     * @since   3.0.0
     */
    function setName($name = '')
    {
        if ($name) {
            session_name($name);
        }
    }

    /**
     * ���å����ID���ֵ�
     *
     * @return  string  ���å����ID
     * @access  public
     * @since   3.0.0
     */
    function getID()
    {
        return session_id();
    }

    /**
     * ���å����ID�򥻥å�
     *
     * @param   string  $id ���å����ID
     * @access  public
     * @since   3.0.0
     */
    function setID($id = '')
    {
        if ($id) {
            session_id($id);
        }
    }

    /**
     * save_path�򥻥å�
     *
     * @param   string  $savePath   save_path
     * @access  public
     * @since   3.0.0
     */
    function setSavePath($savePath)
    {
        if (!isset($savePath)) {
            return;
        }
        session_save_path($savePath);
    }

    /**
     * cache_limiter�򥻥å�
     *
     * @param   string  $cacheLimiter   cache_limiter
     * @access  public
     * @since   3.0.0
     */
    function setCacheLimiter($cacheLimiter)
    {
        if (!isset($cacheLimiter)) {
            return;
        }
        session_cache_limiter($cacheLimiter);
    }

    /**
     * cache_expire�򥻥å�
     *
     * @param   string  $cacheExpire    cache_expire
     * @access  public
     * @since   3.0.0
     */
    function setCacheExpire($cacheExpire)
    {
        if (!isset($cacheExpire)) {
            return;
        }
        session_cache_expire($cacheExpire);
    }

    /**
     * use_cookies �򥻥å�
     *
     * @param   string  $useCookies use_cookies 
     * @access  public
     * @since   3.0.1
     */
    function setUseCookies($useCookies)
    {
        if (!isset($useCookies)) {
            return;
        }
        ini_set('session.use_cookies', $useCookies ? 1 : 0);
    }

    /**
     * cookie_lifetime �򥻥å�
     *
     * @param   string  $cookieLifetime cookie_lifetime
     * @access  public
     * @since   3.0.1
     */
    function setCookieLifetime($cookieLifetime)
    {
        if (!isset($cookieLifetime)) {
            return;
        }

        $cookie_params = session_get_cookie_params();
        session_set_cookie_params($cookieLifetime, $cookie_params['path'], $cookie_params['domain'], $cookie_params['secure']);
    }

    /**
     * cookie_path �򥻥å�
     *
     * @param   string  $cookiePath cookie_path
     * @access  public
     * @since   3.0.1
     */
    function setCookiePath($cookiePath)
    {
        if (!isset($cookiePath)) {
            return;
        }

        $cookie_params = session_get_cookie_params();
        session_set_cookie_params($cookie_params['lifetime'], $cookiePath, $cookie_params['domain'], $cookie_params['secure']);
    }

    /**
     * cookie_domain �򥻥å�
     *
     * @param   string  $cookieDomain   cookie_domain
     * @access  public
     * @since   3.0.1
     */
    function setCookieDomain($cookieDomain)
    {
        if (!isset($cookieDomain)) {
            return;
        }

        $cookie_params = session_get_cookie_params();
        session_set_cookie_params($cookie_params['lifetime'], $cookie_params['path'], $cookieDomain, $cookie_params['secure']);
    }

    /**
     * cookie_secure �򥻥å�(SSL���ѻ��ʤɤ�secure°�������ꤹ��)
     *
     * @param   string  $cookieSecure   cookie_secure
     * @access  public
     * @since   3.0.1
     */
    function setCookieSecure($cookieSecure)
    {
        if (!isset($cookieSecure)) {
            return;
        }

        if (preg_match('/^true$/i', $cookieSecure) ||
            preg_match('/^secure$/i', $cookieSecure) ||
            preg_match('/^on$/i', $cookieSecure) ||
            ($cookieSecure === '1') || ($cookieSecure === 1)) {
            $cookieSecure = true;
        } else {
            $cookieSecure = false;
        }

        $cookie_params = session_get_cookie_params();
        session_set_cookie_params($cookie_params['lifetime'], $cookie_params['path'], $cookie_params['domain'], $cookieSecure);
    }
}
?>
