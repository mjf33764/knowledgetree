<?php
/**
* Dependency Step Step Controller. 
*
* KnowledgeTree Community Edition
* Document Management Made Simple
* Copyright(C) 2008,2009 KnowledgeTree Inc.
* Portions copyright The Jam Warehouse Software(Pty) Limited
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
require_once(WIZARD_DIR.'step.php');

class dependencyCheck extends Step
{
    private $maxPHPVersion = '6.0.0';
    private $minPHPVersion = '5.0.0';
    private $done;
	/**
	* Flag to store class information in session
	*
	* @author KnowledgeTree Team
	* @access public
	* @var array
	*/
    public $storeInSession = true;
    
    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->temp_variables = array("step_name"=>"dependency_check");
        $this->error = array();
        $this->done = true;
    }

    public function doStep()
    {
        // Check dependencies
        $passed = $this->doRun();
        if($this->next()) {
            if($passed)
                return 'next';
            else
                return 'error';
        } else if($this->previous()) {

            return 'previous';
        }

        return 'landing';
    }
    
    public function doRun()
    {
        $check = $this->checkPhpVersion();
        $this->temp_variables['version'] = $check;

        $configs = $this->checkPhpConfiguration();
        $this->temp_variables['configurations'] = $configs;

        // get the list of extensions
        $list = $this->getRequiredExtensions();
        $extensions = array();

        foreach($list as $ext){

            $ext['available'] = 'no';
            if($this->checkExtension($ext['extension'])){
                $ext['available'] = 'yes';
            }else {
                if($ext['required'] == 'no'){
                    $ext['available'] = 'optional';
                }else{
                    $this->done = false;
                    $this->error[] = 'Missing required extension: '.$ext['name'];
                }
            }

            $extensions[] = $ext;
        }

        $this->temp_variables['extensions'] = $extensions;

        return $this->done;
    }

    public function getErrors() {
        return $this->error;
    }

    public function getStepVars()
    {
        return $this->temp_variables;
    }

    private function checkPhpConfiguration()
    {
        $configs = $this->getConfigurations();

        foreach($configs as $key => $config) {
            $setting = ini_get($config['configuration']);

            switch($config['type']){
                case 'bool':
                    $value = ($setting == 1) ? 'ON' : 'OFF';
                    break;

                case 'empty':
                    $value = ($setting === false || $setting === '') ? 'unset' : $setting;
                    break;

                default:
                    $value = $setting;
            }

            $class = ($value == $config['recommended']) ? 'green' : 'orange';
            $configs[$key]['setting'] = $value;
            $configs[$key]['class'] = $class;
        }

        $limits = $this->getLimits();

        foreach($limits as $key => $limit) {
            $setting = ini_get($limit['configuration']);

            $setting = $this->prettySizeToActualSize($setting);
            $recommended = $this->prettySizeToActualSize($limit['recommended']);
            $class = ($recommended < $setting || $setting = -1) ? 'green' : 'orange';

            $limits[$key]['setting'] = $this->prettySize($setting);
            $limits[$key]['class'] = $class;
        }
        $configs = array_merge($configs, $limits);

        return $configs;
    }

    private function checkPhpVersion()
    {
        $phpversion = phpversion();

        $phpversion5 = version_compare($phpversion, $this->minPHPVersion, '>=');
        $phpversion6 = version_compare($phpversion, $this->maxPHPVersion, '<');

        $check['class'] = 'cross';
        if($phpversion5 != 1){
            $this->done = false;
            $check['version'] = "Your PHP version needs to be PHP 5.0 or higher. You are running version <b>{$phpversion}</b>.";
            return $check;
        }

        if($phpversion6 != 1){
            $this->done = false;
            $check['version'] = "KnowledgeTree is not supported on PHP 6.0 and higher. You are running version <b>{$phpversion}</b>.";
            return $check;
        }
        $check['class'] = 'tick';
        $check['version'] =  "You are running version <b>{$phpversion}</b>.";
        return $check;
    }

    private function checkExtension($extension)
    {
        if(extension_loaded($extension)){
            return true;
        }
        $this->continue = false;
        return false;
    }

    private function prettySizeToActualSize($pretty) {
        if (strtoupper(substr($pretty, strlen($pretty) - 1)) == 'G') {
            return (int)substr($pretty, 0, strlen($pretty)) * 1024 * 1024 * 1024;
        }
        if (strtoupper(substr($pretty, strlen($pretty) - 1)) == 'M') {
            return (int)substr($pretty, 0, strlen($pretty)) * 1024 * 1024;
        }
        if (strtoupper(substr($pretty, strlen($pretty) - 1)) == 'K') {
            return (int)substr($pretty, 0, strlen($pretty)) * 1024 * 1024;
        }
        return (int)$pretty;
    }

    private function prettySize($v) {
        $v = (float)$v;
        foreach (array('B', 'K', 'M', 'G') as $unit) {
            if ($v < 1024) {
                return $v . $unit;
            }
            $v = $v / 1024;
        }
    }

    private function getRequiredExtensions()
    {
        return array(
            array('extension' => 'fileinfo', 'required' => 'no', 'name' => 'Fileinfo', 'details' => 'Provides better file identification support - not necessary if you use file extensions'),
            array('extension' => 'iconv', 'required' => 'no', 'name' => 'IconV', 'details' => ''),
            array('extension' => 'mysql', 'required' => 'yes', 'name' => 'MySQL', 'details' => ''),
            array('extension' => 'curl', 'required' => 'yes', 'name' => 'cURL', 'details' => ''),
            array('extension' => 'xmlrpc', 'required' => 'yes', 'name' => 'XMLRPC', 'details' => ''),
            array('extension' => 'win32', 'required' => 'no', 'name' => 'Win32', 'details' => 'Allows control of Microsoft Windows services'),
            array('extension' => 'mbstring', 'required' => 'no', 'name' => 'Multi Byte Strings', 'details' => ''),
            array('extension' => 'ldap', 'required' => 'no', 'name' => 'LDAP', 'details' => ''),
            array('extension' => 'json', 'required' => 'no', 'name' => 'JSON', 'details' => ''),
            array('extension' => 'openssl', 'required' => 'no', 'name' => 'Open SSL', 'details' => ''),
        );
    }

    private function getConfigurations()
    {
        return array(
            array('name' => 'Safe Mode', 'configuration' => 'safe_mode', 'recommended' => 'ON', 'type' => 'bool'),
            array('name' => 'Display Errors', 'configuration' => 'display_errors', 'recommended' => 'OFF', 'type' => 'bool'),
            array('name' => 'Display Startup Errors', 'configuration' => 'display_startup_errors', 'recommended' => 'ON', 'type' => 'bool'),
            array('name' => 'File Uploads', 'configuration' => 'file_uploads', 'recommended' => 'ON', 'type' => 'bool'),
            array('name' => 'Magic Quotes GPC', 'configuration' => 'magic_quotes_gpc', 'recommended' => 'OFF', 'type' => 'bool'),
            array('name' => 'Magic Quotes Runtime', 'configuration' => 'magic_quotes_runtime', 'recommended' => 'OFF', 'type' => 'bool'),
            array('name' => 'Register Globals', 'configuration' => 'register_globals', 'recommended' => 'OFF', 'type' => 'bool'),
            array('name' => 'Output Buffering', 'configuration' => 'output_buffering', 'recommended' => 'OFF', 'type' => 'bool'),
            array('name' => 'Session auto start', 'configuration' => 'session.auto_start', 'recommended' => 'OFF', 'type' => 'bool'),
            array('name' => 'Automatic prepend file', 'configuration' => 'auto_prepend_file', 'recommended' => 'unset', 'type' => 'empty'),
            array('name' => 'Automatic append file', 'configuration' => 'auto_append_file', 'recommended' => 'unset', 'type' => 'empty'),
            array('name' => 'Open base directory', 'configuration' => 'open_basedir', 'recommended' => 'unset', 'type' => 'empty'),
            array('name' => 'Default MIME type', 'configuration' => 'default_mimetype', 'recommended' => 'text/html', 'type' => 'string'),
        );
    }

    private function getLimits()
    {
        return array(
            array('name' => 'Maximum POST size', 'configuration' => 'post_max_size', 'recommended' => '32M', 'type' => 'int'),
            array('name' => 'Maximum upload size', 'configuration' => 'upload_max_filesize', 'recommended' => '32M', 'type' => 'int'),
            array('name' => 'Memory limit', 'configuration' => 'memory_limit', 'recommended' => '32M', 'type' => 'int'),
        );
    }
}
?>