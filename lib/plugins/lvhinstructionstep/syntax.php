<?php
/********************************************************************************************************************************
*
* LabVIEW Hacker Instruction Step Template Plugin
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
class syntax_plugin_lvhinstructionstep extends DokuWiki_Syntax_Plugin 
{

//Return Plugin Info
	function getInfo() 
	{
        return array('author' => 'Sammy_K',
                     'email'  => 'sammyk.labviewhacker@gmail.com',
                     'date'   => '2012-12-21',
                     'name'   => 'LabVIEW Hacker Instruction Step Template Plugin',
                     'desc'   => 'Template for LabVIEW Hacker Landing Pages',
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
	protected $description = '';
	protected $align = '';
	protected $expanded = '';
	protected $image = '';
	protected $imageSize = '';
	protected $subSteps = array();
	protected $subStepElement = '';
	protected $stepNum = 0;
	protected $detailedImageText = array();
	
	protected $tempVal = 'empty';
  
    function getType() { return 'protected'; }
    function getSort() { return 32; }
  
    function connectTo($mode) {
        $this->Lexer->addEntryPattern('{{lvh_instruction_step.*?(?=.*?}})',$mode,'plugin_lvhinstructionstep');
		
		//Add Internal Pattern Match For Product Page Elements	
		$this->Lexer->addPattern('\|.*?(?=.*?)\n','plugin_lvhinstructionstep');
    }
	
    function postConnect() {
      $this->Lexer->addExitPattern('}}','plugin_lvhinstructionstep');
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
					case 'title':						
						$this->title =  p_render('xhtml',p_get_instructions($value));
						break;	
					case 'description':						
						$this->description = $value;
						break;						
					case 'image':						
						$this->image = lvh_getImageLink($value);
						break;
					case 'substep':	
						$value = p_render('xhtml',p_get_instructions($value));
						$this->subSteps[] = $value;
						break;	
					case 'align':						
						$this->align = strtolower($value);
						break;
					case 'expanded':						
						$this->expanded = strtolower($value);
						break;
					//Detailed Image(s)
					case (preg_match('/detailedimage[0-9]*/', $token, $pmDetailedImageRes)? true : false ) :						
						foreach($pmDetailedImageRes as $iVal)
						{
							$detailNum = substr($iVal, 13);		//Get Number At End Of String
							$this->detailedImageText[$detailNum][0] = $value;
						}
						break;
					//Detailed Text(s)
					case (preg_match('/detailedtext[0-9]*/', $token, $pmDetailedTextRes)? true : false ) :
						foreach($pmDetailedTextRes as $iVal)
						{
							$detailNum = substr($iVal, 12);		//Get Number At End Of String
							//If Detailed Step Has No Image Insert Empty Element For Image Path
							if(count($this->detailedImageText[$detailNum]) == 0)
							{
								$this->detailedImageText[$detailNum][0] = '';
							}
							$this->detailedImageText[$detailNum][] = $value;
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
				** Build Substeps
				********************************************************************************************************************************************/			
				$this->subStepElement = "<ul class='subStep'>";
				foreach($this->subSteps as $subStepVal)
				{
					$this->subStepElement .= '<li>' . $subStepVal . '</li>';
				}
				$this->subStepElement .= '</ul>';
				
				/********************************************************************************************************************************************
				** Build Details
				********************************************************************************************************************************************/				
				$details = '';
				foreach($this->detailedImageText as $value)
				{
					$image = $value[0];	
					
					//Build Text For Use In Main Page And Fancy Image Caption
					unset($value[0]);
					$text = $value;
					$fancyText = "<ul class='subStep'>";
					foreach($text as $textElem)
					{
						//$fancyText .= "<li>" . lvh_parseWikiSyntax($textElem) . "</li>";
						$fancyText .= "<li>" . str_replace("\"", "'", lvh_allowSimpleWikiSyntax($textElem)) . "</li>";
						//$retVal = str_replace("\"", "'", $retVal);		//Replace " with ' so that links in detailed text work in image zoom window.
					}
					$fancyText .= "</ul>";	
					
					$fancyImage = lvh_getImageLink($image, $fancyText);
										
					//Add Image
					$details .= "<tr class='stepDetailRow' style='display: none;'>  
									<td class='stepDetailImage'> <center>" . $fancyImage . "</center></td>";
					//Add Text(s)
					$details .= 	"<td class='stepDetailText'> 
										<ul class='subStep'>
										" . $fancyText . "
									</td> 
								</tr>";	
				}		
				
				//isntID Is A Unique ID That Will Be Used To Identifiy The Step Instance
				$iD = preg_replace("/[^a-zA-Z]+/", "", $this->title);
				
				$retVal = array($state, $this->title, $this->description, $this->image, $this->imageSize, $this->subStepElement, $this->align, $this->stepNum, $details, $iD, $this->expanded);
				
				//Clear Variables That Will Be Reused On Next Step(s)
				$this->detailedImageText = array();
				$this->subSteps = '';
				
				$this->stepNum++;
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
				 $instDescription = $data[2];
				 $instImage = $data[3];
				 $instImageSize = $data[4];	
				 $instSubStepElement = $data[5];
				 $instAlign = $data[6];
				 $instStepNum = $data[7];
				 $instDetails = $data[8];
				 $instID = $data[9];
				 $instExpanded = $data[10];
				
				$renderer->doc .= "
					<head>
						<style type='text/css'>

							table.stepContainerTable
							{  
								width:100%;	
								
								border-style:solid;	
								border-spacing:0; 
								border-width:0px;
								border-radius:20px;
								border-bottom: solid 2px #CCCCCC;
								
							}	

							td.stepContainerCell
							{ 
								border:0px;
								vertical-align:middle;	
							}	

							tr.stepContainerRow
							{ 
								border:0px;	
							}

							table.step 
							{ 
								border:0px;
								width:100%;
							}

							table.step tr
							{
								
							}
							
							td.stepImage
							{	
								width:40%;
								height:200px;
								vertical-align:middle;		
								margin:auto;
								border:1px solid;
								border-color:#CCCCCC;
								padding:15px;
								background-color: transparent;
							}
							
							td.stepText
							{
								vertical-align:middle;												
								border:0px;								
							}
							
							stepTitle
							{
								font-size:150%;
							}	
							
							td.stepDetailImage
							{
								vertical-align:middle;	
								margin-left:auto;
								margin-right:auto;
								border:0px solid;
								border-color:#CCCCCC;
								
							}
							
							td.stepDetailText
							{
								vertical-align:middle;															
								border:0px;
								
							}
							
							tr.stepDetailRow
							{
								border-top: 1px dotted #CCCCCC;
							}
							
												
						</style>
					</head>
					
					<script type='text/javascript'>
						
						
						function toggleDisplay(tbl) 
						{							
							var tblRows = tbl.rows;							
							if(tblRows.length > 2)
							{
								var rowVisible = true;
								if(tblRows[2].style.display == 'none')
								{
									rowVisible = false;
								}
								
								for (i = 0; i < tblRows.length; i++) 
								{
									if (tblRows[i].className == 'stepDetailRow') 
									{
										tblRows[i].style.display = (rowVisible) ? 'none' : 'table-row';
									}
								}
								rowVisible = !rowVisible;
							}
						}
					</script>

					<body>
						<table class='stepContainerTable'>
							<tr class='stepContainerRow'>
								<td class='stepContainerCell'>						  
									<table class='step' id='Step" . $instID . "'>
										<tr>
											<td class='stepImage' rowspan='2'>
												<center>
												 " . $instImage . "
												 </center>
											</td>
											<td class='stepText'>
												<stepTitle> " . $instTitle . " </stepTitle>												
											</td>
										</tr>
										<tr>											
											<td class='stepText'>
												" . $instSubStepElement . "
												<br /><br />
												<div align='right'>
													<input type='button' value='Details' onclick='toggleDisplay(document.getElementById(&quot;Step" . $instID . "&quot;))' />
												</div>
											</td>
										</tr>
										" . $instDetails . "
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
	