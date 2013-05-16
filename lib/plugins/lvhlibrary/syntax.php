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
class syntax_plugin_lvhlibrary extends DokuWiki_Syntax_Plugin 
{
	//Return Plugin Info
	function getInfo() 
	{
        return array('author' => 'Sammy_K',
                     'email'  => 'sammyk.labviewhacker@gmail.com',
                     'date'   => '2012-12-21',
                     'name'   => 'LabVIEW Hacker Library Template Plugin',
                     'desc'   => 'Template for LabVIEW Hacker Library Tile',
                     'url'    => 'www.labviewhacker.com');
    }

	//Set This To True To Enable Debug Strings
	protected $lvhDebug = false;
	
	//Quick Customizations
	protected $maxImageSize = 200;
	
	//Store Variables To Render
	protected $title = '';	
	protected $path = '';
	protected $image = '';
	protected $description = '';
	protected $date = '';
	protected $hacker = '';
	
  /********************************************************************************************************************************************
	** Plugin Configuration
	********************************************************************************************************************************************/			
				
    function getType() { return 'protected'; }
    function getSort() { return 32; }
  
    function connectTo($mode) {
        $this->Lexer->addEntryPattern('{{lvh_library.*?(?=.*?}})',$mode,'plugin_lvhlibrary');
		
		//Add Internal Pattern Match For Product Page Elements	
		$this->Lexer->addPattern('\|.*?(?=.*?)\n','plugin_lvhlibrary');
    }
	
    function postConnect() {
      $this->Lexer->addExitPattern('}}','plugin_lvhlibrary');
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
						$this->title = $value;
						break;	
					case 'path':						
						$this->path = $value;
						break;						
					case 'image':						
						$this->image = lvh_allowSimpleWikiSyntax($value);
						break;
					case 'description':						
						$this->description = lvh_allowSimpleWikiSyntax($value);
						break;	
					case 'date':						
						$this->date = $value;
						break;
					case 'hacker':						
						$this->hacker = $value;
						break;						
					default:
						break;
				}
				return array($state, $value);
				break;
			case DOKU_LEXER_UNMATCHED :
				break;
			case DOKU_LEXER_EXIT :
				$retVal = array($state, $this->title, $this->path, $this->image, $this->description, $this->date, $this->hacker);
					//Clear Variables Thta Will Be Resused Here If Neccissary (might not be needed in this plugin)
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
				 $instPath = $data[2];
				 $instImage = $data[3];
				 $instDescription = $data[4];
				 $instDate = $data[5];
				 $instHacker = $data[6];
				
				$renderer->doc .= "
					<head>
						<style type='text/css'>
						
							table.libraryTile
							{  
								width:30%;
								border:2px solid;
								border-color:#CCCCCC;
								background-color: white;	
								float:left;
								margin:10px;								
							}
							
													

							.libraryTileTitle
							{ 		
								border:1px;							
								vertical-align:Top;	
								height:50px;
							}	
							.libraryTileCredits
							{ 
								border:0px;
								vertical-align:middle;	
								height:35px;
							}
							.libraryTileImage
							{ 
								border:0px;							
								vertical-align:middle;	
								height:225px;
							}
							.libraryDescription
							{ 
								border:0px;
								vertical-align:middle;
								height:150px;
							}

							
							
						</style>
					</head>

					<body>
						<table class='libraryTile'>
							<tr>
								<td class='libraryTileTitle'>
									<font size='4em'><a href='doku.php?id=" . $instPath . "'>" . $instTitle . "</a></font>
								</td>
							</tr>
							<tr>
								<td class='libraryTileCredits'>
									<font size='2em'>Hacked By: <b>" . $instHacker . "</b><br />Date:" . $instDate . " </align></font>
								</td>
							</tr>
							<tr>
								<td class='libraryTileImage'>
									<center>" . $instImage . "</center>
								</td>
							</tr>
							<tr>
								<td class='libraryDescription'>
									" . $instDescription . "<a href='doku.php?id=" . $instPath . "'> Read More...</a>
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
	