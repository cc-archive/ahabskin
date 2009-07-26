<?php
/**
 * Ahab (based on Monobook nouveau)
 *
 * Translated from gwicke's previous TAL template version to remove
 * dependency on PHPTAL.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) )
	die( -1 );

/**
 * Inherit main code from SkinTemplate, set the CSS and template filter.
 * @todo document
 * @ingroup Skins
 */
class SkinAhab extends SkinTemplate {
	/** Using ahab. */
	function initPage( &$out ) {
		SkinTemplate::initPage( $out );
		$this->skinname  = 'ahab';
		$this->stylename = 'ahab';
		if ($out->mPagetitle == 'Main Page') {
		  $this->template = 'AhabFrontPageTemplate';
		} else {
		  $this->template  = 'AhabTemplate';
		}
	}
}

/**
 * @todo document
 * @ingroup Skins
 */
class AhabTemplate extends QuickTemplate {
	var $skin;
	/**
	 * Template filter callback for Ahab skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 *
	 * @access private
	 */
	function execute() {
		global $wgRequest;
		$this->skin = $skin = $this->data['skin'];

		$chosen_sidebox = null;

		/* Detect if we are in the landing page category */
		$in_landing_page = (bool) (strpos($this->data['catlinks'], 'Landing_page'));
		/* If we are, oh boy... */
		if ($in_landing_page) {
		  /* create a SMW Page */
		  $smw_page = SMWWikiPageValue::makePageFromTitle($this->skin->mTitle);

		  /* Create a Property for Preferred_sidebox */
		  $property = SMWPropertyValue::makeUserProperty('Preferred sidebox');
		  
		  /* Gank the current SMW data store... */
		  $store = &smwfGetStore();

		  /* ...and ask it if this page has a sidebox preference. */
		  $preferences = $store->getPropertyValues($smw_page, $property);

		  /* if the count is >=1, grab the first one. */
		  if (count($preferences) > 0) {
		      $chosen_sidebox = Title::newFromText($preferences[0]
							   ->getShortText(SMW_OUTPUT_HTML));
		  }
		}
		
		if ($chosen_sidebox === null) {
			/* we ought to pick a random one */
		        /* Whip up a query: String first, then object */
			$query_s = '[[Category:Sidebox]] [[Enabled::True]]';
			$query = SMWQueryProcessor::createQuery($query_s, array());
			/* Perform the query */
			$res = smwfGetStore()->getQueryResult($query);
			/* Initialize an array to store the candidate Title objects in */
			$candidate_titles = array();
			/* Start the iteration */
			$resarray = $res->getNext();
			while ($resarray !== false) {
				$instance = end($resarray)->getNextObject();
				$candidate_titles[] = $instance->getTitle();
				$resarray = $res->getNext();
			}
			/* Pick a random one */
			$random_index = mt_rand(0, count($candidate_titles) - 1);
			$chosen_sidebox = $candidate_titles[$random_index];
		}
		
		$action = $wgRequest->getText( 'action' );

		// Suppress warnings to prevent notices about missing indexes in $this->data
		wfSuppressWarnings();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="<?php $this->text('xhtmldefaultnamespace') ?>" <?php 
	foreach($this->data['xhtmlnamespaces'] as $tag => $ns) {
		?>xmlns:<?php echo "{$tag}=\"{$ns}\" ";
	} ?>xml:lang="<?php $this->text('lang') ?>" lang="<?php $this->text('lang') ?>" dir="<?php $this->text('dir') ?>">
	<head>
		<meta http-equiv="Content-Type" content="<?php $this->text('mimetype') ?>; charset=<?php $this->text('charset') ?>" />
		<?php $this->html('headlinks') ?>
		<title><?php $this->text('pagetitle') ?></title>
        <script type="<?php $this->text('jsmimetype')?>" src="<?php $this->text('stylepath') ?>/ahab/js_css.js"></script>
		<style type="text/css" media="screen, projection">/*<![CDATA[*/
			@import "<?php $this->text('stylepath') ?>/ahab/from_whitewhale/styles/opened.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";
			@import "<?php $this->text('stylepath') ?>/ahab/from_wikipedia/navframe.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";
<?php
/* This code sucks. Is there some other way to ask a page what categories it is in?
   I don't see it. */
    if (! $in_landing_page) { ?>
			@import "<?php $this->text('stylepath') ?>/ahab/from_whitewhale/styles/opened_page.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";
<?php } ?>

		/*]]>*/</style>
		<link rel="stylesheet" type="text/css" <?php if(empty($this->data['printable']) ) { ?>media="print"<?php } ?> href="<?php $this->text('printcss') ?>?<?php echo $GLOBALS['wgStyleVersion'] ?>" />
		<meta http-equiv="imagetoolbar" content="no" />
		
		<?php print Skin::makeGlobalVariablesScript( $this->data ); ?>
                
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath' ) ?>/common/wikibits.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"><!-- wikibits js --></script>
        <script type="<?php $this->text('jsmimetype')?>" src="<?php $this->text('stylepath') ?>/ahab/from_wikipedia/navframe.js"></script>


		<!-- <script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath' ) ?>/ahab/beforeload.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"> wikibits js </script> -->
		<!-- Head Scripts -->
<?php $this->html('headscripts') ?>
<?php	if($this->data['jsvarurl']) { ?>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('jsvarurl') ?>"><!-- site js --></script>
<?php	} ?>
<?php	if($this->data['pagecss']) { ?>
		<style type="text/css"><?php $this->html('pagecss') ?></style>
<?php	}
		if($this->data['usercss']) { ?>
		<style type="text/css"><?php $this->html('usercss') ?></style>
<?php	}
		if($this->data['userjs']) { ?>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('userjs' ) ?>"></script>
<?php	}
		if($this->data['userjsprev']) { ?>
		<script type="<?php $this->text('jsmimetype') ?>"><?php $this->html('userjsprev') ?></script>
<?php	}
		if($this->data['trackbackhtml']) print $this->data['trackbackhtml']; ?>
	</head>
<body<?php if($this->data['body_ondblclick']) { ?> ondblclick="<?php $this->text('body_ondblclick') ?>"<?php } ?>
<?php if($this->data['body_onload']) { ?> onload="<?php $this->text('body_onload') ?>"<?php } ?>
 class="mediawiki <?php $this->text('nsclass') ?> <?php $this->text('dir') ?> <?php $this->text('pageclass') ?>">
<a href="#content" class="skiplink">Skip to content</a>

<div id="container">
   <?php $this->topbox(); ?>
	<div id="betaBox">&nbsp;</div>
	<div id="frame">

		<div id="sidebar">
   <!-- sidebar -->
   <!-- header -->
			<div id="header">
				<h1><a href="<?php echo htmlspecialchars($this->data['nav_urls']['mainpage']['href'])?>"><img src="<?php $this->text('stylepath') ?>/ahab/from_whitewhale/images/common/opened_logo.gif" alt="Open Ed" width="210" height="113"/></a></h1>
				<form id="searchbox" style="z-index: 50;" action="<?php $this->text('searchaction') ?>" method="get">
					<span class="invisible_if_js"><label for="searchbox_query" id="searchbox_title">Search Open Ed</label></span>
					<input name="search" id="searchInput" type="text"/><input type="submit" name="go" class="searchButton" id="searchGoButton" value="<?php $this->msg('searcharticle') ?>"/>
					<ul id="searchbox_sources">
						<li><input type="checkbox" id="searchbox_oer" checked="checked"/><label for="searchbox_oer">Open ed (DiscoverED)</label></li>
						<li><input type="checkbox" id="searchbox_odepo" checked="checked"/><label for="searchbox_odepo">Organizations (ODEPO)</label></li>
						<li><input type="checkbox" id="searchbox_community" checked="checked"/><label for="searchbox_community">Community resources</label></li>
						<li><input type="checkbox" id="searchbox_site" checked="checked"/><label for="searchbox_site">This site</label></li>
					</ul>
				</form>
<?php 
		$sidebar = $this->data['sidebar'];		
		if ( !isset( $sidebar['SEARCH'] ) ) $sidebar['SEARCH'] = true;
		if ( !isset( $sidebar['TOOLBOX'] ) ) $sidebar['TOOLBOX'] = true;
		if ( !isset( $sidebar['LANGUAGES'] ) ) $sidebar['LANGUAGES'] = true;
		foreach ($sidebar as $boxName => $cont) {
			if ( $boxName == 'SEARCH' ) { /* skip search */
			} elseif ( $boxName == 'TOOLBOX' ) {
			  /* skip toolbox */
			} elseif ( $boxName == 'LANGUAGES' ) {
				$this->languageBox();
			} else {
				$this->customBox( $boxName, $cont );
			}
		}
?>

			</div>

<!-- end header -->

<!-- image panel -->

			    <?php
		global $wgParser;
		global $wgUser;
		$wgParser->startExternalParse( $chosen_sidebox, new ParserOptions(), OT_HTML);
		$articleObj = new Article($chosen_sidebox);
		// Try the parser cache first
		$pcache = ParserCache::singleton();
		$p_result = $pcache->get($articleObj, $wgUser);
		if(!$p_result)
		  {
		    $p_result = $wgParser->parse($articleObj->getContent(), $chosen_sidebox, new ParserOptions());
		    global $wgUseParserCache;
		    if($wgUseParserCache)
		      $pcache->save($p_result, $articleObj, $popts);
		  }

		$rendered_text = $p_result->mText;
		# evil evil hackery
		$fixed_text = str_replace('class="mw-headline"', '', $rendered_text);
		if (substr_count($fixed_text, '<img') > 0) {
		  ?><div class="panel with_image">
		    <?php } else {
		  ?><div class="panel">
		       <?php }
		print($fixed_text);
 ?>

			</div> <!-- end image panel -->

<?php $this->copyleft(); ?>
		</div> <!-- end sidebar -->

<!-- content -->
		<div id="content" style="position: relative;">
			<div id="googleTranslate" style="position: absolute; top: 0; left: 0;">
				<!-- Google Translate Options -->

<div id="languages" align="left" style="width:auto; cursor:pointer; font: 11px Verdana, sans-serif;">

<!-- Add English to Chinese (Simplified) -->
<a style="color: #cc0000; text-decoration: underline;" target="_blank" rel="nofollow" onclick="window.open('http://www.google.com/translate?u='+encodeURIComponent(location.href)+'&langpair=en%7Czh-CN&hl=en&ie=UTF8'); return false;" title="Google-Translate-Chinese (Simplified)">中文</a>&nbsp;&nbsp;

<!-- END English to Chinese (Simplified)-->

<!-- Add English to Spanish -->

<a style="color: #cc0000; text-decoration: underline;" target="_blank" rel="nofollow" onclick="window.open('http://www.google.com/translate?u='+encodeURIComponent(location.href)+'&langpair=en%7Ces&hl=en&ie=UTF8'); return false;" title="Google-Translate-English to Spanish ">Español</a>&nbsp;&nbsp;

<!-- END English to Spanish -->

<!-- Add English to French -->

<a style="color: #cc0000; text-decoration: underline;" target="_blank" rel="nofollow" onclick="window.open('http://www.google.com/translate?u='+encodeURIComponent(location.href)+'&langpair=en%7Cfr&hl=en&ie=UTF8'); return false;" title="Google-Translate-English to French ">Français</a>&nbsp;&nbsp;

<!-- END English to French -->
<br />
<!-- Add English to Portuguese -->

<a style="color: #cc0000; text-decoration: underline;"target="_blank" rel="nofollow" onclick="window.open('http://www.google.com/translate?u='+encodeURIComponent(location.href)+'&langpair=en%7Cpt&hl=en&ie=UTF8'); return false;" title="Google-Translate-English to Portuguese ">Português</a>&nbsp;&nbsp;

<!-- END English to Portuguese -->

<a style="color: #cc0000; text-decoration: underline;" href="http://opened.creativecommons.org/Other_Languages">Other Languages</a>
</div>

			</div>
			<div>&nbsp;</div>
		    <h1 class="firstHeading"><?php $this->formattedTitle(); ?></h1>

		 <?php $this->html('bodytext') ?>
		</div>

<!-- end content -->

<!-- clear both -->
	  <div style="clear:both;"></div>

	</div>
<!-- end frame -->

<!-- footer -->
<?php $this->footer(); ?>

</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"></script>
<script type="text/javascript" src="<?php $this->text('stylepath') ?>/ahab/from_whitewhale/scripts/opened.js"></script>
<?php $this->html('bottomscripts'); /* JS call to runBodyOnloadHook */ ?>
<?php $this->html('reporttime') ?>
<?php if ( $this->data['debug'] ): ?>
<!-- Debug output:
<?php $this->text( 'debug' ); ?>

-->
<?php endif; ?>
</body></html>
<?php
	wfRestoreWarnings();
	} // end of execute() method

	function footer() {
?>
	  <ul id="footer">
	    <li id="home"><a href="http://learn.creativecommons.org/">Hosted by ccLearn</a></li>
	    <li id="contact">
	    <a href="http://opened.creativecommons.org/Using_this_Site">Using this site</a> | 
	    <a href="http://opened.creativecommons.org/About">About this site</a> | 
	    <a href="http://opened.creativecommons.org/Special:RecentChanges">Recent Changes</a> (<a href="http://opened.creativecommons.org/index.php?title=Special:RecentChanges&feed=rss">RSS</a>) |
	    <a href="http://creativecommons.org/terms">Terms of use</a> | 
	    <a href="http://creativecommons.org/privacy">Privacy policy</a> |
	    <a href="http://learn.creativecommons.org/contact">Contact us</a>
	    </li>
	    </ul>
	      <?php }

	function topbox() {
?>
   <div id="toplinks">
   <?php $this->views(); ?>
   <?php $this->personaltools(); ?>
   <?php if ($this->data['loggedin']) { $this->toolbox(); } ?>
   </div>
       <?php }
	  

	/*************************************************************************************************/
	function formattedTitle() {
	  /* MonoBook's title formatting code adds a call to htmlspecialchars()
	   * in the case that we are not using a displaytitle.
	   *
	   * I don't know why that is, so I preserve it here.
	   */
	  $title_data = $this->data['title'];
	  $pieces = preg_split('/([\W])/', $title_data, 2,
			       PREG_SPLIT_DELIM_CAPTURE);
	  $first_word = $pieces[0];
	  $delim      = $pieces[1];
	  $rest_words = $pieces[2];

	  if ($this->data['displaytitle']) {
	    /* Don't change the words. */
	  } else {
	    $first_word = htmlspecialchars($first_word);
	    $delim      = htmlspecialchars($delim);
	    $rest_words = htmlspecialchars($rest_words);
	  }

	  /* Finally, add HTML and echo. */
	  echo ('<span class="titleFirstWord">' .
		$first_word . '</span>' .
		$delim . $rest_words);
	}

	function searchBox() {
?>
	<div id="p-search" class="portlet">
		<h5><label for="searchInput"><?php $this->msg('search') ?></label></h5>
		<div id="searchBody" class="pBody">
			<form action="<?php $this->text('searchaction') ?>" id="searchform"><div>
				<input id="searchInput" name="search" type="text"<?php echo $this->skin->tooltipAndAccesskey('search');
					if( isset( $this->data['search'] ) ) {
						?> value="<?php $this->text('search') ?>"<?php } ?> />
				<input type='submit' name="go" class="searchButton" id="searchGoButton"	value="<?php $this->msg('searcharticle') ?>"<?php echo $this->skin->tooltipAndAccesskey( 'search-go' ); ?> />&nbsp;
				<input type='submit' name="fulltext" class="searchButton" id="mw-searchButton" value="<?php $this->msg('searchbutton') ?>"<?php echo $this->skin->tooltipAndAccesskey( 'search-fulltext' ); ?> />
			</div></form>
		</div>
	</div>
<?php
	}

	/*************************************************************************************************/
	function toolbox() {
?>
	<div class="toolbox" id="p-tb">
		<div class="pBody">
			<ul>
<?php
		if($this->data['notspecialpage']) { ?>
				<li id="t-whatlinkshere"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['whatlinkshere']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-whatlinkshere') ?>><?php $this->msg('whatlinkshere') ?></a></li>
<?php
			if( $this->data['nav_urls']['recentchangeslinked'] ) { ?>
				<li id="t-recentchangeslinked"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['recentchangeslinked']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-recentchangeslinked') ?>><?php $this->msg('recentchangeslinked') ?></a></li>
<?php 		}
		}
		if(isset($this->data['nav_urls']['trackbacklink'])) { ?>
			<li id="t-trackbacklink"><a href="<?php
				echo htmlspecialchars($this->data['nav_urls']['trackbacklink']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-trackbacklink') ?>><?php $this->msg('trackbacklink') ?></a></li>
<?php 	}
		if($this->data['feeds']) { ?>
			<li id="feedlinks"><?php foreach($this->data['feeds'] as $key => $feed) {
					?><span id="feed-<?php echo Sanitizer::escapeId($key) ?>"><a href="<?php
					echo htmlspecialchars($feed['href']) ?>"<?php echo $this->skin->tooltipAndAccesskey('feed-'.$key) ?>><?php echo htmlspecialchars($feed['text'])?></a>&nbsp;</span>
					<?php } ?></li><?php
		}

		foreach( array('contributions', 'log', 'blockip', 'emailuser', 'upload', 'specialpages') as $special ) {

			if($this->data['nav_urls'][$special]) {
				?><li id="t-<?php echo $special ?>"><a href="<?php echo htmlspecialchars($this->data['nav_urls'][$special]['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-'.$special) ?>><?php $this->msg($special) ?></a></li>
<?php		}
		}

		if(!empty($this->data['nav_urls']['print']['href'])) { ?>
				<li id="t-print"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['print']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-print') ?>><?php $this->msg('printableversion') ?></a></li><?php
		}

		if(!empty($this->data['nav_urls']['permalink']['href'])) { ?>
				<li id="t-permalink"><a href="<?php echo htmlspecialchars($this->data['nav_urls']['permalink']['href'])
				?>"<?php echo $this->skin->tooltipAndAccesskey('t-permalink') ?>><?php $this->msg('permalink') ?></a></li><?php
		} elseif ($this->data['nav_urls']['permalink']['href'] === '') { ?>
				<li id="t-ispermalink"<?php echo $this->skin->tooltip('t-ispermalink') ?>><?php $this->msg('permalink') ?></li><?php
		}

		wfRunHooks( 'MonoBookTemplateToolboxEnd', array( &$this ) );
		wfRunHooks( 'SkinTemplateToolboxEnd', array( &$this ) );
?>
			</ul>
		</div>
	</div>
<?php
	}

	/*************************************************************************************************/
	function languageBox() {
		if( $this->data['language_urls'] ) { 
?>
	<div id="p-lang" class="portlet">
		<h5><?php $this->msg('otherlanguages') ?></h5>
		<div class="pBody">
			<ul>
<?php		foreach($this->data['language_urls'] as $langlink) { ?>
				<li class="<?php echo htmlspecialchars($langlink['class'])?>"><?php
				?><a href="<?php echo htmlspecialchars($langlink['href']) ?>"><?php echo $langlink['text'] ?></a></li>
<?php		} ?>
			</ul>
		</div>
	</div>
<?php
		}
	}

   function copyleft() {
?>			<div id="copyleft">
				<a href="http://learn.creativecommons.org" id="cclearn"><img src="<?php $this->text('stylepath') ?>/ahab/from_whitewhale/images/common/cclearn_logo.gif" alt="ccLearn" width="92" height="23"/></a>
				<div id="license">
                    <a rel="license" href="http://creativecommons.org/licenses/by/3.0/">
                    <img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/88x31.png" /></a>

<p>
Except where otherwise noted, content on this site available
under a <a rel="license" href="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution 3.0 Unported License</a>.
Attribute to <a xmlns:cc="http://creativecommons.org/ns#" href="http://opened.creativecommons.org/" property="cc:attributionName" rel="cc:attributionURL">OpenEd</a>.
</p>

				</div>
			</div>

<?php
   }

   function personaltools() {
?>
		<div class="pBody">
			<ul>
<?php 			foreach($this->data['personal_urls'] as $key => $item) { ?>
				<li id="pt-<?php echo Sanitizer::escapeId($key) ?>"<?php
					if ($item['active']) { ?> class="active"<?php } ?>><a href="<?php
				echo htmlspecialchars($item['href']) ?>"<?php echo $this->skin->tooltipAndAccesskey('pt-'.$key) ?><?php
				if(!empty($item['class'])) { ?> class="<?php
				echo htmlspecialchars($item['class']) ?>"<?php } ?>><?php
				echo htmlspecialchars($item['text']) ?></a></li>
<?php			} ?>
			</ul>
	</div>
<?php
   }

   function views() {
?>

			<ul class="views">
	<?php		foreach($this->data['content_actions'] as $key => $tab) {
					echo '
				 <li id="ca-' . Sanitizer::escapeId($key).'"';
					if( $tab['class'] ) {
						echo ' class="'.htmlspecialchars($tab['class']).'"';
					}
					echo'><a href="'.htmlspecialchars($tab['href']).'"';
					# We don't want to give the watch tab an accesskey if the
					# page is being edited, because that conflicts with the
					# accesskey on the watch checkbox.  We also don't want to
					# give the edit tab an accesskey, because that's fairly su-
					# perfluous and conflicts with an accesskey (Ctrl-E) often
					# used for editing in Safari.
				 	if( in_array( $action, array( 'edit', 'submit' ) )
				 	&& in_array( $key, array( 'edit', 'watch', 'unwatch' ))) {
				 		echo $this->skin->tooltip( "ca-$key" );
				 	} else {
				 		echo $this->skin->tooltipAndAccesskey( "ca-$key" );
				 	}
				 	echo '>'.htmlspecialchars($tab['text']).'</a></li>';
				} ?>
			</ul>

<?php
   }

	/*************************************************************************************************/
   function customBox( $bar, $cont, $outer_class = 'generated-sidebar portlet', $inner_class = 'pBody' ) {
?>
	<div class='<?php echo $outer_class ?>' id='p-<?php echo Sanitizer::escapeId($bar) ?>'<?php echo $this->skin->tooltip('p-'.$bar) ?>>
		<div class='<?php echo $inner_class ?>'>
<?php   if ( is_array( $cont ) ) { ?>
			<ul>
<?php 			foreach($cont as $key => $val) { ?>
				<li id="<?php echo Sanitizer::escapeId($val['id']) ?>"<?php
					if ( $val['active'] ) { ?> class="active" <?php }
				?>><a href="<?php echo htmlspecialchars($val['href']) ?>"<?php echo $this->skin->tooltipAndAccesskey($val['id']) ?>><?php echo htmlspecialchars($val['text']) ?></a></li>
<?php			} ?>
			</ul>
<?php   } else {
			# allow raw HTML block to be defined by extensions
			print $cont;
		}
?>
		</div>
	</div>
<?php
	}

} // end of class


class AhabFrontPageTemplate extends AhabTemplate {
	function execute() {
		global $wgRequest;
		$this->skin = $skin = $this->data['skin'];
		$action = $wgRequest->getText('action');
		
		// Suppress warnings to prevent notices about missing
		// indexes in $this->data
		
		wfSuppressWarnings();

		?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<title>Open Ed at Creative Commons</title>
		<style type="text/css" media="screen, projection">/*<![CDATA[*/
			@import "<?php $this->text('stylepath') ?>/ahab/from_whitewhale/styles/opened.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";
			@import "<?php $this->text('stylepath') ?>/ahab/from_whitewhale/styles/homepage.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";
		/*]]>*/</style>
		<script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath' ) ?>/common/wikibits.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"><!-- wikibits js --></script>
		<!-- <script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath' ) ?>/ahab/beforeload.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"> wikibits js </script> -->
</head>
<body>
<a href="#content" class="skiplink">Skip to content</a>
<div id="clip">
	<div id="container">
			   <?php $this->topbox(); ?>
		<div id="betaBox">&nbsp;</div>
		<div id="frame">
			<div id="googleTranslate" style="position: absolute; right: 0;">
				<script src="http://www.gmodules.com/ig/ifr?url=http://www.google.com/ig/modules/translatemypage.xml&up_source_language=en&w=160&h=60&title=&border=&output=js"></script>
			</div>
			<div id="header">
				<h1><a href="/"><img src="<?php $this->text('stylepath') ?>/ahab/from_whitewhale/images/common/opened_logo_tagline_an.gif" alt="Open Ed: The Open Education Project at Creative Commons" width="214" height="157"/></a></h1>
<?php
		$sidebar = $this->data['sidebar'];		
		if ( !isset( $sidebar['SEARCH'] ) ) $sidebar['SEARCH'] = true;
		if ( !isset( $sidebar['TOOLBOX'] ) ) $sidebar['TOOLBOX'] = true;
		if ( !isset( $sidebar['LANGUAGES'] ) ) $sidebar['LANGUAGES'] = true;
		foreach ($sidebar as $boxName => $cont) {
			if ( $boxName == 'SEARCH' ) { /* skip search */
			} elseif ( $boxName == 'TOOLBOX' ) {
			  /* skip toolbox */
			} elseif ( $boxName == 'LANGUAGES' ) {
				$this->languageBox();
			} else {
			  $this->customBox( $boxName, $cont, '', 'navigation' );
			}
		}
?>
			</div>
			<div id="content">
					<?php include '/var/www/opened.creativecommons.org/volatile/opened-frontpage-sideboxes.html'; ?>
			</div>
			<?php $this->copyleft(); ?>
		</div>
	<div>
			    <?php $this->footer(); ?>
	</div>
</div>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"></script>
<script type="text/javascript" src="<?php $this->text('stylepath') ?>/ahab/from_whitewhale/scripts/jquery-ui-effects.core-1.6rc6.min.js"></script>
<script type="text/javascript" src="<?php $this->text('stylepath') ?>/ahab/from_whitewhale/scripts/opened.js"></script>
<script type="text/javascript" src="<?php $this->text('stylepath') ?>/ahab/from_whitewhale/scripts/homepage.js"></script>
</body>
</html><?php

		wfRestoreWarnings();
	} // end of execute() method
}

			
		
