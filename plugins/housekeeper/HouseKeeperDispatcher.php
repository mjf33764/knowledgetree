<?php

/**
 * $Id
 *
 * KnowledgeTree Open Source Edition
 * Document Management Made Simple
 * Copyright (C) 2004 - 2008 The Jam Warehouse Software (Pty) Limited
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
 * You can contact The Jam Warehouse Software (Pty) Limited, Unit 1, Tramber Place,
 * Blake Street, Observatory, 7925 South Africa. or email info@knowledgetree.com.
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
 * Contributor( s): ______________________________________
 */

session_start();

require_once("../../config/dmsDefaults.php");
require_once(KT_LIB_DIR . "/templating/templating.inc.php");
require_once(KT_LIB_DIR . "/dispatcher.inc.php");

class HouseKeeperDispatcher extends KTStandardDispatcher
{
    function cleanDirectory($path, $pattern)
	{
		if (!is_readable($path))
		{
			return;
		}
		if ($dh = opendir($path))
		{
			while (($file = readdir($dh)) !== false)
			{
				if (substr($file,0,1) == '.')
				{
					continue;
				}

				$full = $path . '/' . $file;
				if (is_dir($full))
				{
					 $this->cleanDirectory($full,$pattern);
					 if (is_writable($full))
					 {
					 	@rmdir($full);
					 }
					continue;
				}

				if (!empty($pattern) && !preg_match('/' . $pattern . '/', $file))
				{
					continue;
				}

				if (is_writable($full))
				{
					@unlink($full);
				}

			}
			closedir($dh);
		}

	}

	function do_cleanup()
	{
		$folder = KTUtil::arrayGet($_REQUEST, 'folder');
		if (is_null($folder))
		{
			exit(redirect(generateControllerLink('dashboard')));
		}

		$oRegistry =& KTPluginRegistry::getSingleton();
		$oPlugin =& $oRegistry->getPlugin('ktcore.housekeeper.plugin');

        // we must avoid doing anything to the documents folder at all costs!
        $folder = $oPlugin->getDirectory($folder);
        if (is_null($folder) || !$folder['canClean'])
        {
        	exit(redirect(generateControllerLink('dashboard')));
        }

		$this->cleanDirectory($folder['folder'], $folder['pattern']);

		$this->do_refreshFolderUsage();
	}

	function do_refreshDiskUsage()
	{
		session_unregister('DiskUsage');
		exit(redirect(generateControllerLink('dashboard')));
	}

	function do_refreshFolderUsage()
	{
		session_unregister('SystemFolderUsage');
		exit(redirect(generateControllerLink('dashboard')));
	}
}
$oDispatcher = new HouseKeeperDispatcher();
$oDispatcher->dispatch();

?>