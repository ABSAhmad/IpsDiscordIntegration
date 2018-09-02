<?php

namespace IPS\discord\Api\Guild;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

class _Member extends \IPS\Patterns\Singleton
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

    public function get(string $guildId, string $userId)
    {
        $response = $this->httpClient->get("/guilds/{$guildId}/members/{$userId}");

        return $response->decodeJson();
    }

    public function update(string $guildId, string $userId, array $payload)
    {
        $response = $this->httpClient->patch("/guilds/{$guildId}/members/{$userId}", $payload);

        return $response->decodeJson();
    }
}
