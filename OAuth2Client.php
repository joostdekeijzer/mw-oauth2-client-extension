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
	'version' => '0.1',
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

$wgHooks['PersonalUrls'][] = 'OAuth2ClientHooks::onPersonalUrls';
$wgHooks['UserLogout'][] = 'OAuth2ClientHooks::onUserLogout';
//$wgHooks['LoadExtensionSchemaUpdates'][] = 'efSetupSpecialOAuth2ClientSchema';

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

class OAuth2ClientHooks {
	public static function onPersonalUrls( array &$personal_urls, Title $title ) {
		global $wgUser;
		if( $wgUser->isLoggedIn() ) return true;

		$inExt = ('OAuth2Client' == substr( $title->mUrlform, 0, 12) );
		$personal_urls['anon_oauth_login'] = array(
			'text' => wfMsg('oauth2client-login-with-oauth2'),
			//'class' => ,
			'active' => false,
		);
		if( $inExt ) {
			$personal_urls['anon_oauth_login']['href'] = Skin::makeSpecialUrlSubpage( 'OAuth2Client', 'redirect' );
		} else {
			$personal_urls['anon_oauth_login']['href'] = Skin::makeSpecialUrlSubpage( 'OAuth2Client', 'redirect', wfArrayToCGI( array( 'returnto' => $title ) ) );
		}

		if( isset( $personal_urls['anonlogin'] ) ) {
			if( $inExt ) {
				$personal_urls['anonlogin']['href'] = Skin::makeSpecialUrl( 'Userlogin' );
			}
			$item = $personal_urls['anonlogin'];
			unset( $personal_urls['anonlogin'] );
			$personal_urls['anonlogin'] = $item;
		}
		return true;
	}
	public static function onUserLogout( &$user ) {
		global $wgOut;
		$dbr = wfGetDB( DB_SLAVE );
		$row = $dbr->selectRow(
			'external_user',
			'*',
			array( 'eu_local_id' => $user->getId() )
		);
		if( $row ) {
			$wgOut->redirect( SpecialPage::getTitleFor( 'OAuth2Client', 'logout' )->getFullURL() );
		}
		return true;
	}
}
