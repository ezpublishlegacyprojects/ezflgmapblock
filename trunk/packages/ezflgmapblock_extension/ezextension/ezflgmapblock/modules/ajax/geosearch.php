<?php
//
// Definition of classname class
//
// Created on: <Jul 2, 2008 2008 12:06:11 PM nfrp>
//
// ## BEGIN COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
// SOFTWARE NAME: eZ publish
// SOFTWARE RELEASE: 3.10.x
// COPYRIGHT NOTICE: Copyright (C) 1999-2006 eZ systems AS
// SOFTWARE LICENSE: GNU General Public License v2.0
// NOTICE: >
//   This program is free software; you can redistribute it and/or
//   modify it under the terms of version 2.0  of the GNU General
//   Public License as published by the Free Software Foundation.
//
//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.
//
//   You should have received a copy of version 2.0 of the GNU General
//   Public License along with this program; if not, write to the Free
//   Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
//   MA 02110-1301, USA.
//
//
// ## END COPYRIGHT, LICENSE AND WARRANTY NOTICE ##
//
$http = eZHTTPTool::instance();
$response = array();

// Retrieve parameters
$radius           = $http->hasVariable( 'radius' ) ? $http->variable( 'radius' ) : 10000;
$latitude         = $http->hasVariable( 'lat' ) ? $http->variable( 'lat' ) : 48.8566667;  // default : Paris
$longitude        = $http->hasVariable( 'long' ) ? $http->variable( 'long' ) : 2.3509871; // default : Paris
$seed             = $http->hasVariable( 'seed' ) ? $http->variable( 'seed' ) : rand( 0, 10000 );
$mapId            = $http->hasVariable( 'mapId' ) ? $http->variable( 'mapId' ) : rand( 0, 10000 );
$showPopupsOnPage = $http->hasVariable( 'showPopupsOnPage' ) ? $http->variable( 'showPopupsOnPage' ) : true;
$locationAttribute = $http->hasVariable( 'locationAttribute' ) ? $http->variable( 'locationAttribute' ) : 'location';
$shortDescriptiveAttribute = $http->hasVariable( 'shortDescriptiveAttribute' ) ? $http->variable( 'shortDescriptiveAttribute' ) : null;

$width            = $http->hasVariable( 'width' ) ? $http->variable( 'width' ) : 440;
$height           = $http->hasVariable( 'height' ) ? $http->variable( 'height' ) : 600;
$size = array( $width, $height );

$geoCalculation = new GeoCalculations();
try
{
    $searchZoneDesc = $geoCalculation->getSearchZoneDescription( array( $latitude, $longitude ), $radius );
    $solrFilters = $geoCalculation->generateSolrSearchZoneFilters( $searchZoneDesc );

    $solr = new eZSolr();
    $params['Filter'] = array(
        "attr_location_longitude_sf" => $solrFilters['longitude-filter'],
        "attr_location_latitude_sf" => $solrFilters['latitude-filter'],
    );

    //$params['QueryHandler'] = 'simplestandard';
    $searchResults = $solr->search( '', $params );
    $count = $searchResults['SearchCount'];
    $response['searchCount'] = $count;

    if ( $searchResults['SearchResult'] !== false )
    {
        include_once( 'kernel/common/template.php' );
        $tpl = templateInit();
        $tpl->setVariable( "seed", $seed );
        $tpl->setVariable( "locations", $searchResults['SearchResult'] );
        $tpl->setVariable( "map_id", $mapId );
        $tpl->setVariable( "size", $size );
        $tpl->setVariable( "show_popups_on_page", $showPopupsOnPage );
        $tpl->setVariable( "location_attribute", $locationAttribute );
        if ( $shortDescriptiveAttribute )
            $tpl->setVariable( "short_descriptive_attribute", $shortDescriptiveAttribute );

        $response['gmapMaterialPoints'] = $tpl->fetch( 'design:parts/gmap_material_points.tpl' );
        $response['gmapMaterialListing'] = $tpl->fetch( 'design:parts/gmap_material_listing.tpl' );
        $response['feedback'] = 'ok';
    }
    else
    {
        $response['searchCount'] = 0;
        $response['feedback'] = $searchResults['SearchExtras']->attribute( 'error' );
    }
}
catch ( Exception $e )
{
    $response['feedback'] = $e->getMessage();
}

/*
=> gmap_material.tpl
=> facets
=> feedback ( error, no result )
*/

// @WARNING : PHP 5.1+ only.
echo json_encode( $response );
eZExecution::cleanExit();
?>