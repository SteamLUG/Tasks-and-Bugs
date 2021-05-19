<?php
include_once( 'functions_db.php' );

if ( !isset( $database ) )
	$database = connectDB( );

/*
	This should control the request/display of member details in our db
*/

	/*
	* Small helper function to make our DB output roughly match Steam’s json
	*/
	function inflatePlayerSummary( $profile ) {

		if ($profile[ 'profileurl' ] != '' ) {
			$profile[ 'memberurl' ] = '/member/' . $profile[ 'profileurl' ];
			$profile[ 'profileurl' ] = 'https://steamcommunity.com/id/' . $profile[ 'profileurl' ] . '/';
		} else {
			$profile[ 'memberurl' ] = '/member/' . $profile[ 'steamid' ];
			$profile[ 'profileurl' ] = 'https://steamcommunity.com/profiles/' . $profile[ 'steamid' ] . '/';
		}
		$profile[ 'avatarfull' ] = '//steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/' . str_replace( '.jpg', '_full.jpg', $profile[ 'avatar' ]);
		$profile[ 'avatarmedium' ] = '//steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/' . str_replace( '.jpg', '_medium.jpg', $profile[ 'avatar' ]);
		$profile[ 'avatar' ] = '//steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/' . $profile[ 'avatar' ];
		// TODO populate other fields here
		// XXX if no personaname, convert to their steamid?
		$profile[ 'personaname' ] = htmlspecialchars( $profile[ 'personaname' ] );
		return $profile;
	}

	/*
	* Small helper function to make our DB data not waste bytes. sorry it is my OCD :s
	*/
	function deflatePlayerSummary( $profile ) {

		$profile[ 'profileurl' ] = rtrim( $profile[ 'profileurl' ], '/' );
		$profile[ 'profileurl' ] = str_replace( 'http://steamcommunity.com/id/', '', $profile[ 'profileurl' ] );
		if ( ( str_replace( 'http://steamcommunity.com/profiles/', '', $profile[ 'profileurl' ] ) ) == $profile[ 'steamid' ] ) {
			$profile[ 'profileurl' ] = '';
		}
		$profile[ 'avatar' ] = str_replace( 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/', '', $profile[ 'avatar' ] );
		return $profile;
	}

	function getPlayerSummaryDB( $id ) {

		global $database;
		try {
			$database->beginTransaction( );
			/* TODO: safe-ify $id */
			$statement = $database->prepare( "SELECT * FROM members WHERE members.steamid = :steamid LIMIT 1;" );
			$statement->execute( array( 'steamid' => $id ) );
			$user = $statement->fetch( PDO::FETCH_ASSOC );
			$database->commit( );
			if ( $user !== false )
				$user = inflatePlayerSummary( $user );
			return $user;
		} catch ( Exception $e ) {

			return false;
		}
	}

	function getPlayerClansDB( $id ) {

		global $database;
		try {
			// $database->beginTransaction( );
			/* TODO: safe-ify $id */
			$statement = $database->prepare( "SELECT clans.clanid, memberclans.steamid, clanroles.name AS clanrole, clans.name, clans.creator, clans.description, clans.slug
				FROM memberclans LEFT JOIN clans ON clans.clanid = memberclans.clanid LEFT JOIN clanroles ON memberclans.role = clanroles.roleid where steamid = :steamid;" );
			$statement->execute( array( 'steamid' => $id ) );
			$clans = $statement->fetchAll( PDO::FETCH_ASSOC );
			// $database->commit( );
			return $clans;
		} catch ( Exception $e ) {

			return false;
		}
	}

	function removePlayerSummaryDB( $id ) {

		global $database;
		try {
			$database->beginTransaction( );
			/* TODO: safe-ify $id */
			$statement = $database->prepare( "DELETE FROM members WHERE members.steamid = :steamid LIMIT 1;" );
			$statement->execute( array( 'steamid' => $id ) );
			$user = $statement->execute( );
			$database->commit( );
			return $user;
		} catch ( Exception $e ) {

			return false;
		}
	}

	function findPlayerSummaryDB( $vanity ) {

		global $database;
		try {
			$database->beginTransaction( );
			/* TODO: safe-ify $id */
			$statement = $database->prepare( "SELECT * FROM members WHERE members.profileurl = :vanity LIMIT 1;" );
			$statement->execute( array( 'vanity' => $vanity ) );
			$user = $statement->fetch( PDO::FETCH_ASSOC );
			$database->commit( );
			if ( $user !== false )
				$user = inflatePlayerSummary( $user );
			return $user;
		} catch ( Exception $e ) {

			return false;
		}
	}

	/*
	* Note to self, the assoc array coming in here needs to have set isgroupmember
	*/
	function storePlayerSummaryDB( $profile ) {

		global $database;
		try {
			$database->beginTransaction( );
			$profile = deflatePlayerSummary( $profile );
			/* TODO: safe-ify _everything_ */
			$statement = $database->prepare( "INSERT INTO members
				(steamid, personaname, profileurl, avatar, isgroupmember,
				suggestedvisibility) VALUES (:steamid, :persona, :vanity, :avatar,
				:group, :privacy) ON DUPLICATE KEY UPDATE personaname=VALUES(personaname),
				profileurl=VALUES(profileurl), avatar=VALUES(avatar),
				isgroupmember=VALUES(isgroupmember),
				suggestedvisibility=VALUES(suggestedvisibility);" );
			$statement->execute( array( 'steamid' => $profile[ 'steamid' ],
				'persona' => $profile[ 'personaname' ],
				'vanity' => $profile[ 'profileurl' ],
				'avatar' => $profile[ 'avatar' ],
				'group' => $profile[ 'isgroupmember' ],
				'privacy' => $profile[ 'communityvisibilitystate' ] ) );
			$user = $statement->fetch( PDO::FETCH_ASSOC );
			$database->commit( );
			return $user;
		} catch ( Exception $e ) {

			return false;
		}
	}
