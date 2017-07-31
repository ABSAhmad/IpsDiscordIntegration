<?php
/**
 * @brief		syncMembers Task
 * @author		<a href='http://www.invisionpower.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) 2001 - 2016 Invision Power Services, Inc.
 * @license		http://www.invisionpower.com/legal/standards/
 * @package		IPS Community Suite
 * @subpackage	discord
 * @since		06 Mar 2017
 * @version		SVN_VERSION_NUMBER
 */

namespace IPS\discord\tasks;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * syncMembers Task
 */
class _syncMembers extends \IPS\Task
{
    /**
     * If ran successfully, should return anything worth logging. Only log something
     * worth mentioning (don't log "task ran successfully"). Return NULL (actual NULL, not '' or 0) to not log (which will be most cases).
     * If an error occurs which means the task could not finish running, throw an \IPS\Task\Exception - do not log an error as a normal log.
     * Tasks should execute within the time of a normal HTTP request.
     *
     * @throws \IPS\Task\Exception
     *
     * @return mixed Message to log or NULL
     */
    public function execute()
    {
        /** @var \IPS\Member[] $members */
        $members = new \IPS\Patterns\ActiveRecordIterator(
            \IPS\Db::i()->select( '*', 'core_members', ['discord_id != ? AND discord_token != ?', '0', ''] ),
            \IPS\Member::class
        );

        $guild = \IPS\discord\Api\Guild::primary();

        $count = 0;

        foreach ( $members as $key => $member )
        {
            try
            {
                $discordMemberUtility = new \IPS\discord\Util\Member( $member );
                $guild->modifyMember(
                    $member->discord_id,
                    [
                        'roles' => $discordMemberUtility->shouldHaveRoles()->toArray()
                    ]
                );

                /* Prevent hitting API rate limit. */
                if ( $count % 5 === 0 )
                {
                    sleep( 20 );
                }

                ++$count;
            }
            catch ( \RestCord\RateLimit\RatelimitException $e )
            {
                throw new \IPS\Task\Exception( $this, "Hit discord API rate limit at member: {$member->name}, at iteration: {$count}" );
            }
            catch ( \Exception $e )
            {
                throw new \IPS\Task\Exception( $this, $e->getMessage() );
            }
        }

        return NULL;
    }

    /**
     * If your task takes longer than 15 minutes to run, this method
     * will be called before execute(). Use it to clean up anything which
     * may not have been done
     *
     * @return void
     */
    public function cleanup()
    {
    }
}
