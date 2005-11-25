<?php

/** BrowserColumns
 * 
 *  Presentation and render logic for the different columns.  Each has two
 *  major methods:
 *
 *     function renderHeader($sReturnURL)
 *     function renderData($aDataRow)
 *  
 *  renderHeader returns the _content_ of the header row.
 *  renderData returns the _content_ of the body row.
 */
 
require_once(KT_LIB_DIR . "/database/dbutil.inc");

class BrowseColumn {
    var $label = null;
    var $sort_on = false;
    var $sort_direction = "asc";
    var $name = "-";
    
    function BrowseColumn($sLabel, $sName) { 
       $this->label = $sLabel; 
       $this->name = $sName; 
    }
    // FIXME is it _really_ worth using a template here?
    function renderHeader($sReturnURL) { 
        $text = "Abstract: " . $this->label; 
        $href = $sReturnURL . "&sort_on=" . $this->name . "&sort_order=";
        $href .= $this->sort_direction == "asc"? "desc" : "asc" ;
        
        return '<a href="' . $href . '">'.$text.'</a>';
        
    }
    function renderData($aDataRow) { 
       if ($aDataRow["type"] == "folder") {
           return $this->name . ": ". print_r($aDataRow["folder"]->getName(), true);            
        } else {
           return $this->name . ": ". print_r($aDataRow["document"]->getName(), true); 
        }
    }
    function setSortedOn($bIsSortedOn) { $this->sort_on = $bIsSortedOn; }
    function getSortedOn() { return $this->sort_on; }
    function setSortDirection($sSortDirection) { $this->sort_direction = $sSortDirection; }
    function getSortDirection() { return $this->sort_direction; }
    
    function addToFolderQuery() { return array(null, null, null); }
    function addToDocumentQuery() { return array(null, null, null); }
}

class TitleColumn extends BrowseColumn {
    var $aOptions = array();
    function setOptions($aOptions) {
        $this->aOptions = $aOptions;
    }
    // unlike others, this DOESN'T give its name.
    function renderHeader($sReturnURL) { 
        $text = "Title";
        $href = $sReturnURL . "&sort_on=" . $this->name . "&sort_order=";
        $href .= $this->sort_direction == "asc"? "desc" : "asc" ;
        
        return '<a href="' . $href . '">'.$text.'</a>';
        
    }

    function renderFolderLink($aDataRow) {
        $outStr = '<a href="' . $this->buildFolderLink($aDataRow) . '">';
        $outStr .= $aDataRow["folder"]->getName();
        $outStr .= '</a>';
        return $outStr;
    }

    function renderDocumentLink($aDataRow) {
        $outStr = '<a href="' . $this->buildDocumentLink($aDataRow) . '" title="' . $aDataRow["document"]->getFilename().'">';
        $outStr .= $aDataRow["document"]->getName();
        $outStr .= '</a>';
        return $outStr;
    }

    function buildDocumentLink($aDataRow) {
        $baseurl = KTUtil::arrayGet($this->aOptions, "documenturl", "documentmanagement/view.php");
        return $baseurl . '?fDocumentId=' .  $aDataRow["document"]->getId();
    }

    function buildFolderLink($aDataRow) {
        $baseurl = KTUtil::arrayGet($this->aOptions, "folderurl", "");
        return $baseurl . '?fFolderId='.$aDataRow["folder"]->getId();
    }
    
    // use inline, since its just too heavy to even _think_ about using smarty.
    function renderData($aDataRow) { 
       $outStr = '';
       if ($aDataRow["type"] == "folder") {
           $outStr .= '<span class="contenttype folder">';
           $outStr .= $this->renderFolderLink($aDataRow);
           $outStr .= '</span>';           
        } else {
           $outStr .= '<span class="contenttype '.$this->_mimeHelper($aDataRow["document"]->getMimeTypeId()).'">';
           $outStr .= $this->renderDocumentLink($aDataRow);
           $outStr .= ' (' . $this->prettySize($aDataRow["document"]->getSize()) . ')';
           $outStr .= '</span>';
        }
        return $outStr;
    }
    
    function prettySize($size) {
        $finalSize = $size;
        $label = 'b';
        
        if ($finalSize > 1000) { $label='Kb'; $finalSize = floor($finalSize/1000); }
        if ($finalSize > 1000) { $label='Mb'; $finalSize = floor($finalSize/1000); }
        return $finalSize . $label;
    }
    
    function _mimeHelper($iMimeTypeId) {
        // FIXME lazy cache this.
        $sQuery = 'SELECT icon_path FROM mime_types WHERE id = ?';
        $res = DBUtil::getOneResult(array($sQuery, array($iMimeTypeId)));
        
        if ($res[0] !== null) {
           return $res[0];
        } else {
           return 'unspecified_type';
        }
    }
}

class DateColumn extends BrowseColumn {
    var $field_function;
    
    // $sDocumentFieldFunction is _called_ on the document.
    function DateColumn($sLabel, $sName, $sDocumentFieldFunction) {
        $this->field_function = $sDocumentFieldFunction;
        parent::BrowseColumn($sLabel, $sName);
        
    }
    
    function renderHeader($sReturnURL) { 
        $text = $this->label;
        $href = $sReturnURL . "&sort_on=" . $this->name . "&sort_order=";
        $href .= $this->sort_direction == "asc"? "desc" : "asc" ;
        
        return '<a href="' . $href . '">'.$text.'</a>';
        
    }
    
    // use inline, since its just too heavy to even _think_ about using smarty.
    function renderData($aDataRow) { 
       $outStr = '';
       if ($aDataRow["type"] == "folder") {
           $outStr = '&nbsp;';       // no-op on folders.
        } else {
           $fn = $this->field_function;
           $dColumnDate = strtotime($aDataRow["document"]->$fn());
           
           // now reformat this into something "pretty"
           $outStr = date("d M, Y  H\\hi", $dColumnDate);
        }
        return $outStr;
    }
    
    function _mimeHelper($iMimeTypeId) {
        // FIXME lazy cache this.
        $sQuery = 'SELECT icon_path FROM mime_types WHERE id = ?';
        $res = DBUtil::getOneResult(array($sQuery, array($iMimeTypeId)));
        
        if ($res[0] !== null) {
           return $res[0];
        } else {
           return 'unspecified_type';
        }
    }
}

// use the _name_ parameter + _f_ + id to create a non-clashing checkbox.

class SelectionColumn extends BrowseColumn {

    function renderHeader($sReturnURL) { 
        // FIXME clean up access to oPage.
        global $main;
        $main->requireJSResource("resources/js/toggleselect.js");
        
        return '<input type="checkbox" title="toggle all" onactivate="toggleSelectFor('.$this->name.')">';
        
    }
    
    // use inline, since its just too heavy to even _think_ about using smarty.
    function renderData($aDataRow) { 
        $localname = $this->name . '_';
        if ($aDataRow["type"] === "folder") { $localname .= "f_". $aDataRow["folderid"]; }
        else { $localname .= "d_" . $aDataRow["docid"]; }
        
        return '<input type="checkbox" name="'.$localname.'" onactivate="activateRow(this)">';
    }
    
    function _mimeHelper($iMimeTypeId) {
        // FIXME lazy cache this.
        $sQuery = 'SELECT icon_path FROM mime_types WHERE id = ?';
        $res = DBUtil::getOneResult(array($sQuery, array($iMimeTypeId)));
        
        if ($res[0] !== null) {
           return $res[0];
        } else {
           return 'unspecified_type';
        }
    }
}

?>
