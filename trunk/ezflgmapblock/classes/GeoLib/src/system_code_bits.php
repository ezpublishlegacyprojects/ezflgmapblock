<?php
//
// Definition of classname class
//
// Created on: <Jun 19, 2009 2009 3:55:41 PM nfrp>
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


$argumentList = array();
$executable = $this->Executable;

/*
if ( eZSys::osType() == 'win32' and $this->ExecutableWin32 )
    $executable = $this->ExecutableWin32;
else if ( eZSys::osType() == 'mac' and $this->ExecutableMac )
    $executable = $this->ExecutableMac;
else if ( eZSys::osType() == 'unix' and $this->ExecutableUnix )
    $executable = $this->ExecutableUnix;
if ( $this->Path )
    $executable = $this->Path . eZSys::fileSeparator() . $executable;
if ( eZSys::osType() == 'win32' )
    $executable = "\"$executable\"";
*/

$argumentList[] = $executable;

## args management
$qualityParameter = 'quality=%1';
$outputQuality = 200;
var_dump( eZSys::createShellArgument( $qualityParameter, array( '%1' => $outputQuality ) ) );

# OR

eZSys::escapeShellArgument( $sourceMimeData['url'] );

## end of args management

$systemString = implode( ' ', $argumentList );
if ( eZSys::osType() == 'win32' )
    $systemString = "\"$systemString\"";

system( $systemString, $returnCode );

if ( $returnCode == 0 )
{
    // OK
    return true;
}
else
{
    // !OK
    eZDebug::writeWarning( "Failed executing: $systemString, Error code: $returnCode", 'eZImageShellHandler::convert' );
    return false;
}

?>