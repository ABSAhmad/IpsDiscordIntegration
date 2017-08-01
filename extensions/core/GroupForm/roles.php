<?php

/**
 * This file is part of Discord Integration.
 *
 * (c) Ahmad El-Bardan <ahmadelbardan@hotmail.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IPS\discord\extensions\core\GroupForm;

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    header( ( isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0' ) . ' 403 Forbidden' );
    exit;
}

/**
 * Admin CP Group Form
 */
class _roles
{
    /**
     * Process Form
     *
     * @param	\IPS\Helpers\Form		$form	The form
     * @param	\IPS\Member\Group		$group	Existing Group
     * @return	void
     */
    public function process( &$form, $group )
    {
        try
        {
            $guild = \IPS\discord\Api\Guild::primary();

            $roles = $guild->roles()->reject(function (array $role) {
                return $role['name'] == '@everyone';
            })->mapWithKeys(function ($role) {
                return [$role['id'] => $role['name']];
            })->put(0, '')->sort()->toArray();

            /** @noinspection PhpUndefinedFieldInspection */
            $form->add(
                new \IPS\Helpers\Form\Select( 'discord_role', $group->discord_role ?: 0, TRUE, [
                    'options' => $roles
                ] )
            );
        }
        catch ( \Exception $e )
        {
            \IPS\Log::log( $e, 'discord_roles' );
            $form->add(
                new \IPS\Helpers\Form\TextArea( 'discord_error', 'Error occurred while retrieving discord roles, check the logs for more information.' )
            );
        }
    }

    /**
     * Save
     *
     * @param array $values Values from form
     * @param \IPS\Member\Group $group The group
     * @return void
     */
    public function save( $values, &$group )
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $group->discord_role = $values['discord_role'];
    }
}
