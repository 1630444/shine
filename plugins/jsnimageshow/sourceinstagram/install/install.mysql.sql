CREATE TABLE IF NOT EXISTS `#__imageshow_external_source_instagram` (
  `external_source_id` int(11) unsigned NOT NULL auto_increment,
  `external_source_profile_title` varchar(255) default '',
  `instagram_app_id` varchar(255) default '',
  `instagram_secret` varchar(255) default '',
  `instagram_callback_url` varchar(500) default '',
  `instagram_access_token` varchar(255) default '',
  `instagram_current_user_id` varchar(255) default '',
  `instagram_find_user` varchar(255) default '',
  `instagram_find_hashtag` varchar(255) default '',
  PRIMARY KEY  (`external_source_id`)
) DEFAULT CHARSET=utf8;