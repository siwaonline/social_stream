<?php

namespace Socialstream\SocialStream\Nodes;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Script Class for rendering the Table Wizard
 */
class TokenNode extends AbstractNode
{
    public function render()
    {
        $languageService = $this->getLanguageService();
        $options = $this->data['renderData']['fieldControlOptions'];

        $parameterArray = $this->data['parameterArray'];
        $itemName = $parameterArray['itemFormElName'];
        $windowOpenParameters = $options['windowOpenParameters'] ?? 'height=700,width=1000,status=0,menubar=0,scrollbars=1';

        $flexFormDataStructureIdentifier = $this->data['flexFormDataStructureIdentifier'] ?? '';
        $flexFormDataStructurePath = '';
        if (!empty($flexFormDataStructureIdentifier)) {
            if (empty($this->data['flexFormContainerName'])) {
                // simple flex form element
                $flexFormDataStructurePath = 'sheets/'
                    . $this->data['flexFormSheetName']
                    . '/ROOT/el/'
                    . $this->data['flexFormFieldName']
                    . '/TCEforms/config';
            } else {
                // flex form section container element
                $flexFormDataStructurePath = 'sheets/'
                    . $this->data['flexFormSheetName']
                    . '/ROOT/el/'
                    . $this->data['flexFormFieldName']
                    . '/el/'
                    . $this->data['flexFormContainerName']
                    . '/el/'
                    . $this->data['flexFormContainerFieldName']
                    . '/TCEforms/config';
            }
        }

        $urlParameters = [
            'P' => [
                'table' => $this->data['tableName'],
                'field' => $this->data['fieldName'],
                'formName' => 'editform',
                'flexFormDataStructureIdentifier' => $flexFormDataStructureIdentifier,
                'flexFormDataStructurePath' => $flexFormDataStructurePath,
                'hmac' => GeneralUtility::hmac('editform' . $itemName, 'wizard_js'),
                'fieldChangeFunc' => $parameterArray['fieldChangeFunc'],
                'fieldChangeFuncHash' => GeneralUtility::hmac(serialize($parameterArray['fieldChangeFunc'])),
            ],
        ];
        $uriBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Routing\UriBuilder::class);
        $url = (string)$uriBuilder->buildUriFromRoute('wizard_token', $urlParameters);

        $onClick = [];
        $onClick[] = 'this.blur();';
        $onClick[] = 'if (\'\' == \'' . $this->data["databaseRow"]["object_id"] . '\') {';
        $onClick[] =    'top.TYPO3.Modal.confirm(';
        $onClick[] =        '\'' . $languageService->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:warning.header') . '\',';
        $onClick[] =        '\'' . $languageService->sL('LLL:EXT:social_stream/Resources/Private/Language/locallang.xlf:labels.pleaseSave') . '\',';
        $onClick[] =        'top.TYPO3.Severity.notice, [{text: TYPO3.lang[\'button.ok\'] || \'OK\', btnClass: \'btn-notice\', name: \'ok\'}]';
        $onClick[] =    ')';
        $onClick[] =    '.on(\'button.clicked\', function(e) {';
        $onClick[] =        'if (e.target.name == \'ok\') { top.TYPO3.Modal.dismiss(); }}';
        $onClick[] =    ');';
        $onClick[] =    'return false;';
        $onClick[] = '}';
        $onClick[] = 'vHWin=window.open(';
        $onClick[] =    GeneralUtility::quoteJSvalue($url);
        $onClick[] =    '+\'&P[uid]=' . $this->data["databaseRow"]["uid"];
        $onClick[] =    '&P[pid]=' . $this->data["databaseRow"]["pid"];
        $onClick[] =    '&P[itemName]=' . $this->data["parameterArray"]["itemFormElName"];
        $onClick[] =    '&P[returnUrl]=\'+' . GeneralUtility::quoteJSvalue($url);
        $onClick[] =    ',\'\',';
        $onClick[] =    GeneralUtility::quoteJSvalue($windowOpenParameters);
        $onClick[] = ');';
        $onClick[] = 'vHWin.focus();';
        $onClick[] = 'return false;';

        $result = [
            'iconIdentifier' => 'actions-system-extension-configure',
            'title' => 'Token Wizard',
            'linkAttributes' => [
                'data-id' => $this->data['databaseRow']['uid'],
                'onClick' => implode('', $onClick),
            ]
        ];
        return $result;
    }
    /**
     * Returns an instance of LanguageService
     *
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
