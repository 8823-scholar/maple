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
 * @version     CVS: $Id: SingleFile.class.php,v 1.1 2006/08/30 13:22:00 hawkring Exp $
 */

require_once('maplex/generate/creatorLogic/Abstract.class.php');

/**
 * ñ�ȤΥե������񤭽Ф�craetorLogic
 *
 * @package     Maple.generate
 * @author      Hawk <scholar@hawklab.jp>
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @abstract
 */
class Maplex_Generate_CreatorLogic_SingleFile extends Maplex_Generate_CreatorLogic_Abstract
{
    /**
     * @var  FileWriter  $writer  
     */
    var $writer;

    /**
     * $template���ɤ߹����$outputFile�˽񤭽Ф�
     * ʸ�����󥳡��ǥ��󥰤ˤ�$outputEncoding����Ѥ���
     * $vars�ϥƥ�ץ졼�Ȥ��� $skeleton �Ȥ��ƥ��������Ǥ���
     * 
     * ����Ū�˽�����$writer�˰Ѿ�����
     * ��̤�����η��ˤޤȤ��
     * 
     * $template�λ��꤬�ʤ���硢getTemplateFile�Ǽ�ư����
     * $outputEncoding�λ��꤬�ʤ���硢SCRIPT_CODE�����
     * 
     * @param  String    $outputFile
     * @param  array     $vars
     * @param  String    $outputEncoding  [optional]
     * @param  String    $template [optional]
     * @return array
     */
    function output($outputFile, $vars, $outputEncoding='SCRIPT_CODE', $template="")
    {
        if($template == "") {
            $template = $this->getTemplateFile();
        }

        $stat = '';
        if (file_exists($outputFile)) {
            $stat = 'exists';
        } else {
            $stat = $this->writer->write(
                $template, $outputFile, $vars, $outputEncoding) ? 'create' : 'fail';
        }

        return array($outputFile => $stat);
    }

    /**
     * �ƥ�ץ졼�ȥե�����̾����������
     * basename�λ��꤬�ʤ���硢���饹̾���鼫ư����
     * 
     * foo_bar_zoo -> zoo.txt
     * 
     * @access protected
     * @param  String    $basename
     * @return String
     */
    function getTemplateFile($basename="")
    {
        if($basename == "") {
            $c = explode('_', get_class($this));
            $b = array_pop($c);
            $basename = strtolower($b{0}) . substr($b,1);
        }
        return "maple/generate/skeleton/{$basename}.txt";
    }

    /**
     * 
     * @access protected
     * @param  String    $constName
     * @param  String    $subject
     * @return String
     */
    function replaceWithConfig($constName, $subject)
    {
        return preg_replace(
            '/^'. preg_quote(constant($constName), '/') .'/',
            $this->config->getValue($constName),
            $subject);
    }
}
?>
