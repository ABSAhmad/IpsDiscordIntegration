<?php

namespace IPS\discord\LoginHandler;

class _Discord extends \IPS\Login\Handler\OAuth2
{
    const API_URL = 'https://discordapp.com/api/v6';

    public static function getTitle()
    {
        return 'discord_sign_in';
    }

    /**
     * Get the button color
     *
     * @return    string
     */
    public function buttonColor(): string
    {
        return '#2C2F33';
    }

    /**
     * Get the button icon
     *
     * @return    string
     */
    public function buttonIcon(): string
    {
        return 'lock';
    }

    /**
     * Get button text
     *
     * @return    string
     */
    public function buttonText(): string
    {
        return 'discord_sign_in';
    }

    /**
     * Grant Type
     *
     * @return    string
     */
    protected function grantType(): string
    {
        return 'authorization_code';
    }

    /**
     * Authorization Endpoint
     *
     * @param    \IPS\Login $login The login object
     * @return    \IPS\Http\Url
     */
    protected function authorizationEndpoint(\IPS\Login $login)
    {
        return \IPS\Http\Url::external(self::API_URL . '/oauth2/authorize')->setQueryString('type', 'web_server');
    }

    /**
     * Token Endpoint
     *
     * @return    \IPS\Http\Url
     */
    protected function tokenEndpoint()
    {
        return \IPS\Http\Url::external(self::API_URL . '/oauth2/token');
    }

    /**
     * Get authenticated user's identifier (may not be a number)
     *
     * @param    string $accessToken Access Token
     * @return    string
     * @throws \IPS\Login\Exception
     */
    protected function authenticatedUserId($accessToken)
    {
        $response = \IPS\Http\Url::external( "https://discordapp.com/api/v6/users/@me")
            ->request()
            ->setHeaders( array(
                'Authorization' => "Bearer {$accessToken}"
            ) )
            ->get()
            ->decodeJson();

        if ( isset( $response['errorCode'] ) )
        {
            throw new \IPS\Login\Exception( $response['message'], \IPS\Login\Exception::INTERNAL_ERROR );
        }

        return $response['id'];
    }

    /**
     * Get scopes to request
     *
     * @param	array|NULL	$additional	Any additional scopes to request
     *
     * @return	array
     */
    protected function scopesToRequest($additional = null)
    {
        return ['email', 'identify'];
    }
}
