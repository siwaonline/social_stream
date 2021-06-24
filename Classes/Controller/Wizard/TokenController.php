<?php

namespace Socialstream\SocialStream\Controller\Wizard;

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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Socialstream\SocialStream\Utility\Token\FacebookinvolveUtility;
use Socialstream\SocialStream\Utility\Token\YoutubeUtility;
use TYPO3\CMS\Core\Database\RelationHandler;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

use Socialstream\SocialStream\Utility\Token\TokenUtility;

/**
 * Script Class for rendering the Table Wizard
 */
class TokenController extends \TYPO3\CMS\Backend\Controller\Wizard\AbstractWizardController
{
    /**
     * Wizard parameters, coming from FormEngine linking to the wizard.
     *
     * @var array
     */
    public $P;

    /**
     * Boolean; if set, the window will be closed by JavaScript
     *
     * @var int
     */
    public $doClose;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->getLanguageService()->includeLLFile('EXT:lang/locallang_wizards.xlf');
        $GLOBALS['SOBE'] = $this;

        $this->init();
    }

    /**
     * Initialization of the script
     *
     * @return void
     */
    protected function init()
    {
        $this->P = GeneralUtility::_GP('P');
        // Used for the return URL to FormEngine so that we can close the window.
        $this->doClose = GeneralUtility::_GP('doClose');
    }

    /**
     * Injects the request object for the current request or subrequest
     * As this controller goes only through the main() method, it is rather simple for now
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function mainAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->main($request);
        $response->getBody()->write($this->moduleTemplate->renderContent());
        return $response;
    }

    /**
     * Main function
     * Makes a header-location redirect to an edit form IF POSSIBLE from the passed data - otherwise the window will
     * just close.
     *
     * @return string
     */
    public function main($request)
    {
        if ($this->doClose) {
            return $this->closeWindow;
        }
        if (!is_numeric($this->P['uid'])) {
            $this->moduleTemplate->setContent("Bitte speichern Sie zuerst.");
            return;
        }
        if (!$this->checkEditAccess($this->P['table'], $this->P['uid'])) {
            throw new \RuntimeException('Wizard Error: No access', 1349692692);
        }
        // First, check the references by selecting the record:
        $row = BackendUtility::getRecord($this->P['table'], $this->P['uid']);
        if (!is_array($row)) {
            throw new \RuntimeException('Wizard Error: No reference to record', 1294587125);
        }

        $pageRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery("pid", "pages", "uid=" . $this->P['pid']);
        while ($pageRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($pageRes)) {
            $pid = $pageRow['pid'];
        }
        if ($pid <= 0) $pid = $this->P["pid"];

        $utility = TokenUtility::getUtility($row["type"], $pid);

        if (!$row['object_id']) {
            $this->moduleTemplate->setContent("Bitte geben Sie eine " . ucfirst($row['type']) . "-Seiten ID ein.");
            return;
        }

        TokenUtility::initTSFE($pid, 0);
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->configurationManager = $this->objectManager->get(\Socialstream\SocialStream\Configuration\ConfigurationManager::class);
        $this->configurationManager->getConcreteConfigurationManager()->setCurrentPageId($pid);
        $this->settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Socialstream');
        /*
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
        $this->configurationManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        $this->settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Socialstream');
        */
        $this->settings["storagePid"] = $this->P['pid'];


        $redirectUrl = BackendUtility::getModuleUrl(
            'wizard_token',
            array("P" => $this->P),
            false,
            true
        );

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $base = 'https' . '://' . $_SERVER['SERVER_NAME'];
        } else {
            $base = 'http' . '://' . $_SERVER['SERVER_NAME'];
        }
        if (strpos($redirectUrl, "://") === false) {
            $redirectUrl = $base . $redirectUrl;
        }

        if ($utility instanceof YoutubeUtility) {
            $utility->setRedirectUrl($redirectUrl);
        }

        $actualUrl = $base . $_SERVER['REQUEST_URI'];

        if($utility instanceof FacebookinvolveUtility){
            $accessUrl = $utility->getAccessUrl($redirectUrl, $row["object_id"]);
        }else{
            $accessUrl = $utility->getAccessUrl($redirectUrl);
        }

        $tokenString = $utility->retrieveToken($actualUrl);

        if (!$tokenString) {
            //header('Location: '.$accessUrl);
            $this->content .= $utility->getTokenJavascript($accessUrl, $actualUrl);
        } else {
            $res = $utility->getValues($tokenString);
            $tk = $res["tk"];
            $exp = $res["exp"];
            if ($utility instanceof YoutubeUtility) {
                $rf_tk = $res["rf_tk"];
                $this->content .= "
<script>
    var selectorTk = 'form[name=\"" . $this->P['formName'] . "\"] [data-formengine-input-name=\"" . $this->P['itemName'] . "\"]';
    var selectorTkHidden = 'form[name=\"" . $this->P['formName'] . "\"] [name=\"" . $this->P['itemName'] . "\"]';
    var selectorRfTk = 'form[name=\"" . $this->P['formName'] . "\"] [data-formengine-input-name=\"" . str_replace("token", "refresh_token", $this->P['itemName']) . "\"]';
    var selectorRfTkHidden = 'form[name=\"" . $this->P['formName'] . "\"] [name=\"" . str_replace("token", "refresh_token", $this->P['itemName']) . "\"]';
    var selectorExp = 'form[name=\"" . $this->P['formName'] . "\"] [data-formengine-input-name=\"" . str_replace("token", "expires", $this->P['itemName']) . "\"]';
    var selectorExpHidden = 'form[name=\"" . $this->P['formName'] . "\"] [name=\"" . str_replace("token", "expires", $this->P['itemName']) . "\"]';
    if (window.opener && window.opener.document && window.opener.document.querySelector(selectorTk)){
        window.opener.document.querySelector(selectorTk).value = '" . $tk . "';
        window.opener.document.querySelector(selectorTkHidden).value = '" . $tk . "';
        window.opener.document.querySelector(selectorRfTk).value = '" . $rf_tk . "';
        window.opener.document.querySelector(selectorRfTkHidden).value = '" . $rf_tk . "';
        window.opener.document.querySelector(selectorExp).value = '" . $exp . "';
        window.opener.document.querySelector(selectorExpHidden).value = '" . $exp . "';
        close();
    } else {
        alert('Got Token, but cant write to window - Youtube');
    }

</script>
";
            } else {
                $this->content .= "
<script>
    var selectorTk = 'form[name=\"" . $this->P['formName'] . "\"] [data-formengine-input-name=\"" . $this->P['itemName'] . "\"]';
    var selectorTkHidden = 'form[name=\"" . $this->P['formName'] . "\"] [name=\"" . $this->P['itemName'] . "\"]';
    var selectorExp = 'form[name=\"" . $this->P['formName'] . "\"] [data-formengine-input-name=\"" . str_replace("token", "expires", $this->P['itemName']) . "\"]';
    var selectorExpHidden = 'form[name=\"" . $this->P['formName'] . "\"] [name=\"" . str_replace("token", "expires", $this->P['itemName']) . "\"]';
    if (window.opener && window.opener.document && window.opener.document.querySelector(selectorTk)){
        window.opener.document.querySelector(selectorTk).value = '" . $tk . "';
        window.opener.document.querySelector(selectorTkHidden).value = '" . $tk . "';
        window.opener.document.querySelector(selectorExp).value = '" . $exp . "';
        window.opener.document.querySelector(selectorExpHidden).value = '" . $exp . "';
        close();
    } else {
        alert('Got Token, but cant write to window');
    }

</script>
";
            }
        }


        // Build the <body> for the module
        $this->moduleTemplate->setContent($this->content);
    }
}
