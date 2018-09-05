//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !\defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
    exit;
}

class discord_hook_forum_form_extension extends _HOOK_CLASS_
{
    /**
     * [Node] Add/Edit Form
     *
     * @param   \IPS\Helpers\Form   $form   The form
     * @return  void
     */
    public function form( &$form )
    {
        /** @var \IPS\Helpers\Form $form */
        parent::form( $form );

        $form->addHeader( 'discord_notifications' );

        $form->add(
            new \IPS\Helpers\Form\TextArea(
                'discord_topic_format',
                $this->discord_topic_format ?: '{poster} has just posted a new topic called: "{title}". Read more: {link}',
                TRUE, [], NULL, NULL, NULL, 'discord_topic_format'
            )
        );
        $form->add(
            new \IPS\Helpers\Form\TextArea(
                'discord_post_format',
                $this->discord_post_format ?: '{poster} has just posted a new post to the topic: "{title}". Read more: {link}',
                TRUE, [], NULL, NULL, NULL, 'discord_post_format'
            )
        );
    }
}
