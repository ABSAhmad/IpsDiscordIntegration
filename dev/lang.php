<?php

$lang = array(
    /* !Menu */
    '__app_discord'	=> "Discord Integration",
    'menu__discord_settings' => "Discord",
    'menu__discord_settings_settings' => "Settings",
    'module__discord_register' => "Register Module",

    /* !Titles */
    'discord_setting_title' => "Discord Settings",

    /* !Setting Form */
    'discord_connection_settings' => "Authentication",
    'discord_redirect_uris' => "Add the following redirect URIs to your discord application:<br/>%s<br/>%s",
    'discord_client_id' => "Discord Client ID",
    'discord_client_id_desc' => "You can create a discord application <a target='_blank' href='https://discordapp.com/developers/applications/me'>here</a>",
    'discord_client_secret' => "Discord Client Secret",
    'discord_client_secret_desc' => "You can create a discord application <a target='_blank' href='https://discordapp.com/developers/applications/me'>here</a>",
    'discord_bot_token' => "Discord Bot Token",
    'discord_bot_token_desc' => "You can create a discord application <a target='_blank' href='https://discordapp.com/developers/applications/me'>here</a>",
    'discord_guild_id' => "Discord Guild ID",
    'discord_guild_id_desc' => "This will be automatically assigned when choosing the server after saving",

    'discord_map_settings' => "Basic",
    'discord_remove_unmapped' => "Remove unmapped roles?",
    'discord_remove_unmapped_desc' => "Should roles that are not mapped to IPS groups be removed from discord members?",
    'discord_sync_bans' => "Sync bans?",
    'discord_sync_bans_desc' => "Should a member get banned on discord if said member got banned on the forum?",
    'discord_sync_names' => "Sync names?",
    'discord_sync_names_desc' => "This will update the discord nicks to the member names so the names match on discord/IPS.",
    'discord_handshake' => "Handshake",

    /* !Login Handler */
    'discord_sign_in' => "Sign in with Discord",
    'profilesync__Discord' => "Discord",
    'login_handler_Discord' => "Discord",

    /* !Group Form */
    'group__discord_roles' => "Discord",
    'discord_role' => "Discord Role",

    /* !Forums Form */
    'discord_channels' => "Discord Channels",
    'discord_channel_approved' => "Channel to post in if the topic/post is approved.",
    'discord_channel_unapproved' => "Channel to post in if the topic/post is unapproved.",
    'discord_notifications' => "Discord Notifications",

    'discord_post_topics' => "Notify about (all) new topics?",
    'discord_post_topics_desc' => "Should notifications be sent to discord channels about new topics (does not matter if approved or unapproved)?",
    'discord_post_unapproved_topics' => "Notify about new unapproved topics?",
    'discord_post_unapproved_topics_desc' => "Should notifications be sent to discord channels about new (unapproved) topics?",
    'discord_post_posts' => "Notify about (all) new posts?",
    'discord_post_posts_desc' => "Should notifications be sent to discord channels about new posts (does not matter if approved or unapproved)?",
    'discord_post_unapproved_posts' => "Notify about new unapproved posts?",
    'discord_post_unapproved_posts_desc' => "Should notifications be sent to discord channels about new (unapproved) posts?",

    'discord_topic_format' => "Discord topic format",
    'discord_topic_format_desc' => "This will be posted to a channel when there is a new topic.<br>Available variables:<br>{poster} = Poster name.<br>{title} = The title of the topic that is being posted.<br>{link} = Link to the topic.",
    'discord_post_format' => "Discord post format",
    'discord_post_format_desc' => "This will be posted to a channel when there is a new post.<br>Available variables:<br>{poster} = Poster name.<br>{title} = The title of the topic that is being posted in.<br>{link} = Link to the post.",

    /* !Calendar Form */
    'cal_discord_channel_approved' => "Channel to post in if the calendar event is approved.",
    'cal_discord_channel_unapproved' => "Channel to post in if the calendar event is unapproved.",
    'cal_discord_post_format' => "Discord post format",
    'cal_discord_post_format_desc' => "This will be posted to a channel when there is a new calendar event.<br>Available variables:<br>{poster} = Poster name.<br/>{title} = The title of the event that is being posted.<br/>{link} = Link to the event.",

    /* !Downloads Form */
    'cdiscord_channel_approved' => "Channel to post in if the file is approved.",
    'cdiscord_channel_unapproved' => "Channel to post in if the file is unapproved.",
    'cdiscord_post_format' => "Discord post format",
    'cdiscord_post_format_desc' => "This will be posted to a channel when there is a new file.<br>Available variables:<br>{poster} = Poster name.<br/>{title} = The name of the file that is being posted.<br/>{link} = Link to the file.",

    /* !Error Messages */
    'discord_access_denied' => "You denied access",
    'discord_not_verified' => 'Your discord email address is not verified!',
);
