<?php
/********************************************************************************************************************************
*
* LabVIEW Hacker Download Button Plugin
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
class syntax_plugin_lvhpacket extends DokuWiki_Syntax_Plugin 
{

	//Return Plugin Info
	function getInfo() 
	{
        return array('author' => 'Sammy_K',
                     'email'  => 'sammyk.labviewhacker@gmail.com',
                     'date'   => '2013-07-28',
                     'name'   => 'LabVIEW Hacker Packet',
                     'desc'   => 'Template for describing a packet',
                     'url'    => 'www.labviewhacker.com');
    }
	

	//Set This To True To Enable Debug Strings
	protected $lvhDebug = false;
	
	/***************************************************************************************************************************
	* Plugin Variables
	***************************************************************************************************************************/
	protected $name = '';
	protected $description = '';		
	protected $size = '';	
	protected $format = '';	
	protected $subPackets = array();	
	
    /********************************************************************************************************************************************
	** Plugin Configuration
	********************************************************************************************************************************************/			
				
    function getType() { return 'protected'; }
    function getSort() { return 32; }
  
    function connectTo($mode) {
        $this->Lexer->addEntryPattern('{{lvh_packet.*?(?=.*?}})',$mode,'plugin_lvhpacket');
		
		//Add Internal Pattern Match For Product Page Elements	
		$this->Lexer->addPattern('\|.*?(?=.*?)\n','plugin_lvhpacket');
    }
	
    function postConnect() {
      $this->Lexer->addExitPattern('}}','plugin_lvhpacket');
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
					case 'name':						
						$this->name = $value;
						break;	
					case 'description':						
						$this->description = $value;
						break;	
					case 'size':						
						$this->size = $value;
						break;	
					case 'format':						
						$this->format = $value;
						break;
					case (preg_match('/subpacketheader[0-9]*/', $token, $pmSubpacketHeader)? true : false ) :						
						foreach($pmSubpacketHeader as $iVal)
						{
							$subPacketHeaderNum = substr($iVal, 15);		//Get Number At End Of String
							$this->subPackets[$subPacketHeaderNum][0] = $value;
						}
						break;
					case (preg_match('/subpacketsize[0-9]*/', $token, $pmSubPacketSize)? true : false ) :
						foreach($pmSubPacketSize as $iVal)
						{
							$subPacketSizeNum = substr($iVal, 13);		//Get Number At End Of String
							//If Packet Header Has No Data Insert Empty Element For Header
							if(count($this->subPackets[$subPacketSizeNum]) == 0)
							{
								$this->subPackets[$subPacketSizeNum][0] = '';
							}
							$this->subPackets[$subPacketSizeNum][1] = $value;
						}
						break;
					case (preg_match('/subpacketdetails[0-9]*/', $token, $pmSubPacketDetails)? true : false ) :
						foreach($pmSubPacketDetails as $iVal)
						{
							$subPacketDetailsNum = substr($iVal, 16);		//Get Number At End Of String
							//If Packet Header Has No Data Insert Empty Element For Header
							if(count($this->subPackets[$subPacketDetailsNum]) == 0)
							{
								$this->subPackets[$subPacketDetailsNum][0] = '';
							}
							$this->subPackets[$subPacketDetailsNum][2] = $value;
						}
						//$this->detailedText[] = $value;
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
				** Build Subpacket Details
				********************************************************************************************************************************************/				
				$packetBreakdown = '';
				$packetSize = 0;
				
				//Count Packet Size
				foreach($this->subPackets as $subPacketVal)
				{
					$packetSize += $subPacketVal[1];
				}
				
				//Calculate Num Cols
				$numCols = ($packetSize+1)*8;
				
				//Build Packet Breakdown HTML
				foreach($this->subPackets as $subPacketVal)
				{
					$packetBreakdown .= "				
					   <tr>
						  <td class='subPacketHeaderCell'>
							 " . $subPacketVal[0] . "
						  </td>
						  <td class='subPacketDetailsCell' colspan='" . ($numCols) . "'>
							 " . $subPacketVal[2] . "
						  </td>
					   </tr>";					 
				}
				/************************************************************
				 * Helper Functions For HTML Generation
				 *************************************************************/
					 
				//Convert Packet Size From Bits To Bytes
				$partialByte = 0;
				if( ($packetSize % 8) > 0)
				{
					$partialByte = 1;
				}
				$packetSize = floor($packetSize / 8) + $partialByte;
				
				//Create Format Header HTML
				$formatHeader = "
								<tr>
								<td class='subPacketHeaderCell' rowspan='2'>
									Format
								</td>";
				//Add Packet Numbers
				for($i=$packetSize; $i>=0; $i--)
				{
					$formatHeader .="
									<td colspan='8'>
										<center>" . $i . "</center>	
									</td>";
				}	
				
				//Add Bit Numbers
				$formatHeader .="</tr>
								 <tr>";
								
				for($i=$packetSize; $i>=0; $i--)
				{
					$formatHeader .="
								<td>
									<center>7</center>
								</td>
								<td>
									<center>6</center>
								</td>
								<td>
									<center>5</center>
								</td>
								<td>
									<center>4</center>
								</td>
								<td>
									<center>3</center>
								</td>
								<td>
									<center>2</center>
								</td>
								<td>
									<center>1</center>
								</td>
								<td>
									<center>0</center>
								</td>";
				}	
			
				//Build Array To Send To Renderer
				$retVal = array($state, $this->name, $this->description, $this->size, $formatHeader, $packetBreakdown, $numCols);
				
				//Clear Variables That Will Be Resused Here If Neccissary
				$this->name = '';
				$this->description = '';
				$this->size = '';
				$this->format = '';
				
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
				 $instName = $data[1];
				 $instDescription = $data[2];
				 $instSize = $data[3];
				 $instFormat = $data[4];
				 $instPacketBreakdown = $data[5];
				 $instNumCols = $data[6];
				 
				 
				 /************************************************************
				 * Variables For HTML Generation
				 *************************************************************/
							 
				$renderer->doc .= "
					<head>
							<style type='text/css'>

								table.packetTable
								{  
									width:100%;	
									background-color: #EEEEEE;
									border-style:solid;	
									border-spacing:0; 
									border-width:0px;
									border-bottom: solid 2px #CCCCCC;
								}
								
								td.packetNameCell
								{ 									
									text-align: center;
									font-weight:bold;
									background-color: #BBBBBB;
								}
								
								td.packetFormatCell
								{ 									
									text-align: center;
									background-color: #BBBBBB;
									font-weight:bold;									
								}
								
								td.subPacketHeaderCell
								{ 
									width:7%;
									background-color: #BBBBBB;
									border-right-style:none;
									text-align:right;
									font-weight:bold;
								}
								td.subPacketDetailsCell
								{ 
									border-left-style:none;									
								}									
							</style>								
					</head>
				
				
					<body>
						<table class='packetTable'>
							<tr>								
								<td class='packetNameCell' colspan='" . ($instNumCols+1) . "'>
									<center>" . $instName . "</center>
								</td>
							</tr>
							<tr>
								<td class='subPacketHeaderCell'>
									Description
								</td>
								<td colspan='" . ($instNumCols) . "'>
									" . $instDescription . "
								</td>
							</tr>
							<tr>
								<td class='subPacketHeaderCell'>
									Size
								</td>
								<td colspan='" . ($instNumCols) . "'>
									" . $instSize . "
								</td>
							</tr>								
							" . $instFormat . "									
							" . $instPacketBreakdown  . " 
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
	