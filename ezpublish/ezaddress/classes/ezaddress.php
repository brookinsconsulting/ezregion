<?php
//
// $Id: ezaddress.php,v 1.19 2001/10/15 06:22:26 graham Exp $
//
// Definition of eZAddress class
//
// Created on: <07-Oct-2000 12:34:13 bf>
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

//!! eZAddress
//! eZAddress handles addresses.
/*!
  NOTE: this class defaults to Norwegian country is none is
  set.
*/

include_once( "classes/ezdb.php" );
include_once( "ezaddress/classes/ezcountry.php" );
include_once( "ezaddress/classes/ezaddresstype.php" );
include_once( "ezaddress/classes/ezregion.php" );

class eZAddress
{
    /*!
      Constructs a new eZAddress object.
    */
    function eZAddress( $id = "" )
    {
        if ( $id != "" )
        {

            $this->ID = $id;
            $this->get( $this->ID );
        }
    }

    /*!
      graham@brookinsconsulting.com @ 1-19-2002 : recoded for eZRegion 
  
      Note: Not tested.

      Stores a eZAddress
    */

    function store()
    {
        $db =& eZDB::globalDatabase();
        $db->begin();
        
        $ret = false;
        if ( $this->CountryID <= 0 )
            $country_id = "NULL";
        else
            $country_id = "$this->CountryID";
   
	if ( $this->RegionID <= 0 )                           
            $region_id = "NULL";               
    	else
            $region_id = "$this->RegionID";  

        $street1 = $db->escapeString( $this->Street1 );
        $street2 = $db->escapeString( $this->Street2 );
        $name = $db->escapeString( $this->Name );
        $place = $db->escapeString( $this->Place );
        if ( !isSet( $this->ID ) )
        {
            $db->lock( "eZAddress_Address" );
			$this->ID = $db->nextID( "eZAddress_Address", "ID" );
            $res[] = $db->query( "INSERT INTO eZAddress_Address
                                  (ID, Street1, Street2, Zip, Place, CountryID, RegionID, AddressTypeID, Name)
                                  VALUES
                                  ('$this->ID',
                                   '$street1',
                                   '$street2',
                                   '$this->Zip',
                                   '$place',
                                   '$country_id',
				   '$region_id',
                                   '$this->AddressTypeID',
                                   '$name')" );
            $db->unlock();
            $ret = true;
        }
        else
        {
            $res[] = $db->query( "UPDATE eZAddress_Address
                                  SET Street1='$street1',
                                  Street2='$street2',
                                  Zip='$this->Zip',
                                  Place='$place',
                                  AddressTypeID='$this->AddressTypeID',
                                  Name='$name',
                                  CountryID=$country_id,
				  RegionID=$region_id  
                                  WHERE ID='$this->ID'" );            
            $ret = true;            
        }

        eZDB::finish( $res, $db );
        return $ret;
    }

    /*! 
      graham@brookinsconsulting.com @ 1-19-2002 : recoded for eZRegion

      Note: Not tested.

      Fetches an address with object id==$id;
    */  
    function get( $id="" )
    {
        $db =& eZDB::globalDatabase();
        if ( $id != "" )
        {
            $db->array_query( $address_array, "SELECT * FROM eZAddress_Address WHERE ID='$id'" );
            if ( count( $address_array ) > 1 )
            {
                die( "Error: addresses with the same ID was found in the database. This shouldent happen." );
            }
            else if ( count( $address_array ) == 1 )
            {
                $this->ID =& $address_array[ 0 ][ $db->fieldName( "ID" ) ];
                $this->Street1 =& $address_array[ 0 ][ $db->fieldName( "Street1" ) ];
                $this->Street2 =& $address_array[ 0 ][ $db->fieldName( "Street2" ) ];
                $this->Zip =& $address_array[ 0 ][ $db->fieldName( "Zip" ) ];
                $this->Place =& $address_array[ 0 ][ $db->fieldName( "Place" ) ];
                $this->CountryID =& $address_array[ 0 ][ $db->fieldName( "CountryID" ) ];
		$this->RegionID =& $address_array[ 0 ][ $db->fieldName( "RegionID" ) ];
                $this->AddressTypeID =& $address_array[ 0 ][ $db->fieldName( "AddressTypeID" ) ];
                $this->Name =& $address_array[ 0 ][ $db->fieldName( "Name" ) ];
            }
            if ( $this->CountryID == "NULL" )
                $this->CountryID = -1;
 	    if ( $this->RegionID == "NULL" )    
                $this->RegionID = -1; 
        }
    }

    /*!
      Henter ut alle adressene lagret i databasen.
    */
    function getAll( )
    {
        $db =& eZDB::globalDatabase();
        $address_array = 0;
    
        $db->array_query( $address_array, "SELECT * FROM eZAddress_Address" );
    
        return $address_array;
    }

    /*!
      Sletter adressen med ID == $id;
     */
    function delete( $id = false )
    {
        if ( !$id )
            $id = $this->ID;
        $db =& eZDB::globalDatabase();
        $db->begin();
        $res[] = $db->query( "DELETE FROM eZAddress_Address WHERE ID='$id'" );
        eZDB::finish( $res, $db );
    }    
    

    /*!
      Copy this object.
    */
    function &copy( )
    {
        $new = $this;
        $new->unsetID();
        $new->store();

        return $new;
    }

    /*!
      Empty this ID.
    */
    function unsetID(  )
    {
        unset( $this->ID );
    }
    
    /*!
      Setter  street1.
    */
    function setStreet1( $value )
    {
        $this->Street1 = $value;
    }

    /*!
      Setter  street2.
    */
    function setStreet2( $value )
    {
        $this->Street2 = $value;
    }

    /*!
      Sets the name.
    */
    function setName( $value )
    {
        $this->Name = $value;
    }

    /*!
      graham@brookinsconsulting.com @ 1-19-2002 : recoded for eZRegion

      Set the region, takes an eZRegion object as an argument.
    */
    function setRegion( $region )
    {
       if ( get_class( $region ) == "ezregion" )
       {
           $this->RegionID = $region->id();
       }
       else if ( is_numeric( $region ) )
       {
           $this->RegionID = $region;
       }
    }

    /*!
      Setter postkode.
    */
    function setZip( $value )
    {
        $this->Zip = $value;
    }

    /*!
      Setter adressetype.
    */
    function setAddressType( $value )
    {
        if( is_numeric( $value ) )
        {
            $this->AddressTypeID = $value;
        }
        
        if( get_class( $value ) == "ezaddresstype" )
        {
            $this->AddressTypeID = $value->id();
        }
    }

    /*!
      Setter adressetype.
    */
    function setAddressTypeID( $value )
    {
        if( is_numeric( $value ) )
        {
            $this->AddressTypeID = $value;
        }
        
        if( get_class( $value ) == "ezaddresstype" )
        {
            $this->AddressTypeID = $value->id();
        }
    }

    /*!
      Sets the main address
    */
    function setMainAddress( $mainAddress, $user )
    {
        if ( get_class( $mainAddress ) == "ezaddress" )
            $addressID = $mainAddress->id();
        else
            $addressID = $mainAddress;
        if ( get_class ( $user ) == "ezuser" )
            $userID = $user->id();
        else
            $userID = $user;

        $db =& eZDB::globalDatabase();

        $db->array_query( $checkForAddress, "SELECT UserID FROM eZAddress_AddressDefinition
                                     WHERE UserID='$userID'" );

        if ( count ( $checkForAddress ) != 0 )
        {
            $res[] = $db->query( "UPDATE eZAddress_AddressDefinition SET
                                         AddressID='$addressID',
                                         UserID='$userID'
                                         WHERE UserID='$userID'" );
        }
        else
        {
            $db->begin();
            $res[] = $db->query( "INSERT INTO eZAddress_AddressDefinition
                                  (AddressID, UserID)
                                  VALUES
                                  ('$addressID', '$userID')" );
            $db->unlock();
        }
        
        eZDB::finish( $res, $db );
    }
    
    /*!
      Returns the main address
    */
    function mainAddress( $user )
    {
        if ( get_class ( $user ) == "ezuser" )
            $userID = $user->id();
        else
            $userID = $user;

        $db =& eZDB::globalDatabase();

        $db->array_query( $addressArray, "SELECT AddressID FROM eZAddress_AddressDefinition
                                     WHERE UserID='$userID'", 0, 1 );

        if ( count( $addressArray ) == 1 )
        {
            return new eZAddress( $addressArray[0][$db->fieldName( "AddressID" )] );
        }
        else
        {
            return false;
        }
    }

    /*!
      Returns the object ID.
    */
    function id()
    {
        return $this->ID;
    }
    
    /*!
      Returnerer  street1.
    */
    function street1( )
    {
        return $this->Street1;
    }

    /*!
      Returnerer  street2.
    */
    function street2( )
    {
        return $this->Street2;
    }

    /*!
      Returns the name.
    */
    function name( )
    {
        return $this->Name;
    }

    /*!
      graham@brookinsconsulting.com @ 1-19-2002 : recoded for eZRegion

      Return Region as an eZRegion object.
    */
    function region( )
    {
      // return $this->RegionID;
       if ( is_numeric( $this->RegionID ) and $this->RegionID > 0 )
            return new eZRegion( $this->RegionID );
       else
            return false;
    }

    /*!
      Returnerer postkode.
    */
    function zip( )
    {
        return $this->Zip;
    }

    /*!
      Returnerer adressetype id.
    */
    function addressTypeID()
    {
        return $this->AddressTypeID;
    }

    /*!
      Returnerer adressetype.
    */
    function addressType()
    {
        $addressType = new eZAddressType( $this->AddressTypeID );
        return $addressType;
    }

    /*!
      Sets the place value.
    */
    function setPlace( $value )
    {
       $this->Place = $value;
    }

    /*!
      Sets the country, takes an eZCountry object as argument.
    */
    function setCountry( $country )
    {
       if ( get_class( $country ) == "ezcountry" )
       {
           $this->CountryID = $country->id();
       }
       else if ( is_numeric( $country ) )
       {
           $this->CountryID = $country;
       }
    }

    /*!
     Returns the place.
    */
    function place()
    {
       return $this->Place;
    }

    /*!
      Returns the country as an eZCountry object.
    */
    function country()
    {
        if ( is_numeric( $this->CountryID ) and $this->CountryID > 0 )
            return new eZCountry( $this->CountryID );
        else
            return false;
    }

    
    var $ID;
    var $Street1;
    var $Street2;
    var $Zip;
    var $Place;
    var $CountryID;
    var $RegionID;
    var $Name;
    
    /// Relation to an eZAddressTypeID
    var $AddressTypeID;
}

?>
