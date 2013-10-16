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
 * @version     CVS: $Id: Action.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

require_once('maplex/generate/creatorLogic/Abstract.class.php');

/**
 * action関連ファイルを書き出すcraetorLogic
 *
 * @package     Maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 */
class Maplex_Generate_CreatorLogic_Group_Action
 extends Maplex_Generate_CreatorLogic_Abstract
{
    var $actionCreator;
    var $configCreator;
    var $templateCreator;
    var $entryCreator;

    /**
     * action generatorのロジックを実行する
     *
     * @param  object  $dto    DTOクラスのインスタンス
     * @access  public
     */
    function create(&$dto)
    {
        $fileList = array();
        $fileList += $this->actionCreator->create($dto);

        if (!$dto->templateType) {
            return $fileList;
        }

        $fileList += $this->configCreator->create($dto);
        $fileList += $this->templateCreator->create($dto);

        if (!$dto->entryName) {
            return $fileList;
        }

        $fileList += $this->entryCreator->create($dto);
        return $fileList;
    }
}
?>
