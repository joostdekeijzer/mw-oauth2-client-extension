mw-oauth2-client-extension
==========================

MediaWiki OAuth2 Client Extension

MediaWiki implementation of the OAuth2 library.

Required settings in global $wgOAuth2Client

    $wgOAuth2Client['client']['id'] = '';     // Your App Id or Client Id received by OAuth2 Server Administrator
    $wgOAuth2Client['client']['secret'] = ''; // Secret received by OAuth2 Server Administrator
    
    $wgOAuth2Client['configuration']['authorize_endpoint'] = '';    // full url's
    $wgOAuth2Client['configuration']['access_token_endpoint'] = '';
    wgOAuth2Client['configuration']['api_endpoint'] = '';

    $wgOAuth2Client['configuration']['http_bearer_token'];     // Token to use in HTTP Authentication (default 'OAuth')
    $wgOAuth2Client['configuration']['query_parameter_token']; // query parameter to use (default 'oauth_token')
