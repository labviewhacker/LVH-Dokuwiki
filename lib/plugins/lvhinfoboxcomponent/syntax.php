<?php
/********************************************************************************************************************************
*
* LabVIEW Hacker Infobox Component Plugin
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
class syntax_plugin_lvhinfoboxcomponent extends DokuWiki_Syntax_Plugin 
{
	
	
	//Return Plugin Info
	function getInfo() 
	{
        return array('author' => 'Sammy_K',
                     'email'  => 'sammyk.labviewhacker@gmail.com',
                     'date'   => '2012-12-21',
                     'name'   => 'LabVIEW Hacker Infobox Component Plugin',
                     'desc'   => 'LabVIEW Hacker Infobox Component Plugin',
                     'url'    => 'www.labviewhacker.com');
    }

	//Set This To True To Enable Debug Strings
	protected $lvhDebug = false;
	
	//Store Variables To Render	
	//Basics
	protected $name = '';	
	protected $category = '';		
	protected $image = '';

	//Product History
	protected $manufacturer = '';
	
	//General Specs
	protected $width = '';
	protected $depth = '';
	protected $height = '';
	protected $numPins = '';
		
	//Electrical
	protected $vccMin = '';			//VCC
	protected $vccTypical = '';
	protected $vccMax = '';
	protected $vccUnits = '';
	protected $iccMin = '';			//Icc
	protected $iccTypical = '';
	protected $iccMax = '';
	protected $iccUnits = '';
	protected $powerMin = '';		//Power
	protected $vTypical = '';
	protected $powerMax = '';
	protected $powerUnits = '';
	protected $llMin = '';			//Logic Level
	protected $llTypical = '';
	protected $llMax = '';
	protected $llUnits = '';
	
	//Pins
	protected $pins = array();
	

	
	/********************************************************************************************************************************************
	** Plugin Configuration
	********************************************************************************************************************************************/			
				
    function getType() { return 'protected'; }
    function getSort() { return 32; }
  
    function connectTo($mode) 
	{
        $this->Lexer->addEntryPattern('{{lvh_infobox_component.*?(?=.*?}})',$mode,'plugin_lvhinfoboxcomponent');
		
		//Add Internal Pattern Match For Product Page Elements	
		$this->Lexer->addPattern('\|.*?(?=.*?)\n','plugin_lvhinfoboxcomponent');
    }
	
    function postConnect() 
	{
      $this->Lexer->addExitPattern('}}','plugin_lvhinfoboxcomponent');
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
					//Basics
					case 'name':						
						$this->name = $value;
						break;	
					case 'category':
						$this->category = lvh_allowSimpleWikiSyntax($value);
						break;
					case 'image':						
						$this->image = lvh_allowSimpleWikiSyntax($value);
						break;
						
					//Product History
					case 'manufacturer':						
						$this->manufacturer = lvh_allowSimpleWikiSyntax($value);
						break;

					//General Specs
					case 'width':						
						$this->width = $value;
						break;
					case 'depth':						
						$this->depth = $value;
						break;
					case 'height':						
						$this->height = $value;
						break;
					case 'pins':						
						$this->numPins = $value;
						break;
						
					//Electrical
					case 'vcc min':						//VCC			
						$this->vccMin = $value;
						break;
					case 'vcc typical':						
						$this->vccTypical = $value;
						break;
					case 'vcc max':						
						$this->vccMax = $value;
						break;
					case 'vcc units':						
						$this->vccUnits = $value;
						break;						
					case 'icc min':						//ICC			
						$this->iccMin = $value;
						break;
					case 'icc typical':						
						$this->iccTypical = $value;
						break;
					case 'icc max':						
						$this->iccMax = $value;
						break;
					case 'icc units':						
						$this->iccUnits = $value;
						break;						
					case 'power min':					//POWER					
						$this->powerMin = $value;
						break;
					case 'power typical':						
						$this->powerTypical = $value;
						break;
					case 'power max':						
						$this->powerMax = $value;
						break;
					case 'power units':						
						$this->powerUnits = $value;
						break;
					case 'logic level min':				//Logic Level				
						$this->llMin = $value;
						break;
					case 'logic level typical':						
						$this->llTypical = $value;
						break;
					case 'logic level max':						
						$this->llMax = $value;
						break;
					case 'logic level units':						
						$this->llUnits = $value;
						break;
						
					//Pins
					case (preg_match('/pin [0-9]*/', $token, $pinMatches)? true : false ) :								
						//Loop Through All Matches (should just be 1) Populate Pins Variable With Pin Value
						foreach($pinMatches as $match)
						{
							$pinNum = substr( trim($match), 4);
							$this->pins[$pinNum] = $value;
						}						
						break;
						
					//Default
					default:
						break;
				}
				return array($state, $value);
				break;
			case DOKU_LEXER_UNMATCHED :
				break;
			case DOKU_LEXER_EXIT :
			
				//Generate HTML For Infobox SubComponents
				$basics = parseBasics($this->name, $this->category, $this->image);
				$productHistory = parseProductHistory($this->manufacturer);
				$generalSpecs = parseGeneralSpecs($this->width, $this->depth, $this->height, $this->numPins);
				$electrical = parseElectrical($this->vccMin, $this->vccTypical, $this->vccMax, $this->vccUnits, $this->iccMin, 
											  $this->iccTypical, $this->iccMax, $this->iccUnits, $this->powerMin, $this->vTypical, 
											  $this->powerMax, $this->powerUnits, $this->llMin, $this->llTypical, $this->llMax, 
											  $this->llUnits);
				$pinout = parsePinout($this->pins);
											  
				$retVal = array($state, $basics, $productHistory, $generalSpecs, $electrical, $pinout);
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
				 $instBasics = $data[1];	
				 $instProductHistory = $data[2];
				 $instGeneralSpecs = $data[3];
				 $instElectrical = $data[4];
				 $instPinout = $data[5];
				
				$renderer->doc .= "
					<head>
						<style type='text/css'>
						
							table.infoboxComponentOuterTable
							{  
								float:right;
								margin:10px;								
								width:33%;
								margin: 0 0 0 0;
								
								border: 0px solid #BBBBBB;
								border-collapse:collapse;

								background-color: #EEEEEE;								
								background-color: #EEEEEE;								
							}
							
							.infoboxComponentName 
							{ 		
								border-top: 0px;
								border-left: 0px solid #BBBBBB;
								border-right: 0px solid #BBBBBB;
								border-bottom: 1px solid #BBBBBB;
								background-color: white;
								
								font-weight:bold;
								font-size:1em;
							}
							
							table.infoboxComponentInnerTable
							{  
								width:100%;
								border:0px solid;
								border-color:#BBBBBB;
								background-color: #EEEEEE;	
								margin: 0 0 0 0;
								
							}
							
							.infoboxComponentImage
							{ 	
								border:0px solid;
								padding:10px;
							} 
							
							.infoboxComponentSectionHeader
							{ 
								vertical-align:middle;
								background-color: #BBBBBB;	
								padding:0px;
								
								font-size:.85em;
								font-weight:bold;
							}
							
							.infoboxComponentLabel
							{ 
								width:30%;
								
								border:0px solid blue;								
								vertical-align:middle;
								padding:2px;
								
								font-size:.75em;
								font-weight:bold;
							}
							
							.infoboxComponentValue
							{ 
								border:0px;
								vertical-align:middle;
								padding:2px;
								
								font-size:.75em;
							}
							
							.infoboxComponentElectricalOuter
							{ 
								border:0px solid green;
								padding:0px;
							}							
							
							table.infoboxComponentElectrical
							{  
															
								width:100%;
								border: 0px solid red;
								margin: 0 0 0 0;
											
								background-color: #EEEEEE;								
							}							
							
							.infoboxComponentElectricalHeader
							{ 	
								border:0px;								
								vertical-align:middle;
								padding:2px;
								
								font-size:.75em;
								font-weight:bold;
							}
							.infoboxComponentElectricalLabel
							{ 		
								width:30%;
								
								border:0px;								
								vertical-align:middle;
								padding:2px;
								
								font-size:.75em;
								font-weight:bold;
							}
							.infoboxComponentElectricalValue
							{ 		
								border:0px solid red;
								vertical-align:middle;
								padding:2px;
								
								font-size:.75em;
							}
							
						</style>
					</head>

					<body>
						" . $instBasics 				
						  . $instProductHistory
						  . $instGeneralSpecs 
						  . $instElectrical
						  . $instPinout . "
						  
									</table>
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

/********************************************************************************************************************************
* parseBasics()
*
* Generate Infobox HTML Based On User Specified Values
********************************************************************************************************************************/
function parseBasics($name, $category, $image)
{
	$retVal = "<table class='infoboxComponentOuterTable'>";
	
	$name = trim($name);
	$category = trim($category);
	$image = trim($image);
	
	if( ($name == '') && ($category == '') && ($image == '') )
	{
		//This Section Contains No Data - Nothing To Render
		return '';
	}
	else
	{
		//This Section Contains Data.  Add Each Element With Data
		//Add Name And Open Inner Table (Infoboxes Should Always Have A Name)
		if($name != '')
		{
			$retVal .=	"<tr>
							<td class='infoboxComponentName' colspan='2'>
								<center>" . $name . "</center>
							</td>
						</tr>
						<tr>
							<td>
							<table class='infoboxComponentInnerTable'>";
		}
		//Add Image
		if($image != '')
		{
			$retVal .= "<tr>
							<td class='infoboxComponentImage' colspan='2'>
								<center>" . $image . "</center>
							</td>
						</tr>";
		}
		//Add Category
		if($category != '')
		{
			$retVal .= "<tr>
							<td class='infoboxComponentLabel'>
								Category
							</td>
							<td class='infoboxComponentValue'>
								" . $category . "
							</td>
						</tr>";
		}
		
		return $retVal;
	}
}

/********************************************************************************************************************************
* parseProductHistory()
*
* Generate Infobox HTML Based On User Specified Values
********************************************************************************************************************************/
function parseProductHistory($manufacturer)
{
	$retVal = '';
	
	$manufacturer = trim($manufacturer);
	
	if($manufacturer == '')
	{
		//This Section Contains No Data - Nothing To Render
		return $retVal;
	}
	else
	{
		//Section Contains Data.  Add Section Header
		$retVal .= "<tr>
						<td class='infoboxComponentSectionHeader' colspan='2'>
							<center>Product History</center>
						</td>
					</tr>";
		
		//Add Section Labels and Values
		
		//Add Manufacturer Label / Value (If It Exists)
		if($manufacturer != '')
		{
			$retVal .= "<tr>
							<td class='infoboxComponentLabel'>
								Manufacturer
							</td>
							<td class='infoboxComponentValue'>
								" . $manufacturer . "
							</td>
						</tr>";
		}
		
		return $retVal;
	}
}

/********************************************************************************************************************************
* parseGenerralSpecs()
*
* Generate Infobox HTML Based On User Specified Values
********************************************************************************************************************************/
function parseGeneralSpecs($width, $depth, $height, $pins)
{
	$retVal = '';
	
	$width = trim($width);
	$depth = trim($depth);
	$height = trim($height);
	$pins = trim($pins);
	
	if( ($width == '') && ($height == '') && ($pins == '') )
	{
		//This Section Contains No Data - Nothing To Render
		return $retVal;
	}
	else
	{
		//Section Contains Data.  Add Section Header
		$retVal .= "<tr>
						<td class='infoboxComponentSectionHeader' colspan='2'>
							<center>General Specifications</center>
						</td>
					</tr>
					<tr>
							<td class='infoboxComponentLabel'>
								Size (W, D, H)
							</td>
							<td class='infoboxComponentValue'>";
		//Add Width
		if($width != '')
		{
			$retVal .= $width . " X ";
		}
		else
		{
			$retVal .= "- X ";
		}
		//Add Depth
		if($depth != '')
		{
			$retVal .= $depth . " X ";
		}
		else
		{
			$retVal .= "- X ";
		}
		//Add Height
		if($height != '')
		{
			$retVal .= $height;
		}
		else
		{
			$retVal .= "-";
		}
		
		//Close Size Row
		$retVal .= "</tr>";
		
		//Add Pins
		if($pins != '')
		{
			$retVal .= "<tr>
							<td class='infoboxComponentLabel'>
								Pins
							</td>
							<td class='infoboxComponentValue'>
								" . $pins . "
							</td>
						</tr>";
		}		
		return $retVal;
	}
}

/********************************************************************************************************************************
* parseElectrical()
*
* Generate Infobox HTML Based On User Specified Values
********************************************************************************************************************************/
function parseElectrical($vccMin, $vccTypical, $vccMax, $vccUnits, $iccMin, $iccTypical, $iccMax, $iccUnits, $powerMin, $vTypical, $powerMax, $powerUnits, $llMin, $llTypical, $llMax, $llUnits)
{
	$retVal = '';
	
	$vccMin = trim($vccMin);
	$vccTypical = trim($vccTypical); 
	$vccMax = trim($vccMax); 
	$vccUnits = trim($vccUnits); 
	$iccMin = trim($iccMin); 
	$iccTypical = trim($iccTypical); 
	$iccMax = trim($iccMax); 
	$iccUnits = trim($iccUnits); 
	$powerMin = trim($powerMin);  
	$vTypical = trim($vTypical); 
	$powerMax = trim($powerMax); 
	$powerUnits = trim($powerUnits); 
	$llMin = trim($llMin); 
	$llTypical = trim($llTypical); 
	$llMax = trim($llMax); 
	$llUnits = trim($llUnits); 
	
	if( $vccMin == '' && $vccTypical == '' && $vccMax == '' && $vccUnits == '' && $iccMin == '' && $iccTypical == '' && $iccMax == '' && $iccUnits == '' && $powerMin == '' && $vTypical == '' && $powerMax == '' && $powerUnits == '' && $llMin == '' && $llTypical == '' && $llMax == '' && $llUnits =='')
	{
		//This Section Contains No Data - Nothing To Render
		return $retVal;
	}
	else
	{
		//Section Contains Data.  Add Section Header
		$retVal .= "<tr>
						<td class='infoboxComponentSectionHeader' colspan='2'>
							<center>Electrical</center>
						</td>
					</tr>
					<tr>
						<td class='infoboxComponentElectricalOuter' colspan='2'>
							<table class='infoboxComponentElectrical'>";
		
		//Add Section Labels and Values
		
		//Add Vcc If It Exists
		if($vccMin != '' || $vccTypical != '' || $vccMax != '' || $vccUnits != '')
		{
			//Add Electrical Table Header Row
			$retVal .= "<tr>
							<td class='infoboxComponentElectricalHeader'>
								Symbol
							</td>
							<td class='infoboxComponentElectricalHeader'>
								<center>Min</center>
							</td>
							<td class='infoboxComponentElectricalHeader'>
								<center>Typical</center>
							</td>
							<td class='infoboxComponentElectricalHeader'>
								<center>Max</center>
							</td>
							<td class='infoboxComponentElectricalHeader'>
								<center>Units</center>
							</td>
						</tr>
						<tr>
							<td class='infoboxComponentElectricalLabel'>
								Vcc
							</td>";
			//Add Elements
			//Vcc Min
			if($vccMin == '')
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>" . $vccMin . "</center>
							</td>";
			}
			//Vcc Tpyical
			if($vccTypical == '')
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>" . $vccTypical . "</center>
							</td>";
			}
			//Vcc Max
			if($vccMax == '')
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>" . $vccMax . "</center>
							</td>";
			}
			//Vcc Units
			if($vccUnits == '')
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>" . $vccUnits . "</center>
							</td>";
			}
			
			//Close Vcc Row
			$retVal .= "</tr>";
		}
		
		//Add Icc If It Exists
		if($iccMin != '' || $iccTypical != '' || $iccMax != '' || $iccUnits != '')
		{
			//Create icc Row
			$retVal .= "<tr>
							<td class='infoboxComponentElectricalLabel'>
								Icc
							</td>";
			//Add Elements
			//icc Min
			if($iccMin == '')
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>" . $iccMin . "</center>
							</td>";
			}
			//icc Tpyical
			if($iccTypical == '')
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>" . $iccTypical . "</center>
							</td>";
			}
			//icc Max
			if($iccMax == '')
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>" . $iccMax . "</center>
							</td>";
			}
			//icc Units
			if($iccUnits == '')
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'> 
								<center>" . $iccUnits . "</center>
							</td>";
			}
			
			//Close icc Row
			$retVal .= "</tr>";
		}
		
		//Add power If It Exists
		if($powerMin != '' || $powerTypical != '' || $powerMax != '' || $powerUnits != '')
		{
			//Create power Row
			$retVal .= "<tr>
							<td class='infoboxComponentElectricalLabel'>
								Power
							</td>";
			//Add Elements
			//power Min
			if($powerMin == '')
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>" . $powerMin . "</center>
							</td>";
			}
			//power Tpyical
			if($powerTypical == '')
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>" . $powerTypical . "</center>
							</td>";
			}
			//power Max
			if($powerMax == '')
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>" . $powerMax . "</center>
							</td>";
			}
			//power Units
			if($powerUnits == '')
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'> 
								<center>" . $powerUnits . "</center>
							</td>";
			}
			
			//Close power Row
			$retVal .= "</tr>";
		}
		
		//Add Logic Level If It Exists
		if($llMin != '' || $llTypical != '' || $llMax != '' || $llUnits != '')
		{
			//Create Logic Level Row
			$retVal .= "<tr>
							<td class='infoboxComponentElectricalLabel'>
								Logic Level
							</td>";
			//Add Elements
			//Logic Level Min
			if($llMin == '')
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>" . $llMin . "</center>
							</td>";
			}
			//Logic Level Tpyical
			if($llTypical == '')
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td class='infoboxComponentElectricalValue'>
								<center>" . $llTypical . "</center>
							</td>";
			}
			//Logic Level Max
			if($llMax == '')
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>" . $llMax . "</center>
							</td>";
			}
			//Logic Level Units
			if($llUnits == '')
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'>
								<center>-</center>
							</td>";
			}
			else
			{
				$retVal .= "<td  class='infoboxComponentElectricalValue'> 
								<center>" . $llUnits . "</center>
							</td>";
			}
			
			//Close Logic Level Row
			$retVal .= "</tr>";
		}
		//Close Cell And Row Containing Electrical Table Then Close Electrical Table
		$retVal .= "</td></tr></table>";
		
		return $retVal;
	}
}

/********************************************************************************************************************************
* parsePinout()
*
* Generate Infobox HTML Based On User Specified Values
********************************************************************************************************************************/
function parsePinout($pins)
{
	$retVal = "";
	
	//Create A Pin Entry For Every Pin Up To The Highest Pin Number Specified
	$pinCount = max(array_keys($pins));
	
	//Impose Arbirarty Limit Of 256 Pins
	if($pinCount > 256)
	{
		return "<tr>
					<td class='infoboxComponentSectionHeader' colspan='2'>
						<center>Pinout</center>
					</td>
				</tr>
				<tr>
					<td class='infoboxComponentLabel' colspan='2'>
						Too Many Pins (256 Max)
					</td>
				</tr>";
	}
		
	//If No Pins Exist Skip Pinout Section
	if(count($pins) <= 0)
	{
		return '';
	}
	else
	{
		//Pins Exist, Add Pinout Section Header
		$retVal .= "<tr>
						<td class='infoboxComponentSectionHeader' colspan='2'>
							<center>Pinout</center>
						</td>
					</tr>";	
	
		//Add Row For Each Pin (Up To Max Pin Number
		for($i=0; $i<=max(array_keys($pins)); $i++)
		{
			//If Pin Value Is Not Specified Add Row With '-'
			if(trim($pins[$i]) == '')
			{
				$retVal .= "<tr>
							<td class='infoboxComponentLabel'>
								Pin " . $i . "
							</td>
							<td class='infoboxComponentValue'>
								-
							</td>
						<tr>";
			}
			//Pin Value Specified, Add Pin Row With Specified Value
			else
			{
				//Add Pin Row
				$retVal .= "<tr>
								<td class='infoboxComponentLabel'>
									Pin " . $i . "
								</td>
								<td class='infoboxComponentValue'>
									" . $pins[$i] . "
								</td>
							<tr>";
			}
		}
	}
	return $retVal;
}