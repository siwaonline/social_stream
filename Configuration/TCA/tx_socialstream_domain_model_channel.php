<?php

return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel',
        'label' => 'object_id',
        'label_userFunc' => 'Socialstream\\SocialStream\\Userfuncs\\Label->getPageLabel',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => TRUE,
        'versioningWS' => 2,
        'versioning_followPages' => TRUE,

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ),
        'searchFields' => 'object_id,title,about,description,user,type,link,image,token,refresh_token,expires,news,',
        'iconfile' => 'extensions-social-stream-empty',
        'typeicon_column' => 'type',
        'typeicon_classes' => array(
            'facebook' => 'extensions-social-stream-facebook',
            'facebookevent' => 'extensions-social-stream-facebook',
            'instagram' => 'extensions-social-stream-instagram',
            'youtube' => 'extensions-social-stream-youtube',
            'twitter' => 'extensions-social-stream-twitter',
            'xing' => 'extensions-social-stream-xing',
            'linkedin' => 'extensions-social-stream-linkedin',
            'flickr' => 'extensions-social-stream-flickr',
            'soundcloud' => 'extensions-social-stream-soundcloud',
            'default' => 'extensions-social-stream-empty',
        ),
        'security' => [
            'ignorePageTypeRestriction' => true
        ]
    ),
    'interface' => array(
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, object_id, title, about, description, type, posttype, videosync, link, image, token, refresh_token, expires',
    ),
    'types' => array(
        '1' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, --palette--;;paletteTitle, --palette--;;paletteAbout, --palette--;;paletteType, image, --palette--;;paletteToken, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access, starttime, endtime'),
    ),
    'palettes' => array(
        'paletteTitle' => array(
            'showitem' => 'object_id,title,',
        ),
        'paletteAbout' => array(
            'showitem' => 'about,description',
        ),
        'paletteType' => array(
            'showitem' => 'type, posttype, videosync, link',
        ),
        'paletteToken' => array(
            'showitem' => 'token,refresh_token,expires,eid_url',
        ),
    ),
    'columns' => array(

        'sys_language_uid' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => array(
                    array(
                        'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ),
                ),
                'default' => 0,
            )
        ),
        'l10n_parent' => array(
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('', 0),
                ),
                'foreign_table' => 'tx_socialstream_domain_model_channel',
                'foreign_table_where' => 'AND tx_socialstream_domain_model_channel.pid=###CURRENT_PID### AND tx_socialstream_domain_model_channel.sys_language_uid IN (-1,0)',
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
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => array(
                'type' => 'check',
            ),
        ),
        'starttime' => array(
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
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
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
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

        'object_id' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.object_id',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ),
        ),
        'title' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.title',
            'displayCond' => array(
                'OR' => array(
                    'FIELD:token:REQ:TRUE',
                    'FIELD:title:REQ:TRUE'
                )
            ),
            'config' => array(
                'type' => 'input',
                'size' => 30,

                'eval' => 'trim'
            ),
        ),
        'about' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.about',
            'displayCond' => array(
                'OR' => array(
                    'FIELD:token:REQ:TRUE',
                    'FIELD:about:REQ:TRUE'
                )
            ),
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'description' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.description',
            'displayCond' => array(
                'OR' => array(
                    'FIELD:token:REQ:TRUE',
                    'FIELD:description:REQ:TRUE'
                )
            ),
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'user' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.user',
            'displayCond' => array(
                'OR' => array(
                    'FIELD:token:REQ:TRUE',
                    'FIELD:user:REQ:TRUE'
                )
            ),
            'config' => array(
                'type' => 'check',
                'default' => 0
            )
        ),
        'type' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.type',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => \Socialstream\SocialStream\Utility\BaseUtility::getTypesTCA(),
                'size' => 1,
                'maxitems' => 1,
                'eval' => ''
            ),
        ),
        'posttype' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.posttype',
            'displayCond' => array(
                'OR' => array(
                    'FIELD:type:=:facebook',
                    'FIELD:type:=:twitter',
                )
            ),
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => array(
                    array('LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.posttype.0', 0),
                    array('LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.posttype.1', 1)
                ),
                'size' => 1,
                'maxitems' => 1,
                'eval' => ''
            ),
        ),
        'videosync' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.videosync',
            'displayCond' => array(
                'OR' => array(
                    'FIELD:type:=:facebook',
                    'FIELD:type:=:instagram'
                )
            ),
            'config' => array(
                'type' => 'check',
                'default' => 1
            ),
        ),
        'link' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.link',
            'displayCond' => array(
                'OR' => array(
                    'FIELD:token:REQ:TRUE',
                    'FIELD:link:REQ:TRUE'
                )
            ),
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'image' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.image',
            'displayCond' => array(
                'OR' => array(
                    'FIELD:token:REQ:TRUE',
                    'FIELD:image:REQ:TRUE'
                )
            ),
            'config' => [
                'type' => 'file',
                'appearance' => array(
                    'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
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
                'maxitems' => 1,
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ],
        ),
        'token' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.token',
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'fieldControl' => array(
                    'token' => array(
                        'renderType' => 'tokenNode',
                        'iconIdentifier' => 'actions-system-extension-configure'
                    ),
                )
            ),
        ),
        'refresh_token' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.refresh_token',
            'displayCond' => [
                'OR' => [
                    'FIELD:type:=:youtube',
                    'FIELD:type:=:googlephotos',
                ],
            ],
            'config' => array(
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ),
        ),
        'expires' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.expires',
            'config' => array(
                'type' => 'input',
                'size' => 4,
                'eval' => 'int,trim'
            )
        ),
        'eid_url' => array(
            'exclude' => 1,
            'label' => 'Frontend Token URL (click to copy)',
            'config' => array(
                'type' => 'user',
                'renderType' => 'eidNode'
            ),
            'displayCond' => 'FIELD:object_id:REQ:TRUE',
        ),
        'news' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:social_stream/Resources/Private/Language/locallang_db.xlf:tx_socialstream_domain_model_channel.news',
            'config' => array(
                'type' => 'inline',
                'foreign_table' => 'tx_news_domain_model_news',
                'foreign_field' => 'channel',
                'maxitems' => 9999,
                'appearance' => array(
                    'collapseAll' => 0,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ),
            ),

        ),

    ),
);
