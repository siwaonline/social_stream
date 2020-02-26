<?php

/*
if (!isset($GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['type'])) {
	if (file_exists($GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['dynamicConfigFile'])) {
		require_once($GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['dynamicConfigFile']);
	}
	// no type field defined, so we define it here. This will only happen the first time the extension is installed!!
	$GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['type'] = 'tx_extbase_type';
	$tempColumnstx_socialstream_tx_news_domain_model_news = array();
	$tempColumnstx_socialstream_tx_news_domain_model_news[$GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['type']] = array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream.tx_extbase_type',
		'config' => array(
			'type' => 'select',
			'renderType' => 'selectSingle',
			'items' => array(
				array('News','Tx_SocialStream_News')
			),
			'default' => 'Tx_SocialStream_News',
			'size' => 1,
			'maxitems' => 1,
		)
	);
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news', $tempColumnstx_socialstream_tx_news_domain_model_news, 1);
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'tx_news_domain_model_news',
	$GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['type'],
	'',
	'after:' . $GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['label']
);
*/
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
            'type' => 'text',
            'cols' => 40,
            'rows' => 15,
            'eval' => 'trim',
		),
	),
	'media_url' => array(
		'exclude' => 1,
		'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.media_url',
		'config' => array(
            'type' => 'text',
            'cols' => 40,
            'rows' => 15,
            'eval' => 'trim',
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

/*
$tmp_social_stream_columns['channel'] = array(
	'config' => array(
		'type' => 'passthrough',
	)
);
*/


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news',$tmp_social_stream_columns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', 'object_id, link, media_url, channel');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', '--div--;LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.location,place_name, place_street, place_zip, place_city, place_country, place_lat, place_lng', '', 'after:notes');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', 'datetimeend', '', 'after:datetime');

//$GLOBALS['TCA']['tx_news_domain_model_news']['types']['0']['showitem'] .= ',--div--;LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news.location,place_name, place_street, place_zip, place_city, place_country, place_lat, place_lng,';

/* inherit and extend the show items from the parent class */

/*
if(isset($GLOBALS['TCA']['tx_news_domain_model_news']['types']['1']['showitem'])) {
	$GLOBALS['TCA']['tx_news_domain_model_news']['types']['Tx_SocialStream_News']['showitem'] = $GLOBALS['TCA']['tx_news_domain_model_news']['types']['1']['showitem'];
} elseif(is_array($GLOBALS['TCA']['tx_news_domain_model_news']['types'])) {
	// use first entry in types array
	$tx_news_domain_model_news_type_definition = reset($GLOBALS['TCA']['tx_news_domain_model_news']['types']);
	$GLOBALS['TCA']['tx_news_domain_model_news']['types']['Tx_SocialStream_News']['showitem'] = $tx_news_domain_model_news_type_definition['showitem'];
} else {
	$GLOBALS['TCA']['tx_news_domain_model_news']['types']['Tx_SocialStream_News']['showitem'] = '';
}
$GLOBALS['TCA']['tx_news_domain_model_news']['types']['Tx_SocialStream_News']['showitem'] .= ',--div--;LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_news,';
$GLOBALS['TCA']['tx_news_domain_model_news']['types']['Tx_SocialStream_News']['showitem'] .= 'object_id, link, place_name, place_street, place_zip, place_city, place_country, place_lat, place_lng, channel';

$GLOBALS['TCA']['tx_news_domain_model_news']['columns'][$GLOBALS['TCA']['tx_news_domain_model_news']['ctrl']['type']]['config']['items'][] = array('LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_news_domain_model_news.tx_extbase_type.Tx_SocialStream_News','Tx_SocialStream_News');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'',
	'EXT:/Resources/Private/Language/locallang_csh_.xlf'
);
*/

