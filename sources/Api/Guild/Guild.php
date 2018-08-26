<?php

namespace IPS\discord\Api;

class _Guild extends \IPS\Patterns\Singleton
{
    use ResponseTransformer;

    /** @var \IPS\discord\Api\Client */
    protected $httpClient;

    public function __construct()
    {
        $this->httpClient = \IPS\discord\Api\Client::i();
    }

    public function roles(\IPS\discord\Api\Request $request): array
    {
        $guildId = $request->getQueryParameter('guild_id');

        return $this->transformResponse(
            $this->httpClient->get("/guilds/{$guildId}/roles")
        );
    }
}
