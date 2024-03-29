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
use Socialstream\SocialStream\Utility\Token\BaseInvolveUtility;
use Socialstream\SocialStream\Utility\Token\GooglephotosUtility;
use Socialstream\SocialStream\Utility\Token\YoutubeUtility;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Backend\Template\ModuleTemplate;

use Socialstream\SocialStream\Utility\Token\TokenUtility;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Script Class for rendering the Table Wizard
 */
class TokenController extends ActionController
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
     * @var string
     */
    protected $content = '';

    protected ModuleTemplate $moduleTempalte;
    /**
     * Constructor
     */
    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory)
    {
        // @extensionScannerIgnoreLine
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
    public function mainAction(ServerRequestInterface $request)
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($request);

        $this->main($request);
        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * Main function
     * Makes a header-location redirect to an edit form IF POSSIBLE from the passed data - otherwise the window will
     * just close.
     *
     * @return string
     * @throws \ReflectionException
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
//        if (!$this->checkEditAccess($this->P['table'], $this->P['uid'])) {
//            throw new \RuntimeException('Wizard Error: No access', 1349692692);
//        }
        // First, check the references by selecting the record:
        $row = BackendUtility::getRecord($this->P['table'], $this->P['uid']);
        if (!is_array($row)) {
            throw new \RuntimeException('Wizard Error: No reference to record', 1294587125);
        }

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('pages');
        $pageRes = $queryBuilder->select("pid")->from("pages")->where($queryBuilder->expr()->eq('uid', $this->P['pid']))->setMaxResults(1)->execute()->fetchAll();
        foreach ($pageRes as $page) {
            $pid = $page['pid'];
        }
        if ($pid <= 0) $pid = $this->P["pid"];

        $utility = TokenUtility::getUtility($row["type"], $pid);

        if (!$row['object_id']) {
            $this->moduleTemplate->setContent("Bitte geben Sie eine " . ucfirst($row['type']) . "-Seiten ID ein.");
            return;
        }

        $this->configurationManager = GeneralUtility::makeInstance(\Socialstream\SocialStream\Configuration\ConfigurationManager::class);
//        $this->configurationManager->getConcreteConfigurationManager()->setCurrentPageId($pid);
        $this->settings = $this->configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'Socialstream');

        $this->settings["storagePid"] = $this->P['pid'];

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        $redirectUrl = $uriBuilder->buildUriFromRoute(
            'wizard_token',
            ["P" => $this->P]
        );

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $base = 'https' . '://' . $_SERVER['SERVER_NAME'];
        } else {
            $base = 'http' . '://' . $_SERVER['SERVER_NAME'];
        }
        if (strpos($redirectUrl, "://") === false) {
            $redirectUrl = $base . $redirectUrl;
        }

        if ($utility instanceof YoutubeUtility || $utility instanceof GooglephotosUtility) {
            $utility->setRedirectUrl($redirectUrl);
        }

        $actualUrl = $base . $_SERVER['REQUEST_URI'];

        if($utility instanceof BaseInvolveUtility){
            $accessUrl = $utility->getAccessUrl($redirectUrl, $row["object_id"]);
        }else{
            $accessUrl = $utility->getAccessUrl($redirectUrl);
        }

        $tokenString = $utility->retrieveToken($actualUrl);
        if($utility instanceof BaseInvolveUtility){
            $objectId = $utility->retrieveObjectId($actualUrl);
        }

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
";
                if($objectId){
                    $this->content .= "
    var selectorObj = 'form[name=\"" . $this->P['formName'] . "\"] [data-formengine-input-name=\"" . str_replace("token", "object_id", $this->P['itemName']) . "\"]';
    var selectorObjHidden = 'form[name=\"" . $this->P['formName'] . "\"] [name=\"" . str_replace("token", "object_id", $this->P['itemName']) . "\"]';
";
                }
                $this->content .= "
    if (window.opener && window.opener.document && window.opener.document.querySelector(selectorTk)){
        window.opener.document.querySelector(selectorTk).value = '" . $tk . "';
        window.opener.document.querySelector(selectorTkHidden).value = '" . $tk . "';
        window.opener.document.querySelector(selectorExp).value = '" . $exp . "';
        window.opener.document.querySelector(selectorExpHidden).value = '" . $exp . "';
";
                if($objectId){
                    $this->content .= "
        window.opener.document.querySelector(selectorObj).value = '" . $objectId . "';
        window.opener.document.querySelector(selectorObjHidden).value = '" . $objectId . "';
";
                }
                $this->content .= "
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
