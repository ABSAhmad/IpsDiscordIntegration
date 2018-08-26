//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

abstract class discord_hook_register_login_handler extends _HOOK_CLASS_
{
	/**
	 * Get all handler classes
	 */
	public static function handlerClasses()
	{
	    $loginHandlers = parent::handlerClasses();

	    $loginHandlers[] = \IPS\discord\LoginHandler\Discord::class;

		return $loginHandlers;
	}
}
