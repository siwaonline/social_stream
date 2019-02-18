<?php

$ll = 'LLL:EXT:news/Resources/Private/Language/locallang_db.xlf:';

$tmp_social_stream_columns = array(

	'object_id' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.object_id',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'link' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.link',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'media_url' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.media_url',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'place_name' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_name',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'place_street' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_street',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'place_zip' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_zip',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'place_city' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_city',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'place_country' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_country',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'place_lat' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_lat',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'place_lng' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_lng',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => array(
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		),
	),
	'datetimeend' => array(
		'exclude' => 0,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.datetimeend',
		'config' => array(
			'type' => 'input',
			'size' => 16,
			'max' => 20,
            'default' => 0,
			'eval' => 'datetime',
		),
	),
	'channel' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.channel',
		'config' => array(
			'type' => 'select',
			'renderType' => 'selectSingle',
			'foreign_table' => 'tx_socialstream_domain_model_channel',
            'items' => array(
                array('', 0),
            ),
			'default' => 0,
			'minitems' => 0,
			'maxitems' => 1,
		),
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news',$tmp_social_stream_columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', 'object_id, link, media_url, channel');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', '--div--;LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.location,place_name, place_street, place_zip, place_city, place_country, place_lat, place_lng', '', 'after:notes');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', 'datetimeend', '', 'after:datetime');
