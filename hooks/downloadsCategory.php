//<?php

/**
 * This file is part of Discord Integration.
 *
 * (c) Ahmad El-Bardan <ahmadelbardan@hotmail.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class discord_hook_downloadsCategory extends _HOOK_CLASS_
{
    /**
     * [Node] Add/Edit Form
     *
     * @param	\IPS\Helpers\Form	$form	The form
     * @return	void
     */
    public function form( &$form )
    {
        parent::form( $form );

        $guild = \IPS\discord\Api\Guild::primary();
        // TODO: extract this into an function
        $channels = $guild->textChannels()->mapWithKeys(function (array $channel) {
            return [$channel['id'] => $channel['name']];
        })->toArray();

        $form->addTab( 'discord_channels' );
        $form->addHeader( 'discord_channels' );
        $form->add(
            new \IPS\Helpers\Form\Select( 'cdiscord_channel_approved', $this->discord_channel_approved ?: 0, TRUE, [
                'options' => $channels
            ] )
        );
        $form->add(
            new \IPS\Helpers\Form\Select( 'cdiscord_channel_unapproved', $this->discord_channel_unapproved ?: 0, TRUE, [
                'options' => $channels
            ] )
        );

        $form->addHeader( 'discord_notifications' );
        $form->add(
            new \IPS\Helpers\Form\TextArea(
                'cdiscord_post_format',
                $this->discord_post_format ?: '{poster} has just uploaded a new file called: "{title}". Read more: {link}',
                TRUE
            )
        );
    }
}
