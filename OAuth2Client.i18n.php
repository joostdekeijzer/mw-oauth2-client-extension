<?php

/**
 * Internationalization file for the OAuth2Client extension.
 *
 * @file OAuth2Client.i18n.php
 */

$messages = array();

/** English
 * @author Joost de Keijzer
 */
$messages['en'] = array(
	'oauth2client' => 'OAuth2 Client',

	'oauth2client-act-as-a-client-to-any-oauth2-server' => 'Act as a client to any OAuth2 server',
	'oauth2client-couldnotconnect' => 'Could not connect to OAuth2 Server',
	'oauth2client-header-link-text' => '$1 login',

	'oauth2client-login-header' => '$1 login',
	'oauth2client-you-can-login-to-this-wiki-with-oauth2' => 'You can login this wiki with your $1 account',
	'oauth2client-login-with-oauth2' => '[[$1|Login with $2]]',
	'oauth2client-youre-already-loggedin' => "You're already logged in.",

	'oauth2client-logout-header' => '$1 logged out',
	'oauth2client-logged-out' => 'You have been logged out of this wiki.',
	'oauth2client-login-with-oauth2-again' => '[[$1|You can login again with your $2 account]]',
);

$messages['nl'] = array(
	'oauth2client' => 'OAuth2 Client',

	'oauth2client-act-as-a-client-to-any-oauth2-server' => 'Act as a client to any OAuth2 server',
	'oauth2client-couldnotconnect' => 'Kon geen verbinding maken met de OAuth2 server',
	'oauth2client-header-link-text' => 'Aanmelden via $1',

	'oauth2client-login-header' => 'Aanmelden via $1',
	'oauth2client-you-can-login-to-this-wiki-with-oauth2' => 'U kunt zich aanmelden op deze wiki via uw $1 account',
	'oauth2client-login-with-oauth2' => '[[$1|Aanmelden via $2]]',
	'oauth2client-youre-already-loggedin' => "U bent al ingelogd.",

	'oauth2client-logout-header' => '$1 uitgelogd',
	'oauth2client-logged-out' => 'U bent succesvol uitgelogd.',
	'oauth2client-login-with-oauth2-again' => '[[$1|U kunt u weer aanmelden via uw $2 account]]',
);

/**
 * Message Documentation, incomplete
 */
$messages['qqq'] = array(
	'oauth2client' => 'Link of the special page',
	'oauth2client-login-header' => 'OAuth2 Login',
	'oauth2client-you-can-register-to-this-wiki-using-oauth2' => 'Explain to user that OAuth2 link is available',
	'oauth2client-login-with-oauth2' => 'link text',
	'oauth2client-youre-already-loggedin' => 'Message displayed on the default specialpage. tietoaccount replacement.',
	'oauth2client-act-as-a-client-to-any-oauth2-server' => 'Description of the extension, see setup file',
	'oauth2client-couldnotconnect' => 'Message for user when OAuth2 server is unavailable',
);
