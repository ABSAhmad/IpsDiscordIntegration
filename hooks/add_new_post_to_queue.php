//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    exit;
}

class discord_hook_add_new_post_to_queue extends _HOOK_CLASS_
{
    /**
     * Create comment
     *
     * @param   \IPS\Content\Item       $item               The content item just created
     * @param   \IPS\forums\Topic\Post  $comment            The comment
     * @param   bool                    $first              Is the first comment?
     * @param   string                  $guestName          If author is a guest, the name to use
     * @param   bool|NULL               $incrementPostCount Increment post count? If NULL, will use static::incrementPostCount()
     * @param   \IPS\Member|NULL        $member             The author of this comment. If NULL, uses currently logged in member.
     * @param   \IPS\DateTime|NULL      $time               The time
     * @param   string|NULL             $ipAddress          The IP address or NULL to detect automatically
     * @param   int|NULL                $hiddenStatus       NULL to set automatically or override: 0 = unhidden; 1 = hidden, pending moderator approval; -1 = hidden (as if hidden by a moderator)
     *
     * @return  static
     */
    public static function create( $item, $comment, $first=false, $guestName=NULL, $incrementPostCount=NULL, $member=NULL, \IPS\DateTime $time=NULL, $ipAddress=NULL, $hiddenStatus=NULL )
    {
        /** @var \IPS\forums\Topic\Post $comment */
        $comment = parent::create( ...func_get_args() );

        if ( self::isNotTopic($first, $item) && $channels = self::shouldBeSentToChannels($comment) )
        {
            try {
                \IPS\Db::i()->insert('discord_sync_posts', [
                    'post_id' => $comment->pid,
                    'discord_channel_ids' => implode(',', $channels)
                ]);
            } catch (\Throwable $e) {
                \IPS\Log::log($e, 'discord_queue_post');
            }
        }

        return $comment;
    }

    public function onUnhide( $approving, $member )
    {
        $return = parent::onUnhide( ...func_get_args() );

        if ( $approving && $channels = self::shouldBeSentToChannels($this) )
        {
            try {
                \IPS\Db::i()->insert('discord_sync_posts', [
                    'post_id' => $this->pid,
                    'discord_channel_ids' => implode(',', $channels)
                ]);
            } catch (\Throwable $e) {
                \IPS\Log::log($e, 'discord_queue_post');
            }
        }

        return $return;
    }

    /**
     * @param bool              $isFirstPost
     * @param \IPS\Content\Item $item
     *
     * @return bool
     */
    protected static function isNotTopic(bool $isFirstPost, \IPS\Content\Item $item): bool
    {
        return !$isFirstPost && $item instanceof \IPS\forums\Topic;
    }

    /**
     * @param \IPS\forums\Topic\Post $comment
     *
     * @return array|false
     */
    protected static function shouldBeSentToChannels(\IPS\forums\Topic\Post $comment)
    {
        $approvedForums = \IPS\Settings::i()->discord_approved_posts_from_forums;
        $unapprovedForums = \IPS\Settings::i()->discord_unapproved_posts_from_forums;
        $postToChannels = [];

        /** @var \IPS\forums\Forum $forum */
        $forum = $comment->container();
        $forumIds[] = $forum->id;

        $parents = $forum->parents();

        foreach ($parents as $parent) {
            $forumIds[] = $parent->id;
        }

        // Check if there are channels defined for the current forum
        if (!$comment->hidden() && $approvedForums != 0)
        {
            foreach ($approvedForums as $channelId => $forums)
            {
                if (count(array_intersect($forumIds, $forums)) > 0)
                {
                    $postToChannels[] = $channelId;
                }
            }

            return $postToChannels;
        }

        if ($comment->hidden() && $unapprovedForums != 0)
        {
            foreach ($unapprovedForums as $channelId => $forums)
            {
                if (count(array_intersect($forumIds, $forums)) > 0)
                {
                    $postToChannels[] = $channelId;
                }
            }

            return $postToChannels;
        }

        return FALSE;
    }
}
