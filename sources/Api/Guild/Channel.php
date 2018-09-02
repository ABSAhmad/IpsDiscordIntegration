<?php

namespace IPS\discord\Api\Guild;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

class _Channel extends \IPS\Patterns\Singleton
{
    /**
     * @brief	Singleton Instances
     */
    protected static $instance;

    /** @var \IPS\discord\Api\Client */
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = \IPS\discord\Api\Client::i();
    }

    public function all(string $guildId)
    {
        $response = $this->httpClient->get("/guilds/{$guildId}/channels");

        return $response->decodeJson();
    }
}
