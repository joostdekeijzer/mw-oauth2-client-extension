mw-oauth2-client-extension
==========================

MediaWiki OAuth2 Client Extension

MediaWiki implementation of the [OAuth2 Client library](https://github.com/vznet/oauth_2.0_client_php).

Required settings in global $wgOAuth2Client

    $wgOAuth2Client['client']['id']     = ''; // Your App Id or Client Id received by OAuth2 Server Administrator
    $wgOAuth2Client['client']['secret'] = ''; // Secret received by OAuth2 Server Administrator
    
    $wgOAuth2Client['configuration']['authorize_endpoint']    = '';            // full url's
    $wgOAuth2Client['configuration']['access_token_endpoint'] = '';
    wgOAuth2Client['configuration']['api_endpoint']           = '';

Optional settings in global $wgOAuth2Client

    $wgOAuth2Client['configuration']['http_bearer_token']     = 'OAuth';       // Token to use in HTTP Authentication
    $wgOAuth2Client['configuration']['query_parameter_token'] = 'oauth_token'; // query parameter to use

The callback url back to your wiki would be:
http://your.wiki.domain/path/to/wiki/Special:OAuth2Client/callback