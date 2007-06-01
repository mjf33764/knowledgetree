<?php
/**
 * $Id$
 *
 * Main dashboard page -- This page is presented to the user after login.
 * It contains a high level overview of the users subscriptions, checked out 
 * document, pending approval routing documents, etc. 
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
 */

// main library routines and defaults
require_once('config/dmsDefaults.php');
require_once(KT_LIB_DIR . '/unitmanagement/Unit.inc');

require_once(KT_LIB_DIR . '/dashboard/dashletregistry.inc.php');
require_once(KT_LIB_DIR . '/dashboard/dashlet.inc.php');
require_once(KT_LIB_DIR . '/templating/templating.inc.php');
require_once(KT_LIB_DIR . '/templating/kt3template.inc.php');
require_once(KT_LIB_DIR . '/dispatcher.inc.php');

require_once(KT_LIB_DIR . '/dashboard/DashletDisables.inc.php');

$sectionName = 'dashboard';

class DashboardDispatcher extends KTStandardDispatcher {
    
    var $notifications = array();
    var $sHelpPage = 'ktcore/dashboard.html';    

    function DashboardDispatcher() {
        $this->aBreadcrumbs = array(
            array('action' => 'dashboard', 'name' => _kt('Dashboard')),
        );
        return parent::KTStandardDispatcher();
    }
    function do_main() {
        $this->oPage->setShowPortlets(false);
        // retrieve action items for the user.
        // FIXME what is the userid?
        
        
        $oDashletRegistry =& KTDashletRegistry::getSingleton();
        $aDashlets = $oDashletRegistry->getDashlets($this->oUser);
        
        $this->sSection = 'dashboard';
        $this->oPage->setBreadcrumbDetails(_kt('Home'));
        $this->oPage->title = _kt('Dashboard');
    
        // simplistic improvement over the standard rendering:  float half left
        // and half right.  +Involves no JS -can leave lots of white-space at the bottom.

        $aDashletsLeft = array();
        $aDashletsRight = array(); 

        $i = 0;
        foreach ($aDashlets as $oDashlet) {
            if ($i == 0) { $aDashletsLeft[] = $oDashlet; }
            else {$aDashletsRight[] = $oDashlet; }
            $i += 1;
            $i %= 2;
        }

        // javascript
        // yahoo
        $this->oPage->requireJSResource('thirdpartyjs/yui/yahoo/yahoo.js');
        $this->oPage->requireJSResource('thirdpartyjs/yui/event/event.js');
        $this->oPage->requireJSResource('thirdpartyjs/yui/dom/dom.js');
        $this->oPage->requireJSResource('thirdpartyjs/yui/dragdrop/dragdrop.js');
        $this->oPage->requireJSResource('resources/js/DDList.js');
        

        $this->oUser->refreshDashboadState();
        
        // dashboard
        $sDashboardState = $this->oUser->getDashboardState();
        $sDSJS = 'var savedState = ';
        if($sDashboardState == null) {
            $sDSJS .= 'false';
            $sDashboardState = false;
        } else {
            $sDSJS .= $sDashboardState;
        }
        $sDSJS .= ';';
        $this->oPage->requireJSStandalone($sDSJS);
        $this->oPage->requireJSResource('resources/js/dashboard.js');


        // render
        $oTemplating =& KTTemplating::getSingleton();
        $oTemplate = $oTemplating->loadTemplate('kt3/dashboard');
        $aTemplateData = array(
              'context' => $this,
              'dashlets_left' => $aDashletsLeft,
              'dashlets_right' => $aDashletsRight,
        );
        return $oTemplate->render($aTemplateData);
    }
    
    // return some kind of ID for each dashlet
    // currently uses the class name
    function _getDashletId($oDashlet) {
        return get_class($oDashlet);
    }

    // disable a dashlet.  
    // FIXME this very slightly violates the separation of concerns, but its not that flagrant.
    function do_disableDashlet() {
        $sNamespace = KTUtil::arrayGet($_REQUEST, 'fNamespace');
        $iUserId = $this->oUser->getId();
        
        if (empty($sNamespace)) {
            $this->errorRedirectToMain('No dashlet specified.');
            exit(0);
        }
    
        // do the "delete"
        
        $this->startTransaction();
        $aParams = array('sNamespace' => $sNamespace, 'iUserId' => $iUserId);
        $oDD = KTDashletDisable::createFromArray($aParams);
        if (PEAR::isError($oDD)) {
            $this->errorRedirectToMain('Failed to disable the dashlet.');
        }
    
        $this->commitTransaction();
        $this->successRedirectToMain('Dashlet disabled.');
    }


    function json_saveDashboardState() {
        $sState = KTUtil::arrayGet($_REQUEST, 'state', array('error'=>true));
        $this->oUser->setDashboardState($sState);
        return array('success' => true);
    }
}

$oDispatcher = new DashboardDispatcher();
$oDispatcher->dispatch();

?>

