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
 * @version     CVS: $Id: Abstract.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

/**
 * craetorLogic�Υ��󥿡��ե���������
 *
 * @package     Maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @abstract
 */
class Maplex_Generate_CreatorLogic_Abstract
{
    /**
     * @var  GlobalConfig  $config  
     */
    var $config;

    /**
     * ���Υ᥽�åɤ���Ǥ�뤳�Ȥ�
     * 
     * 1. outputFile̾����������
     * 2. template��ɬ�פȤ�������Ϣ������ˤޤȤ�
     * 3. output�᥽�åɤ�Ϳ����
     * 
     * ʣ����creatorLogic��composite�ξ���
     * 
     * 1. ��˻�creatorLogic->create($dto)��ƤӽФ��ơ�
     * 2. ��̤��Ĥ�����ˤޤȤ��
     * 
     * @abstract
     * @param  Object $dto
     * @return array
     */
    function create(&$dto)
    {
        
    }

    /**
     * create�Υ��Υ˥�
     * 
     * @param  Object $dto
     * @return array
     */
    function execute(&$dto)
    {
        return $this->create($dto);
    }
}
?>
