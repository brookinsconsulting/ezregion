<?php
//
// $Id: countrylist.php,v 1.3 2001/07/19 12:06:56 jakobn Exp $
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


include_once( "ezaddress/classes/ezcountry.php" );

$ini =& INIFile::globalINI();
$Max = $ini->read_var( "eZAddressMain", "MaxCountryList" );

include( "ezaddress/admin/typelist_pre.php" );

$language_file = "country.php";
$page_path = "/address/country";
$typelist = "typelist.tpl";
$template_array = array( "country_tpl" => "countrylist.tpl" );
$variable_array = array( "country_header" => "country_header_tpl",
                         "country_item" => "country_item_tpl",
                         "extra_type_header" => "country_header",
                         "extra_type_item" => "country_item" );
$block_array = array( array( "country_tpl", "country_header_tpl", "country_header" ),
                      array( "country_tpl", "country_item_tpl", "country_item" ) );

$item_type_array = eZCountry::getAll( true, $SearchText, $Index, $Max );
$total_types = eZCountry::getAllCount( $SearchText );
$func_call = array( "item_id" => 'id',
                    "item_name" => 'name',
                    "item_iso" => 'iso' );
$Searchable = true;

include( "ezaddress/admin/typelist.php" );

?>
