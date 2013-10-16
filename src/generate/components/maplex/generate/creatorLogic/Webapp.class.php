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
 * @package     maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: Webapp.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

require_once('maplex/generate/creatorLogic/Abstract.class.php');

/**
 * build a webapp dir.
 *
 * @package     Maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 */
class Maplex_Generate_CreatorLogic_Webapp extends Maplex_Generate_CreatorLogic_Abstract
{
    var $fileUtil;
    
    /**
     * webapp ディレクトリを構築する
     * 
     * @param  String    $webapp
     * @return String
     */
    function create(&$dto)
    {
        $webappPath = $this->_normalizePath(
            $this->config->getValue('WEBAPP_DIR'));
        
        if(file_exists($webappPath)) {
            return array($webappPath => 'exists');
        }

        $subDirs = $this->_getSubDirs($webappPath);

        $this->fileUtil->makeDir($webappPath);
        $result = array($webappPath => 'create') + $this->_makeSubDirs($subDirs);
        return $result;
    }

    /**
     * makes sub directories.
     * $dirInfoListは _getSubDirs() が返却する配列
     * 
     * @since 06/07/17 17:01
     * @param  array    $dirInfoList
     * @return array
     */
    function _makeSubDirs($dirInfoList)
    {
        $result = array();
        
        foreach($dirInfoList as $absPath => $dirInfo) {
            $this->fileUtil->makeDir($absPath);
            if($dirInfo['writable']) {
                chmod($absPath, 0777);
            }
            $result[$absPath] = 'create';

            foreach($dirInfo['files'] as $file) {
                list($from, $to) = $file;
                copy($from, $to);
                $result[$to] = 'create';
            }
        }
        return $result;

    }

    /**
     * _getSubDirInfo()にconfig およびscriptの情報を加えて返す
     * 
     * @since 06/07/17 17:01
     * @param  String    $webappDir
     * @return array
     */
    function _getSubDirs($webappDir)
    {
        $config_dir = $this->_normalizePath($webappDir .'/config');
        $result = $this->_getSubDirInfo(array(
            'WEBAPP_CONFIG_DIR' => $config_dir
            ));

        //src, dest
        $cfgTmpl = $this->_normalizePath(
            $this->fileUtil->findIncludableFile("maple/config/webapp/"));
        $result[$config_dir]['files'] = array(
            array($cfgTmpl. '/maple.inc.php',    "$config_dir/maple.inc.php"),
            array($cfgTmpl. '/'. GLOBAL_CONFIG,  "$config_dir/". GLOBAL_CONFIG),
            array($cfgTmpl. '/base.ini',          "$config_dir/base.ini")
            );
        return $result;
    }

    
    /**
     * WEBAPP_DIRセクション + 追加的なディレクトリ情報を集め
     * array(
     *  path1 => array(
     *     writable => boolean,
     *     files    => array(file1, file2...)
     *  )
     * )
     * という配列にして返す。
     * 通常この段階では files は空
     * 
     * @since 06/07/17 16:59
     * @param  array    $additional
     * @return array
     */
    function _getSubDirInfo($additional=array())
    {
        $result = array();
        $webappSection = $this->config->getSection('WEBAPP_DIR') + $additional;
        $wrtableDirPatterns = preg_split(
            '/\s*,\s*/', $this->config->getValue('generator.writable_dir'));

        foreach($webappSection as $constName => $absPath) {
            if(!preg_match('/_DIR$/', $constName)) {
                continue;
            }

            $path = $this->_normalizePath($absPath);

            if(!isset($result[$path])) {
                $result[$path] = array(
                    'files' => array(),
                    'writable' => $this->_isWritable($path, $wrtableDirPatterns));
            }
        }
        return $result;
    }

    /**
     * $absPathが$patternsにマッチするか調べる
     * 
     * @since 06/07/17 16:59
     * @param  String    $absPath
     * @param  array    $patterns
     * @return boolean
     */
    function _isWritable($absPath, $patterns)
    {
        $dir = basename($absPath);
        foreach($patterns as $ptn) {
            if(preg_match($ptn, $dir)) {
                return true;
            }
        }
        return false;
    }
    
    function _normalizePath($path)
    {
        return preg_replace('|/$|', '', str_replace('\\', '/', $path));
    }

}
?>
