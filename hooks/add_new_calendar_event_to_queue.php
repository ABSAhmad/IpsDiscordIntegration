//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class discord_hook_add_new_calendar_event_to_queue extends _HOOK_CLASS_
{
    /**
     * Process created object AFTER the object has been created
     *
     * @param   \IPS\Content\Comment|NULL   $comment    The first comment
     * @param   array                       $values     Values from form
     * @return  void
     */
    protected function processAfterCreate( $comment, $values )
    {
        call_user_func_array( 'parent::processAfterCreate', func_get_args() );

        $channelId = $this->hidden() ? $this->container()->discord_channel_unapproved : $this->container()->discord_channel_approved;

        if ( !$channelId )
        {
            return;
        }

        try {
            \IPS\Db::i()->insert('discord_sync_calendar_events', [
                'calendar_event_id' => $this->id,
                'discord_channel_id' => $channelId
            ]);
        } catch (\Throwable $e) {
            \IPS\Log::log($e, 'discord_queue_calendar_event');
        }
    }

    /**
     * Syncing to run when unhiding
     *
     * @param   bool                    $approving  If true, is being approved for the first time
     * @param   \IPS\Member|NULL|FALSE  $member The member doing the action (NULL for currently logged in member, FALSE for no member)
     * @return  void
     */
    public function onUnhide( $approving, $member )
    {
        call_user_func_array( 'parent::onUnhide', func_get_args() );

        $channelId = $this->hidden() ? $this->container()->discord_channel_unapproved : $this->container()->discord_channel_approved;

        if ( !$approving || !$channelId )
        {
            return;
        }

        try {
            \IPS\Db::i()->insert('discord_sync_calendar_events', [
                'calendar_event_id' => $this->event_id,
                'discord_channel_id' => $channelId
            ]);
        } catch (\Throwable $e) {
            \IPS\Log::log($e, 'discord_queue_calendar_event');
        }
    }
}
