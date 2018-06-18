<?php

namespace Crtweb\CloudProperty;

use Crtweb\CloudProperty\Connectors\ConnectorInterface;

class CloudService
{
    private static $instance = null;
    
    /**
     * @var \Crtweb\CloudProperty\Connectors\ConnectorInterface $connector
     */
    protected $connector = null;
    
    /**
     * @return CloudService
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __clone()
    {
    }
    
    private function __construct()
    {
    }
    
    public function auth()
    {
        return $this->connector->auth();
    }
    
    
    public function setConnector(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }
    
    
    public function get($file_id)
    {
        return $this->connector->get($file_id);
    }
    
    public function save($file)
    {
        return $this->connector->save($file);
    }
    
    public function check($file_id)
    {
        return $this->connector->check($file_id);
    }
    
    public function delete($file)
    {
        return $this->connector->delete($file);
    }
}
