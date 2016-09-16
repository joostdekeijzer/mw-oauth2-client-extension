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

foreach(array('Client', 'DataStore', 'Exception', 'HttpClient', 'Service', 'Token') as $class) {
	$wgAutoloadClasses[sprintf('%s\%s', 'OAuth2', $class)] = sprintf('%s/%s/%s.php', OAuth2ClientHooks::getOAuth2VendorClassPath(), 'OAuth2', $class);
}
$wgAutoloadClasses[sprintf('%s\%s\%s', 'OAuth2', 'DataStore', 'Session')] = sprintf('%s/%s/%s/%s.php', OAuth2ClientHooks::getOAuth2VendorClassPath(), 'OAuth2', 'DataStore', 'Session');
$wgAutoloadClasses[sprintf('%s\%s\%s', 'OAuth2', 'Service', 'Configuration')] = sprintf('%s/%s/%s/%s.php', OAuth2ClientHooks::getOAuth2VendorClassPath(), 'OAuth2', 'Service', 'Configuration');

spl_autoload_register(function ($class) {
	if( substr( $class, 0, 7 ) == 'OAuth2\\' ) {
		include OAuth2ClientHooks::getOAuth2VendorClassPath() . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
	}
});

$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'OAuth2 Client',
	'version' => '0.2',
	'author' => array( 'Joost de Keijzer', '[http://dekeijzer.org]' ), 
	'url' => 'http://dekeijzer.org',
	'descriptionmsg' => 'oauth2client-act-as-a-client-to-any-oauth2-server'
);

// Create a twiter group
$wgGroupPermissions['oauth2'] = $wgGroupPermissions['user'];

$wgAutoloadClasses['SpecialOAuth2Client'] = dirname(__FILE__) . '/SpecialOAuth2Client.php';

$wgExtensionMessagesFiles['OAuth2Client'] = dirname(__FILE__) .'/OAuth2Client.i18n.php';
$wgExtensionMessagesFiles['OAuth2ClientAlias'] = dirname(__FILE__) .'/OAuth2Client.alias.php';

$wgSpecialPages['OAuth2Client'] = 'SpecialOAuth2Client';
$wgSpecialPageGroups['OAuth2Client'] = 'login';

$wgHooks['PersonalUrls'][] = 'OAuth2ClientHooks::onPersonalUrls';
$wgHooks['UserLogout'][] = 'OAuth2ClientHooks::onUserLogout';
$wgHooks['GetPreferences'][] = 'OAuth2ClientHooks::RedirectPrefs';

class OAuth2ClientHooks {
	public static function onPersonalUrls( array &$personal_urls, Title $title ) {
		if( !SpecialOAuth2Client::OAuthEnabled() ) return true;

		global $wgOAuth2Client, $wgUser, $wgRequest;
		if( $wgUser->isLoggedIn() ) return true;


		# Due to bug 32276, if a user does not have read permissions, 
		# $this->getTitle() will just give Special:Badtitle, which is 
		# not especially useful as a returnto parameter. Use the title 
		# from the request instead, if there was one.
		# see SkinTemplate->buildPersonalUrls()
		$page = Title::newFromURL( $wgRequest->getVal( 'title', '' ) );

		$sevice_name = isset( $wgOAuth2Client['configuration']['sevice_name'] ) && 0 < strlen( $wgOAuth2Client['configuration']['sevice_name'] ) ? $wgOAuth2Client['configuration']['sevice_name'] : 'OAuth2';
		if( isset( $wgOAuth2Client['configuration']['sevice_login_link_text'] ) && 0 < strlen( $wgOAuth2Client['configuration']['sevice_login_link_text'] ) ) {
			$sevice_login_link_text = $wgOAuth2Client['configuration']['sevice_login_link_text'];
		} else {
			$sevice_login_link_text = wfMsg('oauth2client-header-link-text', $sevice_name);
		}

		$inExt = ( null == $page || ('OAuth2Client' == substr( $page->mUrlform, 0, 12) ) );
		$personal_urls['anon_oauth_login'] = array(
			'text' => $sevice_login_link_text,
			//'class' => ,
			'active' => false,
		);
		if( $inExt ) {
			$personal_urls['anon_oauth_login']['href'] = Skin::makeSpecialUrlSubpage( 'OAuth2Client', 'redirect' );
		} else {
			# Due to bug 32276, if a user does not have read permissions, 
			# $this->getTitle() will just give Special:Badtitle, which is 
			# not especially useful as a returnto parameter. Use the title 
			# from the request instead, if there was one.
			# see SkinTemplate->buildPersonalUrls()
			$personal_urls['anon_oauth_login']['href'] = Skin::makeSpecialUrlSubpage(
				'OAuth2Client',
				'redirect',
				wfArrayToCGI( array( 'returnto' => $page ) )
			);
		}

		if( isset( $personal_urls['anonlogin'] ) ) {
			if( $inExt ) {
				$personal_urls['anonlogin']['href'] = Skin::makeSpecialUrl( 'Userlogin' );
			}
		}
		return true;
	}
	public static function onUserLogout( &$user ) {
		if( !SpecialOAuth2Client::OAuthEnabled() ) return true;

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

	// Remove reset password link and remember password checkbox from preferences page
	public static function RedirectPrefs($user, &$preferences) {
		unset($preferences["password"]);
		unset($preferences["rememberpassword"]);
		return true;
	}

	public static function getOAuth2VendorClassPath() {
		return dirname(__FILE__) . DIRECTORY_SEPARATOR . 'vendors' . DIRECTORY_SEPARATOR . 'oauth_2';
	}
}
