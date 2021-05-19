<?php
	header("Content-Type: application/rss+xml; charset=utf-8");
	header("Access-Control-Allow-Origin: *");
	date_default_timezone_set('UTC');

	if (!isset($_GET['t'])|| $_GET['t'] == "ogg" ) {

		$type = "ogg";
		$mime = "audio/ogg";
	} else {

		$type = "mp3";
		$mime = "audio/mpeg";
	}
	include_once('../includes/functions_cast.php');

	function slenc($u) {

		return htmlspecialchars($u, ENT_NOQUOTES, "UTF-8");
	}

	/* gives us a list, like s02e03, s02e02, etc of all of our casts */
	$casts = getCasts( );
	/* naïve as fook, but we know this. */
	$latestCast = date("D, d M Y H:i:s O", filemtime( $castNotesRepo . '/' . $casts[0] ));

	// it is important that the atom:link self reference is truly referencial, do our best to provide that
	// note: this can leaky and/or broken on some server configs;
	$server = 'http://';
	if ( !empty($_SERVER['HTTPS']) && ($_SERVER["HTTPS"] == "on") ) {
        $server = 'https://';
    }
	$server .= $_SERVER['SERVER_NAME'];
	$server .= (((!empty($_SERVER['SERVER_PORT'])) and ($_SERVER['SERVER_PORT'] != '443')) ? ":" . $_SERVER['SERVER_PORT'] : '');

	/* for sake of reading/modification, use HEREDOC syntax */
	echo <<<CASTHEAD
<?xml version="1.0" encoding="UTF-8"?>
<rss xmlns:media="http://search.yahoo.com/mrss/" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:atom="http://www.w3.org/2005/Atom" version="2.0" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:cc="http://web.resource.org/cc/">
	<channel>
		<title>SteamLUG Cast</title>
		<atom:link href="{$server}/feed/cast/$type" rel="self" type="application/rss+xml" />
		<link>https://steamlug.org/cast</link>
		<description>SteamLUG Cast is a casual, fortnightly audiocast which aims to provide interesting news and discussion for the SteamLUG and broader Linux gaming communities.</description>
		<itunes:author>SteamLUG</itunes:author>
		<itunes:owner>
			<itunes:name>SteamLUG</itunes:name>
			<itunes:email>cast@steamlug.org</itunes:email>
		</itunes:owner>
		<language>en</language>
		<image>
			<url>http://steamlug.org/images/steamlugcast.png</url>
			<title>SteamLUG Cast</title>
			<link>https://steamlug.org/cast</link>
		</image>
		<itunes:image href="http://steamlug.org/images/steamlugcast.png" />
		<copyright>2013 – 2015 © SteamLUG cast, CC-BY-SA http://creativecommons.org/licenses/by-sa/3.0/</copyright>
		<cc:license rdf:resource="http://creativecommons.org/licenses/by-sa/3.0/" />
		<pubDate>$latestCast</pubDate>
		<itunes:category text="Games &amp; Hobbies">
			<itunes:category text="Video Games" />
		</itunes:category>
		<itunes:keywords>Linux, Steam, SteamLUG, Gaming, FOSS</itunes:keywords>
		<media:keywords>Linux, Steam, SteamLUG, Gaming, FOSS</media:keywords>
		<itunes:explicit>no</itunes:explicit><media:rating scheme="urn:simple">nonadult</media:rating>
CASTHEAD;

	foreach( $casts as $castdir ) {

		$shownotes			= getCastBody( $castdir );
		$meta				= getCastHeader( $castdir );

		if ( ( $meta == false ) or ( $shownotes == false ) )
			continue;

		/* if published unset, skip this entry */
		if ( $meta['PUBLISHED'] === '' )
			continue;

		/* if file missing, skip this entry */
		if ( !file_exists( $meta[ 'ABSFILENAME' ] . '.' . $type ) )
			continue;

		$meta['PUBLISHED']	= date( DATE_RFC2822, strtotime( $meta['PUBLISHED'] ) );
		$meta['TITLE']		= slenc( $meta['TITLE'] );
		$meta['SHORTDESC']	= slenc( substr( $meta['DESCRIPTION'],0,158 ) );
		$meta['DESCRIPTION']= slenc( $meta['DESCRIPTION'] );

		$episodeSize		= filesize( $meta[ 'ABSFILENAME' ] . '.' . $type );

		echo <<<CASTENTRY

		<item>
			<title>{$meta['SLUG']} – {$meta[ 'TITLE' ]}</title>
			<pubDate>{$meta['PUBLISHED']}</pubDate>
			<itunes:duration>{$meta['DURATION']}</itunes:duration>
			<link>https://steamlug.org/cast/{$meta['SLUG']}</link>
			<guid>https://steamlug.org/cast/{$meta['SLUG']}</guid>
			<enclosure url="http:{$meta['ARCHIVE']}.{$type}" length="{$episodeSize}" type="{$mime}" />
			<media:content url="http:{$meta['ARCHIVE']}.{$type}" fileSize="{$episodeSize}" type="{$mime}" medium="audio" expression="full" />
			<itunes:explicit>{$meta['ISEXPLICIT']}</itunes:explicit>
			<media:rating scheme="urn:simple">{$meta['MEDIARATING']}</media:rating>
			<description><![CDATA[<p>{$meta['DESCRIPTION']}</p>

CASTENTRY;

		echo _castBody( $shownotes, true );

		echo <<<CASTENTRY
			]]></description>
			<itunes:subtitle>{$meta['SHORTDESC']}…</itunes:subtitle>
		</item>
CASTENTRY;
	}
	echo <<<CASTFOOT
	</channel>
</rss>
CASTFOOT;
?>
