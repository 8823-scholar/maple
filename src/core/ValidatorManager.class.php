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
 * @version     CVS: $Id: ValidatorManager.class.php,v 1.8 2006/08/30 12:48:26 hawkring Exp $
 */

require_once VALIDATOR_DIR . '/Validator.interface.php';

/**
 * Validator��������륯�饹
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class ValidatorManager
{
    /**
     * @var Validator���ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_validators;

    /**
     * @var ɬ�ܹ��ܤ��ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_required;

    /**
     * @var Validate�롼����ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_list;

    /**
     * @var stopper�ξ��֤��ݻ�����
     *
     * @access  private
     * @since   3.0.0
     */
    var $_stoppers;

    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.0.0
     */
    function ValidatorManager()
    {
        $this->_validators = array();
        $this->_required   = array();
        $this->_list       = array();
    }

    /**
     * Validate��Ԥ�
     *
     * @param   array   $params Validate�����郎���ä�����
     * @access  public
     * @since   3.0.0
     */
    function execute($params)
    {
        if (!is_array($params) || (count($params) < 1)) {
            return true;
        }

        // Validator��List������
        $this->_buildValidatorList($params);

        // Validate��¹�
        $this->_validate($params);

        return true;
    }

    /**
     * Validator��List������
     *
     * @param   array   $params Validate�����郎���ä�����
     * @access  private
     * @since   3.0.0
     */
    function _buildValidatorList($params)
    {
        $log =& LogFactory::getLog();

        foreach ($params as $key => $value) {
            $key   = preg_replace("/\s+/", "", $key);
            $value = preg_replace('/\s*,\s*/', ",", trim($value));

            if (($key == "") || ($value == "")) {
                $log->error("Validator�λ��꤬�����Ǥ�", "ValidatorManager#_buildValidatorList");
                continue;
            }

            //
            // $key �� attribute.name:group �Υѥ�����
            //
            $keyArray = explode(".", $key);
            if (count($keyArray) != 2) {
                break;
            }
            $attribute = $keyArray[0]; // °����̾��

            if (preg_match("/:/", $keyArray[1])) {
                $keySubArray = explode(":", $keyArray[1]);
                $name  = $keySubArray[0]; // Validator��̾�� 
                $group = $keySubArray[1]; // ValidateGroup��̾��
            } else {
                $name  = $keyArray[1]; // Validator��̾�� 
                $group = "";
            }

            //
            // $value �� stopper,errStr,....(validateParams) �Υѥ�����
            //
            $valueArray = explode(",", $value);
            if (count($valueArray) < 2) {
                break;
            }
            $stopper = $valueArray[0]; // ���ȥåѡ����ɤ�����
            $errStr  = $valueArray[1]; // ���顼ʸ����
            $validateParams = array();
            if (count($valueArray) > 2) {
                $validateParams = array_slice($valueArray, 2);
            }

            //
            // ɬ�ܹ��ܤ�̵��凉�ȥåѡ��ˤʤ�
            //
            if ($name == "required") {
                $this->_required[$attribute] = true;
                $stopper = true;
            }

            //
            // ValidateRule���Ȥ�Ω��
            //
            $validateRule = array(
                'attribute' => $attribute,
                'name'      => $name,
                'stopper'   => $stopper,
                'errStr'    => $errStr,
                'params'    => $validateParams,
            );

            if ($group) {
                $this->_list[$group][$attribute][] = $validateRule;
            } else {
                $this->_list[$attribute][] = $validateRule;
            }

            //
            // Validator�Υե����뤬���뤫������å�
            //
            $className = "Validator_" . ucfirst($name);
            $filename  = VALIDATOR_DIR . "/${className}.class.php";

            if (!(@include_once $filename) or !class_exists($className)) {
                $log->error("¸�ߤ��Ƥ��ʤ�Validator�����ꤵ��Ƥ��ޤ�(${name})", "ValidatorManager#_buildValidatorList");
                return false;
            }

            //
            // ����Ʊ̾��Validator���ɲä���Ƥ����鲿�⤷�ʤ�
            //
            if (isset($this->_validators[$name]) &&
                is_object($this->_validators[$name])) {
                continue;
            }

            //
            // ���֥������Ȥ������˼��Ԥ��Ƥ����饨�顼
            //
            $validator =& new $className();

            if (!is_object($validator)) {
                $log->error("Converer�������˼��Ԥ��ޤ���(${name})", "ValidatorManager#_buildValidatorList");
                return false;
            }

            $this->_validators[$name] =& $validator;
        }
    }

    /**
     * Validate��¹�
     *
     * @access  private
     * @since   3.0.0
     */
    function _validate()
    {
        $container =& DIContainerFactory::getContainer();
        $actionChain =& $container->getComponent("ActionChain");
        $errorList =& $actionChain->getCurErrorList();

        foreach ($this->_list as $validateKey => $validateRules) {
            foreach ($validateRules as $value) {
                //
                // Validate�롼�뤬���롼�ԥ󥰤���Ƥ��뤫�ɤ�����ʬ��
                //
                if (isset($value["attribute"])) {
                    if (!$this->_execute($validateKey, $value)) {
                        break;
                    }
                } else {
                    foreach ($value as $subValue) {
                        if (!$this->_execute($validateKey, $subValue)) {
                            break;
                        }
                    }
                }
            }
        }

        if ($errorList->isExists()) {
            $errorList->setType(VALIDATE_ERROR_TYPE);
        }
    }

    /**
     * Validate��¹�(�롼��ñ��)
     *
     * @param   string  $validateKey    Validate�롼���̾��
     * @param   array   $validateRule   Validate�롼������ä�Ϣ������
     * @access  private
     * @since   3.0.0
     */
    function _execute($validateKey, $validateRule)
    {
        $container =& DIContainerFactory::getContainer();
        $actionChain =& $container->getComponent("ActionChain");
        $errorList =& $actionChain->getCurErrorList();

        $attribute = $validateRule["attribute"];
        $name      = $validateRule["name"];
        $stopper   = $validateRule["stopper"];
        $errStr    = $validateRule["errStr"];
        $params    = $validateRule["params"];

        //
        // ���ȥå׾��֤ˤʤäƤ���Х����å����ʤ�
        //
        if (isset($this->_stoppers[$validateKey]) &&
            ($this->_stoppers[$validateKey] == true)) {
            return false;
        }

        //
        // �ꥯ�����ȥѥ�᡼�������
        //
        $isEmpty = true;

        $container =& DIContainerFactory::getContainer();
        $request =& $container->getComponent("Request");

        if (preg_match("/,/", $attribute)) {
            $attributes = array();
            foreach (explode(",", $attribute) as $key) {
                $param = $request->getParameter($key);
                if ($param != "") {
                    $isEmpty = false;
                }
                $attributes[] = $param;
            }
        } else {
            $attributes = $request->getParameter($attribute);
            if ($attributes != "") {
                $isEmpty = false;
            }
        }

        //
        // ɬ�ܹ��ܤǤʤ��ơ��ͤ��Ϥ��äƤʤ���Х����å����ʤ�
        //
        if ($isEmpty && !isset($this->_required[$attribute])) {
            return false;
        }

        //
        // Validate�����
        //
        $validator =& $this->_validators[$name];

        if (!is_object($validator)) {
            return false;
        }

        //
        // Validator��Ŭ��
        //
        $result = $validator->validate($attributes, $errStr, $params);
        if ($result != "") {
            $errorList->add($validateKey, $result);

            //
            // ���ȥåѡ��ʤ�Ф��Υѥ�᡼���򵭲�
            //
            if ($stopper) {
                $this->_stoppers[$validateKey] = true;
            }
        }

        return true;
    }
}
?>
