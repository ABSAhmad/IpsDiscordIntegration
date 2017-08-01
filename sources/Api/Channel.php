<?php

namespace IPS\discord\Api;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Class _Channel
 *
 * @package IPS\discord\Api
 *
 * Handles any requests that are related to a discord channel.
 */
class _Channel extends \IPS\discord\Api
{
    const TYPE_TEXT = 0;
    const TYPE_VOICE = 2;

    /**
     * Sends a message to the given channel.
     *
     * @param string $message  The message to be sent.
     * @param int $channelId
     * @param array $options
     *
     * @return \RestCord\Model\Channel\Message
     */
    public function message($message, $channelId, array $options = [])
    {
        $options = array_merge([
            'channel.id' => (int) $channelId,
            'content' => $message
        ], $options);

        return $this->discord->channel->createMessage($options);
    }

    /**
     * @return string
     */
    protected function getResourceName()
    {
        return 'channel';
    }
}
