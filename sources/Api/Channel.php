<?php

namespace IPS\discord\Api;

/**
 * Class Channel
 *
 * @package IPS\discord\Api
 */
class _Channel extends \IPS\discord\Api\AbstractResponse
{
    /**
     * Post a notification about new content to discord.
     *
     * @param \IPS\Content $content
     * @param \IPS\Member $member
     * @return array|NULL
     */
    public function postContentItem( \IPS\Content $content, \IPS\Member $member = NULL )
    {
        $member = $member ?: $content->author();
        $channelId = $content->hidden() ? $content->container()->discord_channel_unapproved : $content->container()->discord_channel_approved;

        if ( !$channelId )
        {
            /* Ignore... */
            return NULL;
        }

        return $this->post(
            $this->createMessage( $member, $content ),
            $channelId
        );
    }

    /**
     * Send a message to a member.
     * Used for notifications and watched content.
     *
     * @param \IPS\Member $member
     * @param string $content
     * @return array|NULL
     */
    public function postToMember( \IPS\Member $member, $content)
    {
        $this->api->setUrl( \IPS\discord\Api::API_URL )
            ->setAuthType( \IPS\discord\Api::AUTH_TYPE_BOT )
            ->setUri( 'users/@me/channels' )
            ->setMethod(
                'post' )
            ->setParams(json_encode([
                'recipient_id' => $member->discord_id
            ]));

        $response = $this->handleApi();
        $channelId = $response['recipient_id'];

        return $this->post($content, $channelId);
    }

    /**
     * Post given message to the given channel.
     *
     * @param string $content
     * @param string $channelId
     * @return array|NULL
     */
    protected function post( $content, $channelId )
    {
        $this->api->setUrl( \IPS\discord\Api::API_URL )
            ->setAuthType( \IPS\discord\Api::AUTH_TYPE_BOT )
            ->setUri( "channels/{$channelId}/messages" )
            ->setMethod( 'post' )
            ->setParams(json_encode([
                'content' => $content
            ]));

        return $this->handleApi();
    }

    /**
     * Create message to be send.
     *
     * @param \IPS\Member $member
     * @param \IPS\Content $content
     * @return string
     */
    protected function createMessage( \IPS\Member $member, \IPS\Content $content )
    {
        $poster = static::getPoster( $member );

        $search = [
            '{poster}',
            '{title}',
            '{link}'
        ];

        $replace = [
            $poster,
            static::getContentTitle( $content ),
            (string) $content->url()
        ];

        $formatProperty = $content instanceof \IPS\forums\Topic ? 'discord_topic_format' : 'discord_post_format';
        $subject = $content->container()->$formatProperty;
        $message = str_replace( $search, $replace, $subject );

        return $message;
    }

    /**
     * Get appropriate title of the content item.
     *
     * @param \IPS\Content $content
     * @return string
     */
    protected static function getContentTitle( \IPS\Content $content )
    {
        if ( $content instanceof \IPS\forums\Topic\Post )
        {
            return $content->item()->title;
        }

        if ( $content instanceof \IPS\downloads\File )
        {
            return $content->name;
        }

        return $content->title;
    }

    /**
     * Tag the poster on discord.
     *
     * @param \IPS\Member $member
     * @return string
     */
    protected static function getPoster( \IPS\Member $member )
    {
        if ( $member->is_discord_connected )
        {
            return "<@!{$member->discord_id}>";
        }

        return "@{$member->name}";
    }
}
