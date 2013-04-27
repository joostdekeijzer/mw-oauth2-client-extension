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
	'name' => 'OAuth2 Client',
	'version' => '0.01',
	'author' => array( 'Joost de Keijzer', '[http://dekeijzer.org]' ), 
	'url' => 'http://dekeijzer.org',
	'descriptionmsg' => 'oauth2client-act-as-a-client-to-any-oauth2-server'
);

// Create a twiter group
$wgGroupPermissions['oauth2'] = $wgGroupPermissions['user'];

$wgAutoloadClasses['SpecialOAuth2Client'] = dirname(__FILE__) . '/SpecialOAuth2Client.php';

$wgExtensionMessagesFiles['OAuth2Client'] = dirname(__FILE__) .'/OAuth2Client.i18n.php';

$wgSpecialPages['OAuth2Client'] = 'SpecialOAuth2Client';
$wgSpecialPageGroups['OAuth2Client'] = 'login';

//$wgHooks['LoadExtensionSchemaUpdates'][] = 'efSetupSpecialOAuth2ClientSchema';

$OAuth2LoginButton = new OAuth2LoginButton;
$wgHooks['BeforePageDisplay'][] = array( $OAuth2LoginButton, 'efAddSigninButton' );

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

class OAuth2LoginButton {
	/**
	 * Add a sign in with Twitter button but only when a user is not logged in
	 */
	public function efAddSigninButton( &$out, &$skin ) {
		global $wgUser, $wgExtensionAssetsPath, $wgScriptPath;
	
		if ( !$wgUser->isLoggedIn() ) {
			$link = SpecialPage::getTitleFor( 'OAuth2Client', 'redirect' )->getLinkUrl(); 
			$out->addInlineScript('$j(document).ready(function(){
				$j("#pt-anonlogin, #pt-login").after(\'<li id="pt-twittersignin">'
				.'<a href="' . $link  . '">'
				.wfMsg( 'oauth2client-login-with-oauth2' ).'</a></li>\');
			})');
		}
		return true;
	}
}
