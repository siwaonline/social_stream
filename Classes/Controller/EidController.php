<?php

namespace Socialstream\SocialStream\Controller;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Utility\EidUtility;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class EidController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * Initialize frontend environment
     * @throws \TYPO3\CMS\Core\Error\Http\ServiceUnavailableException
     */
    public function __construct()
    {
        parent::__construct();

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
        //$typoScriptFrontendController->connectToDB();
        $typoScriptFrontendController->fe_user = $feUserObj;
        $typoScriptFrontendController->id = $pageId;
        $typoScriptFrontendController->determineId();
        $typoScriptFrontendController->initTemplate();
        $typoScriptFrontendController->getConfigArray();
        //EidUtility::initTCA();
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
        if ($channelID = GeneralUtility::_GET('channel')) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_socialstream_domain_model_channel');
            $statement = $queryBuilder
                ->select('uid', 'token')
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
                                echo '<p>'.LocalizationUtility::translate('eid.success', 'social_stream').'</p>';
                            }
                        } else {
                            $http = isset($_SERVER['HTTPS']) ? "https" : "http";
                            $host = $_SERVER["HTTP_HOST"];
                            $url = $http . "://" . $host . $_SERVER["REQUEST_URI"];

                            echo "<p><a href=\"#\" onClick=\"logInWithFacebook(); return false;\">Facebookzugriff erlauben</a></p>

<script>
  logInWithFacebook = function() {
    FB.login(function(response) {
      if (response.authResponse) {
        console.log(response.authResponse);
        if(response.authResponse.accessToken){
            window.location.replace('" . $url . "&access_token=' + response.authResponse.accessToken + '&expires_in=' + response.authResponse.expiresIn);
        }
        // Now you can redirect the user or do an AJAX request to
        // a PHP script that grabs the signed request from the cookie.
      } else {
        alert('User cancelled login or did not fully authorize.');
      }
    });
    return false;
  };
  window.fbAsyncInit = function() {
    FB.init({
      appId: '" . $this->settings["fbappid"] . "',
      cookie: true, // This is important, it's not enabled by default
      version: 'v2.2'
    });
  };

  (function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {return;}
    js = d.createElement(s); js.id = id;
    js.src = \"https://connect.facebook.net/en_US/sdk.js\";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));
</script>";
                        }
                    }else{
                        echo '<p>'.LocalizationUtility::translate('eid.already', 'social_stream').'</p>';
                    }
                }else{
                    echo '<p>'.LocalizationUtility::translate('eid.notfound', 'social_stream').'</p>';
                }
            }else{
                echo '<p>'.LocalizationUtility::translate('eid.notfound', 'social_stream').'</p>';
            }
        }
        return $response;
    }
}