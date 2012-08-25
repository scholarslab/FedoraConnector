<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4; */

/**
 * DC importer.
 *
 * @package     omeka
 * @subpackage  fedoraconnector
 * @author      Scholars' Lab <>
 * @author      David McClure <david.mcclure@virginia.edu>
 * @copyright   2012 The Board and Visitors of the University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */


class MODS_Importer extends FedoraConnector_AbstractImporter
{

    /**
     * Checks to see if the datastream is MODS format.
     *
     * @param object $dsid The dsid.
     *
     * @return boolean True if format is MODS.
     */
    public function canImport($dsid)
    {
        return ($dsid == 'descMetadata');
    }

    /**
     * Get xpath queries to extract node for a given DC element.
     *
     * @param string $name The DC name of the element.
     *
     * @return array The array of xpath queries.
     */
    public function getQueries($name)
    {

        switch ($name) {

            case 'Title':

                $queries = array(
                    '//*[local-name()="mods"]/*[local-name()="titleInfo"]'
                    . '/*[local-name()="title"]'
                );

                break;

            case 'Creator':

                $queries = array(
                    '//*[local-name()="mods"]'
                    . '/*[local-name()="name"][*[local-name()="role"] = "creator"]'
                );

                break;

            case 'Subject':

                $queries = array(
                    '//*[local-name()="mods"]/*[local-name()="subject"]'
                    . '/*[local-name()="topic"]'
                );

                break;

            case 'Description':

                $queries = array(
                    '//*[local-name()="mods"]/*[local-name()="abstract"]',
                    '//*[local-name()="mods"]/*[local-name()="note"]',
                    '//*[local-name()="mods"]/*[local-name()="tableOfContents"]'
                );

                break;

            case 'Publisher':

                $queries = array(
                    '//*[local-name()="mods"]/*[local-name()="originInfo"]'
                    . '/*[local-name()="publisher"]'
                );

                break;

            case 'Contributor':

                // Mapping from name/namePart to Contributor specifically is 
                // difficult.  There are likely institutional differences in 
                // mapping.
                $queries = array();

                break;

            case 'Date':

                $prefix = '//*[local-name()="mods"]/*[local-name()="originInfo"]';
                $queries = array(
                    $prefix . '/*[local-name()="dateIssued"]',
                    $prefix . '/*[local-name()="dateCreated"]',
                    $prefix . '/*[local-name()="dateCaptured"]',
                    $prefix . '/*[local-name()="dateOther"]'
                );

                break;

            case 'Type':

                // XXX: Originally, this set $queries to something sane and
                // immediately set it again to an empty array.  I need to test to
                // make sure I'm using the right one.
                $queries = array(
                    '//*[local-name()="mods"]/*[local-name()="typeOfResource"]',
                    '//*[local-name()="mods"]/*[local-name()="genre"]'
                );

                break;

            case 'Format':

                // XXX: Originally, this set $queries to something sane and
                // immediately set it again to an empty array.  I need to test to
                // make sure I'm using the right one.
                $prefix
                    = '//*[local-name()="mods"]'
                    . '/*[local-name()="physicalDescription"]';
                $queries = array(
                    $prefix . '/*[local-name()="internetMediaType"]',
                    $prefix . '/*[local-name()="extent"]',
                    $prefix . '/*[local-name()="form"]'
                );

                break;

            case 'Identifier':

                $queries = array(
                    '//*[local-name()="mods"]/*[local-name()="identifier"]',
                    '//*[local-name()="mods"]/*[local-name()="location"]'
                    . '/*[local-name()="uri"]'
                );

                break;

            case 'Source':

                $prefix
                    = '//*[local-name()="mods"]/*[local-name()="relatedItem"]'
                    . '[@type="original"]/*';
                $queries = array(
                    $prefix .  '[local-name()="titleInfo"]/*[local-name()="title"]',
                    $prefix .  '[local-name()="location"]/*[local-name()="url"]'
                );

                break;

            case 'Language':

                $queries = array(
                    '//*[local-name()="mods"]/*[local-name()="language"]'
                );

                break;

            case 'Relation':

                $prefix
                    = '//*[local-name()="mods"]/*[local-name()="relatedItem"]/*';
                $queries = array(
                    $prefix .  '[local-name()="titleInfo"]/*[local-name()="title"]',
                    $prefix .  '[local-name()="location"]/*[local-name()="url"]'
                );

                break;

            case 'Coverage':

                $prefix = '//*[local-name()="mods"]/*[local-name()="subject"]/*';
                $queries = array(
                    $prefix . '[local-name()="temporal"]',
                    $prefix . '[local-name()="geographic"]',
                    $prefix . '[local-name()="hierarchicalGeographic"]',
                    $prefix . '[local-name()="cartographics"]'
                );

                break;

            case 'Rights':

                $queries == array(
                    '//*[local-name()="mods"]/*[local-name()="accessCondition"]'
                );

                break;

        }

        return $queries;

    }

}
