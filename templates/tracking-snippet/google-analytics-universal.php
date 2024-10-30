<script type="text/javascript">
/* <![CDATA[ */

<?php if ( $gaoptout ) : ?>
	function gaOptOut () {
		if ( document.cookie.indexOf( 'ga-disable-<?php echo $tracking_id ?>=true' ) > -1 ) {
			window['ga-disable-<?php echo $tracking_id ?>'] = true;
			alert('Google Analytics wurde bereits erfolgreich für diese Seite deaktiviert.');
		}
		else {
			document.cookie = "ga-disable-<?php echo $tracking_id ?>=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/";
			window['ga-disable-<?php echo $tracking_id ?>'] = true;
			alert('Google Analytics wurde erfolgreich für diese Seite deaktiviert.');
		}
	}
	if ( document.cookie.indexOf( 'ga-disable-<?php echo $tracking_id ?>=true' ) > -1 ) {
		window['ga-disable-<?php echo $tracking_id ?>'] = true;
	}
<?php endif; ?>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js', 'ga');
	ga( 'create', '<?php echo $tracking_id ?>', 'auto' );
<?php if ( $anonymizeip ) : ?>
	ga( 'set', 'anonymizeIp', true );
<?php endif; ?>
<?php if ( $linker ) : ?>
	ga( 'require', 'linker' );
	ga( 'linker:autoLink', <?php echo $autolink ?>, false, true );
<?php endif; ?>
	ga( 'send', 'pageview' );

/* ]]> */
</script>