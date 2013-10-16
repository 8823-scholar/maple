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
 * @package     Maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: GeneratorManager.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

/**
 * Generator�δ�����Ԥ�
 * 
 * @package maple.generate
 * @author Hawk <scholar@hawklab.jp>
 */
class GeneratorManager
{
    /**
     * @var  String  $_modulePath  
     */
    var $_modulePath = 'maple/generate/generators/';
    
    /**
     * array(<generator_name> =>
     *   array(
     *      'name' => <generator_name>,
     *      'classFile' => full-path to a class file,
     *      'usage_template' => template file,
     *      'action' => action name
     *    ),...
     * )
     * 
     * @var  array  $_generators  
     */
    var $_generators = array();

    /**
     * constructor
     * 
     * @access public
     */
    function GeneratorManager()
    {
        $this->_collectGenerators();
    }

    /**
     * ���Ƥ�generator�ξ���򽸤��
     * 
     * @access private
     * @return array
     */
    function _collectGenerators()
    {
        $classes   = glob(MODULE_DIR .'/'. $this->_modulePath  .'*/*.class.php');

        foreach($classes as $actionClassFile) {
            $name = basename(dirname($actionClassFile));

            $this->_generators[$name] = array(
                'name' => $name,
                'classFile' => $actionClassFile,
                'usage_template' => $this->_modulePath . $name .".txt",
                'action' => str_replace('/', '_', $this->_modulePath) . $name
                );
        }
    }

    /**
     * 
     * @since 06/07/30 16:07
     * @return array
     */
    function getAllGeneratorInfo()
    {
        return $this->_generators;
    }

    /**
     * ���Ƥ�Generator��̾��������Ȥ����֤�
     * 
     * @access public
     * @return array
     */
    function getAllGeneratorNames()
    {
        return array_keys($this->_generators);
    }

    /**
     * Generator��¸�ߤ��뤫�ɤ���Ĵ�٤�
     * 
     * @since 06/07/29 12:35
     * @param  String    $generator_name
     * @return boolean
     */
    function exists($generator_name)
    {
        return isset($this->_generators[$generator_name]);
    }

    /**
     * Generator�ˤĤ��Ƥξ�����������
     * 
     * @since 06/07/29 14:22
     * @return array
     */
    function getGeneratorInfo($generator_name)
    {
        if($this->exists($generator_name)) {
            return $this->_generators[$generator_name];
        } else {
            return null;
        }
    }
}

?>
