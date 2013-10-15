<?php
/********************************************************************************************************************************
*
* LabVIEW Hacker Landing Page Plugin
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
class syntax_plugin_lvhlandingpage extends DokuWiki_Syntax_Plugin 
{
	//Return Plugin Info
	function getInfo() 
	{
        return array('author' => 'Sammy_K',
                     'email'  => 'sammyk.labviewhacker@gmail.com',
                     'date'   => '2012-12-21',
                     'name'   => 'LabVIEW Hacker Landing Page Plugin',
                     'desc'   => 'Template for LabVIEW Hacker Landing Pages',
                     'url'    => 'www.labviewhacker.com');
    }

	
	//include 'common.php';	
	//protected   $imageFetchPath = 'http://75.101.137.8/lib/exe/fetch.php?media=';

	//Set This To True To Enable Debug Strings
	protected $lvhDebug = false;
	
	/***************************************************************************************************************************
	* Plugin Variables
	***************************************************************************************************************************/
	protected	$fullName = '';
	protected	$shortName = '';
	protected	$description = '';
	protected	$logoPath = '';
	protected   $downloadURL = '';
	protected	$gettingStartedPath = '';
	protected	$tutorialsPath = '';
	protected	$forumPath = '';
	protected	$gitHubPath = '';
	protected	$howItWorks = '';
	protected   $howItWorksPath = '';
	protected	$exploreFeatures = '';
	protected   $exploreFeaturesPath = '';
	protected	$seeItInAction = '';
	protected   $seeItInActionPath = '';
	protected	$gettingStarted = '';		
  
    function getType() { return 'protected'; }
    function getSort() { return 32; }
  
    function connectTo($mode) {
        $this->Lexer->addEntryPattern('{{lvh_landingpage.*?(?=.*?}})',$mode,'plugin_lvhlandingpage');
		
		//Add Internal Pattern Match For Product Page Elements	
		$this->Lexer->addPattern('\|.*?(?=.*?)\n','plugin_lvhlandingpage');
    }
	
    function postConnect() {
      $this->Lexer->addExitPattern('}}','plugin_lvhlandingpage');
    }
	 
    function handle($match, $state, $pos, &$handler) 
	{	
		global $imageFetchPath;
		
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
					case 'full name':						
						$this->fullName = $value;
						break;	
					case 'short name':						
						$this->shortName = $value;
						break;	
					case 'getting started path':						
						$this->gettingStartedPath = stripWikiLinkSyntax($value);
						break;	
					case 'github path':						
						$this->gitHubPath = $value;
						break;
					case 'tutorials path':						
						$this->tutorialsPath = stripWikiLinkSyntax($value);
						break;
					case 'forum path':						
						$this->forumPath = $value;
						break;	
					case 'description':						
						$this->description = p_render('xhtml',p_get_instructions($value));
						break;				
					case 'logo path':						
						$this->logoPath = lvh_getImageURL($value);
						break;
					case 'how it works':						
						$this->howItWorks = p_render('xhtml',p_get_instructions($value));
						break;	
					case 'how it works path':						
						$this->howItWorksPath = stripWikiLinkSyntax($value);
						break;
					case 'explore features':						
						$this->exploreFeatures = p_render('xhtml',p_get_instructions($value));
						break;	
					case 'explore features path':						
						$this->exploreFeaturesPath = stripWikiLinkSyntax($value);
						break;	
					case 'see it in action':						
						$this->seeItInAction = p_render('xhtml',p_get_instructions($value));
						break;	
					case 'see it in action path':						
						$this->seeItInActionPath = stripWikiLinkSyntax($value);
						break;	
					case 'getting started':						
						$this->gettingStarted = p_render('xhtml',p_get_instructions($value));
						break;
					case 'download url':
						$this->downloadURL = lvh_forceExternalLink($value);
						break;
					default:
						break;
				}
				break;
			case DOKU_LEXER_UNMATCHED :
				break;
			case DOKU_LEXER_EXIT :
				return array($state, $this->fullName, $this->shortName, $this->description, $this->logoPath, $this->gettingStartedPath, $this->tutorialsPath, $this->forumPath, $this->gitHubPath, $this->howItWorks, $this->howItWorksPath, $this->exploreFeatures, $this->exploreFeaturesPath, $this->seeItInAction, $this->seeItInActionPath, $this->gettingStarted, $this->downloadURL);
				break;
			case DOKU_LEXER_SPECIAL :
				break;
		}
			
		return array($state, $match);
    }
 
    function render($mode, &$renderer, $data) 
	{
    // $data is what the function handle return'ed.
        if($mode == 'xhtml')
		{		
			
			//$renderer->doc .= $this->fullName;
			switch ($data[0]) 
			{
			  case DOKU_LEXER_ENTER : 
				//Initialize Table	
				if($this->skDebug) $renderer->doc .= 'ENTER';		//Debug
				
				//$renderer->doc.= '<HTML><body><table border="0">';
				break;
			  case DOKU_LEXER_MATCHED :
				//Add Table Elements Based On Type
				if($this->skDebug) $renderer->doc .= 'MATCHED';		//Debug
								
				//$renderer->doc .= '<tr><td>';
				//$renderer->doc .= $data[2];	
				//$renderer->doc .= '</td></tr>';
				
				break;
			  case DOKU_LEXER_UNMATCHED :
				//Ignore
				if($this->skDebug) $renderer->doc .= 'UNMATCHED';	//Debug
				break;
			  case DOKU_LEXER_EXIT :
				//Close Elements
				if($this->skDebug) $renderer->doc .= 'EXIT';		//Debug
				
				//Break Out Local Variables For Rendering
				$instfullName = $data[1];
				$instshortName = $data[2];
				$instdescription = $data[3];
				$instlogoPath = $data[4];
				$instgettingStartedPath = $data[5];				
				$insttutorialsPath = $data[6];
				$instforumPath = $data[7];
				$instgitHubPath = $data[8];
				$insthowItWorks = $data[9];
				$insthowItWorksPath = $data[10];
				$instexploreFeatures = $data[11];
				$instexploreFeaturesPath = $data[12];
				$instseeItInAction = $data[13];
				$instseeItInActionPath = $data[14];
				$instgettingStarted = $data[15];
				$isntDownloadURL = $data[16];
				
				$renderer->doc .= "
					<HTML>
						<head>
							<style type='text/css'>
								.productPage { border-collapse: collapse; width:100%; background-color:white;}
								.productPage-head { border:0;margin-bottom:0;padding-bottom:0; }
								.productPage-head td { border:0; }
								.productPage-body { border:0;border-top:0;margin-top:0;padding-top:0;margin-bottom:0;padding-bottom:0; width:100%; }
								.productPage-body td { border:0;border-top:0;}
								.productPage-footer { border:0;border-top:0;margin-top:0;padding-top:0; }
								.productPage-footer td { border:0;border-top:0;}
							</style>
						</head>
						<body>
							<table class='productPage productPage-head'>
								<tr>
									<td>
									
										<!-- This Title Was Removed So A Wiki Page Title Could Be Included To Generate A Page Name 
										<h1> " . $instfullName . " </h1>	
										-->
										
									</td>  					
								</tr>								
							</table>
							
							<table class='productPage productPage-body'>
								<tr>
									<td colspan='4'>
										" . $instdescription . " <br />
									</td>
									<td rowspan=\"2\">									
										<p align='center'>
											<a href='" . $isntDownloadURL . "'><img src='" . DOKU_BASE . "lib/plugins/lvhdownloadbutton/DownloadButton.png'></a>
											<br /><br />
											<img src='" . $instlogoPath . "' width='60%' height='60%'> 
										</p>
									</td>	
								</tr>
								<tr>
									<td width='15%'>
										<center><a href='" . $instgettingStartedPath . "'><img src='" . DOKU_BASE . "lib/plugins/lvhlandingpage/images/getting_started_black.png' onmouseover=\"this.src='" . DOKU_BASE . "lib/plugins/lvhlandingpage/images/getting_started_green.png'\" onmouseout=\"this.src='" . DOKU_BASE . "lib/plugins/lvhlandingpage/images/getting_started_black.png'\" align='middle'><br />Getting Started</a></center><br />
									</td>
									<td width='15%'>										
										<center><a href='" . $insttutorialsPath . "'><img src='" . DOKU_BASE . "lib/plugins/lvhlandingpage/images/tutorials_black.png' onmouseover=\"this.src='" . DOKU_BASE . "lib/plugins/lvhlandingpage/images/tutorials_green.png'\" onmouseout=\"this.src='" . DOKU_BASE . "lib/plugins/lvhlandingpage/images/tutorials_black.png'\" align='middle'><br />Tutorials</a></center><br />
									</td>
									<td width='15%'>
										<center><a href='" . $instforumPath . "'><img src='" . DOKU_BASE . "lib/plugins/lvhlandingpage/images/forums_black.png' onmouseover=\"this.src='" . DOKU_BASE . "lib/plugins/lvhlandingpage/images/forums_green.png'\" onmouseout=\"this.src='" . DOKU_BASE . "lib/plugins/lvhlandingpage/images/forums_black.png'\" align='middle'><br />Forums</a></center><br />
									</td>
									<td width='15%'>
										<center><a href='" . $instgitHubPath . "'><img src='" . DOKU_BASE . "lib/plugins/lvhlandingpage/images/github_black.png' onmouseover=\"this.src='" . DOKU_BASE . "lib/plugins/lvhlandingpage/images/github_green.png'\" onmouseout=\"this.src='" . DOKU_BASE . "lib/plugins/lvhlandingpage/images/github_black.png'\" align='middle'><br />Git Hub</a></center><br />
									</td>
																							
								</tr>
							</table>
							
							
							<table class='productPage productPage-footer'>
								<tr>
									<td width='25%' style=\"border-right: dotted 2px #CCCCCC; padding-left: 20px; padding-right: 20px;\">
										<p><b>How It Works</b><br /><br />
										" . $insthowItWorks . "</p>										
									</td>
									<td width='25%' style=\"border-right: dotted 2px #CCCCCC; padding-left: 20px; padding-right: 20px;\">
										<p><b>Explore Features</b><br /><br />
										" . $instexploreFeatures . "</p>																				
									</td>
									<td width='25%' style=\"border-right: dotted 2px #CCCCCC; padding-left: 20px; padding-right: 20px;\">
										<p><b>See It In Action</b><br /><br />
										" . $instseeItInAction . "</p>	
									</td>
									<td width='25%' style=\"padding-left: 20px; padding-right: 20px;\">
										<p><b>Getting Started</b><br /><br />
										" . $instgettingStarted . "</p>												
									</td>
								</tr>
								<tr>
									<td style=\"border-right: dotted 2px #CCCCCC; padding-left: 20px; \">
										<a href='" . $insthowItWorksPath . "'> See How " . $instshortName . " Works</a>
									</td>
									<td style=\"border-right: dotted 2px #CCCCCC; padding-left: 20px; \">
										<a href='" . $instexploreFeaturesPath . "'> Explore " . $instshortName . " Features</a> 	
									</td>
									<td style=\"border-right: dotted 2px #CCCCCC; padding-left: 20px; \">
										<a href='" . $instseeItInActionPath . "'> Projects Using " . $instshortName . "</a> 
									</td>
									<td style=\"padding-left: 20px;\">
										 <a href='" . $instgettingStartedPath . "'> Start Using " . $instshortName . "</a> 
									</td>
							</table>
						</body>
					</HTML>				
					";
				
				
				
				break;
			  case DOKU_LEXER_SPECIAL :
				//Ignore
				if($this->skDebug) $renderer->doc .= 'SPECIAL';		//Debug
				break;
			}			
            return true;
        }
        return false;
    }
}

?>