<?php
/********************************************************************************************************************************
*
* LabVIEW Hacker Social Buttons Plugin
*
* Written By Sammy_K
* www.labviewhacker.com
*
/*******************************************************************************************************************************/
  
 
// must be run within DokuWiki
if(!defined('DOKU_INC')) die();
 
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once DOKU_PLUGIN.'syntax.php';

//Include LVH Plugin Common Code
if(!defined('LVH_COMMON'))
{
	define('LVH_COMMON', 'lib/plugins/lvhplugincommon.php');
	include 'lib/plugins/lvhplugincommon.php'; 
}
 
/********************************************************************************************************************************
* All DokuWiki plugins to extend the parser/rendering mechanism
* need to inherit from this class
********************************************************************************************************************************/
class syntax_plugin_lvhsocial extends DokuWiki_Syntax_Plugin 
{

	//Return Plugin Info
	function getInfo() 
	{
        return array('author' => 'Sammy_K',
                     'email'  => 'sammyk.labviewhacker@gmail.com',
                     'date'   => '2013-02-09',
                     'name'   => 'LabVIEW Hacker Social Buttons Plugin',
                     'desc'   => 'A simple way to add social media buttons to DokuWiki pages',
                     'url'    => 'www.labviewhacker.com');
    }
	

	//Set This To True To Enable Debug Strings
	protected $lvhDebug = false;
	
	/***************************************************************************************************************************
	* Plugin Variables
	***************************************************************************************************************************/
	/*
	protected $googleShareDefaultTitle = '';
	protected $googleShareDefaultImage = '';
	protected $googleShareDefaultDesc = '';
		
	protected $googlePlusOneDefaultTitle = '';
	protected $googlePlusOneDefaultImage = '';
	protected $googlePlusOneDefaultDesc = '';	
	
	*/
	
	protected $socialMode = 'none';
	
	protected $google =   array('share' => array('enabled' => false, 
												 'title' => '',   
												 'image' => '', 
												 'datapageoverride' => '', 
												 ),
								'plusone' => array('enabled' => false, 
												   'title' => '',   
												   'image' => '',
												   'datapageoverride' => '', 
												 )
     							);
								
	
    /********************************************************************************************************************************************
	** Plugin Configuration
	********************************************************************************************************************************************/			
				
    function getType() { return 'protected'; }
    function getSort() { return 32; }
  
    function connectTo($mode) {
        $this->Lexer->addEntryPattern('{{lvh_social.*?(?=.*?}})',$mode,'plugin_lvhsocial');
		
		//Add Internal Pattern Match For Product Page Elements	
		$this->Lexer->addPattern('\|.*?(?=.*?)\n','plugin_lvhsocial');
    }
	
    function postConnect() {
      $this->Lexer->addExitPattern('}}','plugin_lvhsocial');
    }
	 
	/********************************************************************************************************************************************
	** Handle
	********************************************************************************************************************************************/			
				
    function handle($match, $state, $pos, &$handler) 
	{	
		
		switch ($state) 
		{
		
			case DOKU_LEXER_ENTER :
				break;
			case DOKU_LEXER_MATCHED :					
				//Find The Token And Value (Before '=' remove white space, convert to lower case).
				$tokenDiv = strpos($match, '=');								//Find Token Value Divider ('=')
				$token = strtolower(trim(substr($match, 1, ($tokenDiv - 1))));	//Everything Before '=', Remove White Space, Convert To Lower Case
				$value = trim(substr($match, ($tokenDiv + 1)));						//Everything after '='
				switch($token)
				{
					//Google Share Button
					case 'googleshare':
						$this->socialMode = 'share';
						$this->google['share']['enabled'] = true;
						break;
					case 'googleplusone':
						$this->socialMode = 'plusone';
						$this->google['plusone']['enabled'] = true;
						break;					
					case 'title':
						$this->google[$this->socialMode]['title'] = $value; 
						break;
					case 'image':
						$this->google[$this->socialMode]['image'] = $value;
						break;
					case 'datapageoverride':
						$this->google[$this->socialMode]['datapageoverride'] = stripWikiLinkSyntax($value);
						break;					
				}
				return array($state, $value);
				break;
			case DOKU_LEXER_UNMATCHED :
				break;
			case DOKU_LEXER_EXIT :
				//Build Components		
				
				
				
				//Build Array To Send To Renderer
				$retVal = array($state, $this->google);
				
				//Clear Vals
				$this->socialMode = 'none';
				$this->google =  array('share' => array('enabled' => false, 
														'title' => '',   
														'image' => '', 
														'datapageoverride' => '', 
														),
										'plusone' => array('enabled' => false, 
														'title' => '',   
														'image' => '',
														'datapageoverride' => '', 
														)
									);
				
				return $retVal;
				break;
			case DOKU_LEXER_SPECIAL :
				break;
		}			
		return array($state, $match);
    }
 
	/********************************************************************************************************************************************
	** Render
	********************************************************************************************************************************************/
	
    function render($mode, &$renderer, $data) 
	{
    // $data is what the function handle return'ed.
        if($mode == 'xhtml')
		{
			switch ($data[0]) 
			{
			  case DOKU_LEXER_ENTER : 
				//Initialize Table	
				if($this->lvhDebug) $renderer->doc .= 'ENTER';		//Debug
				
				//$renderer->doc.= '<HTML><body><table border="0">';
				break;
			  case DOKU_LEXER_MATCHED :
				//Add Table Elements Based On Type
				if($this->lvhDebug) $renderer->doc .= 'MATCHED';		//Debug				
				break;
			  case DOKU_LEXER_UNMATCHED :
				//Ignore
				if($this->lvhDebug) $renderer->doc .= 'UNMATCHED';	//Debug
				break;
			  case DOKU_LEXER_EXIT :
				//Close Elements
				if($this->lvhDebug) $renderer->doc .= 'EXIT';		//Debug
				//$renderer->doc.= '</table></body></HTML>';
				
				//Separate Data
				 $instGoogle = $data[1];
				 
				/*********************************************************************************************
				* Google Share Button
				*********************************************************************************************/
				$googleShareButton = '';
				$googleShareTitle = $instGoogle['share']['title'];
				$googleShareImage = $instGoogle['share']['image'];
				$googleShareDataPageOverride = $instGoogle['share']['datapageoverride'];
				
				if($instGoogle['share']['enabled'] == true)
				{					 
					//Check If User Specified Title
					if($googleShareTitle == '')
					{
						//No Title Specified Generate From Page Name
						$googleShareTitle = p_get_metadata(getID(), 'title', false);
					}
					//Check If User Specified Image 
					if($googleShareImage == '')
					{
						//No Image Specified Use Plugin Default Image
						$googleShareImage = "" . DOKU_URL . 'lib/plugins/lvhsocial/images/google_share_default_image.png';	
					}	
					//Check If User Specified Data Page Override 
					if($googleShareDataPageOverride == '')
					{
						//No Data Override, Build Default
						 $googleShareDataPageOverride = "data-href='" . wl(getID(),'',true) . "'";	
					}
					else
					{
						//Data Page Override Specified, Build HTML
						$googleShareDataPageOverride = "data-href='" . DOKU_URL . $googleShareDataPageOverride . "'";	
					}

					//Build Button 
					$googleShareButton = "	<meta property='og:title' content='". $googleShareTitle ."' />
											<meta property='og:image' content='" . $googleShareImage . "' />
											<div class='g-plus' data-action='share' data-annotation='bubble'" . $googleShareDataPageOverride . "></div>";						
				}				
				
				
				/*********************************************************************************************
				* Google Plus One Button
				*********************************************************************************************/
				$googlePlusOneButton = '';
				$googlePlusOneTitle = $instGoogle['plusone']['title'];
				$googlePlusOneImage = $instGoogle['plusone']['image'];
				$googlePlusOneDataPageOverride = $instGoogle['plusone']['datapageoverride'];
				
				if($instGoogle['plusone']['enabled'] == true)
				{					 
					//Check If User Specified Title
					if($googlePlusOneTitle == '')
					{
						//No Title Specified Generate From Page Name
						$googlePlusOneTitle = p_get_metadata(getID(), 'title', false);
					}
					//Check If User Specified Image 
					if($googlePlusOneImage == '')
					{
						//No Image Specified Use Plugin Default Image
						$googlePlusOneImage = "" . DOKU_URL . 'lib/plugins/lvhsocial/images/google_PlusOne_default_image.png';	
					}	
					//Check If User Specified Data Page Override 
					if($googlePlusOneDataPageOverride == '')
					{
						$googlePlusOneDataPageOverride = "href='" . wl(getID(),'',true) . "'";
					}
					else
					{
						//Data Page Override Specified, Build HTML
						$googlePlusOneDataPageOverride = "href='" . DOKU_URL . $googlePlusOneDataPageOverride . "'";	
					}					
					
					//Build Button 
					$googlePlusOneButton = "<meta property='og:title' content='". $googlePlusOneTitle ."' />
										<meta property='og:image' content='" . $googlePlusOneImage . "' />
										<g:plusone ". $googlePlusOneDataPageOverride ." size='medium' data-annotation='bubble'></g:plusone>";
				}				
									
				/*********************************************************************************************
				* Render Output
				*********************************************************************************************/
				$renderer->doc .=  $googleShareButton . $googlePlusOneButton;
				
				break;
			  case DOKU_LEXER_SPECIAL :
				//Ignore
				if($this->lvhDebug) $renderer->doc .= 'SPECIAL';		//Debug
				break;
			}			
            return true;
        }
        return false;
    }
}
	