<?php
/**
 * OAuth2Client.php
 * Based on TwitterLogin by David Raison, which is based on the guideline published by Dave Challis at http://blogs.ecs.soton.ac.uk/webteam/2010/04/13/254/
 * @license: LGPL (GNU Lesser General Public License) http://www.gnu.org/licenses/lgpl.html
 *
 * @file OAuth2Client.php
 * @ingroup OAuth2Client
 *
 * @author Joost de Keijzer
 *
 * Uses the OAuth2 library https://github.com/vznet/oauth_2.0_client_php
 *
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This is a MediaWiki extension, and must be run from within MediaWiki.' );
}

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'OAuth2Client',
	'version' => '0.01',
	'author' => array( 'Joost de Keijzer', '[http://dekeijzer.org]' ), 
	'url' => 'http://dekeijzer.org',
	'descriptionmsg' => 'oauth2client-desc'
);

// Create a twiter group
$wgGroupPermissions['oauth2'] = $wgGroupPermissions['user'];

$wgAutoloadClasses['SpecialOAuth2Client'] = dirname(__FILE__) . '/SpecialOAuth2Client.php';
//$wgAutoloadClasses['TwitterOAuth'] = dirname(__FILE__) . '/twitteroauth/twitteroauth.php';
//$wgAutoloadClasses['MwTwitterOAuth'] = dirname(__FILE__) . '/TwitterLogin.twitteroauth.php';
//$wgAutoloadClasses['TwitterSigninUI'] = dirname(__FILE__) . '/TwitterLogin.body.php';

//$wgExtensionMessagesFiles['TwitterLogin'] = dirname(__FILE__) .'/TwitterLogin.i18n.php';
//$wgExtensionAliasFiles['TwitterLogin'] = dirname(__FILE__) .'/TwitterLogin.alias.php';

$wgSpecialPages['TwitterLogin'] = 'SpecialOAuth2Client';
$wgSpecialPageGroups['OAuth2Login'] = 'login';

//$wgHooks['LoadExtensionSchemaUpdates'][] = 'efSetupSpecialOAuth2ClientSchema';

$tsu = new TwitterSigninUI;
$wgHooks['BeforePageDisplay'][] = array( $tsu, 'efAddSigninButton' );

$stl = new SpecialTwitterLogin;
$wgHooks['UserLoadFromSession'][] = array($stl,'efTwitterAuth');
$wgHooks['UserLogoutComplete'][] = array($stl,'efTwitterLogout');

function efSetupSpecialOAuth2ClientSchema( $updater ) {
	return true;
	$updater->addExtensionUpdate( array( 'addTable', 'twitter_user',
		dirname(__FILE__) . '/schema/twitter_user.sql', true ) );
	$updater->addExtensionUpdate( array( 'modifyField', 'twitter_user','user_id',
		dirname(__FILE__) . '/schema/twitter_user.patch.user_id.sql', true ) );
	$updater->addExtensionUpdate( array( 'modifyField', 'twitter_user','twitter_id',
		dirname(__FILE__) . '/schema/twitter_user.patch.twitter_id.sql', true ) );
	return true;
}
