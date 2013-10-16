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
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: common.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

define('VALIDATE_ERROR_TYPE', 'input');
define('TOKEN_ERROR_TYPE', 'invalidToken');
define('UPLOAD_ERROR_TYPE', 'upload');

define('GLOBAL_CONFIG', 'global-config.ini');
define('CONFIG_FILE',   'maple.ini');
define('BASE_INI',      '/config/base.ini');

define('FILTER_DIR',    MAPLE_DIR . '/filter');
define('CONVERTER_DIR', MAPLE_DIR . '/converter');
define('VALIDATOR_DIR', MAPLE_DIR . '/validator');
define('LOGGER_DIR',    MAPLE_DIR . '/logger');

define('LEVEL_FATAL', 6);
define('LEVEL_ERROR', 5);
define('LEVEL_WARN',  4);
define('LEVEL_INFO',  3);
define('LEVEL_DEBUG', 2);
define('LEVEL_TRACE', 1);

if (!defined('PATH_SEPARATOR')) {
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		define('PATH_SEPARATOR', ';');
	} else {
		define('PATH_SEPARATOR', ':');
	}
}


?>
