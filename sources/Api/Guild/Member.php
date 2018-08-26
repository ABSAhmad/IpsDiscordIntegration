<?php

namespace IPS\discord\Api\Guild;

use IPS\discord\Api\ResponseTransformer;

class _Member
{
    use ResponseTransformer;

    /** @var \IPS\discord\Api\Client */
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = \IPS\discord\Api\Client::i();
    }

    public function update(\IPS\discord\Api\Request $request)
    {
        $guildId = $request->getQueryParameter('guild_id');
        $userId = $request->getQueryParameter('user_id');

        $response = $this->httpClient->patch("/guilds/{$guildId}/members/{$userId}", $request->getPayload());

        return $this->transformResponse($response);
    }
}
