<?php

namespace Socialstream\SocialStream\Utility\Feed;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Socialstream\SocialStream\Domain\Model\News;
use Socialstream\SocialStream\Utility\BaseUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use Sabre\HTTP;
use Sabre\DAV\Sharing;

/**
 * FacebookUtility
 */
class NextcloudUtility extends \Socialstream\SocialStream\Utility\Feed\FeedUtility
{
    protected $properties = array(
        '{DAV:}displayname',
        '{DAV:}resourcetype',
        '{DAV:}creationdate',
        '{DAV:}creationtime',
        '{DAV:}creationdate',
        '{DAV:}creationDateTime',
        '{DAV:}getetag',
        '{DAV:}getcontenttype',
        '{DAV:}permissions',
        '{DAV:}comments-unread',
        '{DAV:}lockdiscovery',
        '{DAV:}getcontentlength',
        '{DAV:}getlastmodified',
        '{http://owncloud.org/ns}share-types',
        '{http://owncloud.org/ns}comments-unread',
        '{http://owncloud.org/ns}comments-count',
        '{http://owncloud.org/ns}comments-href',
        '{http://owncloud.org/ns}owner-id',
        '{http://owncloud.org/ns}owner-display-name',
        '{http://owncloud.org/ns}id',
        '{http://owncloud.org/ns}message',
        '{http://owncloud.org/ns}fileid',
        '{http://owncloud.org/ns}favorite',
        '{http://owncloud.org/ns}creationDateTime',
        '{http://owncloud.org/ns}checksums',
        '{http://owncloud.org/ns}has-preview'

    );

    protected $client;
    protected $davurl = "";
    protected $baseUri = "";

    public function getChannel(\Socialstream\SocialStream\Domain\Model\Channel $channel, $isProcessing = 0)
    {
        return $channel;
    }

    /**
     * @param \Socialstream\SocialStream\Domain\Model\Channel $channel
     * @return \Socialstream\SocialStream\Domain\Model\Channel
     */
    public function renewToken(\Socialstream\SocialStream\Domain\Model\Channel $channel)
    {
        return $channel;
    }

    public function getFeed(\Socialstream\SocialStream\Domain\Model\Channel $channel, $limit = 100)
    {
        $this->baseUri = $this->endsWith($this->settings['webdavrooturl'], '/') ? $this->settings['webdavrooturl'] : $this->settings['webdavrooturl'] . '/';
        $webdavuri = $this->baseUri . 'remote.php/webdav';
        $settings = array(
            'baseUri' => $webdavuri,
            'userName' => $this->settings['webdavuser'],
            'password' => $this->settings['webdavpw'],
        );
        $this->client = new \Sabre\DAV\Client($settings);

        $folder = $channel->getObjectId();

        $folderName = ($this->startsWith($folder, '/') ? $folder : '/' . $folder);
        $url = str_replace(" ", "%20", $webdavuri . $folderName);
        $publicLinkuri = $this->getPublicUrlOfFolder($url);

        $folderDepth = $this->settings['webdavdepth'] ?? 1;
        $timestampOffset = $this->settings['webdavtimestampoffset'] ?? '-1 day';

        if ($publicLinkuri !== null) {
            $currentYear = date("Y");

            // urls
            $urls = [
                [
                    'url' => $this->endsWith($url, '/') ? $url . $currentYear : $url . '/' . $currentYear, // this year
                    'archiv' => false,
                    'timestamp' => $timestampOffset
                ],
                [
                    'url' => $this->endsWith($url, '/') ? $url . 'Archiv' : $url . '/Archiv', // archiv
                    'archiv' => true,
                    'timestamp' => $timestampOffset
                ],
                [
                    'url' => $this->endsWith($url, '/') ? $url . (intval($currentYear) - 1) : $url . '/' . (intval($currentYear) - 1), // this year
                    'archiv' => false,
                    'timestamp' => $timestampOffset
                ]
            ];


            foreach($urls as $urlData){
                try{
                    $dirs = $this->client->propFind($urlData['url'], $this->properties, $folderDepth);
                    foreach ($dirs as $dirname => $dir) {
                        $this->checkGalleryFolderAndCreateNews($urlData['url'], $dir, $dirname, $channel, $publicLinkuri, $folderName, $folderDepth, $urlData['timestamp'], $urlData['archiv']);
                    }
                }catch (HTTP\ClientHttpException $e){
                    if($e->getCode() === 404){
                        BaseUtility::log(__CLASS__, 'error', $e->getMessage() . ' - ' . $urlData['url']);
                    }else{
                        throw $e;
                    }
                }
            }


            // delete old data
            // urls
            $urls = [
                [
                    'url' => $this->endsWith($url, '/') ? $url . $currentYear : $url . '/' . $currentYear, // this year
                    'data' => $this->newsRepository->findAllRawByCurrentYearFolder($channel)
                ],
                [
                    'url' => $this->endsWith($url, '/') ? $url . 'Archiv' : $url . '/Archiv', // archiv
                    'data' => $this->newsRepository->findAllRawByArchivFolder($channel)
                ],
                [
                    'url' => $this->endsWith($url, '/') ? $url . (intval($currentYear) - 1) : $url . '/' . (intval($currentYear) - 1), // this year
                    'data' => $this->newsRepository->findAllRawByLastYearFolder($channel)
                ]
            ];
            foreach($urls as $urlData){
                $dataToDelete = $urlData['data'];
                try{
                    $dirs = $this->client->propFind($urlData['url'], $this->properties, $folderDepth);
                    foreach ($dirs as $dirname => $dir) {
                        if ($dirname != '/remote.php/webdav' . $folderName . '/') {
                            if (!empty($dir['{DAV:}resourcetype']) && $dir['{DAV:}resourcetype']->getValue()[0] == '{DAV:}collection') {
                                $objectId = $dir['{http://owncloud.org/ns}id'];
                                $channelUid = $channel->getUid();
                                $objectIdColumn = array_column($dataToDelete, 'object_id');
                                $channelColumn = array_column($dataToDelete, 'channel');
                                if(in_array($objectId, $objectIdColumn) && in_array($channelUid, $channelColumn)){
                                    $index = array_search($objectId, $objectIdColumn);
                                    array_splice($dataToDelete, $index, 1);
                                }
                            }
                        }
                    }

                    foreach($dataToDelete as $news){
                        $this->deleteNews($news['uid']);
                    }
                }catch (HTTP\ClientHttpException $e){
                    if($e->getCode() === 404){
                        BaseUtility::log(__CLASS__, 'error', $e->getMessage() . ' - ' . $urlData['url']);
                    }else{
                        throw $e;
                    }
                }
            }
        }
    }



    private function checkGalleryFolderAndCreateNews($url, $dir, $dirname, $channel, $publicLinkuri, $folderName, $folderDepth, $timestampOffset = -1, $archiv = false) {
        $regex = '/\B(\/[a-zA-Z0-9%]*\/[a-zA-Z0-9%]*\/)\B/';
        if ($dirname != '/remote.php/webdav' . $folderName . '/' &&
            rtrim($dirname, '/') != parse_url($url, PHP_URL_PATH)
            && preg_match($regex, substr($dirname, strlen('/remote.php/webdav' . $folderName)))// check only the first level of folder as images nested deeper can already be found
        ) {
            if (
                !empty($dir['{DAV:}resourcetype'])
                && $dir['{DAV:}resourcetype']->getValue()[0] == '{DAV:}collection'
                && strtotime($dir['{DAV:}getlastmodified']) >= strtotime($timestampOffset)
            ) {
                $fileurl = $this->baseUri . substr($dirname, 1);
                $files = $this->client->propFind($fileurl, $this->properties, $folderDepth);
                $directoryId = $dir['{http://owncloud.org/ns}fileid'];
                $globalDirectoryId = $dir['{http://owncloud.org/ns}id'];
                $oldestTimestamp = $this->findOldestTimestamp($files);
                foreach ($files as $filename => $file) {
                    if (key_exists('{DAV:}getcontenttype', $file) && $this->startsWith($file['{DAV:}getcontenttype'], 'image')) {
                        $this->createNewsBlog($filename, $channel, $dirname, $directoryId, $globalDirectoryId, $publicLinkuri, $oldestTimestamp, $folderName, $archiv);
                        break;
                    }
                }
            }
        }
    }

    function findOldestTimestamp($files)
    {
        $date = null;
        foreach ($files as $file) {
            if (empty($date) || $date > $file['{DAV:}getlastmodified']) {
                $date = $file['{DAV:}getlastmodified'];
            }
        }
        return $date;
    }

    function getDirectoryComment($directoryId)
    {
        $url = $this->baseUri . 'remote.php/dav/comments/files/' . $directoryId;
        $comments = $this->client->propFind($url, $this->properties, 1);
        $comment = null;
        foreach ($comments as $c) {
            if (key_exists('{http://owncloud.org/ns}message', $c) && $c['{http://owncloud.org/ns}message'] && key_exists('{http://owncloud.org/ns}creationDateTime', $c)) {
                if (empty($comment)) {
                    $comment['crdate'] = $c['{http://owncloud.org/ns}creationDateTime'];
                    $comment['text'] = $c['{http://owncloud.org/ns}message'];

                } else if ($comment['crdate'] < $c['{http://owncloud.org/ns}creationDateTime']) {
                    $comment['crdate'] = $c['{http://owncloud.org/ns}creationDateTime'];
                    $comment['text'] = $c['{http://owncloud.org/ns}message'];
                }
            }
        }
        return $comment;
    }

    function createNewsBlog($filename, $channel, $dirname, $directoryId, $globalDirectoryId, $linkuri, $oldestTimestamp, $folderName, $archiv)
    {
        $folderName = $this->endsWith($folderName, '/') ? $folderName : $folderName.'/';
        $tmpArr = explode($folderName, $dirname);
        $linkuri.='?path='.end($tmpArr) . '#view-grid';
        $dirArray = explode('/', $dirname);

        $new = 0;
        $news = $this->newsRepository->findHiddenById($globalDirectoryId, $channel->getUid(), 1);
        if (!$news) {
            $news = new \Socialstream\SocialStream\Domain\Model\News();
            $new = 1;
        }

        $news->setType(0);
        $news->setChannel($channel);
        $news->setObjectId($globalDirectoryId);
        $news->setLink($linkuri);
        $news->setTitle(urldecode($dirArray[sizeof($dirArray) - 2]));
        if($archiv) {
            $this->addArchiveCategory($news);
        }

        $bodytext = $this->getDirectoryComment($directoryId);
        if (!empty($bodytext)) {
            $news->setBodytext($bodytext['text']);
        }

        if (!$news->getPathSegment()) $news->setPathSegment($this->getSlug($news->getUid(), $news->getTitle(), $channel));


        $news->setPid($channel->getPid());
        $news->setDatetime(strtotime($oldestTimestamp));
        $imageUrl = $this->baseUri . substr($filename, 1);

        if ($new) {
            $this->newsRepository->add($news);
        } else {
            $this->newsRepository->update($news);
        }

        $this->persistenceManager->persistAll();
        $this->processNewsMedia($news, $imageUrl);


        $this->newsRepository->update($news);
        $this->persistenceManager->persistAll();
    }

    function endsWith($string, $endString)
    {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }

    function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    /**
     * @param string $url
     * @param $model
     * @return array
     */
    function grabImage($url, $model)
    {

        $grabedImage = [];

        $curl = curl_init();
        $encodedAuth = base64_encode($this->settings['webdavuser'] . ":" . $this->settings['webdavpw']);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . $encodedAuth,
                "Cache-Control: no-cache"
            ),
        ));


        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            BaseUtility::log(__CLASS__, "error", "Entry " . $model->getObjectId() . " got CURL Error: " . $err);
        } else {
            $grabedImage = array(
                'imageName' => $model->getObjectId() . "." . end(explode('/', $url)),
                'image' => $response
            );
        }
        curl_close($curl);
        return $grabedImage;
    }

    function getPublicUrlOfFolder($dirname)
    {
        $url = $this->baseUri . 'ocs/v1.php/apps/files_sharing/api/v1/shares?format=json&path=' . end(explode('webdav', $dirname));
        $encodedAuth = base64_encode($this->settings['webdavuser'] . ":" . $this->settings['webdavpw']);
        $publicUrl = null;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic " . $encodedAuth,
                "OCS-APIRequest: true",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            BaseUtility::log(__CLASS__, "error", "Folder " . end(explode('webdav', $dirname)) . " got CURL Error: " . $err);
        } else {
            $responseArray = json_decode($response);
            $publicUrl = $responseArray->ocs->data[0]->url;
        }
        curl_close($curl);
        return $publicUrl;
    }

    /**
     * @param News|int $news
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    function deleteNews($news){
        if(is_numeric($news)){
            $news = $this->newsRepository->findByUid($news);
        }

        if($news instanceof News){
            $this->newsRepository->remove($news);
            $this->persistenceManager->persistAll();
        }
    }

    function addArchiveCategory($news) {
        if($this->settings && is_array($this->settings) && key_exists('webdavarchivecategory', $this->settings)){
            $categoryUid = intval($this->settings['webdavarchivecategory']);
            if($categoryUid){
                $category = $this->categoryRepository->findByUid($categoryUid);
                if($category){
                    $news->addCategory($category);
                }
            }
        }
    }
}
