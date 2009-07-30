<?php
/**
* Installer Paths.
*
* KnowledgeTree Community Edition
* Document Management Made Simple
* Copyright (C) 2008,2009 KnowledgeTree Inc.
* Portions copyright The Jam Warehouse Software (Pty) Limited
*
* This program is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License version 3 as published by the
* Free Software Foundation.
*
* This program is distributed in the hope that it will be useful, but WITHOUT
* ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
* details.
*
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
* You can contact KnowledgeTree Inc., PO Box 7775 #87847, San Francisco,
* California 94120-7775, or email info@knowledgetree.com.
*
* The interactive user interfaces in modified source and object code versions
* of this program must display Appropriate Legal Notices, as required under
* Section 5 of the GNU General Public License version 3.
*
* In accordance with Section 7(b) of the GNU General Public License version 3,
* these Appropriate Legal Notices must retain the display of the "Powered by
* KnowledgeTree" logo and retain the original copyright notice. If the display of the
* logo is not reasonably feasible for technical reasons, the Appropriate Legal Notices
* must display the words "Powered by KnowledgeTree" and retain the original
* copyright notice.
*
* @copyright 2008-2009, KnowledgeTree Inc.
* @license GNU General Public License version 3
* @author KnowledgeTree Team
* @package Installer
* @version Version 0.1
*/
	// Define installer environment
	if (substr(php_uname(), 0, 7) == "Windows"){
    	define('WINDOWS_OS', true);
    	define('UNIX_OS', false);
    	define('OS', 'windows');
	} else {
    	define('WINDOWS_OS', false);
    	define('UNIX_OS', true);
    	define('OS', 'unix');
	}
	if(WINDOWS_OS) {
		define('DS', '\\');
	} else {
		define('DS', '/');
	}
	$wizard_dir = realpath(dirname(__FILE__));
	$xdir = explode(DS, $wizard_dir);
	array_pop($xdir);
	array_pop($xdir);
	$sys = '';
	foreach ($xdir as $k=>$v) {
		$sys .= $v.DS;
	}
	
    define('WIZARD_DIR', $wizard_dir.DS);
    define('SYSTEM_DIR', $sys);
    define('SYS_BIN_DIR', $sys."bin".DS);
    define('SYS_LOG_DIR', $sys."var".DS."log".DS);
    define('SQL_DIR', WIZARD_DIR.DS."sql".DS);
    define('SQL_UPGRADE_DIR', SQL_DIR.DS."upgrades".DS);
    define('CONF_DIR', WIZARD_DIR.DS."config".DS);
    define('RES_DIR', WIZARD_DIR.DS."resources".DS);
    define('STEP_DIR', WIZARD_DIR.DS."steps".DS);
    define('TEMP_DIR', WIZARD_DIR.DS."templates".DS);
    preg_match('/Zend/', $sys, $matches);// Install Type
    if($matches) {
		$sysdir = explode(DS, $sys);
		array_pop($sysdir);
		array_pop($sysdir);
		array_pop($sysdir);
		array_pop($sysdir);
		$zendsys = '';
		foreach ($sysdir as $k=>$v) {
			$zendsys .= $v.DS;
		}
    	define('INSTALL_TYPE', 'Zend');
    	define('PHP_DIR', $zendsys."ZendServer".DS."bin".DS);
    }
    date_default_timezone_set('Africa/Johannesburg');
?>
