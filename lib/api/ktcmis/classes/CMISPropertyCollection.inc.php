<?php
/**
 * CMIS Repository Base Object Property Collection API class for KnowledgeTree.
 *
 * KnowledgeTree Community Edition
 * Document Management Made Simple
 * Copyright (C) 2008, 2009, 2010 KnowledgeTree Inc.
 * 
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

/**
 *
 * @copyright 2008-2010, KnowledgeTree Inc.
 * @license GNU General Public License version 3
 * @author KnowledgeTree Team
 * @package KTCMIS
 * @version Version 0.1
 */

/**
 * This class contains the base properties shared by all object types
 */
abstract class CMISPropertyCollection {

    static $objectId;
    static $baseTypeId;
    static $uri;
    static $objectTypeId;
    static $createdBy;
    static $creationDate;
    static $lastModifiedBy;
    static $lastModificationDate;
    static $changeToken;
    // TODO these definitions belong in their own classe definition (see property type definions,) but here will do for now
    static public $propertyTypes;

    public function __construct()
    {
        self::$propertyTypes = array('objectId' => 'propertyId',
                                     'author' => 'propertyString',
                                     'baseTypeId' => 'propertyId',
                                     'objectTypeId' => 'propertyId',
                                     'createdBy' => 'propertyString',
                                     'creationDate' => 'propertyDateTime',
                                     'lastModifiedBy' => 'propertyString',
                                     'lastModificationDate' => 'propertyDateTime',
                                     'name' => 'propertyString',
                                     'uri' => 'propertyUri',
                                     'allowedChildObjectTypeIds' => 'propertyId',
                                     'createdBy' => 'propertyString',
                                     'creationDate' => 'propertyDateTime',
                                     'changeToken' => 'propertyString',
                                     'parentId' => 'propertyId');
    }

    /**
     * Gets the property value.
     */
    public function getValue($field)
    {
        return $this->{$field};
    }

    /**
     * Sets the property value.
     */
    // for connection-tied live objects
    public function setValue($field, $value)
    {
        $this->{$field} = $value;
    }

    public function getFieldType($field)
    {
        return $this->propertyTypes[$field];
    }

}

?>
