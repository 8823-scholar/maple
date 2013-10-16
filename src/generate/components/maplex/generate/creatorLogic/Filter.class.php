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
 * @version     CVS: $Id: Filter.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

require_once('maplex/generate/creatorLogic/SingleFile.class.php');

/**
 * filter craetorLogic
 *
 * @package     Maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 */
class Maplex_Generate_CreatorLogic_Filter extends Maplex_Generate_CreatorLogic_SingleFile
{
    function create(&$dto)
    {
        $classname = "Filter_" . ucfirst($dto->filterName);
        $filename  = $this->config->getValue('COMPONENT_DIR') ."/".
                     $this->config->getValue('FILTER_DIR') ."/${classname}.class.php";

        return $this->output(
            $filename,
            array('classname' => $classname));
    }
}

?>
