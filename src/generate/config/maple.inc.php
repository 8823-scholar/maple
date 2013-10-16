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
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: maple.inc.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

//
//���ܤȤʤ�ǥ��쥯�ȥ������
//
if (!defined('BASE_DIR')) {
	define('BASE_DIR', dirname(dirname(dirname(__FILE__))));
}
define('WEBAPP_DIR', dirname(dirname(__FILE__)));
define('MAPLE_DIR',  'maple');

//
//���ܤȤʤ�������ɤ߹���
//
require_once(MAPLE_DIR .'/config/common.php');
require_once(MAPLE_DIR .'/core/GlobalConfig.class.php');
GlobalConfig::loadConstantsFromFile(dirname(__FILE__) .'/'. GLOBAL_CONFIG);

//
//Flexy������ʤɡ������ɥ١���������Ϥ����ǹԤ�
//
/*
require_once(MAPLE_DIR .'/flexy/Flexy_Flexy4Maple.class.php');

Flexy_Flexy4Maple::globalOptions(array(
   "allowPHP" => false,
   "globals"  => true,
   "debug"    => true
));
*/

//
//include_path�������Controller���ɤ߹���
//
ini_set('include_path', COMPONENT_DIR . PATH_SEPARATOR . ini_get('include_path'));
require_once(MAPLE_DIR .'/core/Controller.class.php');

?>
