<?php

/**
 * $Id$
 *
 * Copyright (c) 2006 Jam Warehouse http://www.jamwarehouse.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; using version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * -------------------------------------------------------------------------
 *
 * You can contact the copyright owner regarding licensing via the contact
 * details that can be found on the KnowledgeTree web site:
 *
 *         http://www.ktdms.com/
 */

class KTBaseIndexerTrigger { 
    /**
     * Which MIME types that this indexer acts upon.
     */
    var $mimetypes = array(
       // 'text/plain' => true,
    );

    /**
     * commandconfig is where to find the command to use in the
     * KnowledgeTree configuration.  For example, it may be
     * "indexing/catdoc", which would correspond to the "indexing"
     * section of config.ini, item "catdoc".
     */
    var $commandconfig = '';    // Something like "indexing/catdoc"

    /**
     * In the absence of the command in the configuration, what command
     * to use directly.
     */
    var $command = '';          // Something like "catdoc"


    /**
     * Any options to send to the command before the input file.
     */
    var $args = array();

    /**
     * Setting use_pipes to true will cause the output of the command to
     * be sent to a temporary file created and chosen by the system.
     *
     * If it is false, the temporary file will be sent as the last
     * parameter.
     */
    var $use_pipes = true; 
    
    /* return a diagnostic string _if_ there is something wrong.  NULL otherwise. */
    function getDiagnostic() {
        return null;
    }

    function setDocument($oDocument) {
        $this->oDocument = $oDocument;
    }

    function transform() {
        $iMimeTypeId = $this->oDocument->getMimeTypeId();
        $sMimeType = KTMime::getMimeTypeName($iMimeTypeId);
        if (!array_key_exists($sMimeType, $this->mimetypes)) {
            return;
        }

        $oStorage = KTStorageManagerUtil::getSingleton();
        $sFile = $oStorage->temporaryFile($this->oDocument);

        $tempstub = 'transform';
        if ($this->command != null) {
            $tempstub = $this->command;
        }
        $myfilename = tempnam("/tmp", 'kt.' . $tempstub);
        $contents = $this->extract_contents($sFile, $myfilename);
        
        unlink($myfilename);
        
        if (empty($contents)) {
            return;
        }
        $aInsertValues = array(
            'document_id' => $this->oDocument->getId(),
            'document_text' => $contents,
        );
        $sTable = KTUtil::getTableName('document_text');
        
        // clean up the document query "stuff".
        // FIXME this suggests that we should move the _old_ document_searchable_text across to the old-document's id if its a checkin.
        DBUtil::runQuery(array('DELETE FROM ' . $sTable . ' WHERE document_id = ?', array($this->oDocument->getId())));
        DBUtil::autoInsert($sTable, $aInsertValues, array('noid' => true));

    }
    
    // handles certain, _very_ simple reader types.
    function extract_contents($sFilename, $sTempFilename) {
        $sCommand = KTUtil::findCommand($this->commandconfig, $this->command);
        if (empty($sCommand)) {
            return false;
        }

        $cmdline = array($sCommand);
        $cmdline = array_merge($cmdline, $this->args);
        $cmdline[] = $sFilename;
        
        $aOptions = array();
        if ($this->use_pipes) {
            $aOptions["append"] = $sTempFilename;
        } else {
            $cmdline[] = $sTempFilename;
        }
        KTUtil::pexec($cmdline, $aOptions);
        $contents = file_get_contents($sTempFilename);
        
        return $contents;
    }
}

?>
