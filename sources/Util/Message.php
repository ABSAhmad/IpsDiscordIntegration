<?php

namespace IPS\discord\Util;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Class _Message
 *
 * @package IPS\discord\Util
 */
class _Message
{
    /**
     * @var \IPS\Member $member
     */
    protected $member;

    /**
     * The complete message that will be sent to discord.
     *
     * @var string $message
     */
    protected $message;

    /**
     * The channel id of the discord channel that the message is being sent to.
     *
     * @var string $channelId
     */
    protected $channelId;

    /**
     * The entered title for the given content item by the user.
     * This will be included in the message if configured to do so.
     *
     * @var string $title
     */
    protected $title;

    /**
     * An URL that links back to the content item.
     *
     * @var string $url
     */
    protected $url;

    /**
     * Indicates whether the message has already been formatted so
     * we do not format it every time that it is requested.
     *
     * @var bool $isMessageFormatted
     */
    protected $isMessageFormatted = false;

    /**
     * @param \IPS\Member $member
     * @param string $message
     * @param string $channelId
     * @param string $title
     * @param string $url
     */
    public function __construct(\IPS\Member $member, $message, $channelId, $title, $url)
    {
        $this->member = $member;
        $this->message = $message;
        $this->channelId = $channelId;
        $this->title = $title;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getChannelId()
    {
        return $this->channelId;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        if (!$this->isMessageFormatted) {
            return $this->formatMessage();
        }

        return $this->message;
    }

    /**
     * Create an instance from the given calendar event.
     *
     * @param \IPS\calendar\Event $calendarEvent
     *
     * @return static
     */
    public static function fromCalendarEvent(\IPS\calendar\Event $calendarEvent)
    {
        $container = $calendarEvent->container();
        $channelId = $calendarEvent->hidden() ? $container->discord_channel_unapproved : $container->discord_channel_approved;

        return new static(
            $calendarEvent->author(),
            $container->discord_post_format,
            $channelId,
            $calendarEvent->title,
            (string) $calendarEvent->url()
        );
    }

    /**
     * Create an instance from the given downloads file.
     *
     * @param \IPS\downloads\File $downloadsFile
     *
     * @return static
     */
    public static function fromDownloadsFile(\IPS\downloads\File $downloadsFile)
    {
        $container = $downloadsFile->container();
        $channelId = $downloadsFile->hidden() ? $container->discord_channel_unapproved : $container->discord_channel_approved;

        return new static(
            $downloadsFile->author(),
            $container->discord_post_format,
            $channelId,
            $downloadsFile->name, // TODO: abstract this
            (string) $downloadsFile->url()
        );
    }

    /**
     * Create an instance from the given post.
     *
     * @param \IPS\forums\Topic\Post $post
     * @param \IPS\Member|null $member
     *
     * @return static
     */
    public static function fromForumPost(\IPS\forums\Topic\Post $post, \IPS\Member $member = NULL)
    {
        $container = $post->container();
        $channelId = $post->hidden() ? $container->discord_channel_unapproved : $container->discord_channel_approved;

        return new static(
            $member ?: $post->author(),
            $container->discord_post_format,
            $channelId,
            $post->item()->title, // TODO: abstract this
            (string) $post->url()
        );
    }

    /**
     * Create an instance from the given topic.
     *
     * @param \IPS\forums\Topic $topic
     *
     * @return static
     */
    public static function fromForumTopic(\IPS\forums\Topic $topic)
    {
        $container = $topic->container();
        $channelId = $topic->hidden() ? $container->discord_channel_unapproved : $container->discord_channel_approved;

        return new static(
            $topic->author(),
            $container->discord_topic_format,
            $channelId,
            $topic->title,
            (string) $topic->url()
        );
    }

    /**
     * Format the message to replace any variables with their actual
     * content for the current message so that it can be sent to
     * discord.
     *
     * @return string
     */
    public function formatMessage()
    {
        $search = [
            '{poster}',
            '{title}',
            '{link}'
        ];

        $replace = [
            $this->formattedPosterName(),
            $this->title,
            $this->url
        ];

        $this->message = str_replace( $search, $replace, $this->message );
        $this->isMessageFormatted = true;

        return $this->message;
    }

    /**
     * Get appropriate title of the content item.
     *
     * @param \IPS\Content $content
     *
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
     * If the member has an discord account linked, actually mention them in discord
     * so that they get notified. Otherwise we just prepend their name with an at
     * symbol (@) to indicate that they have been mentioned.
     *
     * @return string
     */
    protected function formattedPosterName()
    {
        if ( $this->member->is_discord_connected )
        {
            return "<@!{$this->member->discord_id}>";
        }

        return "@{$this->member->name}";
    }
}
