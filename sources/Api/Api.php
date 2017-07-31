<?php

namespace IPS\discord;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

require_once( \IPS\ROOT_PATH . '/applications/discord/vendor/autoload.php' );

/**
 * Class _Api
 *
 * @package IPS\discord
 *
 * Provides general methods to setup the underlying RestCord library.
 */
abstract class _Api
{
    /**
     * @var \RestCord\DiscordClient $discord
     */
    protected $discord;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $options['token'] = isset( $options['token'] ) ? $options['token'] : \IPS\Settings::i()->discord_bot_token;

        $this->discord = new \RestCord\DiscordClient($options);
    }

    /**
     * Delegates the calls to the RestCord library (the resource must be specified in static::getResourceName).
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        $resource = $this->getResourceName();

        if (!method_exists($this->discord->$resource, $name)) {
            trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);

            return;
        }

        return call_user_func_array([$this->discord->$resource, $name], $arguments);
    }

    /**
     * The resource name (RestCord library @see \RestCord\Interfaces) that not found methods should be delegated to.
     *
     * @return string
     */
    abstract protected function getResourceName();
}
