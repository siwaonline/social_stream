<?php

namespace Socialstream\SocialStream\Controller;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Utility\EidUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class EidController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ConfigurationManager
     */
    protected $configurationManager;

    /**
     * Initialize frontend environment
     * @throws \TYPO3\CMS\Core\Error\Http\ServiceUnavailableException
     */
    public function __construct()
    {
        parent::__construct();

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->configurationManager = $this->objectManager->get(ConfigurationManager::class);

        $feUserObj = EidUtility::initFeUser();
        $pageId = GeneralUtility::_GET('id') ?: 1;
        $typoScriptFrontendController = GeneralUtility::makeInstance(
            'TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController',
            $GLOBALS['TYPO3_CONF_VARS'],
            $pageId,
            0,
            true
        );
        $GLOBALS['TSFE'] = $typoScriptFrontendController;
        $typoScriptFrontendController->fe_user = $feUserObj;
        $typoScriptFrontendController->id = $pageId;
        $typoScriptFrontendController->determineId();
        $typoScriptFrontendController->initTemplate();
        $typoScriptFrontendController->getConfigArray();
        $typoScriptService = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\TypoScriptService');
        $pluginConfiguration = $typoScriptService->convertTypoScriptArrayToPlainArray($typoScriptFrontendController->tmpl->setup['module.']['tx_socialstream.']);

        $this->settings = $pluginConfiguration['settings'];
    }

    /**
     * get titles from database
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function generateTokenAction(ServerRequestInterface $request, ResponseInterface $response)
    {
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $emailView */
        $authorizationView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');

        $authorizationView->setFormat('html');

        if ($channelID = GeneralUtility::_GET('channel')) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_socialstream_domain_model_channel');
            $statement = $queryBuilder
                ->select('uid', 'token', 'type')
                ->from('tx_socialstream_domain_model_channel')
                ->where(
                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($channelID, \PDO::PARAM_INT))
                )
                ->setMaxResults(1)
                ->execute();

            $result = $statement->fetch();

            if (!empty($result)) {
                if (array_key_exists('token', $result)) {
                    if (empty($result['token'])) {
                        if ($access_token = GeneralUtility::_GET('access_token')) {
                            $statement = $queryBuilder
                                ->update('tx_socialstream_domain_model_channel')
                                ->where(
                                    $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($channelID, \PDO::PARAM_INT))
                                )
                                ->set('token', $access_token);
                            if ($expires_in = GeneralUtility::_GET('expires_in')) {
                                $statement = $statement->set('expires', time() + $expires_in);
                            }
                            if ($statement->execute()) {
                                $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Templates/Eid/Success.html';
                                $authorizationView->setTemplatePathAndFilename($templatePathAndFilename);
                                echo $authorizationView->render();
                            }
                        } else {
                            $http = isset($_SERVER['HTTPS']) ? "https" : "http";
                            $host = $_SERVER["HTTP_HOST"];
                            $url = $http . "://" . $host . $_SERVER["REQUEST_URI"];
                            switch ($result['type']) {
                                case 'instagram':
                                    $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Templates/Eid/Instagram.html';
                                    $authorizationView->setTemplatePathAndFilename($templatePathAndFilename);
                                    $authorizationView->assignMultiple(['instaappid' => $this->settings["instaappid"], 'url' => $url]);
                                    echo $authorizationView->render();
                                    break;
                                case 'facebook':
                                    $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Templates/Eid/Facebook.html';
                                    $authorizationView->setTemplatePathAndFilename($templatePathAndFilename);
                                    $authorizationView->assignMultiple(['fbappid' => $this->settings["fbappid"], 'url' => $url]);
                                    echo $authorizationView->render();
                                    break;
                                default:
                                    $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Templates/Eid/TokenNotFound.html';
                                    $authorizationView->setTemplatePathAndFilename($templatePathAndFilename);
                                    echo $authorizationView->render();
                                    break;
                            }
                        }
                    } else {
                        $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Templates/Eid/AlreadyGranted.html';
                        $authorizationView->setTemplatePathAndFilename($templatePathAndFilename);
                        echo $authorizationView->render();
                    }
                } else {
                    $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Templates/Eid/TokenNotFound.html';
                    $authorizationView->setTemplatePathAndFilename($templatePathAndFilename);
                    echo $authorizationView->render();
                }
            } else {
                $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Templates/Eid/TokenNotFound.html';
                $authorizationView->setTemplatePathAndFilename($templatePathAndFilename);
                echo $authorizationView->render();
            }
        }
        return $response;
    }
}