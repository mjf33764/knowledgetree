<?php
/**
* Migrate Step Controller. 
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
* @package Migrater
* @version Version 0.1
*/

class migrateInstallation extends step 
{
	/**
	* Flag to display confirmation page first
	*
	* @author KnowledgeTree Team
	* @access public
	* @var array
	*/
	public $displayFirst = false;
	
	/**
	* Flag to store class information in session
	*
	* @author KnowledgeTree Team
	* @access public
	* @var array
	*/
    protected $storeInSession = true;

	/**
	* List of paths
	*
	* @author KnowledgeTree Team
	* @access public
	* @var array
	*/
    protected $paths = array();

	/**
	* Flag if step needs to run silently
	*
	* @author KnowledgeTree Team
	* @access public
	* @var array
	*/
    protected $silent = false;
    
	/**
	* Reference to Utility object
	*
	* @author KnowledgeTree Team
	* @access public
	* @var object
	*/	
    public $util = null;
    
	private $location = '';
	
	private $dbSettings = array();
	
	private $ktSettings = array();
	
	private $urlPaths = array();
	
	private $knownWindowsLocations = array("C:\Program Files\ktdms"=>"C:\Program Files\ktdms\knowledgeTree\config\config-path","C:\Program Files x86\ktdms"=>"C:\Program Files x86\ktdms\knowledgeTree\config\config-path","C:\ktdms"=>"C:\ktdms\knowledgeTree\config\config-path");
	
	private $knownUnixLocations = array("/opt/ktdms","/var/www/ktdms");

	/**
	* Installation Settings
	*
	* @author KnowledgeTree Team
	* @access public
	* @var object
	*/	
    private $settings = array();
    
    private $supportedVersion = '3.6.1';
    
    private $foundVersion = 'Unknown';
    
    private $versionError = false;
    
    function __construct() {
        $this->temp_variables = array("step_name"=>"installation", "silent"=>$this->silent);
        $this->util = new MigrateUtil();
    }

    public function doStep() {
    	$this->detectInstallation();
    	if(!$this->inStep("installation")) {
    		$this->setDetails();
    		$this->doRun();
    		return 'landing';
    	}
        if($this->next()) {
        	if($this->doRun()) {
        		$this->setDetails();
            	return 'confirm';
        	} else {
            	return 'error';
        	}
        } else if($this->previous()) {
            return 'previous';
        } else if($this->confirm()) {
            	return 'next';
        }
		$this->doRun();
        
        return 'landing'; 
    }

    public function detectInstallation() {
    	if(WINDOWS_OS) {
    		foreach ($this->knownWindowsLocations as $loc=>$configPath) {
    			if(file_exists($configPath))
    				$this->location = $loc;
    		}
    	} else {
    		foreach ($this->knownUnixLocations as $loc=>$configPath) {
    			if(file_exists($configPath))
    				$this->location = $loc;
    		}
    	}
    }
    
    public function doRun() {
		if(!$this->readConfig()) {
			$this->storeSilent();
			return false;
		} else {
			if($this->readVersion()) {
				$this->checkVersion();
			}
			$this->storeSilent();
			return true;
		}
		
    }

    public function checkVersion() {
		if($this->foundVersion < $this->supportedVersion) {
			$this->versionError = true;
			$this->error[] = "KT installation needs to be 3.6.1 or higher";
		} else {
			return true;
		}
    }
    
    public function readVersion() {
    	$verFile = $this->location."/knowledgeTree/docs/VERSION.txt";
    	if(file_exists($verFile)) {
			$this->foundVersion = file_get_contents($verFile);
			return true;
    	} else {
			$this->error[] = "KT installation version not found";
    	}

		return false;    	
    }
    
    public function readConfig() {
		$ktInstallPath = isset($_POST['location']) ? $_POST['location']: '';
		if($ktInstallPath != '') {
			$this->location = $ktInstallPath;
			if(file_exists($ktInstallPath)) {
				$configPath = $ktInstallPath.DS."knowledgeTree".DS."config".DS."config-path";
				if(file_exists($configPath)) {
					$configFilePath = file_get_contents($configPath);
					if(file_exists($configFilePath)) { // For 3.7 and after
						$this->loadConfig($configFilePath);
						$this->storeSilent();
						
						return true;
					} else {
						$configFilePath = $ktInstallPath.DS."knowledgeTree".DS.$configFilePath; // For older than 3.6.2
						$configFilePath = trim($configFilePath);
						if(file_exists($configFilePath)) {
							$this->loadConfig($configFilePath);
							$this->storeSilent();
						
							return true;
						}
						$this->error[] = "KT installation configuration file empty";
					}
				} else {
					$this->error[] = "KT installation configuration file not found";
				}
			} else {
				$this->error[] = "KT installation not found";
			}
		}
		
		return false;
    }
    
    public function getPort() {
    	$dbConfigPath = $this->location.DS."mysql".DS."my.ini";
    	if(file_exists($dbConfigPath)) {
    		$ini = $this->util->loadInstallIni($dbConfigPath); //new Ini($path);
    		$dbSettings = $ini->getSection('mysqladmin');
    		return $dbSettings['port'];
    	}
    	
    	return '3306';
    }
    
    private function loadConfig($path) {
    	$ini = $this->util->loadInstallIni($path);//new Ini($path);
    	$dbSettings = $ini->getSection('db');
    	$this->dbSettings = array('dbHost'=> $dbSettings['dbHost'],
    								'dbName'=> $dbSettings['dbName'],
    								'dbUser'=> $dbSettings['dbUser'],
    								'dbPass'=> $dbSettings['dbPass'],
    								'dbPort'=> $this->getPort(),
    								'dbAdminUser'=> $dbSettings['dbAdminUser'],
    								'dbAdminPass'=> $dbSettings['dbAdminPass'],
    	);
		$ktSettings = $ini->getSection('KnowledgeTree');
		$froot = $ktSettings['fileSystemRoot'];
		if ($froot == 'default') {
			$froot = $this->location;
		}
		$this->ktSettings = array('fileSystemRoot'=> $froot,
    	);
    	$urlPaths = $ini->getSection('urls');
    	$varDir = $froot.DS.'var';
		$this->urlPaths = array(array('name'=> 'Var Directory', 'path'=> $varDir),
									array('name'=> 'Log Directory', 'path'=> $varDir.DS.'log'),
									array('name'=> 'Document Root', 'path'=> $varDir.DS.'Documents'),
									array('name'=> 'UI Directory', 'path'=> $froot.DS.'presentation'.DS.'lookAndFeel'.DS.'knowledgeTree'),
									array('name'=> 'Temporary Directory', 'path'=> $varDir.DS.'tmp'),
									array('name'=> 'Cache Directory', 'path'=> $varDir.DS.'cache'),
									array('name'=> 'Upload Directory', 'path'=> $varDir.DS.'uploads'),
    	);
    	$this->temp_variables['urlPaths'] = $this->urlPaths;
    	$this->temp_variables['ktSettings'] = $this->ktSettings;
    	$this->temp_variables['dbSettings'] = $this->dbSettings;
    }
    
    private function setDetails() {
    	$inst = $this->getDataFromSession("installation");
    	if ($inst) {
    		if(file_exists($this->location)) {
    			$this->location = $inst['location'];
    		}
    	}
    }
    
    public function getStepVars() {
        return $this->temp_variables;
    }

    public function getErrors() {
        return $this->error;
    }
    
    public function storeSilent() {
    	if($this->location==1) { $this->location = '';}
    	$this->temp_variables['location'] = $this->location;
    	$this->temp_variables['foundVersion'] = $this->foundVersion;
    	$this->temp_variables['versionError'] = $this->versionError;
    	$this->temp_variables['settings'] = $this->settings;
    }  
}
?>