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

use Socialstream\SocialStream\Utility\BaseUtility;
use TYPO3\CMS\Core\Resource\Folder;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * FeedUtility
 */
class FeedUtility extends \Socialstream\SocialStream\Utility\BaseUtility
{
    /**
     * Emoji begin string, to be searched and replaced (must be 8 chars long!)
     *
     * @var array
     */
    public $clearStrings = array('\ud83c\u', '\ud83d\u', '\ud83e\u', '\u2600\u');

    /**
     * __construct
     */
    public function __construct($pid = 0)
    {
        parent::__construct();
        if ($pid) {
            //$this->initTSFE($pid, 0);
            $this->initSettings($pid);
        }
    }

    public static function initTSFE($id = 1, $typeNum = 0)
    {
        parent::initTSFE($id, $typeNum);
    }

    public function initSettings($pid)
    {
        parent::initSettings($pid);
    }

    /**
     * @param $type
     * @param int $pid
     * @return mixed
     */
    public static function getUtility($type, $pid = 0)
    {
        $classname = "\\Socialstream\\SocialStream\\Utility\\Feed\\" . ucfirst($type) . "Utility";
        return new $classname($pid);
    }

    /**
     * @param $url
     * @return mixed
     */
    public function getElems($url, $associative = false)
    {
        $elems = file_get_contents($url);
        $elems = $this->clearString($elems);
        return json_decode($elems, $associative);
    }

    /**
     * @param string $url
     * @return mixed
     * @throws \Exception
     */
    public function getElemsCurl($url)
    {
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
                "Cache-Control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            throw new \Exception("cURL Error #:" . $err);
        } else {
            return json_decode($response);
        }
    }

    /**
     * @param $elems
     * @return mixed
     */
    public function clearString($elems)
    {
        foreach ($this->clearStrings as $str) {
            while (strpos($elems, $str) !== false) {
                $pos = strpos($elems, $str);
                $elems = substr_replace($elems, '', $pos, 12);
            }
        }
        return $elems;
    }

    /**
     * @return mixed
     */
    protected function getStorage()
    {
        $storageRepository = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\StorageRepository');
        return $storageRepository->findByUid($this->settings["storage"]);
    }

    /**
     * @return mixed
     */
    protected function getMainFolder()
    {
        $storage = $this->getStorage();
        if ($storage->hasFolder($this->settings["folder"])) {
            $folder = $storage->getFolder($this->settings["folder"]);
        } else {
            $folder = $storage->createFolder($this->settings["folder"]);
        }
        return $folder;
    }

    /**
     * @param \TYPO3\CMS\Core\Resource\Folder $folder
     * @param $folderName
     * @return \TYPO3\CMS\Core\Resource\Folder
     */
    protected function getSubFolder(\TYPO3\CMS\Core\Resource\Folder $folder, $folderName)
    {
        if ($folder->hasFolder($folderName)) {
            $subFolder = $folder->getSubfolder($folderName);
        } else {
            $subFolder = $folder->createFolder($folderName);
        }
        return $subFolder;
    }

    /**
     * @param \Socialstream\SocialStream\Domain\Model\Channel $channel
     * @param $imageUrl
     */
    protected function processChannelMedia(\Socialstream\SocialStream\Domain\Model\Channel $channel, $imageUrl)
    {
        $folder = $this->getSubFolder($this->getSubFolder($this->getMainFolder(), $channel->getType()), $channel->getObjectId());
        $this->processMedia($channel, "tx_socialstream_domain_model_channel", "image", $folder, $imageUrl);
    }

    /**
     * @param \Socialstream\SocialStream\Domain\Model\News $news
     * @param $imageUrl
     */
    protected function processNewsMedia(\Socialstream\SocialStream\Domain\Model\News $news, $imageUrl)
    {
        $folder = $this->getSubFolder($this->getSubFolder($this->getSubFolder($this->getMainFolder(), $news->getChannel()->getType()), $news->getChannel()->getObjectId()), "news");
        $this->processMedia($news, "tx_news_domain_model_news", "fal_media", $folder, $imageUrl);
    }

    function getExtensionFromMimeType($mime)
    {
        $all_mimes = '{"png":["image\/png","image\/x-png"],"bmp":["image\/bmp","image\/x-bmp","image\/x-bitmap","image\/x-xbitmap","image\/x-win-bitmap","image\/x-windows-bmp","image\/ms-bmp","image\/x-ms-bmp","application\/bmp","application\/x-bmp","application\/x-win-bitmap"],"gif":["image\/gif"],"jpeg":["image\/jpeg","image\/pjpeg"],"xspf":["application\/xspf+xml"],"vlc":["application\/videolan"],"wmv":["video\/x-ms-wmv","video\/x-ms-asf"],"au":["audio\/x-au"],"ac3":["audio\/ac3"],"flac":["audio\/x-flac"],"ogg":["audio\/ogg","video\/ogg","application\/ogg"],"kmz":["application\/vnd.google-earth.kmz"],"kml":["application\/vnd.google-earth.kml+xml"],"rtx":["text\/richtext"],"rtf":["text\/rtf"],"jar":["application\/java-archive","application\/x-java-application","application\/x-jar"],"zip":["application\/x-zip","application\/zip","application\/x-zip-compressed","application\/s-compressed","multipart\/x-zip"],"7zip":["application\/x-compressed"],"xml":["application\/xml","text\/xml"],"svg":["image\/svg+xml"],"3g2":["video\/3gpp2"],"3gp":["video\/3gp","video\/3gpp"],"mp4":["video\/mp4"],"m4a":["audio\/x-m4a"],"f4v":["video\/x-f4v"],"flv":["video\/x-flv"],"webm":["video\/webm"],"aac":["audio\/x-acc"],"m4u":["application\/vnd.mpegurl"],"pdf":["application\/pdf","application\/octet-stream"],"pptx":["application\/vnd.openxmlformats-officedocument.presentationml.presentation"],"ppt":["application\/powerpoint","application\/vnd.ms-powerpoint","application\/vnd.ms-office","application\/msword"],"docx":["application\/vnd.openxmlformats-officedocument.wordprocessingml.document"],"xlsx":["application\/vnd.openxmlformats-officedocument.spreadsheetml.sheet","application\/vnd.ms-excel"],"xl":["application\/excel"],"xls":["application\/msexcel","application\/x-msexcel","application\/x-ms-excel","application\/x-excel","application\/x-dos_ms_excel","application\/xls","application\/x-xls"],"xsl":["text\/xsl"],"mpeg":["video\/mpeg"],"mov":["video\/quicktime"],"avi":["video\/x-msvideo","video\/msvideo","video\/avi","application\/x-troff-msvideo"],"movie":["video\/x-sgi-movie"],"log":["text\/x-log"],"txt":["text\/plain"],"css":["text\/css"],"html":["text\/html"],"wav":["audio\/x-wav","audio\/wave","audio\/wav"],"xhtml":["application\/xhtml+xml"],"tar":["application\/x-tar"],"tgz":["application\/x-gzip-compressed"],"psd":["application\/x-photoshop","image\/vnd.adobe.photoshop"],"exe":["application\/x-msdownload"],"js":["application\/x-javascript"],"mp3":["audio\/mpeg","audio\/mpg","audio\/mpeg3","audio\/mp3"],"rar":["application\/x-rar","application\/rar","application\/x-rar-compressed"],"gzip":["application\/x-gzip"],"hqx":["application\/mac-binhex40","application\/mac-binhex","application\/x-binhex40","application\/x-mac-binhex40"],"cpt":["application\/mac-compactpro"],"bin":["application\/macbinary","application\/mac-binary","application\/x-binary","application\/x-macbinary"],"oda":["application\/oda"],"ai":["application\/postscript"],"smil":["application\/smil"],"mif":["application\/vnd.mif"],"wbxml":["application\/wbxml"],"wmlc":["application\/wmlc"],"dcr":["application\/x-director"],"dvi":["application\/x-dvi"],"gtar":["application\/x-gtar"],"php":["application\/x-httpd-php","application\/php","application\/x-php","text\/php","text\/x-php","application\/x-httpd-php-source"],"swf":["application\/x-shockwave-flash"],"sit":["application\/x-stuffit"],"z":["application\/x-compress"],"mid":["audio\/midi"],"aif":["audio\/x-aiff","audio\/aiff"],"ram":["audio\/x-pn-realaudio"],"rpm":["audio\/x-pn-realaudio-plugin"],"ra":["audio\/x-realaudio"],"rv":["video\/vnd.rn-realvideo"],"jp2":["image\/jp2","video\/mj2","image\/jpx","image\/jpm"],"tiff":["image\/tiff"],"eml":["message\/rfc822"],"pem":["application\/x-x509-user-cert","application\/x-pem-file"],"p10":["application\/x-pkcs10","application\/pkcs10"],"p12":["application\/x-pkcs12"],"p7a":["application\/x-pkcs7-signature"],"p7c":["application\/pkcs7-mime","application\/x-pkcs7-mime"],"p7r":["application\/x-pkcs7-certreqresp"],"p7s":["application\/pkcs7-signature"],"crt":["application\/x-x509-ca-cert","application\/pkix-cert"],"crl":["application\/pkix-crl","application\/pkcs-crl"],"pgp":["application\/pgp"],"gpg":["application\/gpg-keys"],"rsa":["application\/x-pkcs7"],"ics":["text\/calendar"],"zsh":["text\/x-scriptzsh"],"cdr":["application\/cdr","application\/coreldraw","application\/x-cdr","application\/x-coreldraw","image\/cdr","image\/x-cdr","zz-application\/zz-winassoc-cdr"],"wma":["audio\/x-ms-wma"],"vcf":["text\/x-vcard"],"srt":["text\/srt"],"vtt":["text\/vtt"],"ico":["image\/x-icon","image\/x-ico","image\/vnd.microsoft.icon"],"csv":["text\/x-comma-separated-values","text\/comma-separated-values","application\/vnd.msexcel"],"json":["application\/json","text\/json"]}';
        $all_mimes = json_decode($all_mimes, true);
        foreach ($all_mimes as $key => $value) {
            if (array_search($mime, $value) !== false) return $key;
        }
        return false;
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

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Cache-Control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        if ($err) {
            BaseUtility::log(__CLASS__, "error", "Entry " . $model->getObjectId() . " got CURL Error: " . $err);
        } else {
            $imageName = $model->getObjectId() . "." . $this->getExtensionFromMimeType(curl_getinfo($curl, CURLINFO_CONTENT_TYPE));
            $grabedImage = array(
                'imageName' => $imageName,
                'image' => $response
            );
        }
        curl_close($curl);
        return $grabedImage;
    }

    /**
     * @param $model
     * @param $table
     * @param $field
     * @param Folder $folder
     * @param $imageUrl
     */
    protected function processMedia($model, $table, $field, $folder, $imageUrl)
    {
        $imageArray = $this->grabImage($imageUrl, $model);

        if (!empty($imageArray['imageName']) && !empty($imageArray['image'])) {
            $imageName = $imageArray['imageName'];

            $storage = $this->getStorage();

            if ((!$folder->hasFile($imageName) && $imageName) || ($storage->getFileInFolder($imageName, $folder)->getSize() <= 0 && $folder->hasFile($imageName) && $imageName)) {
                if (file_exists($this->settings["tmp"] . $imageName)) {
                    unlink($this->settings["tmp"] . $imageName);
                }
                $fp = fopen($this->settings["tmp"] . $imageName, 'x');
                fwrite($fp, $imageArray['image']);
                fclose($fp);

                if (filesize($this->settings["tmp"] . $imageName) > 0) {
                    $movedNewFile = $storage->addFile($this->settings["tmp"] . $imageName, $folder, $imageName, \TYPO3\CMS\Core\Resource\DuplicationBehavior::REPLACE);
                    $image = $movedNewFile->getUid();
                }

                if ($table == "tx_socialstream_domain_model_channel") {
                    if ($model->getImage()) {
                        $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("sys_file_reference", "uid=" . $model->getImage()->getUid(), array('deleted' => '1'));
                    }
                } elseif ($table == "tx_news_domain_model_news") {
                    if (count($model->getFalMedia()) > 0) {
                        $media = $model->getFalMedia()->current();
                        if ($media) {
                            $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("sys_file_reference", "uid=" . $media->getUid(), array('deleted' => '1'));
                        }
                    }
                }
            } elseif ($table == "tx_socialstream_domain_model_channel") {
                if (!$model->getImage() && $imageName) {
                    $image = $storage->getFileInFolder($imageName, $folder);
                    $image = $image->getUid();
                }
            } elseif ($table == "tx_news_domain_model_news") {
                if (count($model->getFalMedia()) <= 0 && $imageName) {
                    $image = $storage->getFileInFolder($imageName, $folder);
                    $image = $image->getUid();
                }
            }

            if ($image) {
                $data = array();
                $data['sys_file_reference']['NEW12345'] = array(
                    'uid_local' => $image,
                    'uid_foreign' => $model->getUid(), // uid of your content record
                    'tablenames' => $table,
                    'fieldname' => $field,
                    'pid' => $this->settings["storagePid"], // parent id of the parent page
                    'table_local' => 'sys_file',
                    'showinpreview' => 1,
                );
                $data[$table][$model->getUid()] = array($field => 'NEW12345'); // set to the number of images?

                /** @var \TYPO3\CMS\Core\DataHandling\DataHandler $tce */
                $tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler'); // create TCE instance
                $tce->bypassAccessCheckForRecords = TRUE;
                $tce->start($data, array());
                $tce->admin = TRUE;
                $tce->process_datamap();

                if ($newUid = $tce->substNEWwithIDs['NEW12345']) {
                    $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("sys_file_reference", "uid=" . $newUid, array('showinpreview' => 1));
                }
                $clear = 1;
            }
        }
    }

    /**
     * @param $path
     * @return bool
     */
    protected function exists($path)
    {
        return (@fopen($path, "r") == true);
    }

    /**
     * @param $url
     * @param $saveto
     */
    protected function grab_image($url, $saveto)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        $raw = curl_exec($ch);
        curl_close($ch);
        if (file_exists($saveto)) {
            unlink($saveto);
        }
        $fp = fopen($saveto, 'x');
        fwrite($fp, $raw);
        fclose($fp);
    }

    /**
     * @param \Socialstream\SocialStream\Domain\Model\Channel $channel
     * @param $sysmail
     * @param string $sendermail
     */
    public function sendTokenInfoMail(\Socialstream\SocialStream\Domain\Model\Channel $channel, $sysmail, $sendermail = "")
    {
        $uriBuilder = $this->objectManager->get('TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder');
        $uriBuilder->initializeObject();
        $uriBuilder->setCreateAbsoluteUri(1);
        $url = explode("?", $uriBuilder->buildBackendUri())[0];
        if (substr($url, 0, 1) === "/") {
            if ($_SERVER["HTTP_HOST"]) {
                $url = "http://" . $_SERVER["HTTP_HOST"] . $url;
            } else {
                $url = "http://" . $_SERVER["HOSTNAME"] . $url;
            }
        }

        $subject = "Social Stream - " . \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('labels.subject_token', 'social_stream');
        $text = "Social Stream " . $this->getType($channel->getType()) . " " . $channel->getTitle() . ": <br/>";
        $text .= \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('labels.body_token', 'social_stream');
        $text .= "<br/><br/><a href='" . $url . "'>" . $url . "</a>";

        if (!$sendermail) $sendermail = "no-reply@example.com";
        $this->sendInfoMail(array($sendermail => "Social Stream"), array($sysmail => $sysmail), $subject, $text);
    }

    /**
     * @param $txt
     * @param $head
     * @param $type
     * @param $obj
     */
    public function addFlashMessage($txt, $head, $type, $obj)
    {
        $message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage', $txt, $head, $type, TRUE);
        $messageQueue = $obj->getMessageQueueByIdentifier();
        $messageQueue->addMessage($message);
    }

    /**
     * @param $type
     * @param \GeorgRinger\News\Domain\Model\Category|NULL $parent
     * @return \GeorgRinger\News\Domain\Model\Category
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    protected function getCategory($type, \GeorgRinger\News\Domain\Model\Category $parent = NULL)
    {
        $title = $this->getType($type);

        $cat = $this->categoryRepository->findOneByTitle($title);

        if (!$cat) {
            $cat = new \GeorgRinger\News\Domain\Model\Category();
            $cat->setTitle($title);
            if ($parent) $cat->setParentcategory($parent);
            $this->categoryRepository->add($cat);
            $this->persistenceManager->persistAll();
        }
        return $cat;
    }

}
