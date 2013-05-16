<?php
/********************************************************************************************************************************
*
* LabVIEW Hacker Hardware Tile Template Plugin
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
class syntax_plugin_lvhfeature extends DokuWiki_Syntax_Plugin 
{

	//Return Plugin Info
	function getInfo() 
	{
        return array('author' => 'Sammy_K',
                     'email'  => 'sammyk.labviewhacker@gmail.com',
                     'date'   => '2012-12-21',
                     'name'   => 'LabVIEW Hacker Feature Template Plugin',
                     'desc'   => 'Template for LabVIEW Hacker Feature',
                     'url'    => 'www.labviewhacker.com');
    }
	

	//Set This To True To Enable Debug Strings
	protected $lvhDebug = false;
	
	//Quick Customizations
	protected $maxImageSize = 200;
	
	/***************************************************************************************************************************
	* Plugin Variables
	***************************************************************************************************************************/
	protected $title = '';	
	protected $image = '';
	protected $description = array();
	
	
    /********************************************************************************************************************************************
	** Plugin Configuration
	********************************************************************************************************************************************/			
				
    function getType() { return 'protected'; }
    function getSort() { return 32; }
  
    function connectTo($mode) {
        $this->Lexer->addEntryPattern('{{lvh_feature.*?(?=.*?}})',$mode,'plugin_lvhfeature');
		
		//Add Internal Pattern Match For Product Page Elements	
		$this->Lexer->addPattern('\|.*?(?=.*?)\n','plugin_lvhfeature');
    }
	
    function postConnect() {
      $this->Lexer->addExitPattern('}}','plugin_lvhfeature');
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
				$value = substr($match, ($tokenDiv + 1));						//Everything after '='
				switch($token)
				{
					case 'title':						
						$this->title = lvh_parseWikiSyntax($value);
						break;						
					case 'image':						
						$this->image = lvh_getImageLink($value);
						break;
					case 'description':						
						$this->description[] = lvh_parseWikiSyntax($value);
						break;						
					default:
						break;
				}
				return array($state, $value);
				break;
			case DOKU_LEXER_UNMATCHED :
				break;
			case DOKU_LEXER_EXIT :
				/********************************************************************************************************************************************
				** Build Details Unordered List
				********************************************************************************************************************************************/			
				$fullDescription = "<ul>";
				foreach($this->description as $descLine)
				{
					$fullDescription .= '<li>' . $descLine . '</li>';
				}
				$fullDescription .= '</ul>';
				
				$retVal = array($state, $this->title, $this->image, $fullDescription);
				//Clear Variables Thta Will Be Resused Here If Neccissary (might not be needed in this plugin)
				$this->description = array();
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
				 $instTitle = $data[1];
				 $instImage = $data[2];
				 $instDescription = $data[3];				
				
				$renderer->doc .= "
					<head>
						<style type='text/css'>
						
							table.libraryFeature
							{  
								width:100%;
								border-width:0px;
								border-bottom: solid 2px #CCCCCC;
								background-color: white;	
								float:left;
							}
							
							tr.libraryFeatureRow
							{ 
								border:0px solid;	
							}							

							td.libraryFeatureCell
							{ 
								border:0px solid;
								vertical-align:middle;	                                
							}	
							
						</style>
					</head>

					<body>
						<table class='libraryFeature'>
							<tr class='libraryFeatureRow'>
								<td class='libraryFeatureCell'>
									<h3> " . $instTitle . " </h3>
								</td>
								<td class='libraryFeatureCell' rowspan='2'>
									<center>" . $instImage . " </center>
								</td>
							</tr>
							<tr class='libraryFeatureRow'>
								<td class='libraryFeatureCell' width='50%'>
									" . $instDescription . "
								</td>
								
							</tr>
						</table>
					</body>				
				";		
				
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
	