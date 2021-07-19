<?php

$ll = 'LLL:EXT:news/Resources/Private/Language/locallang_db.xlf:';

$tmp_social_stream_columns = [

	'object_id' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.object_id',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
	],
	'link' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.link',
		'config' => [
            'type' => 'text',
            'cols' => 40,
            'rows' => 15,
            'eval' => 'trim',
		],
	],
	'media_url' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.media_url',
		'config' => [
            'type' => 'text',
            'cols' => 40,
            'rows' => 15,
            'eval' => 'trim',
		],
	],
	'place_name' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_name',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
	],
	'place_street' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_street',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
	],
	'place_zip' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_zip',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
	],
	'place_city' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_city',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
	],
	'place_country' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_country',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
	],
	'place_lat' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_lat',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
	],
	'place_lng' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.place_lng',
		'displayCond' => 'FIELD:place_name:REQ:true',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim'
		],
	],
	'datetimeend' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.datetimeend',
		'config' => [
			'type' => 'input',
			'size' => 16,
			'max' => 20,
			'default' => 0,
			'eval' => 'datetime',
		],
	],
	'channel' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.channel',
		'config' => [

// Would be the 'normal' config for this field - but this causes the TYPO3 Backend to be really slow e.g. on deleting records
//			'type' => 'select',
//			'renderType' => 'selectSingle',
//			'foreign_table' => 'tx_socialstream_domain_model_channel',
//            'items' => [
//                ['', 0],
//            ],
//			'default' => 0,

//			'minitems' => 0,
//			'maxitems' => 1,

//        Type Group reduces Loading Time by ~25%
//            'type' => 'group',
//            'internal_type' => 'db',
//            'allowed' => 'tx_socialstream_domain_model_channel',

//        Type Passthrough is not the best option, but with this no speed losses are measurable
            'type' => 'passthrough'
		],
	],
];

//$tmp_social_stream_columns['channel'] = [
//	'config' => [
//        'type' => 'group',
//        'internal_type' => 'db',
//        'allowed' => 'tx_socialstream_domain_model_channel'
//	]
//];


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news',$tmp_social_stream_columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', 'object_id, link, media_url, channel');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', '--div--;LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.location,place_name, place_street, place_zip, place_city, place_country, place_lat, place_lng', '', 'after:notes');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', 'datetimeend', '', 'after:datetime');
