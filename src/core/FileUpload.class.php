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
 * @author      KeyPoint
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version     CVS: $Id: FileUpload.class.php,v 1.6 2006/02/11 19:18:22 kunit Exp $
 */

/**
 * FileUpload��Ϣ�ν�����Ԥ���ʣ�����åץ����б��ǡ�
 *
 * @package     Maple
 * @author      TAKAHASHI Kunihiko <kunit@kunit.jp>
 * @author      KeyPoint
 * @copyright   2004-2006 The Maple Project
 * @license     http://www.php.net/license/3_0.txt  PHP License 3.0
 * @access      public
 * @since       3.0.0
 */
class FileUpload
{
    /**
     * @var �ե�����ǻ��ꤷ���ե������̾���ݻ�
     *
     * @access  private
     * @since   3.1.0
     */
    var $_name;
    
    /**
     * @var �ե������ư��Υե�����Υ⡼�ɤ��ݻ�����
     *
     * @access  private
     * @since   3.1.0
     */
    var $_filemode;

    /**
     * ���󥹥ȥ饯����
     *
     * @access  public
     * @since   3.1.0
     */
    function FileUpload()
    {
        $this->_name     = "";   //�ե�����̾������˳�Ǽ
        $this->_filemode = 0644;
    }

    /**
     * �ե�����ǻ��ꤷ���ե������̾���ֵ�
     *
     * @return  string  �ե������̾
     * @access  public
     * @since   3.1.0
     */
    function getName()
    {
        return $this->_name;
    }
    
    /**
     * �ե�����ǻ��ꤷ���ե������̾�򥻥å�
     *
     * @param   string  $name   �ե������̾
     * @access  public
     * @since   3.1.0
     */
    function setName($name)
    {
        $this->_name = $name;
    }
    
    /**
     * �ե������ư��Υե�����Υ⡼�ɤ��ֵ�
     *
     * @return  integer �ե�����Υ⡼��
     * @access  public
     * @since   3.1.0
     */
    function getFilemode()
    {
        return $this->_filemode;
    }
    
    /**
     * �ե������ư��Υե�����Υ⡼�ɤ򥻥å�
     *
     * @param   integer $filemode   �ե�����Υ⡼��
     * @access  public
     * @since   3.1.0
     */
    function setFilemode($filemode)
    {
        $this->_filemode = octdec($filemode);
    }

    /**
     * ���åץ��ɤ��줿�����ֵ�
     *
     * @return  integer ���åץ��ɤ��줿��
     * @access  public
     * @since   3.1.0
     */
    function count() {
        $name = $this->getName();
        if (is_array($_FILES[$name]["name"])) {
            return count($_FILES[$name]["name"]);
        } else {
            return 1;
        }
    }
    
    /**
     * ���饤����ȥޥ���θ��Υե�����̾���ֵ�
     *
     * @return  array   ���饤����ȥޥ���θ��Υե�����̾������
     * @access  public
     * @since   3.1.0
     */
    function getOriginalName()
    {
        $original_name = array();
        //������֤�
        $name = $this->getName();
        if (($name != "") && isset($_FILES[$name])) {
            if (is_array($_FILES[$name]["name"])) {
                foreach ($_FILES[$name]["name"] as $key => $value) {
                    $original_name[$key] = $value;
                }
            }else if (isset($_FILES[$name]["name"])){
                $original_name[0] = $_FILES[$name]["name"];
            }
        }
        return $original_name;
    }
    
    /**
     * �ե������MIME�����ֵ�
     *
     * @return  array   �ե������MIME��������
     * @access  public
     * @since   3.1.0
     */
    function getMimeType()
    {
        $mime_type = array();
        //������֤�
        $name = $this->getName();
        if (($name != "") && isset($_FILES[$name])) {
            if (is_array($_FILES[$name]["type"])) {
                foreach ($_FILES[$name]["type"] as $key => $value) {
                    $mime_type[$key] = $value;
                }
            }else if (isset($_FILES[$name]["type"])){
                $mime_type[0] = $_FILES[$name]["type"];
            }
        }
        return $mime_type;
    }
    
    /**
     * ���åץ��ɤ��줿�ե�����ΥХ���ñ�̤Υ��������ֵ�
     *
     * @return  array   �ե�����Υ�����������
     * @access  public
     * @since   3.1.0
     */
    function getFilesize()
    {
        $filesize = array();
        //������֤�
        $name = $this->getName();
        if (($name != "") && isset($_FILES[$name])) {
            if (is_array($_FILES[$name]["size"])) {
                foreach ($_FILES[$name]["size"] as $key => $value) {
                    $filesize[$key] = $value;
                }
            }else if (isset($_FILES[$name]["size"])){
                $filesize[0] = $_FILES[$name]["size"];
            }
        }
        return $filesize;
    }
    
    /**
     * �ƥ�ݥ��ե������̾�����ֵ�
     *
     * @return  array   �ƥ�ݥ��ե������̾��������
     * @access  public
     * @since   3.1.0
     */
    function getTmpName()
    {
        $tmp_name = array();
        //������֤�
        $name = $this->getName();
        if (($name != "") && isset($_FILES[$name])) {
            if (is_array($_FILES[$name]["tmp_name"])) {
                foreach ($_FILES[$name]["tmp_name"] as $key => $value) {
                    $tmp_name[$key] = $value;
                }
            }else if (isset($_FILES[$name]["tmp_name"])){
                $tmp_name[0] = $_FILES[$name]["tmp_name"];
            }
        }
        return $tmp_name;
    }
    
    /**
     * �ե����륢�åץ��ɤ˴ؤ��륨�顼�����ɤ��ֵ�
     *
     * @return  array   �ե����륢�åץ��ɤ˴ؤ��륨�顼�����ɤ�����
     * @access  public
     * @since   3.1.0
     */
    function getError()
    {
        $error_list = array();
        //������֤�
        $name = $this->getName();
        if (($name != "") && isset($_FILES[$name])) {
            if (is_array($_FILES[$name]["error"])) {
                foreach ($_FILES[$name]["error"] as $key => $value) {
                    $error_list[$key] = $value;
                }
            }else if (isset($_FILES[$name]["error"])){
                $error_list[0] = $_FILES[$name]["error"];
            }
        }
        return $error_list;
    }
    
    /**
     * ���ꤵ�줿MIME���ˤʤäƤ��뤫��
     *
     * @param   array    $type  MIME��������
     * @return  array[boolean]  ���ꤵ�줿MIME���ˤʤäƤ��뤫��������
     * @access  public
     * @since   3.1.0
     */
    function checkMimeType($type_list)
    {
        $mime_type_check = array();
        $mime_type = $this->getMimeType();
        if (count($mime_type) > 0) {
            foreach ($mime_type as $key => $val) {
                if (isset($type_list[$key])) {
                    $type = $type_list[$key];
                } else if (isset($type_list["default"])){
                    $type = $type_list["default"];
                } else {
                    $type = "";
                }
                if ($type == "" || in_array($val,$type)  ) {
                    $mime_type_check[$key] = true;
                }else {
                    $mime_type_check[$key] = false;
                }
            }
        }
        return $mime_type_check;
    }
    
    /**
     * �ե����륵���������ꤵ�줿�������ʲ����ɤ�����
     *
     * @param   array   $size_list  ���Ȥʤ�ե����륵����������
     * @return  array[boolean]    �ե����륵���������ꤵ�줿�������ʲ����ɤ�����������
     * @access  public
     * @since   3.1.0
     */
    function checkFilesize($size_list)
    {
        $filesize_check = array();
        $filesize = $this->getFilesize();
        if (count($filesize) > 0) {
            foreach ($filesize as $key => $val) {
                if (isset($size_list[$key])) {
                    $size = $size_list[$key];
                } else if (isset($size_list["default"])) {
                    $size = $size_list["default"];
                } else {
                    $size = "";
                }
                if ($size == "" || $val <= $size) {
                    $filesize_check[$key] = true;
                }else {
                    $filesize_check[$key] = false;
                }
            }
        }
        return $filesize_check;
    }

    /**
     * ���ꤵ�줿�ѥ��إե�������ư(one file)
     *
     * @param   strint  $name   ��ư���Υե�����κ����ֹ�
     * @param   strint  $dest   ��ư��Υե�����̾
     * @return  boolean ��ư�������������ɤ���
     * @access  public
     * @since   3.1.0
     */
    function move($id,$dest)
    {
        $tmp_name = $this->getTmpName();
        if (isset($tmp_name[$id])) {
            if (move_uploaded_file($tmp_name[$id], $dest)) {
                chmod($dest, $this->getFilemode());
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
?>
