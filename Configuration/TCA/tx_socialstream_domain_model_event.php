<?php
return array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event',
		'label' => 'id',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'default_sortby' => 'start_time DESC',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'id,start_time,end_time,name,description,place_name,place_street,place_zip,place_city,place_country,lat,lng,picture_url,picture,page,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('social_stream') . 'Resources/Public/Icons/tx_socialstream_domain_model_event.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, id, start_time, end_time, name, description, place_name, place_street, place_zip, place_city, place_country, lat, lng, picture_url, picture, page',
	),
	'types' => array(
		'1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, id, start_time, end_time, name, description, place_name, place_street, place_zip, place_city, place_country, lat, lng, picture_url, picture, page, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'),
	),
	'palettes' => array(
		'1' => array('showitem' => ''),
	),
	'columns' => array(

		'sys_language_uid' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => array(
					array('LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1),
					array('LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0)
				),
			),
		),
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'tx_socialstream_domain_model_event',
				'foreign_table_where' => 'AND tx_socialstream_domain_model_event.pid=###CURRENT_PID### AND tx_socialstream_domain_model_event.sys_language_uid IN (-1,0)',
			),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
			),
		),

		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			)
		),

		'hidden' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => array(
				'type' => 'check',
			),
		),
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
				),
			),
		),

		'id' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.id',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1,
				'eval' => 'trim'
			),
		),
		'start_time' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.start_time',
			'config' => array(
				'dbType' => 'datetime',
				'type' => 'input',
				'size' => 12,
				'eval' => 'datetime',
				'checkbox' => 0,
				'readOnly' => 1,
				'default' => '0000-00-00 00:00:00'
			),
		),
		'end_time' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.end_time',
			'config' => array(
				'dbType' => 'datetime',
				'type' => 'input',
				'size' => 12,
				'eval' => 'datetime',
				'checkbox' => 0,
				'readOnly' => 1,
				'default' => '0000-00-00 00:00:00'
			),
		),
		'name' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1,
				'eval' => 'trim'
			),
		),
		'description' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.description',
			'config' => array(
				'type' => 'text',
				'cols' => 40,
				'rows' => 15,
				'readOnly' => 1,
				'eval' => 'trim'
			)
		),
		'place_name' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.place_name',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1,
				'eval' => 'trim'
			),
		),
		'place_street' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.place_street',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1,
				'eval' => 'trim'
			),
		),
		'place_zip' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.place_zip',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1,
				'eval' => 'trim'
			),
		),
		'place_city' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.place_city',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1,
				'eval' => 'trim'
			),
		),
		'place_country' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.place_country',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1,
				'eval' => 'trim'
			),
		),
		'lat' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.lat',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1,
				'eval' => 'double2'
			)
		),
		'lng' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.lng',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1,
				'eval' => 'double2'
			)
		),
		'picture_url' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.picture_url',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'readOnly' => 1,
				'eval' => 'trim'
			),
		),
		'picture' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.picture',
			'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
				'picture',
				array(
					'appearance' => array(
						'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
						'enabledControls' => array(
							'info' => true,
							'new' => false,
							'dragdrop' => false,
							'sort' => false,
							'hide' => false,
							'delete' => false,
							'localize' => false,
						),
					),
					'foreign_types' => array(
						'0' => array(
							'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
						),
						\TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => array(
							'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
						),
						\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
							'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
						),
						\TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => array(
							'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
						),
						\TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => array(
							'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
						),
						\TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => array(
							'showitem' => '
							--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
							--palette--;;filePalette'
						)
					),
					'maxitems' => 1
				),
				$GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
			),
		),
		'page' => array(
			'exclude' => 1,
			'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_event.page',
			'config' => array(
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'tx_socialstream_domain_model_page',
				'minitems' => 0,
				'maxitems' => 1,
				'readOnly' => 1,
			),
		),

	),
);