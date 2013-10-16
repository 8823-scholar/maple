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
 * @version     CVS: $Id: Router.class.php,v 1.1 2006/08/30 13:22:01 hawkring Exp $
 */

/**
 * Generator�����usage���Ф��ƥꥯ�����Ȥ�ž������
 * 
 * @package     Maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 */
class Maple_Generate_Router
{
    var $manager;

    var $dto;
    
    /**
     * generator_name�ΰ��֤�'help'�����ꤵ��Ƥ�����硢
     * usage���Ф��ƥꥯ�����Ȥ�ž������
     * 
     * @access  public
     * @since   3.1.0
     */
    function execute()
    {
        $generator_name = $this->dto->generator_name;

        if($generator_name == 'help') {
            return 'usage';
        }
        if($this->manager->exists($generator_name)) {
            return $generator_name;
        }
        return 'usage';
    }

}
?>
