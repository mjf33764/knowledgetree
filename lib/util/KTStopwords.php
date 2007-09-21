<?php

/**
 * $Id$
 *
 * The contents of this file are subject to the KnowledgeTree Public
 * License Version 1.1.2 ("License"); You may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.knowledgetree.com/KPL
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied.
 * See the License for the specific language governing rights and
 * limitations under the License.
 *
 * All copies of the Covered Code must include on each user interface screen:
 *    (i) the "Powered by KnowledgeTree" logo and
 *    (ii) the KnowledgeTree copyright notice
 * in the same form as they appear in the distribution.  See the License for
 * requirements.
 *
 * The Original Code is: KnowledgeTree Open Source
 *
 * The Initial Developer of the Original Code is The Jam Warehouse Software
 * (Pty) Ltd, trading as KnowledgeTree.
 * Portions created by The Jam Warehouse Software (Pty) Ltd are Copyright
 * (C) 2007 The Jam Warehouse Software (Pty) Ltd;
 * All Rights Reserved.
 * Contributor( s): ______________________________________
 *
 */

class KTStopwords {
    var $words = array();

    var $conf = array();
    var $aSectionFile;
    var $aFileRoot;
    var $flat = array();
    var $flatns = array();

    function loadCache($filename) {
        $cache_str = file_get_contents($filename);
	$this->words = unserialize($cache_str);
        return true;
    }

    function createCache($filename) {
        file_put_contents($filename, serialize($this->words));
    }

    function loadFile($filename) {
	$this->words = array();
	foreach(file($filename) as $line) {
	    $this->words[] = trim($line);
	}
    }

    function isStopword($sWord) {
	return in_array($sWord, $this->words);
    }

    static function &getSingleton() {
    	static $singleton = null;
    	if (is_null($singleton))
    	{
    		$singleton = new KTStopwords;
    		$oConfig =& KTConfig::getSingleton();
	    	$singleton->loadFile($oConfig->get('urls/stopwordsFile'));
    	}

        return $singleton;
    }
}


?>
