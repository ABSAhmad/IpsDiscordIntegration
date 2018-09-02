<?php
/**
 * @brief       Admin CP Group Form
 * @author      <a href='https://www.invisioncommunity.com'>Invision Power Services, Inc.</a>
 * @copyright   (c) Invision Power Services, Inc.
 * @license     https://www.invisioncommunity.com/legal/standards/
 * @package     Invision Community
 * @subpackage  Discord Integration
 * @since       30 Aug 2018
 */

namespace IPS\discord\extensions\core\GroupForm;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Admin CP Group Form
 */
class _ExtendWithDiscordRoles
{
    /**
     * Process Form
     *
     * @param   \IPS\Helpers\Form       $form   The form
     * @param   \IPS\Member\Group       $group  Existing Group
     * @return  void
     */
    public function process( &$form, $group )
    {
        try
        {
            $roles = \IPS\discord\Api\Guild::i()->roles(\IPS\Settings::i()->discord_guild_id);
            $roles = \IPS\discord\Util\RolesFormatter::onlyNormalGroups($roles);
            $roles = \IPS\discord\Util\RolesFormatter::onlyIdsAndNames($roles);
            $roles = \IPS\discord\Util\RolesFormatter::addEmptyRole($roles);

            $form->add(
                new \IPS\Helpers\Form\Select( 'discord_role', $group->discord_role ?: 0, TRUE, [
                    'options' => $roles
                ] )
            );
        }
        catch ( \Exception $e )
        {
            \IPS\Log::log( $e, 'discord_roles' );
            $form->error = 'Error occurred while retrieving discord roles, check the logs for more information.';
        }
    }

    /**
     * Save
     *
     * @param   array               $values Values from form
     * @param   \IPS\Member\Group   $group  The group
     * @return  void
     */
    public function save( $values, &$group )
    {
        $group->discord_role = $values['discord_role'];
    }
}
