<?php
//
// $Id: datasupplier.php,v 1.32 2001/10/04 14:31:14 bf Exp $
//
// Created on: <23-Oct-2000 17:53:46 bf>
//
// This source file is part of eZ publish, publishing software.
//
// Copyright (C) 1999-2001 eZ Systems.  All rights reserved.
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, US
//


$ini =& INIFile::globalINI();
if ( isset( $GlobalSectionIDOverride ) )
{
    $GlobalSectionID = $GlobalSectionIDOverride;
}
else
{
    $GlobalSectionID = $ini->read_var( "eZUserMain", "DefaultSection" );
}

switch ( $url_array[2] )
{

    case "login" :
    {
        $Action = $url_array[3];
        include( "ezuser/user/login.php" );
    }
    break;

    case "loginmain" :
    {
        $Action = $url_array[3];
        include( "ezuser/user/loginmain.php" );
    }
    break;

    case "norights":
    {
        include( "ezuser/user/norights.php" );        
    }
    break;

    case "user" :
    case "userwithaddress" :
    {
        if ( $url_array[3] == "new" )
        {
            $Action = "New";
			//$RedirectURL = $session->variable( "RedirectURL" );
        }
        if ( $url_array[3] == "edit" )
        {
            if ( $url_array[5] == "MissingAddress" )
                $MissingAddress = true;
            else
                $MissingAddress = false;
            if ( $url_array[5] == "MissingCountry" )
                $MissingCountry = true;
            else
                $MissingCountry = false;

            $UserID = $url_array[4];
            $Action = "Edit";
			//$RedirectURL = "/BypassRedirect/Edit/";
			//$RedirectURL = $RedirectURL;
			//$RedirectURL = $session->variable( "RedirectURL" );
			if ( !isset( $RedirectURL ) or !$RedirectURL )
		        $RedirectURL = $session->variable( "RedirectURL" );
			
        }
        if ( $url_array[3] == "update" )
        {
            $UserID = $url_array[4];
            $Action = "Update";
			if ( !isset( $RedirectURL ) or !$RedirectURL )
		        $RedirectURL = $session->variable( "RedirectURL" );
        }

        if ( $url_array[3] == "insert" )
			//$RedirectURL = "/BypassRedirect/Insert";
            $Action = "Insert";

        $OverrideUserWithAddress = $ini->read_var( "eZUserMain", "OverrideUserWithAddress" );

        if ( empty( $OverrideUserWithAddress ) )
        {
            include( "ezuser/user/userwithaddress.php" );
        }
        else
        {
            if ( is_file( $OverrideUserWithAddress ) )
            {
                include( $OverrideUserWithAddress );
            }
            else
            {
                include( "ezuser/user/userwithaddress.php" );
            }
        }

    }
    break;

    case "forgot" :
    {
        $Action = $url_array[3];
        $Hash = $url_array[4];
        include( "ezuser/user/forgot.php" );
    }
    break;

    case "address" :
    {
        if ( $url_array[3] == "new" )
            $Action = "New";
        if ( $url_array[3] == "insert" )
            $Action = "Insert";

        if ( $url_array[3] == "edit" )
        {
            $Action = "Edit";
        }
        if ( $url_array[3] == "update" )
        {
            $Action = "Update";
        }
        
        include( "ezuser/user/addressedit.php" );
    }
    break;

    case "logout" :
    {
        $Action = $url_array[2];
        include( "ezuser/user/login.php" );
    }
    break;

    case "unsuccessfull":
    {
        $unsuccessfull = true;
        include( "ezuser/user/forgotmessage.php" );
    }
    break;

    case "successfull":
    {
        $successfull = true;
        include( "ezuser/user/forgotmessage.php" );
    }
    break;

    case "missingemail":
    {
        $successfull = true;
        include( "ezuser/user/missingemailmessage.php" );
    }
    break;

    case "generated":
    {
        $generated = true;
        include( "ezuser/user/forgotmessage.php" );
    }
    break;

    default :
    {
        include( "ezuser/user/login.php" );
    }
    break;


}
?>
