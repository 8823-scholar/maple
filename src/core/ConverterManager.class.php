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
 * @version     CVS: $Id: ConverterManager.class.php,v 1.5 2006/08/30 12:48:26 hawkring Exp $
 */

require_once MAPLE_DIR .'/converter/Converter.interface.php';

/**
 * Converter��������륯�饹
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class ConverterManager
{
    /**
     * @var Converter���ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_list;

    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function ConverterManager()
    {
        $this->_list = array();
    }

    /**
     * Convert��Ԥ�
     *
     * @param   array   $params Convert�����郎���ä�����
     * @access  public
     * @since   3.0.0
     */
    function execute($params)
    {
        if (!is_array($params) || (count($params) < 1)) {
            return true;
        }

        // Converter��List������
        $this->_buildConverterList($params);

        //
        // Convert��¹�
        //
        $this->_convert($params);

        return true;
    }

    /**
     * Converter��List������
     *
     * @param   array   $params Convert�����郎���ä�����
     * @access  private
     * @since   3.0.0
     */
    function _buildConverterList($params)
    {
        $log =& LogFactory::getLog();

        foreach ($params as $key => $value) {
            $key   = preg_replace("/\s+/", "", $key);
            $value = preg_replace("/\s+/", "", $value);

            if ($key == "") {
                $log->error("Converter�λ��꤬�����Ǥ�", "ConverterManager#_buildConverterList");
                continue;
            }

            //
            // $key �� attribute.name �Υѥ�����
            //
            $keyArray = explode(".", $key);
            if (count($keyArray) != 2) {
                break;
            }
            $attribute = $keyArray[0];     // °����̾��
            $name      = $keyArray[1];     // Converter��̾�� 

            $className = "Converter_" . ucfirst($name);
            $filename  = CONVERTER_DIR . "/${className}.class.php";

            if (!(@include_once $filename) or !class_exists($className)) {
                $log->error("¸�ߤ��Ƥ��ʤ�Converter�����ꤵ��Ƥ��ޤ�(${name})", "ConverterManager#_buildConverterList");
                continue;
            }

            //
            // ����Ʊ̾��Converter���ɲä���Ƥ����鲿�⤷�ʤ�
            //
            if (isset($this->_list[$name]) && is_object($this->_list[$name])) {
                continue;
            }

            //
            // ���֥������Ȥ������˼��Ԥ��Ƥ����饨�顼
            //
            $converter =& new $className();

            if (!is_object($converter)) {
                $log->error("Converer�������˼��Ԥ��ޤ���(${name})", "ConverterManager#_buildConverterList");
                return false;
            }

            $this->_list[$name] =& $converter;
        }
    }

    /**
     * Converter��¹�
     *
     * @param   array   $params Convert�����郎���ä�����
     * @access  private
     * @since   3.0.0
     */
    function _convert($params)
    {
        $log =& LogFactory::getLog();

        foreach ($params as $key => $value) {
            $key   = preg_replace("/\s+/", "", $key);
            $value = preg_replace("/\s+/", "", $value);

            if ($key == "") {
                $log->error("Converter�λ��꤬�����Ǥ�", "ConverterManager#_convert");
                continue;
            }

            //
            // $key �� attribute.name �Υѥ�����
            //
            $keyArray = explode(".", $key);
            if (count($keyArray) != 2) {
                break;
            }
            $attribute = $keyArray[0];     // °����̾��
            $name      = $keyArray[1];     // Converter��̾�� 

            //
            // $value �ˤ�Convert����ͤ�������ѿ�̾�����åȤǤ���
            //
            $newAttribute = $value;

            //
            // Converter�����
            //
            $converter =& $this->_list[$name];

            if (!is_object($converter)) {
                continue;
            }

            //
            // attribute�� * �����ꤵ��Ƥ������
            // �ꥯ�����ȥѥ�᡼�����Ƥ��Ѵ��оݤȤʤ�
            //
            $container =& DIContainerFactory::getContainer();
            $request =& $container->getComponent("Request");

            if ($attribute == '*') {
                $attribute = join(",", array_keys($request->getParameters()));
            }

            if (preg_match("/,/", $attribute)) {
                $attributes = array();
                foreach (explode(",", $attribute) as $param) {
                    if ($param) {
                       $attributes[$param] = $request->getParameter($param);
                    }
                }
            } else {
                $attributes = $request->getParameter($attribute);
            }

            //
            // Converter��Ŭ��
            //
            $result = $converter->convert($attributes);

            if ($newAttribute != "") {
                $request->setParameter($newAttribute, $result);
            } else {
                if (is_array($attributes)) {
                    foreach ($result as $key => $value) {
                        if ($key) {
                            $request->setParameter($key, $value);
                        }
                    }
                } else {
                    $request->setParameter($attribute, $result);
                }
            }
        }
    }
}
?>
