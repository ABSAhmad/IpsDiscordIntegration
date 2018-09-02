<?php
/**
 * @brief		SynchronizeGroupChanges Task
 * @author		<a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright	(c) Invision Power Services, Inc.
 * @license		https://www.invisioncommunity.com/legal/standards/
 * @package		Invision Community
 * @subpackage	discord
 * @since		01 Sep 2018
 */

namespace IPS\discord\tasks;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * SynchronizeGroupChanges Task
 */
class _SynchronizeGroupChanges extends \IPS\Task
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
        if (!\IPS\Settings::i()->discord_sync_groups) {
            return;
        }

        /** @var \IPS\discord\Discord\GroupsToSync[] $membersToSync */
        $membersToSync = new \IPS\Patterns\ActiveRecordIterator(
            \IPS\Db::i()
                ->select(
                    '*',
                    ['discord_sync_groups', 'discord'],
                    ['login_method.login_classname = ?', \IPS\discord\LoginHandler\Discord::class]
                )
                ->join(
                    ['core_login_links', 'login_link'],
                    'login_link.token_member = discord.member_id'
                )
                ->join(
                    ['core_login_methods', 'login_method'],
                    'login_method.login_id = login_link.token_login_method'
                ),
            \IPS\discord\Discord\GroupsToSync::class
        );

        $idsToRemove = [];

        foreach ($membersToSync as $memberToSync)
        {
            try {
                \IPS\discord\Synchronizer::i()->syncMemberRoles( \IPS\Member::load($memberToSync->member_id), $memberToSync->token_identifier );

                $idsToRemove[] = $memberToSync->id;
            } catch (\Exception $e) {}
        }

        \IPS\Db::i()->delete('discord_sync_groups', \IPS\Db::i()->in('id', $idsToRemove));

        return NULL;
	}
}
