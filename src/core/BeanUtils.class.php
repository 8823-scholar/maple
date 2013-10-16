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
 * @version     CVS: $Id: BeanUtils.class.php,v 1.8 2006/02/15 12:24:04 bobchin Exp $
 */

/**
 * �Ϥ��줿���饹��°�������ꤹ��
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class BeanUtils
{
    var $_pool = array();

    /**
     * ���ꤵ�줿���饹����°�������
     *
     * @param   Object  $instance   °���򥻥åȤ��륯�饹�Υ��󥹥���
     * @return  array   °������(����)
     * @access  public
     * @since   3.0.0
     */
    function getAttributes(&$instance)
    {
        if (!is_object($instance)) {
            return;
        }

        $getterVars = BeanUtils::getGetterVars($instance);

        $attributes = array();

        foreach ($getterVars as $key => $value) {
            $method = "get${key}";
            $attributes[$key] =& $instance->$method();
        }

        return $attributes;
    }

    /**
     * �Ϥ��줿���饹��°���򥻥å�
     * 
     * @param   Object  $instance   ���饹�Υ��󥹥���
     * @param   array   $attributes °������(����)
     * @param   boolean $nullCheck  ��񤭻���null���ɤ���������å����뤫��
     * @access  public
     * @since   3.0.0
     * @author  Hawk
     * @static
     */
    function setAttributes(&$instance, $attributes, $checkNull = false)
    {
        if (!is_object($instance) ||
            !is_array($attributes) ||(count($attributes) < 1)) {
            return;
        }

        $classVars = get_class_vars(get_class($instance));

        foreach ($attributes as $name => $value) {
            if (preg_match('/^_/', $name) ||
                !array_key_exists($name, $classVars)) {
                continue;
            }

            $setter = "set" . ucfirst($name);
            if (method_exists($instance, $setter)) {
                $instance->$setter($attributes[$name]);
            } elseif (!$checkNull ||
                      ($checkNull && (is_null($instance->$name)))) {
                if (is_object($value)) {
                    $instance->$name =& $attributes[$name];
                } else {
                    $instance->$name = $attributes[$name];
                }
            }
        }
    }

    /**
     * �Ϥ��줿���饹���ѿ����ֵ�
     *
     * @param   Object  $instance   ���饹�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function getVars(&$instance)
    {
        $varArray = array();

        foreach (get_class_vars(get_class($instance)) as $key => $value) {
            $varArray[strtolower($key)] = true;
        }

        return $varArray;
    }

    /**
     * �Ϥ��줿���饹��getter�������ѿ����ֵ�
     *
     * @param   Object  $instance   ���饹�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function getGetterVars(&$instance)
    {
        return BeanUtils::getMethods($instance, "get");
    }

    /**
     * �Ϥ��줿���饹��setter�������ѿ����ֵ�
     *
     * @param   Object  $instance   ���饹�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function getSetterVars(&$instance)
    {
        return BeanUtils::getMethods($instance, "set");
    }

    /**
     * ���ꤵ�줿ʸ���ǻϤޤ�᥽�å�̾�Υꥹ�Ȥ��ֵ�
     *
     * @param   Object  $instanse   ���饹�Υ��󥹥���
     * @param   string  $prefix     ��������᥽�å�̾�Υץ�ե��å���
     * @return  array   �᥽�å�̾�Υꥹ��
     * @access  public
     * @static
     */
    function getMethods(&$instance, $prefix)
    {
        $vars = BeanUtils::getVars($instance);

        $methods = array();

        foreach (get_class_methods(get_class($instance)) as $method) {
            if (preg_match("/^${prefix}/", $method)) {
                $attribute = strtolower(preg_replace("/^${prefix}/", "", $method));
                if (isset($vars[$attribute])) {
                    $methods[$attribute] = true;
                }
            }
        }

        return $methods;
    }

    /**
     * ���֥������Ȥ����󲽤���(��碌�ƥ��������׽����򤹤�)
     * 
     * @param array $values �оݤȤʤ�����
     * @param array $result �������
     * @param array $parent �ƥ��饹
     */
    function toArray(&$values, &$result, &$parent, $escape = true)
    {
        foreach ($values as $key => $value) {
            if (preg_match('/^_/', $key)) {
                continue;
            }

            if (is_object($parent)) {
                $getter = "get" . ucfirst($key);
                if (method_exists($parent, $getter)) {
                    $value = $parent->$getter();
                }
            }

            if (is_object($value)) {
                $class = get_class($value);
                if (array_key_exists($class, $this->_pool)) {
                    if (array_key_exists($key, $this->_pool[$class])) {
                        if (is_object($result)) {
                            unset($result->$key);
                        } else {
                            unset($result[$key]);
                        }
                        continue;
                    }
                }
                $this->_pool[$class][$key] = $key;
                $result[$key] = array();
                $this->toArray(get_object_vars($value), $result[$key], $value, $escape);
            } else if (is_array($value)) {
                $dummy = null;
                $result[$key] = array();
                $this->toArray($value, $result[$key], $dummy, $escape);
            } else {
                // �꥽�����ξ�票�顼���Ф뤿��ʸ����ΤȤ��Τ߼»�
                if ($escape && is_string($value)) {
                    $value = htmlspecialchars($value, ENT_QUOTES);
                }
                $result[$key] = $value;
            }
        }
    }
}
?>
