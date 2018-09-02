//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class discord_hook_add_new_topic_to_queue extends _HOOK_CLASS_
{
    /**
     * Process created object AFTER the object has been created.
     *
     * @param \IPS\Content\Comment|NULL $comment The first comment
     * @param array $values Values from form
     * @return mixed
     */
    protected function processAfterCreate( $comment, $values )
    {
        $return = call_user_func_array( 'parent::processAfterCreate', func_get_args() );

        if ($channels = self::shouldBeSentToChannels($this))
        {

        }

        if ( $this->container()->discord_post_topics || ( $this->hidden() && $this->container()->discord_post_unapproved_topics ) )
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

        return $return;
    }

    /**
     * Syncing to run when unhiding.
     *
     * @param bool $approving If true, is being approved for the first time
     * @param \IPS\Member|NULL|FALSE $member The member doing the action (NULL for currently logged in member, FALSE for no member)
     * @return mixed
     */
    public function onUnhide( $approving, $member )
    {
        $return = call_user_func_array( 'parent::onUnhide', func_get_args() );

        if ( $approving && $this->container()->discord_post_topics )
        {
            $channel = new \IPS\discord\Api\Channel;
            $channel->postContentItem( $this );
        }

        return $return;
    }

    protected static function shouldBeSentToChannels(\IPS\forums\Topic $topic)
    {
        $approvedForums = \IPS\Settings::i()->discord_approved_posts_from_forums;
        $unapprovedForums = \IPS\Settings::i()->discord_unapproved_posts_from_forums;
        $postToChannels = [];

        /** @var \IPS\forums\Forum $forum */
        $forum = $topic->container();
        $forumIds[] = $forum->id;

        $parents = $forum->parents();

        foreach ($parents as $parent) {
            $forumIds[] = $parent->id;
        }

        // Check if there are channels defined for the current forum
        if (!$topic->hidden() && $approvedForums != 0)
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

        if ($topic->hidden() && $unapprovedForums != 0)
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
