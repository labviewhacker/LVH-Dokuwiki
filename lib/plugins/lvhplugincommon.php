<?php
/********************************************************************************************************************************
*
* LabVIEW Hacker Plugin Common
*
/
	/*************************************************************************************
	** Common Variables
	**************************************************************************************/
	function lvh_getDebugMode()
	{
		return false;	//If Endabled Print Debug Messages
	}
	
	//Dokuwiki Media Manager Fetch URL
	function lvh_getMediaManagerFetchURL()
	{
		return 'http://labviewhacker.com/lib/exe/fetch.php?media=';
	}
	
	//Allow Simple Wiki Syntax (Strips <p> tags)
	function lvh_allowSimpleWikiSyntax($value)
	{
		$retVal = str_replace( '<p>', '', (p_render('xhtml',p_get_instructions($value))) );
		return str_replace('</p>', '', $retVal);
	}

	/*************************************************************************************
	** Image URLS
	** Convert Wiki Markup Image Into Image URL
	**************************************************************************************/
	//
	function lvh_getImageURL($value)
	{
		//Get Common Variables
		$mediaManagerFetchURL = lvh_getMediaManagerFetchURL();
		
		//$mediaManagerFetch = 'http://75.101.137.8/lib/exe/fetch.php?media=';
		
		//Wiki Format
		if(preg_match('{{.*?}}', $value) == 1)
		{
			/* THIS IS NOT FULLY IMPLEMEMNTED
			//Check if image size is specified
			if(pregmatch('\?[0-9]+?', $value, $matches) == 1)
			{
				//Image Syntax Contains Size
				//Get Image Size
				$imageSize = $matches[0];
				//Image URL
				$delPos = strpos($value, ':');
				$endPos = strPos($value, '?');
				$shortVal = substr($value, $delPos, ($endPos-$delPos)); 
				return $mediaManagerFetchURL . $shortVal;		
			}
			*/
		
			//Image URL
			$delPos = strpos($value, ':');
			$endPos = strPos($value, '?');
			$shortVal = substr($value, $delPos, ($endPos-$delPos)); 
			return $mediaManagerFetchURL . $shortVal;			
		}
		//: Delimated Path
		else
		{	
			//Not sure this works...but probably won't use it
			return $mediaManagerFetchURL . $value;
		}		
	}
	
	function lvh_getImageLink($value, $caption)
	{
		//Get Common Variables
		$mediaManagerFetchURL = lvh_getMediaManagerFetchURL();
		$imageURL = '';
		$imageSize = 150;	//Arbitrary Default Should Be Overwritten By User
		
		//Wiki Format
		if(preg_match('/{{.*?}}/', $value) == 1)
		{
			//Check If Wiki Syntax Contains A '?'.  If So Strip Any Meta Data Such As Link Type Or Image Size
			if(strstr($value, '?') == true)
			{
				//Image Syntax Contains Size
				//Get Image Size
                $sizeStart = strpos($value, '?') + 1;        //This is a bad way to do this, I was in a hurry.  There might be other meta data that should be properly parsed here.
                $sizeEnd = strpos($value, '}');
                $imageSize = substr($value, $sizeStart, $sizeEnd-$sizeStart);
				
                //Image URL
				$delPos = strpos($value, ':');
				$endPos = strPos($value, '?');
				$shortVal = substr($value, $delPos, ($endPos-$delPos)); 
				$imageURL = $mediaManagerFetchURL . $shortVal;					
			}
			else
			{
				//No Size Specified
				//Image URL
				$delPos = strpos($value, ':');
				$shortVal = substr($value, $delPos); 
				$imageURL = $mediaManagerFetchURL . $shortVal;				
			}		
		}
		else
		{
			//Could Add Code To Display Some Sort Of Default Missing Image...image...
		}
		
		return "<a href=\"" . $imageURL . "\" class=\"highslide\" onclick=\"return hs.expand(this)\" margin: 0 0 10px 15px\">
					<img src=\"" . $imageURL . "\" alt=\"" . $caption . "\" width=\"" . $imageSize . "\"/>
				</a>
				";
		/*
		return "<a href=\"" . $imageURL . "\" class=\"highslide\" onclick='return hs.expand(this)' style=\"float:right; margin: 0 0 10px 15px\">
				<img src=\"" . $imageURL . "\"  alt=\"" . $caption . "\" max-width:100%; height:120px;\" />
				";
		*/
	}
	
	/*************************************************************************************
	** Wiki Syntax
	** Support Wiki Markup In lvh Plugins
	**************************************************************************************/
	function lvh_parseWikiSyntax($value)
	{
		
		$retVal = lvh_parseStyle($value);		//Parse Style Must Be Called Before Parse Links Since Parse Links Will Add HTML Tags That Parse Style Will Destroy.
		$retVal = lvh_parseLinks($retVal);
		$retVal = str_replace("\"", "'", $retVal);		//Replace " with ' so that links in detailed text work in image zoom window.
		//$retval = p_render('xhtml',p_get_instructions($value));
		return $retVal;
	}
	
	function lvh_parseLinks($value)
	{
		if(preg_match_all("/\[\[(.*?)\|(.*?)\]\]/", $value, $matches) > 0)
		{
			//Links Found
			foreach($matches[0] as $match)
			{
				$urlEnd = strpos($match, "|") - 2;
				$userLinkURL = substr($match, 2, $urlEnd);
				//Check Link Type (Wiki Page, External, etc)
				if( (strpos($userLinkURL, 'http://')) === false && strpos($userLinkURL, 'https://') === false && strpos($userLinkURL, ':') )
				{
					//Link Contains A ':' But Not HTTP:// or HTTPS:// So It Must Be A Wiki Link
					$linkURL = 'doku.php?id=' . $userLinkURL;
				}
				else if( (strpos($userLinkURL, 'http://') === false) && (strpos($userLinkURL, 'https://') === false))
				{
					//If Web Link Doesn't Contain HTTP:// Add It
					$linkURL = 'http://' . $userLinkURL;
				}
				else
				{
					//Web Link Already Has HTTP or HTTPS
					$linkURL = $userLinkURL;
				}
				
				$userLinkURL = str_replace("/", "\/", $userLinkURL);
				//echo "userLinkURL = " . $userLinkURL . "<br />";
				//echo "linkURL = " . $linkURL . "<br />";
				$value = preg_replace("/\[\[" . $userLinkURL . "\|(.*?)\]\]/", "<a href=\"" . $linkURL . "\">$1</a>", $value);
			}
		}
		return $value;
	}
	
	
	//Parse Wiki Markup Links
	function lvh_parseStyle($value)
	{
		//Allow Users To Use < > characters
		$value = str_replace('<', '&lt', $value);
		$value = str_replace('>', '&gt', $value);
		$retVal = preg_replace("/\*\*(.*?)\*\*/", "<b>$1</b>", $value);
		return $retVal;
	}
	
	/*************************************************************************************
	** forceExternalLink
	** This function takes a url and forces it to look externally by prepending http:// to
	** the link if it does not already include it.
	**************************************************************************************/
	function lvh_forceExternalLink($value)
	{
		//Check if link already contains http:// or https:// if not add http://
		if( (strpos($value, 'http://')) === false && strpos($value, 'https://') === false)
		{
			$retVal = "http://" . $value;
			return $retVal;
		}		
		return $value;		
	}
    
    /*************************************************************************************
	** parseWikiLinkSyntax
	** This function will remove the [[ | and ]] bits from a wiki link and add the base
    ** URL to the path.  This helps analytics and tracking.
	**************************************************************************************/
	function stripWikiLinkSyntax($value)
	{
		//Check if link already contains http:// or https:// if not add http://
		$value = str_replace('[', '', $value);
        $value = str_replace(']', '', $value);
        $value = str_replace('|', '', $value);
        return "/doku.php?id=" . $value;        
	}
    
	
		
?>