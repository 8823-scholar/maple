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
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: File.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

/**
 * �ǥ��쥯�ȥꡦ�ե��������饹
 *
 * @package     Maple.generate
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @author      Kazunobu Ichihashi <bobchin_ryu@bb.excite.co.jp>
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.1.0
 */
class Maplex_Generate_Util_File
{
    /**
     * include_path����ե�����򸡺��������Хѥ��η����֤�
     * ���Ĥ���ʤ��ä�����false���֤�
     * 
     * @author  Hawk <scholar@hawklab.jp>
     * @since   3.2.0
     * @return  String or false
     */
    function findIncludableFile($path)
    {
        if (!is_array($include_paths = explode(PATH_SEPARATOR, get_include_path()))) {
            return realpath($path);
        }
        
        foreach ($include_paths as $include_path) {
            if (($realpath = realpath($include_path . DIRECTORY_SEPARATOR . $path)) !== false) {
                return $realpath;
            }
        }
        return false;
    }

    /**
     * �ե�������ɤ߹���
     *
     * �ե�������ɤ߹���ǰ�Ĥ�ʸ����Ȥ����֤���
     *
     * @param string $fileName  �ե�����̾
     * @return string  �ե����������
     * @access public
     */
    function read($fileName)
    {
        $buf = '';
        if (file_exists($fileName)) {
            if (function_exists('file_get_contents')) {
                $buf = file_get_contents($fileName);
            } else {
                $fh = fopen($fileName, "rb");
                $buf = fread($fh, filesize($fileName));
                fclose($fh); 
            }
        }
        return $buf;
    }

    /**
     * �ե�������ɵ�����
     *
     * ���ꤷ�����Ƥ�ե�������ɵ��ǽ񤭹��ࡣ
     * ����Υե������¸�ߤ��ʤ����ϼ�ưŪ�˺������롣
     *
     * @param string $fileName  �ե�����̾
     * @param string $buf  �񤭹�������
     * @return boolean
     * @access public
     */
    function append($fileName, $buf)
    {
        return Maplex_Generate_Util_File::write($fileName, $buf, "ab");
    }

    /**
     * �ե�����˽񤭹���
     *
     * ���ꤷ�����Ƥ�ե�����˾�񤭤ǽ񤭹��ࡣ
     * ����Υե������¸�ߤ��ʤ����ϼ�ưŪ�˺������롣
     *
     * @param string $fileName  �ե�����̾
     * @param string $buf  �񤭹�������
     * @param string $mode  �񤭹��ߥ⡼��
     * @return boolean
     * @access public
     */
    function write($fileName, $buf, $mode = "wb")
    {
        Maplex_Generate_Util_File::makeDir(dirname($fileName));
        
        if (!($fh = fopen($fileName, $mode))) {
            return false;
        }
        if (!fwrite($fh, $buf)) {
            return false;
        }
        if (!fclose($fh)) {
            return false;
        }
        return true;
    }

    /**
     * �ǥ��쥯�ȥ��������롣
     * 
     * ʣ�����ؤΥǥ��쥯�ȥ����ꤷ�����ˡ�
     * ����Υǥ��쥯�ȥ�⼫ưŪ�˺������롣
     * 
     * @param mixed $dirNames  ��������ǥ��쥯�ȥ�̾���ޤ��Ϥ�������
     * @access public
     */
    function makeDir($dirNames)
    {
        if (!is_array($dirNames)) {
            $dirNames = array($dirNames);
        }
        
        foreach($dirNames as $dir){
            Maplex_Generate_Util_File::_makeDir($dir);
        }
    }

    /**
     * �ǥ��쥯�ȥ��������
     *
     * ���ꤷ���ǥ��쥯�ȥ��겼�Υǥ��쥯�ȥꡦ�ե������
     * ��ưŪ�˺�����롣
     *
     * @param mixed $dirNames  �������ǥ��쥯�ȥ�̾���ޤ��Ϥ�������
     * @access public
     */
    function removeDir($dirNames)
    {
        if (!is_array($dirNames)) {
            $dirNames = array($dirNames);
        }
        
        foreach($dirNames as $dir){
            Maplex_Generate_Util_File::_removeDir($dir);
        }
    }

    /**
     * �ǥ��쥯�ȥ�ȥե�����Υꥹ�Ȥ��������
     *
     * ���ͤ�����ǡ��ǽ餬�ǥ��쥯�ȥ�����󡢼��˥ե����������Ȥʤ롣
     * list($dirs, $files) = Maplex_Generate_Util_File::ls($dirname);
     *
     * @param string $dirName  �ǥ��쥯�ȥ�̾
     * @return array  �ǥ��쥯�ȥ�ȥե����������
     * @access public
     */
    function ls($dirName)
    {
        if (!$dh = @opendir($dirName)) {
            return false;
        }

        $dirs = $files = array();
        while (($file = readdir($dh)) !== false) {
            if (preg_match("/^[.]{1,2}$/", $file)) {
                continue;
            }
            $fullPath = Maplex_Generate_Util_File::_addTail($dirName, DIRECTORY_SEPARATOR).$file;
            if (is_dir($fullPath)) {
                $dirs[] = $fullPath;
            } else {
                $files[] = $fullPath;
            }
        }
        closedir($dh);
        return array($dirs, $files);
    }

    /**
     * ���ꤷ���ǥ��쥯�ȥ�Υե�����ꥹ�Ȥ��������
     *
     * @param string $dirName  �ǥ��쥯�ȥ�̾
     * @param string [$regex]  ��������ե�����̾������ɽ��
     * @return array  �ե����������
     * @access public
     */
    function find($dirName, $regex = "")
    {
        $data = Maplex_Generate_Util_File::ls($dirName);
        if (!is_array($data)) {
            return array();
        }
        list($dirs, $files) = $data;

        if ($regex == "") {
            return $files;
        }
        $found = array();
        foreach ($files as $file) {
            if (preg_match($regex, basename($file))) {
                $found[] = $file;
            }
        }
        return $found;
    }

    /**
     * ���ꤷ���ǥ��쥯�ȥ�Υե�����ꥹ�Ȥ��������
     * 
     * ���֥ǥ��쥯�ȥ꤬������ϺƵ�Ū�˼�������
     *
     * @param string $dirName  �ǥ��쥯�ȥ�̾
     * @param string [$regex]  ��������ե�����̾������ɽ��
     * @return array  �ե����������
     * @access public
     */
    function findRecursive($dirName, $regex = "")
    {
        $data = Maplex_Generate_Util_File::ls($dirName);
        if (!is_array($data)) {
            return array();
        }
        list($dirs, $files) = $data;
        
        $found = Maplex_Generate_Util_File::find($dirName, $regex);

        foreach ($dirs as $dir) {
            $found = array_merge($found, Maplex_Generate_Util_File::findRecursive($dir, $regex));
        }

        return $found;
    }

    /**
     * �ǥ��쥯�ȥ��������롣
     *
     * �ƥǥ��쥯�ȥ꤬�ʤ���м�ưŪ�˺������롣
     *
     * @param string $dirName  �ǥ��쥯�ȥ�̾
     * @access private
     */
    function _makeDir($dirName)
    {
        $dirstack = array();
        while (!@is_dir($dirName) && $dirName != DIRECTORY_SEPARATOR) {

        	array_unshift($dirstack, $dirName);
        	$dirName = dirname($dirName);
        }
        while ($newdir = array_shift($dirstack)) {
        	mkdir($newdir);
        }
    }

    /**
     * �ǥ��쥯�ȥ��������
     *
     * ���֥ǥ��쥯�ȥ꤬����м�ưŪ�˺�����롣
     * ���ꤷ���ǥ��쥯�ȥ�ʲ��Υե�����⼫ưŪ�˺�����롣
     *
     * @param string $dirName  �ǥ��쥯�ȥ�̾
     * @access private
     */
    function _removeDir($dirName)
    {
        $data = Maplex_Generate_Util_File::ls($dirName);
        if (!is_array($data)) {
            return;
        }
        list($dirs, $files) = $data;
        
        if (is_dir($dirName)) {
            array_unshift($dirs, $dirName);
        }

        foreach($files as $file){
            if (file_exists($file)) {
                unlink($file);
            }
        }

        foreach(array_reverse($dirs) as $dir){
            if (file_exists($dir)) {
                rmdir($dir);
            }
        }
    }

    /**
     * ʸ�����������ʸ������ɲä���
     *
     * ʸ����θ���ɲä���ʸ�����Ʊ������
     * ���⤷�ޤ���
     * 
     * @param string $target  �ɲ��оݤ�ʸ����
     * @param string $add  �ɲä���ʸ���� 
     * @return string �Ѵ����ʸ����
     * @access private
     */
    function _addTail($target, $add)
    {
        $regex = preg_quote($add);
        if (!preg_match("|.*{$regex}$|", $target)) {
            $target = $target.$add;
        }
        return $target;
    }
}
?>
