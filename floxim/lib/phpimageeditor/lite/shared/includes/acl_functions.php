<?php
	
	
	    /*
	    Copyright 2008, 2009, 2010, 2011 Patrik Hultgren
	    
	    YOUR PROJECT MUST ALSO BE OPEN SOURCE IN ORDER TO USE THIS VERSION OF PHP IMAGE EDITOR.
	    BUT YOU CAN USE PHP IMAGE EDITOR JOOMLA PRO IF YOUR CODE NOT IS OPEN SOURCE.
	    
	    This file is part of PHP Image Editor Joomla.
	
	    PHP Image Editor Joomla is free software: you can redistribute it and/or modify
	    it under the terms of the GNU General Public License as published by
	    the Free Software Foundation, either version 3 of the License, or
	    (at your option) any later version.
	
	    PHP Image Editor Joomla is distributed in the hope that it will be useful,
	    but WITHOUT ANY WARRANTY; without even the implied warranty of
	    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	    GNU General Public License for more details.
	
	    You should have received a copy of the GNU General Public License
	    along with PHP Image Editor Joomla. If not, see <http://www.gnu.org/licenses/>.
	    */
	
	
	function PIE_Access($user)
	{
		global $mainframe;
		
		if ($mainframe)
		{
			//In Joomla 1.5, change this code if you wan´t to adjust which users who can edit images.
			return ($user->usertype == 'Manager' || $user->usertype == 'Administrator' || $user->usertype == 'Super Administrator');
		}
		else
		{
			//In Joomla 1.6 and 1.7, change this code if you wan´t to adjust which users who can edit images.
			//Group 8 = Super Users
			return (JAccess::check($user->id, 'core.edit') || in_array(8, $user->groups));
		}
	}		
?>