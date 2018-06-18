<?php

namespace Crtweb\CloudProperty\Connectors;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Crtweb\CloudProperty\File\File;
use Kunnu\Dropbox\DropboxFile;
use Kunnu\Dropbox\Exceptions\DropboxClientException;

class DropboxConnector implements ConnectorInterface
{
    /**
     * @var \Kunnu\Dropbox\DropboxApp $app
     */
    protected $app = false;
    /**
     * @var \Kunnu\Dropbox\Dropbox $dropbox
     */
    protected $dropbox = false;
    protected $authHelper;
    protected $callbackLink;
    
    public function __construct($app, $dropbox)
    {
        $this->app = $app;
        $this->dropbox = $dropbox;
        $this->authHelper = $this->dropbox->getAuthHelper();
        $this->callbackLink = Option::get('crtweb.cloudproperty', 'authPath');
    }
    
    public function auth()
    {
        $request = Application::getInstance()->getContext()->getRequest();
        
        $code = htmlspecialchars($request->getQuery("code"));
        $state = htmlspecialchars($request->getQuery("state"));
        if ($code && $state) {
            return $this->callbackPage($code, $state);
        } else {
            return $this->getAuthLink();
        }
    }
    
    protected function callbackPage($code, $state)
    {
        $accessToken = $this->authHelper->getAccessToken($code, $state, $this->callbackLink);
        $token = $accessToken->getToken();
        Option::set('crtweb.cloudproperty', 'accessToken', $token);
        return $token;
    }
    
    protected function getAuthLink()
    {
        $authUrl = $this->authHelper->getAuthUrl($this->callbackLink);
        if (function_exists('\LocalRedirect')) {
            \LocalRedirect($authUrl);
        } else {
            return $authUrl;
        }
    }
    
    /**
     * @param File $file
     * @return string
     * @throws DropboxClientException
     */
    public function save(File $file)
    {
        $fileStream = fopen($file->getPath(), DropboxFile::MODE_READ);
        $dropboxFile = DropboxFile::createByStream($file->getName(), $fileStream);
        $cloudFile = $this->dropbox->upload($dropboxFile, '/' . $file->getName(), ['autorename' => true]);
        return $cloudFile->getPathLower();
    }
    
    /**
     * @param $file_id
     * @return string
     * @throws DropboxClientException
     */
    public function get($file_id)
    {
        $temporaryLink = $this->dropbox->getTemporaryLink($file_id);
        return $temporaryLink->getLink();
    }
    
    /**
     * @param $file_id
     * @return bool
     */
    public function check($file_id)
    {
        try {
            $this->dropbox->getMetadata($file_id);
        } catch (DropboxClientException $e) {
            return false;
        }
        return true;
    }
    
    /**
     * @param $file_id
     * @return bool|true
     * @throws DropboxClientException
     */
    public function delete($file_id)
    {
        $this->dropbox->delete($file_id);
        return true;
    }
}
