<?php

namespace Socialstream\SocialStream\Controller;

use Google\Auth\OAuth2;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Mail\MailMessage;
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
                        $http = isset($_SERVER['HTTPS']) ? "https" : "http";
                        $host = $_SERVER["HTTP_HOST"];
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

                                $channel = $this->getChannelFromDatabase($channelID);
                                if ($channel && array_key_exists('uid', $channel)) {
                                    $this->sendTokenGeneratedMail($channel);
                                }
                            }
                        } else if ($code = GeneralUtility::_GET('code')) {
                            $base = $http . "://" . $host;

                            $oauth2 = new OAuth2([
                                'clientId' => $this->settings["googlephotosclientid"],
                                'clientSecret' => $this->settings["googlephotosclientsecret"],
                                'authorizationUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
                                'redirectUri' => $base . "/typo3conf/ext/social_stream/GooglePhotos.php",
                                'tokenCredentialUri' => 'https://www.googleapis.com/oauth2/v4/token',
                                'scope' => ['https://www.googleapis.com/auth/photoslibrary.readonly'],
                                'state' => 'offline'
                            ]);

                            $oauth2->setCode($code);
                            $authToken = $oauth2->fetchAuthToken();

                            if ($authToken["access_token"]) {
                                $statement = $queryBuilder
                                    ->update('tx_socialstream_domain_model_channel')
                                    ->where(
                                        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($channelID, \PDO::PARAM_INT))
                                    )
                                    ->set('token', $authToken['access_token']);

                                if ($expires_in = $authToken['expires_in']) {
                                    $statement = $statement->set('expires', time() + $expires_in);
                                }
                                if ($refresh_token = $authToken['refresh_token']) {
                                    $statement = $statement->set('refresh_token', $refresh_token);
                                }
                                if ($statement->execute()) {
                                    $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Templates/Eid/Success.html';
                                    $authorizationView->setTemplatePathAndFilename($templatePathAndFilename);
                                    echo $authorizationView->render();

                                    $channel = $this->getChannelFromDatabase($channelID);
                                    if ($channel && array_key_exists('uid', $channel)) {
                                        $this->sendTokenGeneratedMail($channel);
                                    }
                                }
                            }
                        } else {
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
                                case 'googlephotos':
                                    $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Templates/Eid/Googlephotos.html';
                                    $authorizationView->setTemplatePathAndFilename($templatePathAndFilename);
                                    $authorizationView->assignMultiple(['googlephotosclientid' => $this->settings["googlephotosclientid"], 'scope' => urlencode("https://www.googleapis.com/auth/photoslibrary.readonly"), 'state' => $channelID, 'url' => $url]);
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
    protected function getChannelFromDatabase($channelID)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_socialstream_domain_model_channel');

        $statement = $queryBuilder
            ->select('uid', 'token', 'type', 'title', 'pid')
            ->from('tx_socialstream_domain_model_channel')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($channelID, \PDO::PARAM_INT))
            )
            ->setMaxResults(1)
            ->execute();

        $channel = $statement->fetch();
        return $channel;
    }

    protected function sendTokenGeneratedMail($channel)
    {
        if ($this->settings['sysmail'] && $this->settings['sendermail']) {
            /** @var \TYPO3\CMS\Fluid\View\StandaloneView $tokenGeneratedEmailView */
            $tokenGeneratedEmailView = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
            $tokenGeneratedEmailView->setFormat('html');
            $templatePathAndFilename = 'EXT:social_stream/Resources/Private/Templates/Email/TokenGenerated.html';
            $tokenGeneratedEmailView->setTemplatePathAndFilename($templatePathAndFilename);

            $tokenGeneratedEmailView->assignMultiple(['channel' => $channel]);

            $tokenGeneratedEmailContent = $tokenGeneratedEmailView->render();

            /** @var MailMessage $email */
            $email = GeneralUtility::makeInstance(MailMessage::class);

            $email->setFrom($this->settings['sendermail'])->setTo($this->settings['sysmail'])->setSubject('Social Stream Token generated')->setBody($tokenGeneratedEmailContent, 'text/html')->send();
        }
    }
}