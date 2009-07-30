<?php
//
// Definition of GeoCalculations class
//
// Created on: <Jun 19, 2009 2009 3:50:39 PM nfrp>
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

/**
 *
 * Using :
 * "GeographicLib is a small set of C++ classes for performing conversions
 * between geographic, UTM, UPS, MGRS, geocentric, and local cartesian
 * coordinates and for solving geodesic problems. The emphasis is on returning
 * accurate results with errors close to round-off (about 5Ð15 nm).
 * In addition, various properties of the Transverse Mercator Projection
 * are described and an accurate algorithm for Geodesics on the Spheroid is given."
 *
 * @uses GeographicLib ( from Charles Karney <charles@karney.com> )
 * @see http://charles.karney.info/geographic/
 */
class GeoCalculations
{
    /**
     * GeoConvert is a command line utility for geographic
     * coordinate conversions via GeographicLib::GeoCoords.
     *
     * @todo : this should become a static property, modified by the constructor
     *         according to the local OS.
     *         Example : under windows, it should become GeoConvert.exe.
     */
    const GEO_CONVERT = 'GeoConvert';

    /**
     * Default scale parameter for all bc math functions.
     * @see http://fr2.php.net/manual/en/function.bcscale.php
     * @todo : make this parameter settable
     *         so that the accuracy can be adjusted on demand.
     */
    const BCSCALE = 20;

    /**
     * Earth Radius, in meters.
     * @see http://en.wikipedia.org/wiki/Earth_radius
     */
    const EARTH_RADIUS = 6371009;

    public function __construct()
    {
        bcscale( self::BCSCALE  );
    }

    /**
     * Generate the Solr compliant filter values, which can then be used to filter a search result
     * by positionning criterias.
     *
     * @param array $searchZoneDescription The result of GeoCalculations::getSearchZoneDescription()
     * @return array First element of the array contains the latitude filter value (string)
     *               Second element of the array contains the longitude filter value (string)
     */
    public function generateSolrSearchZoneFilters( array $searchZoneDescription )
    {
        $latFilter  = '[' . $searchZoneDescription[0][0] . ' TO ' . $searchZoneDescription[1][0] . ']';
        $longFilter = '[' . $searchZoneDescription[3][1] . ' TO ' . $searchZoneDescription[2][1] . ']';
        $result = array( 'latitude-filter'  => $latFilter,
                         'longitude-filter' => $longFilter );
        return $result;
    }

    /**
     * Returns the elements fully describing the search zone. For now, the "bounds"
     * of the square search zone are returned under the form of four geo points,
     * each of them being a pair of decimal coordinates.
     *
     * @param array $origin Origin of the search. An array of decimal coordinates,
     *              typically obtained from an address-to-coordinates GMaps conversion.
     * @param float $radius Radius describing the search zone, positive floating point number.
     * @return array the search zone description, array of 4 points
     * @see GeoCalculations::comcomputeZoneBounds
     * @throws GeoCalculationsWrongArgumentFormatException, GeoCalculationsSystemExecutionException from subcalls.
     */
    public function getSearchZoneDescription( array $origin, $radius )
    {
        // degrees --> radians
        $radianOrigin = $this->convertDegreeCoordinateToRadians( $origin );

        // compute
        $zoneDescription = $this->computeZoneBounds( $radianOrigin, $radius );

        // radians --> degrees
        $degreesZoneDescription = array();
        foreach ( $zoneDescription as $desc )
        {
            $degreesZoneDescription[] = $this->convertRadianCoordinateToDegrees( $desc );
        }

        return $degreesZoneDescription;
    }

    /**
     * Calculates the coordinates of the significant geographical search bounds.
     * For now, the following approximation is assumed :

        +----- A -----+
        |      |      |
        |      r      |
        C ---r O r--- D
        |      r      |
        |      |      |
        +----- B -----+

     * Where :
     *  * r    = Search radius
     *  * O    = The origin of the geographical search
     *  * A, B = the 2 points having the same longitude as O, r meters away from 0, marking the northern and southern search zone frontiers.
     *  * C, D = the 2 points having the same latitude as O, r meters away from 0 marking the eastern and western search zone frontiers.
     *
     * @param array $origin The O point. A {latitude, longitude} array, containing radian coordinates.
     * @param float $radius The search radius, in meters.
     * @return array The coordinates of A,B,C,D , each as an array of radian coordinates.
     */
    protected function computeZoneBounds( array $origin, $radius )
    {
        $result = array();

        if ( !is_numeric( $radius ) or gmp_sign( $radius ) <= 0 )
        {
            $message = "The radius must be a positive floating point value, in meters.";
            throw new GeoCalculationsWrongArgumentFormatException( $message );
        }

        if ( count( $origin ) !== 2 )
        {
            $message = "The origin point must be passed as a {latitude, longitude} pair (array).";
            throw new GeoCalculationsWrongArgumentFormatException( $message );
        }

        $OLatitude = $origin[0];
        $OLongitude = $origin[1];

        $ALatitude = bcsub( $OLatitude, bcdiv( $radius, self::EARTH_RADIUS ) );
        $Apoint = array( $ALatitude, $OLongitude );

        $BLatitude = bcadd( $OLatitude, bcdiv( $radius, self::EARTH_RADIUS ) );
        $Bpoint = array( $BLatitude, $OLongitude );

        $CLongitude = bcadd( $OLongitude, bcdiv( $radius, self::EARTH_RADIUS ) );
        $Cpoint = array( $OLatitude, $CLongitude );

        $DLongitude = bcsub( $OLongitude, bcdiv( $radius, self::EARTH_RADIUS ) );
        $Dpoint = array( $OLatitude, $DLongitude );

        $result[] = $Apoint;
        $result[] = $Bpoint;
        $result[] = $Cpoint;
        $result[] = $Dpoint;
        return $result;
    }

    /**
     * Converts the input decimal coordinates to degrees coordinates.
     *
     * @param mixed Array value containing the decimal coordinate(s).
     *              The coordinates must be passed as {latitude, longitude} pairs.
     * @return array the input coordinate in degress
     * @throws GeoCalculationsWrongArgumentFormatException when coordinates are not passed as pairs.
     */
    protected function convertDecimalCoordinatesToDegrees( array $decimalCoordinates )
    {
        $result = array();

        if ( count( $decimalCoordinates ) % 2 !== 0 )
        {
            $message = "Decimal coordinates must be passed as {latitude, longitude} pairs.";
            throw new GeoCalculationsWrongArgumentFormatException( $message );
        }
        else
        {
            // echo 33.3 44.4 | GeoConvert -d
            $postCommand = self::GEO_CONVERT;
            $postArguments = array( '-d' );
            $postFullCommand = ' | ' . $this->generateSystemCommand( $postCommand, $postArguments );

            $preCommand = 'echo';

            for ( $i = 0; $i < count( $decimalCoordinates ); $i +=2 )
            {
                $preArguments = array();
                $preArguments[] = $decimalCoordinates[$i];
                $preArguments[] = $decimalCoordinates[$i+1];
                $tmpResult =  $this->executeSystemCommand( $this->generateSystemCommand( $preCommand, $preArguments ) . $postFullCommand );
                $result = array_merge( $result, explode( ' ', $tmpResult[0] ) );
            }
            return $result;
        }
    }

    /**
     * Converts the input radian coordinate to degrees.
     *
     * @param mixed a scalar or array value containing the radian coordinate(s)
     * @return array the input coordinate in degrees
     */
    protected function convertRadianCoordinateToDegrees( $radianCoordinate )
    {
        $radianCoordinate = is_array( $radianCoordinate ) ? $radianCoordinate : array( $radianCoordinate ) ;
        $result = array();

        foreach ( $radianCoordinate as $coordinate )
        {
            // $result[] = ( float ) $coordinate * 180 / M_PI;
            $result[] = bcdiv( bcmul( $coordinate, '180' ), M_PI );
            //$result[] = rad2deg( $coordinate );
        }
        return $result;
    }

    /**
     * Converts the input degree coordinate to radians.
     *
     * @param mixed a scalar or array value containing the degrees coordinate(s)
     * @return array the input coordinate in radians
     */
    protected function convertDegreeCoordinateToRadians( $degreeCoordinate )
    {
        $degreeCoordinate = is_array( $degreeCoordinate ) ? $degreeCoordinate : array( $degreeCoordinate ) ;
        $result = array();

        foreach ( $degreeCoordinate as $coordinate )
        {
            // $result[] = ( float ) $coordinate *  M_PI / 180;
            $result[] = bcdiv( bcmul( $coordinate, M_PI ), '180' );
            //$result[] = deg2rad( $coordinate );
        }
        return $result;
    }

    /**
     * Generates a system command line, and returns it as a string.
     *
     * @param string $executableName The name of the executable
     * @param array $arguments Parameters to the executable
     *
     * @return string The generated command line, which can then be executed
     *
     * @link http://www.php.net/manual/en/function.escapeshellcmd.php
     *       http://www.php.net/manual/en/function.escapeshellarg.php
     *
     * @todo Have a better argument management ( key-value pairs, single ones ).
     *       Use an Option object for this, with a smart __toString() method ?
     */
    protected function generateSystemCommand( $executableName, array $arguments = array() )
    {
        $command = $executableName;
        foreach ( $arguments as $key => $value )
        {
            // Numeric key here, assumed for now that the argument was not an
            // explicit key-value pair, but a plain value
            if ( is_numeric( $key ) )
                $key = '';

            $command .= ' ' . escapeshellcmd( $key ) . ' ' . escapeshellcmd( $value );
        }
        return $command;
    }

    /**
     * Calls a system executable, and returns the exit code.
     *
     * @param string $executableName The name of the executable
     * @param array $arguments Parameters the executable should be called with.
     *
     * @return mixed The output of the command of the return code means OK
     * @throws GeoCalculationsSystemExecutionException
     *         if the property $name does not exist
     *
     */
    protected function callSystemExecutable( $executableName, array $arguments = array() )
    {
        $fullCommandLine = $this->generateSystemCommand( $executableName, $arguments );
        return $this->executeSystemCommand( $fullCommandLine );
    }

    /**
     * Executes a system command line.
     *
     * @param string $fullCommandLine The full command line
     *
     * @return mixed The output of the command of the return code means OK
     * @throws GeoCalculationsSystemExecutionException
     *         if the property $name does not exist
     *
     */
    protected function executeSystemCommand( $fullCommandLine )
    {
        $output = null;
        $returnCode = null;
        $lastLine = exec( $fullCommandLine, $output, $returnCode );

        if ( $returnCode == 0 )
        {
            return $output;
        }
        else
        {
            throw new GeoCalculationsSystemExecutionException( $returnCode, $fullCommandLine, $lastLine );
        }
    }
}
?>