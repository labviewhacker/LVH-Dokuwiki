<?php
/**
 * Template header, included in the main and detail files
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();
?>

<!-- ********** HEADER ********** -->

<!-- Added By SK - Highslide Image Viewer -->
<script type="text/javascript" src="highslide/highslide-with-gallery.js"></script>
<script type="text/javascript" src="highslide/highslide.config.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="highslide/highslide.css" />
<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="highslide/highslide-ie6.css" />
<![endif]-->

<!--  ************Google Analytics ***************** -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36825298-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>





<div id="dokuwiki__header"><div class="pad group">

    <?php tpl_includeFile('header.html') ?>

    <div class="headings group">
        <ul class="a11y skip">
            <li><a href="#dokuwiki__content"><?php echo $lang['skip_to_content']; ?></a></li>
        </ul>

        
        <?php if ($conf['tagline']): ?>
            <p class="claim"><?php echo $conf['tagline']; ?></p>
        <?php endif ?>
    </div>

    <div class="tools group">
        <!-- USER TOOLS -->
        <?php if ($conf['useacl']): ?>
            <div id="dokuwiki__usertools">
                <h3 class="a11y"><?php echo $lang['user_tools']; ?></h3>
                <ul>
                    <?php
                        if ($_SERVER['REMOTE_USER']) {
                            echo '<li class="user">';
                            tpl_userinfo(); /* 'Logged in as ...' */
                            echo '</li>';
                        }
                        tpl_action('admin', 1, 'li');
                        tpl_action('profile', 1, 'li');
                        tpl_action('register', 1, 'li');
                        tpl_action('login', 1, 'li');
                    ?>
                </ul>
            </div>
        <?php endif ?>

        <!-- SITE TOOLS -->
        <div id="dokuwiki__sitetools">
            <h3 class="a11y"><?php echo $lang['site_tools']; ?></h3>
            
			<?php tpl_searchform(); ?>
            
			<div class="mobileTools">
                <?php tpl_actiondropdown($lang['tools']); ?>
            </div>
            <ul>
                <?php
                    //tpl_action('recent', 1, 'li');
                    //tpl_action('media', 1, 'li');
                    //tpl_action('index', 1, 'li');
                ?>
            </ul>
        </div>

    </div>

    <!-- BREADCRUMBS -->
    <?php if($conf['breadcrumbs'] || $conf['youarehere']): ?>
        <div class="breadcrumbs">
            <?php if($conf['youarehere']): ?>
                <div class="youarehere"><?php tpl_youarehere() ?></div>
            <?php endif ?>
            <?php if($conf['breadcrumbs']): ?>
                <div class="trace"><?php tpl_breadcrumbs() ?></div>
            <?php endif ?>
        </div>
    <?php endif ?>

    <?php html_msgarea() ?>

    <hr class="a11y" />
</div></div><!-- /header -->




<!-- Added By SK - LabVIEW Hacker Logo -->
<div id='siteLogo'>	
	<img src='/lib/tpl/lvh/images/LabVIEWHacker.png'>
	<br /><br />
</div>

<!-- Added By SK - Site Top Navigation -->
<link href="/menu_assets/styles.css" rel="stylesheet" type="text/css"> 

<!--******** Added By SK - Navbar Menu ********-->		
<div id='cssmenu'>
<ul>
   <li><a href='/'><span>Home</span></a></li>
   <li class='has-sub '><a href='/doku.php?id=libraries:libraries'><span>Libraries</span></a>
      <ul>
		 <li><a href='/doku.php?id=libraries:ardrone:ardrone'><span>AR.Drone</span></a></li>
         <li><a href='/doku.php?id=libraries:lifa:lifa'><span>Arduino</span></a></li>
      </ul>
   </li>   
   
   <li class='has-sub '><a href='/doku.php?id=projects:projects'><span>Projects</span></a>
      <ul>
         <li><a href='/doku.php?id=projects:lv_android_interface:lv_android_interface'><span>LabVIEW Android Interface</span></a></li>
		 <li><a href='/doku.php?id=projects:lv_ez430_interface:lv_ez430_interface'><span>LabVIEW EZ430-Chronos Interface</span></a></li>
		 <li><a href='/doku.php?id=projects:lv_epoc_interface:lv_epoc_interface'><span>LabVIEW Emotiv Epoc Interface</span></a></li>
		 <li><a href='/doku.php?id=projects:lv_irobot_create_interface:lv_irobot_create_interface'><span>LabVIEW iRobot Create Interface</span></a></li>
		 <li><a href='/doku.php?id=projects:lv_kinect_interface:lv_kinect_interface'><span>LabVIEW Kinect Interface</span></a></li>
		 <li><a href='/doku.php?id=projects:lv_leap_interface:lv_leap_interface'><span>LabVIEW Leap Interface</span></a></li>
		 <li><a href='/doku.php?id=projects:lv_neatolds_interface:lv_neatolds_interface'><span>LabVIEW Neato LDS Interface</span></a></li>
		 <li><a href='/doku.php?id=projects:lv_twitter_interface:lv_twitter_interface'><span>LabVIEW Twitter Interface</span></a></li>
		 <li><a href='/doku.php?id=projects:lv_mindshark:lv_mindshark'><span>LabVIEW MindShark</span></a></li>
		 <li><a href='/doku.php?id=projects:lv_vex_interface:lv_vex_interface'><span>LabVIEW VEX Interface</span></a></li>
		 <li><a href='/doku.php?id=projects:lv_wiimote_interface:lv_wiimote_interface'><span>LabVIEW Wiimote Interface</span></a></li>
		 <li><a href='/doku.php?id=projects:wll:science_fair_mashup'><span>Waterloo Labs: Science Fair Mash Up</span></a></li>		 
      </ul>
   </li>
   
   <!-- Learn -->
	<li class='has-sub '><a href=''><span>Learn</span></a>
		<ul>
			<li><a href='/doku.php?id=learn:software:github:getting_started'><span>GitHub Basics</span></a></li>
            <li class='has-sub '><a href='/doku.php?id=learn:software:github:labview_development_process_first_time_setup'><span>LabVIEW & GitHub</span></a>
                <ul>
                    <li class='last'><a href='/doku.php?id=learn:software:github:labview_development_process_first_time_setup'><span>First Time Setup</span></a></li>
                    <li class='last'><a href='/doku.php?id=learn:software:github:labview_development_process_new_project'><span>Starting a New Project</span></a></li>
                    <li class='last'><a href='/doku.php?id=learn:software:github:labview_development_process_existing_project'><span>Contributing to a LVH Project</span></a></li>
                </ul>
            </li>
		</ul>
   </li>
   
   <!-- Learn With Deeper Nesting
	<li class='has-sub '><a href=''><span>Learn</span></a>
		<ul>
			<li class='has-sub '><a href=''><span>GitHub</span></a>
				<ul>
					<li><a href='/doku.php?id=learn:tutorials:software:github:getting_started'><span>Getting Started with GitHub</span></a></li>   
				</ul>
			</li>
		</ul>
   </li>
   -->
</ul>
</div>
