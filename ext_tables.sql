#
# Table structure for table 'tx_news_domain_model_news'
#
CREATE TABLE tx_news_domain_model_news (

	channel int(11) unsigned DEFAULT '0' NOT NULL,

  datetimeend int(11) DEFAULT '0' NOT NULL,
	object_id varchar(255) DEFAULT '' NOT NULL,
	link varchar(255) DEFAULT '' NOT NULL,
	media_url varchar(255) DEFAULT '' NOT NULL,
	place_name varchar(255) DEFAULT '' NOT NULL,
	place_street varchar(255) DEFAULT '' NOT NULL,
	place_zip varchar(255) DEFAULT '' NOT NULL,
	place_city varchar(255) DEFAULT '' NOT NULL,
	place_country varchar(255) DEFAULT '' NOT NULL,
	place_lat varchar(255) DEFAULT '' NOT NULL,
	place_lng varchar(255) DEFAULT '' NOT NULL,
	channel int(11) unsigned DEFAULT '0',

);

#
# Table structure for table 'tx_socialstream_domain_model_channel'
#
CREATE TABLE tx_socialstream_domain_model_channel (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	object_id varchar(255) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	about varchar(255) DEFAULT '' NOT NULL,
	description varchar(255) DEFAULT '' NOT NULL,
	user tinyint(1) unsigned DEFAULT '0' NOT NULL,
	type varchar(255) DEFAULT '' NOT NULL,
	link varchar(255) DEFAULT '' NOT NULL,
	image int(11) unsigned NOT NULL default '0',
	token varchar(255) DEFAULT '' NOT NULL,
	expires int(11) DEFAULT '0' NOT NULL,
	news int(11) unsigned DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 KEY language (l10n_parent,sys_language_uid)

);

#
# Table structure for table 'tx_news_domain_model_news'
#
CREATE TABLE tx_news_domain_model_news (

	channel  int(11) unsigned DEFAULT '0' NOT NULL,

);
