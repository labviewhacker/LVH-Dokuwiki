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
class syntax_plugin_lvhhardware extends DokuWiki_Syntax_Plugin 
{

	//Return Plugin Info
	function getInfo() 
	{
        return array('author' => 'Sammy_K',
                     'email'  => 'sammyk.labviewhacker@gmail.com',
                     'date'   => '2012-12-21',
                     'name'   => 'LabVIEW Hacker Hardware Tile Template Plugin',
                     'desc'   => 'Template for LabVIEW Hacker Hardware Tile',
                     'url'    => 'www.labviewhacker.com');
    }

	//Set This To True To Enable Debug Strings
	protected $lvhDebug = false;
	
	//Quick Customizations
	protected $maxImageSize = 200;
	
	/***************************************************************************************************************************
	* Plugin Variables
	***************************************************************************************************************************/
	protected	$name = '';
	protected	$page = '';
	protected	$version = '';
	protected	$image = '';	
	protected	$imageSize = '';	
  
    function getType() { return 'protected'; }
    function getSort() { return 32; }
  
    function connectTo($mode) {
        $this->Lexer->addEntryPattern('{{lvh_hardware.*?(?=.*?}})',$mode,'plugin_lvhhardware');
		
		//Add Internal Pattern Match For Product Page Elements	
		$this->Lexer->addPattern('\|.*?(?=.*?)\n','plugin_lvhhardware');
    }
	
    function postConnect() {
      $this->Lexer->addExitPattern('}}','plugin_lvhhardware');
    }
	 
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
					case 'name':						
						$this->name = $value;
						break;	
					case 'page':						
						$this->page = $value;
						break;	
					case 'version':						
						$this->version = $value;
						break;	
					case 'image':						
						$this->image = lvh_getImageURL($value);
						break;
					default:
						break;
				}
				return array($state, $value);
				break;
			case DOKU_LEXER_UNMATCHED :
				break;
			case DOKU_LEXER_EXIT :
				return array($state, $this->name, $this->page, $this->version, $this->image, $this->imageSize);
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
			
			$renderer->doc .= $this->fullName;
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
								
				//$renderer->doc .= '<tr><td>';
				//$renderer->doc .= $data[2];	
				//$renderer->doc .= '</td></tr>';
				
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
				 $instName = $data[1];
				 $instPage = $data[2];
				 $instVersion = $data[3];
				 $instImage = $data[4];
				 $instImageSize = $data[5];	
				
				//Optional Version Cell
				$versionCell = '';
				if($instVersion != '')
				{
					$versionCell = 	"<tr>
										<td>
											<center><font size='1'> Version: " . $instVersion . " </font></center>
										</td>
									</tr>";
				}
				
				
				$renderer->doc .= "
					<html>
						<head>
							<style type='text/css'>
								table.hwContainerTable
								{
									background:transparent;
									float: left;
									border-collapse:collapse; 
									border:0px solid black;
									border-radius: 10px;									
								}								
								table.hwContainerTable td:hover
								{
									float: left;
									border-radius: 10px;									
								}
								table.hwContainerTable td 
								{ 									
									border:0px;
								}	
								
								table.hardware 
								{ 
									border-collapse:collapse; 
									width:200px; 
									border:0px solid black;
									border-radius:10px;
									background:transparent;
								}									
								table.hardware td 
								{ 									
									border:0px;
								}								
							</style>
						</head>
						<body>
						<table class='hwContainerTable' >
						<tr>
						<td>
							<table class='hardware'>
								<tr>
									<td>
										<center><font size='4'><a href='http://ec2-107-21-156-97.compute-1.amazonaws.com/doku.php?id=" . $instPage . "'>" . $instName . "</a> </font></center>
									</td>
								</tr>
									" . $versionCell . "
								<tr>
									<td>
										<center><img src = '" . $instImage . "' width='" . $instImageSize . "'></center>
									</td>
								</tr>
							</table>
						</td>
						</tr>
						</table>
						</body>
					</html>				
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
	