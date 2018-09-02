<?php

namespace IPS\discord\Api;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

class _Client extends \IPS\Patterns\Singleton
{
    /* API URLs */
    const API_URL = 'https://discordapp.com/api/v6';
    const OAUTH2_URL = 'https://discordapp.com/api/oauth2/';

    const USER_AGENT = 'DiscordBot (ahmadel, v2)';
    const DEFAULT_CONTENT_TYPE = 'application/json';

    /* Scopes according to https://discordapp.com/developers/docs/topics/oauth2#scopes */
    const SCOPE_BOT = 'bot';
    const SCOPE_CONNECTIONS = 'connections';
    const SCOPE_EMAIL = 'email';
    const SCOPE_IDENTIFY = 'identify';
    const SCOPE_GUILDS = 'guilds';
    const SCOPE_GUILDS_JOIN = 'guilds.join';
    const SCOPE_GDM_JOIN = 'gdm.join';
    const SCOPE_MESSAGES_READ = 'messages.read';
    const SCOPE_RPC = 'rpc';
    const SCOPE_RPC_API = 'rpc.api';
    const SCOPE_WEBHOOK_INCOMING = 'webhook.incoming';

    /* Permissions according to https://discordapp.com/developers/docs/topics/permissions#permissions */
    const PERM_CREATE_INSTANT_INVITE = 0x00000001;
    const PERM_KICK_MEMBERS = 0x00000002;
    const PERM_BAN_MEMBERS = 0x00000004;
    const PERM_ADMINISTRATOR = 0x00000008;
    const PERM_MANAGE_CHANNELS = 0x00000010;
    const PERM_MANAGE_GUILD = 0x00000020;
    const PERM_ADD_REACTIONS = 0x00000040;
    const PERM_READ_MESSAGES = 0x00000400;
    const PERM_SEND_MESSAGES = 0x00000800;
    const PERM_SEND_TTS_MESSAGES = 0x00001000;
    const PERM_MANAGE_MESSAGES = 0x00002000;
    const PERM_EMBED_LINKS = 0x00004000;
    const PERM_ATTACH_FILES = 0x00008000;
    const PERM_READ_MESSAGE_HISTORY = 0x00010000;
    const PERM_MENTION_EVERYONE = 0x00020000;
    const PERM_USE_EXTERNAL_EMOJIS = 0x00040000;
    const PERM_CONNECT = 0x00100000;
    const PERM_SPEAK = 0x00200000;
    const PERM_MUTE_MEMBERS = 0x00400000;
    const PERM_DEAFEN_MEMBERS = 0x00800000;
    const PERM_MOVE_MEMBERS = 0x01000000;
    const PERM_USE_VAD = 0x02000000;
    const PERM_CHANGE_NICKNAME = 0x04000000;
    const PERM_MANAGE_NICKNAMES = 0x08000000;
    const PERM_MANAGE_ROLES = 0x10000000;
    const PERM_MANAGE_WEBHOOKS = 0x20000000;
    const PERM_MANAGE_EMOJIS = 0x40000000;

    const AUTH_TYPE_OAUTH = 'Bearer';
    const AUTH_TYPE_BOT = 'Bot';

    /**
     * @brief	Singleton Instances
     */
    protected static $instance;

    /** @var \IPS\Data\Store */
    protected $store;

    public function __construct()
    {
        $this->store = \IPS\Data\Store::i();
    }

    public function get(string $uri, array $params = [], $authType = self::AUTH_TYPE_BOT, string $token = null)
    {
        $this->delayIfNecessary();

        $response = $this->buildRequest($uri, $params, $authType, $token)->get();

        if ((int) $response->httpResponseCode === 429) {
            usleep($response->decodeJson()['retry_after'] * 1000);
            return $this->get($uri, $params, $authType, $token);
        }

        $this->saveRateLimitAttributes($response);

        return $response;
    }

    public function post(
        string $uri,
        array $body = [],
        array $queryParameters = [],
        $authType = self::AUTH_TYPE_BOT,
        string $token = null
    ) {
        $this->delayIfNecessary();

        $response = $this->buildRequest($uri, $queryParameters, $authType, $token)->post(json_encode($body));

        if ((int) $response->httpResponseCode === 429) {
            usleep($response->decodeJson()['retry_after'] * 1000);
            return $this->post($uri, $body, $queryParameters, $authType, $token);
        }

        $this->saveRateLimitAttributes($response);

        return $response;
    }

    public function patch(
        string $uri,
        array $body = [],
        array $queryParameters = [],
        $authType = self::AUTH_TYPE_BOT,
        string $token = null
    ): \IPS\Http\Response {
        $this->delayIfNecessary();

        $response = $this->buildRequest($uri, $queryParameters, $authType, $token)->patch(json_encode($body));

        if ((int) $response->httpResponseCode === 429) {
            usleep($response->decodeJson()['retry_after'] * 1000);
            return $this->patch($uri, $body, $queryParameters, $authType, $token);
        }

        $this->saveRateLimitAttributes($response);

        return $response;
    }

    protected function delayIfNecessary()
    {
        try {
            $lastRequestTime  = $this->store->discord_last_request_time;
            $requestAllowance = $this->store->discord_request_allowance;
        } catch (\OutOfRangeException $e) {
            $lastRequestTime  = NULL;
            $requestAllowance = NULL;
        }

        $requestTime = microtime(true);

        $delay = max(0, $requestAllowance - ($requestTime - $lastRequestTime));

        if ($delay === 0) {
            return;
        }

        $floor = floor($delay);
        $micro = max(0, floor(($delay - $floor) * 1000000));
        sleep($floor);
        usleep($micro);
    }

    protected function saveRateLimitAttributes(\IPS\Http\Response $response)
    {
        if (!isset($response->httpHeaders['x-ratelimit-remaining'], $response->httpHeaders['x-ratelimit-reset'])) {
            unset($this->store->discord_request_allowance);
            return;
        }

        $requests = (int) $response->httpHeaders['x-ratelimit-remaining'];
        $seconds = (int) $response->httpHeaders['x-ratelimit-reset'] - time();

        $this->store->discord_request_allowance = $seconds / $requests;
        $this->store->discord_last_request_time = microtime(true);
    }

    /**
     * @param string      $uri
     * @param array       $params
     * @param string      $authType
     * @param string|null $token
     *
     * @return \IPS\Http\Request\Curl|\IPS\Http\Request\Sockets
     */
    protected function buildRequest(string $uri, array $params = [], $authType = self::AUTH_TYPE_BOT, string $token = null)
    {
        // TODO: Make it available in Login Handler settings
        $token = $token ?? \IPS\Settings::i()->discord_bot_token;

        return \IPS\Http\Url::external( self::API_URL . "/{$uri}" )
            ->setQueryString( $params )
            ->request()
            ->setHeaders([
                'Authorization' => "{$authType} {$token}",
                'User-Agent' => self::USER_AGENT,
                'Content-Type' => self::DEFAULT_CONTENT_TYPE
            ]);
    }
}
