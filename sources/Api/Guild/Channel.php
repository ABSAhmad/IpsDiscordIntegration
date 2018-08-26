<?php

namespace IPS\discord\Api\Guild;

use IPS\discord\Api\ResponseTransformer;

class _Channel extends \IPS\Patterns\Singleton
{
    use ResponseTransformer;

    /** @var \IPS\discord\Api\Client */
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = \IPS\discord\Api\Client::i();
    }

    public function all(\IPS\discord\Api\Request $request)
    {
        $guildId = $request->getQueryParameter('guild_id');

        $response = $this->httpClient->get("/guilds/{$guildId}/channels");

        return $this->transformResponse($response);
    }
}
