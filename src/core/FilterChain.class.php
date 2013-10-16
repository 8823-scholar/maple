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
 * @version     CVS: $Id: FilterChain.class.php,v 1.8 2006/08/30 12:48:26 hawkring Exp $
 */

require_once FILTER_DIR . '/Filter.interface.php';

/**
 * Filter���ݻ����륯�饹
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class FilterChain
{
    /**
     * @var Filter���ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_list;

    /**
     * @var Filter�ΰ��֤��ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_position;

    /**
     * @var ���߼¹Ԥ���Ƥ���Filter�ΰ��֤��ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_index;

    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function FilterChain()
    {
        $this->_list     = array();
        $this->_position = array();
        $this->_index    = -1;
    }

    /**
     * FilterChain�κǸ��Filter���ɲ�
     *
     * @param   string  $name   Filter�Υ��饹̾
     * @param   string  $alias  Filter�Υ����ꥢ��̾
     * @access  public
     * @since   3.0.0
     */
    function add($name, $alias = '')
    {
        $log =& LogFactory::getLog();

        //
        // �����ꥢ��̾�����ꤵ��Ƥ��ʤ����ϥ��饹̾�򥻥å�
        //
        if (empty($alias)) {
            $alias = $name;
        }

        //
        // Filter�μ¹Ԥ����˻ϤޤäƤ����饨�顼(�¹Ը���ɲäϥ��顼)
        //
        if ($this->_index > -1) {
            $log->error("�¹Ը��Filter���ɲä���Ƥ��ޤ�(${name}[alias:${alias}])", "FilterChain#add");
            return false;
        }

        //
        // Filter�Υ��饹̾���������ä��饨�顼
        //
        if (!preg_match("/^[0-9a-zA-Z_]+$/", $name)) {
            $log->error("������Filter�����ꤵ��Ƥ��ޤ�(${name}[alias:${alias}])", "FilterChain#add");
            return false;
        }

        //
        // �ե����뤬¸�ߤ��Ƥ��ʤ���Х��顼
        //
        $className = "Filter_" . ucfirst($name);
        $filename  = FILTER_DIR . "/${className}.class.php";

        if (!(@include_once $filename) or !class_exists($className)) {
            $log->error("¸�ߤ��Ƥ��ʤ�Filter�����ꤵ��Ƥ��ޤ�(${name}[alias:${alias}])", "FilterChain#add");
            return false;
        }

        //
        // ����Ʊ̾��Filter���ɲä���Ƥ����鲿�⤷�ʤ�
        //
        if (isset($this->_list[$alias]) && is_object($this->_list[$alias])) {
            $log->info("����Filter�ϴ�����Ͽ����Ƥ��ޤ�(${name}[alias:${alias}])", "FilterChain#add");
            return true;
        }

        //
        // ���֥������Ȥ������˼��Ԥ��Ƥ����饨�顼
        //
        $filter =& new $className();

        if (!is_object($filter)) {
            $log->error("Filter�������˼��Ԥ��ޤ���(${name}[alias:${alias}])", "FilterChain#add");
            return false;
        }

        $this->_list[$alias] =& $filter;
        $this->_position[]   =  $alias;

        return true;
    }

    /**
     * FilterChain�򥯥ꥢ
     *
     * @access  public
     * @since   3.0.0
     */
    function clear()
    {
        $this->_list     = array();
        $this->_position = array();
        $this->_index    = -1;
    }

    /**
     * FilterChain��Ĺ�����ֵ�
     *
     * @return  integer FilterChain��Ĺ��
     * @access  public
     * @since   3.0.0
     */
    function getSize()
    {
        return count($this->_list);
    }

    /**
     * ���ߤ�Filter̾���ֵ�
     *
     * @return  string  Filter��̾��
     * @access  public
     * @since   3.0.0
     */
    function getCurFilterName()
    {
        if (isset($this->_position[$this->_index])) {
            return $this->_position[$this->_index];
        }
    }

    /**
     * ���ꤵ�줿̾����Filter���ֵ�
     *
     * @return  Object  Filter�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function &getFilterByName($name)
    {
        $log =& LogFactory::getLog();
        $result = false;

        if ($name == "") {
            $log->warn("�����������Ǥ�", "FilterChain#getFilterByName");
            return $result;
        }

        if (!isset($this->_list[$name]) || !is_object($this->_list[$name])) {
            $log->error("���ꤵ�줿Filter����Ͽ����Ƥ��ޤ���(${name})", "FilterChain#getFilterByName");
            return $result;
        }

        $filter =& $this->_list[$name];

        if (!is_object($filter)) {
            $log->error("Filter�μ����˼��Ԥ��ޤ���(${name})", "FilterChain#getFilterByname");
            return $result;
        }

        return $filter;
    }

    /**
     * ���ꤷ��Filter��°���򥻥å�
     *
     * @param   string  $name   Filter��̾��
     * @param   array   $attributes ���åȤ���°��(����)
     * @access  public
     * @since   3.0.0
     */
    function setAttributes($name, $attributes)
    {
        $filter =& $this->getFilterByname($name);

        if (!is_object($filter)) {
        	$log =& LogFactory::getLog();
            $log->error("Filter�μ����˼��Ԥ��ޤ���(${name})", "FilterChain#setAttributes");
            return false;
        }

        return $filter->setAttributes($attributes);
    }

    /**
     * FilterChain���Ȥ�Ω�Ƥ�
     *
     * @param   Object  $config ConfigUtils�Υ��󥹥���
     * @access  public
     * @since   3.0.0
     */
    function build(&$config)
    {
        $log =& LogFactory::getLog();

        foreach ($config->getConfig() as $section => $value) {
            $sections = explode(':', $section);
            $filterName = $sections[0]; // �ե��륿̾
            if (isset($sections[1]) && $sections[1]) { // ȯư����REQUEST_METHOD
                $method = strtoupper($sections[1]);
            } else {
                $method = 'BOTH';
            }
            if (isset($sections[2]) && $sections[2]) { // �����ꥢ��̾
                $alias = $sections[2];
            } else {
                $alias = $filterName;
            }

            if (($method == 'BOTH') ||
                ($method == $_SERVER['REQUEST_METHOD'])) {
                $filterConfig =& $config->getSectionConfig($section);
                if (!$this->add($filterName, $alias)) {
                    $log->error("FilterChain�ؤ��ɲä˼��Ԥ��ޤ���(${section})", "FilterChain#build");
                    return false;
                }
                if (is_array($filterConfig) && (count($filterConfig) > 0)) {
                    $this->setAttributes($alias, $filterConfig);
                }
            }
        }

        return true;
    }

    /**
     * FilterChain����μ���Filter��¹�
     *
     * ���Υ᥽�åɤϥ��饹�᥽�å�
     *
     * @access  public
     * @since   3.0.0
     */
    function execute()
    {
        $log =& LogFactory::getLog();

        if ($this->getSize() < 1) {
            $log->error("Filter���ɲä���Ƥ��ޤ���", "FilterChain#execute");
            return false;
        }

        if ($this->_index < ($this->getSize() - 1)) {
            $this->_index++;

            $name = $this->getCurFilterName();
            $filter =& $this->getFilterByname($name);

            if (!is_object($filter)) {
                $log->error("Filter�μ����˼��Ԥ��ޤ���(${name})", "FilterChain#execute");
                return false;
            }

            return $filter->execute();
        }

        return true;
    }
}
?>
