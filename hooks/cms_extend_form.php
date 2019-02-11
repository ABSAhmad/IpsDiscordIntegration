//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class discord_hook_cms_extend_form extends _HOOK_CLASS_
{
    /**
     * Generate the database form
     *
     * @param	\IPS\cms\Databases	$current	The current database
     * @param	\IPS\cms\Categories	$category	The default catgory
     *
     * @return	\IPS\Helpers\Form
     */
    protected function _getDatabaseForm( $current, $category )
    {
        /** @var \IPS\Helpers\Form $form */
        $form = parent::_getDatabaseForm( ...func_get_args() );

        $form->addTab('discord_test');
        $form->addHeader('test');

        return $form;
    }
}
