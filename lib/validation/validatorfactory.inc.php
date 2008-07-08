<?php
/**
 * $Id$
 *
 * KnowledgeTree Community Edition
 * Document Management Made Simple
 * Copyright (C) 2008 KnowledgeTree Inc.
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
 * Contributor( s): ______________________________________
 */

/*
 * The valiodator factory is a singleton, which can be used to create
 * and register validators.
 *
 */

class KTValidatorFactory {
    var $validators = array();

    static function &getSingleton () {
		static $singleton=null;
    	if (is_null($singleton))
    	{
    		$singleton = new KTValidatorFactory();
    	}
    	return $singleton;
    }

    function registerValidator($sClassname, $sNamespace,  $sFilename = null) {
        $this->validators[$sNamespace] = array(
            'ns' => $sNamespace,
            'class' => $sClassname,
            'file' => $sFilename,
        );
    }

    function &getValidatorByNamespace($sNamespace) {
        $aInfo = KTUtil::arrayGet($this->validators, $sNamespace);
        if (empty($aInfo)) {
            return PEAR::raiseError(sprintf(_kt('No such validator: %s'), $sNamespace));
        }
        if (!empty($aInfo['file'])) {
            require_once($aInfo['file']);
        }

        return new $aInfo['class'];
    }

    // this is overridden to either take a namespace or an instantiated
    // class.  Doing it this way allows for a consistent approach to building
    // forms including custom widgets.
    function &get($namespaceOrObject, $aConfig = null) {
        if (is_string($namespaceOrObject)) {
            $oValidator =& $this->getValidatorByNamespace($namespaceOrObject);
        } else {
            $oValidator = $namespaceOrObject;
        }

        if (PEAR::isError($oValidator)) {
            return $oValidator;
        }

        $aConfig = (array) $aConfig; // always an array
        $res = $oValidator->configure($aConfig);
        if (PEAR::isError($res)) {
            return $res;
        }

        return $oValidator;
    }
}

?>
