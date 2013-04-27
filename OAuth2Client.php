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

class MwOAuth2Client {
	protected $settings = array();

	public function __construct( array $settings ) {
		//session_start();
		$this->settings = array_merge_recursive( array(
			'client' => array( 'callback_url' => SpecialPage::getTitleFor( 'OAuth2Client', 'callback' )->getFullURL() ),
		), $settings );
	}

	public function authorizationRedirect() {
		$state = MWCryptRand::generateHex(32);
		$_SESSION['OAuth2Client'] = array(
			'state' => $state,
		);

		$parameters = array(
			'type' => 'web_server',
			'client_id' => $this->settings['client']['id'],
			'redirect_uri' => $this->settings['client']['callback_url'],
			'response_type' => 'code',
			'state' => $state,
		);
		if( isset($this->settings['client']['scope']) ) {
			$parameters['scope'] = $this->settings['client']['scope'];
		}

		$url = $this->settings['configuration']['authorize_endpoint'];
		$url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($parameters);

		header('Location: ' . $url);
		die();
	}

	public function getAccessToken() {
		if (! isset($_GET['code'])) {
			throw new MWException('could not retrieve code out of callback request and no code given');
		}
		$code = $_GET['code'];

		$state = $_SESSION['OAuth2Client']['state'];
		// TODO: force_state configuration?
		if(isset($_GET['state'])) {
			$_SESSION['OAuth2Client']['state'] = ''; // clear state
			if($state !== $_GET['state']) {
				throw new MWException('state of callback request does not match original state');
			}
		}

		$parameters = array(
			'grant_type' => 'authorization_code',
			'type' => 'web_server',
			'client_id' => $this->settings['client']['id'],
			'client_secret' => $this->settings['client']['secret'],
			'redirect_uri' => $this->settings['client']['callback_url'],
			'code' => $code,
		);
		if( isset($this->settings['client']['scope']) ) {
			$parameters['scope'] = $this->settings['client']['scope'];
		}

		$options = array(
			'method' => 'POST',
			'postData' => $parameters,
		);
		$result = $this->serverRequest( $this->settings['configuration']['access_token_endpoint'], $options );
		if( false != $result && isset( $result['access_token'] ) ) {
			$_SESSION['OAuth2Client'] = $result;
			$_SESSION['OAuth2Client']['timestamp'] = time();
			return true;
		} else {
			return false;
		}
	}

	public function getApiContent() {
	}

	protected function serverRequest( $url, $parameters, $authorization = array() ) {
		$req = MWHttpRequest::factory( $url, $parameters );
		$status = $req->execute();

		if ( $status->isOK() ) {
			$rHeaders = $req->getResponseHeaders();
			if( isset( $rHeaders['content-type'] ) && in_array( 'application/json', $rHeaders['content-type'] ) ) {
				return json_decode( $req->getContent() );
			} else {
				return $req->getContent();
			}
		} else {
			return false;
		}
	}
}