<?php
/**
 * @brief		SynchronizeNewPosts Task
 * @author		<a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) Invision Power Services, Inc.
 * @license		https://www.invisioncommunity.com/legal/standards/
 * @package		Invision Community
 * @subpackage	discord
 * @since		26 Aug 2018
 */

namespace IPS\discord\tasks;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * SynchronizeNewPosts Task
 */
class _SynchronizeNewPosts extends \IPS\Task
{
	/**
	 * Execute
	 *
	 * If ran successfully, should return anything worth logging. Only log something
	 * worth mentioning (don't log "task ran successfully"). Return NULL (actual NULL, not '' or 0) to not log (which will be most cases).
	 * If an error occurs which means the task could not finish running, throw an \IPS\Task\Exception - do not log an error as a normal log.
	 * Tasks should execute within the time of a normal HTTP request.
	 *
	 * @return	mixed	Message to log or NULL
	 * @throws	\IPS\Task\Exception
	 */
	public function execute()
	{
        /** @var \IPS\discord\Discord\PostsToSync[] $postsToSyncs */
        $postsToSyncs = new \IPS\Patterns\ActiveRecordIterator(
            \IPS\Db::i()->select( '*', 'discord_sync_posts' ),
            \IPS\discord\Discord\PostsToSync::class
        );

        $idsToRemove = [];

        foreach ($postsToSyncs as $postToSync)
        {
            $postId = $postToSync->post_id;
            $post = \IPS\forums\Topic\Post::load($postId);
            $channelIds = explode(',', $postToSync->discord_channel_ids);
            try
            {
                foreach ($channelIds as $channelId)
                {
                        \IPS\discord\Api\Channel::i()->createMessage(
                            $channelId,
                            [
                                'content' => \IPS\discord\Util\MessageFormatter::createMessageFromIpsData(
                                    $post->item()->title,
                                    $post->author(),
                                    $post->url(),
                                    $post->hidden() ? \IPS\Settings::i()->discord_unapproved_post_format : \IPS\Settings::i()->discord_approved_post_format
                                ),
                            ]
                        );
                }

                $idsToRemove[] = $postToSync->id;
            }
            catch (\Exception $e) {}
        }

        \IPS\Db::i()->delete('discord_sync_posts', \IPS\Db::i()->in('id', $idsToRemove));

		return NULL;
	}
}
