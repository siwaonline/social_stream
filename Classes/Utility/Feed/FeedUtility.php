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
    public $clearStrings = array('\ud83c\u','\ud83d\u','\u2600\u');
    
    public static function initTSFE($id = 1, $typeNum = 0) {
        parent::initTSFE($id,$typeNum);
    }
    public function initSettings(){
        parent::initSettings();
    }
    public static function getUtility($type,$pid=0){
        $classname = "\\Socialstream\\SocialStream\\Utility\\Feed\\".ucfirst($type)."Utility";
        return new $classname($pid);        
    }
    public function getElems($url){
        $elems = file_get_contents($url);
        $elems = $this->clearString($elems);
        return json_decode($elems);
    }
    public function clearString($elems){
        foreach ($this->clearStrings as $str) {
            while(strpos($elems, $str) !== false){
                $pos = strpos($elems, $str);
                $elems = substr_replace($elems,'',$pos,12);
            }
        }
        return $elems;
    }   
    
    protected function getStorage(){
        $storageRepository = $this->objectManager->get('TYPO3\\CMS\\Core\\Resource\\StorageRepository');
        return $storageRepository->findByUid($this->settings["storage"]);
    }
    protected function getMainFolder(){
        $storage = $this->getStorage();
        if($storage->hasFolder($this->settings["folder"])){
            $folder = $storage->getFolder($this->settings["folder"]);
        }else{
            $folder = $storage->createFolder($this->settings["folder"]);
        }
        return $folder;
    }
    protected function getSubFolder(\TYPO3\CMS\Core\Resource\Folder $folder, $folderName){
        if($folder->hasFolder($folderName)){
            $subFolder = $folder->getSubfolder($folderName);
        }else{
            $subFolder = $folder->createFolder($folderName);
        }
        return $subFolder;
    }

    protected function processChannelMedia(\Socialstream\SocialStream\Domain\Model\Channel $channel, $imageUrl){
        $folder = $this->getSubFolder($this->getSubFolder($this->getMainFolder(),$channel->getType()),$channel->getObjectId());
        $this->processMedia($channel,"tx_socialstream_domain_model_channel","image",$folder,$imageUrl);
    }
    protected function processNewsMedia(\Socialstream\SocialStream\Domain\Model\News $news, $imageUrl){
        $folder = $this->getSubFolder($this->getSubFolder($this->getSubFolder($this->getMainFolder(),$news->getChannel()->getType()),$news->getChannel()->getObjectId()),"news");
        $this->processMedia($news,"tx_news_domain_model_news","fal_media",$folder,$imageUrl);
    }
    protected function processMedia($model,$table,$field,$folder,$imageUrl){
        $imageName = explode("?",basename($imageUrl));
        if (is_array($imageName)){
            if(strpos($imageName[0], 'safe_image.php') !== false){
                $imageName = basename(urldecode($imageName[1]));
            }else{
                $imageName = $imageName[0];
            }            
        }
        //$imageName = $model->getObjectId().".jpg";

        $storage = $this->getStorage();

        if($table == "tx_news_domain_model_newsa") {
            var_dump($this->settings["tmp"]);
            var_dump($imageName);
            var_dump($folder);
            var_dump($imageName);
            var_dump(!$folder->hasFile($imageName) && $imageName);
        }
        if((!$folder->hasFile($imageName) && $imageName) || ($storage->getFileInFolder($imageName,$folder)->getSize() <= 0 && $folder->hasFile($imageName) && $imageName)) {
            $this->grab_image($imageUrl,$this->settings["tmp"] . $imageName);
            if(filesize($this->settings["tmp"] . $imageName) > 0) {
                $movedNewFile = $storage->addFile($this->settings["tmp"] . $imageName, $folder, $imageName, \TYPO3\CMS\Core\Resource\DuplicationBehavior::REPLACE);
                $image = $movedNewFile->getUid();
            }

            if($table == "tx_socialstream_domain_model_channel") {
                if ($model->getImage()) {
                    $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("sys_file_reference", "uid=" . $model->getImage()->getUid(), array('deleted' => '1'));
                }
            }elseif($table == "tx_news_domain_model_news"){
                if (count($model->getFalMedia()) > 0) {
                    $media = $model->getFalMedia()->current();
                    if($media) {
                        $GLOBALS["TYPO3_DB"]->exec_UPDATEquery("sys_file_reference", "uid=" . $media->getUid(), array('deleted' => '1'));
                    }
                }
            }
        }elseif($table == "tx_socialstream_domain_model_channel"){
            if(!$model->getImage() && $imageName){
                $image = $storage->getFileInFolder($imageName,$folder);
                $image = $image->getUid();
            }
        }elseif($table == "tx_news_domain_model_news"){
            if(count($model->getFalMedia()) <= 0 && $imageName){
                $image = $storage->getFileInFolder($imageName,$folder);
                $image = $image->getUid();
            }            
        }
        if($table == "tx_news_domain_model_newsa") {
            var_dump($image);
            var_dump($model->getUid());
            //var_dump($this->settings["storagePid"]);
            exit;
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
            $clear = 1;
        }
    }
    protected function exists($path){
        return (@fopen($path,"r")==true);
    }
    protected function grab_image($url,$saveto){
        if(strpos($url, 'safe_image.php') !== false && strpos($url, '&url=') !== false && strpos($url, '&url=fbstaging') === false){
            $urlpath = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
                return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
            }, $url);

            $parts = parse_url($urlpath);
            parse_str($parts['query'], $query);
            $url = $query['url'];
        }

        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw=curl_exec($ch);
        curl_close ($ch);
        if(file_exists($saveto)){
            unlink($saveto);
        }
        $fp = fopen($saveto,'x');
        fwrite($fp, $raw);
        fclose($fp);
    }

    public function sendTokenInfoMail(\Socialstream\SocialStream\Domain\Model\Channel $channel,$sysmail){
        //$this->uriBuilder->reset();
        //$this->uriBuilder->setCreateAbsoluteUri(1);
        //$url = explode("?",$this->uriBuilder->buildBackendUri())[0];
        $uriBuilder = $this->objectManager->get( 'TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder');
        $uriBuilder->initializeObject();
        $uriBuilder->setCreateAbsoluteUri(1);
        $url = explode("?",$uriBuilder->buildBackendUri())[0];

        $subject = "Social Stream - Token abgelaufen";
        //$subject = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('mail.subject_token', 'social_stream');
        $text = "Social Stream ".$this->getType($channel->getType())." ".$channel->getTitle().": <br/>";
        //$text .= \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('mail.body_token', 'social_stream');
        $text .= "Bitte melden Sie sich im Backend an und aktualisieren Sie den Token.";
        $text .= "<br/><br/><a href='".$url."'>".$url."</a>";

        $this->sendInfoMail(array("social.stream@typo3.org" => "Social Stream"),array($sysmail => $sysmail),$subject,$text);
        $this->sendInfoMail(array("noreply@paweb.at" => "Social Stream"),array($sysmail => $sysmail),$subject,$text);
    }
    public function addFlashMessage($txt,$head,$type,$obj){
        $message = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Messaging\\FlashMessage',$txt,$head,$type,TRUE);
        $messageQueue = $obj->getMessageQueueByIdentifier();
        $messageQueue->addMessage($message);
    }

}