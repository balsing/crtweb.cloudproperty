<?php

namespace Crtweb\CloudProperty\File;

class File
{
    protected $init = false;
    protected $name;
    protected $path;
    protected $type;
    protected $size;
    
    /**
     * File constructor.
     * @param array $file
     */
    public function __construct(array $file = [])
    {
        if (!empty($file)) {
            $this->makeFromArray($file);
        }
    }
    
    /**
     * @param $file
     * @throws \Exception
     */
    public function makeFromArray($file)
    {
        if ($file['name']) {
            $this->name = $this->makeSecureName($file['name']);
        } else {
            throw new \Exception('В полученном массиве не содержится поле [name]');
        }
        
        if ($file['type']) {
            $this->type = $file['type'];
        } else {
            throw new \Exception('В полученном массиве не содержится поле [type]');
        }
        
        if ($file['tmp_name']) {
            $this->path = $file['tmp_name'];
        } else {
            throw new \Exception('В полученном массиве не содержится поле [tmp_name]');
        }
        
        $this->init = true;
        if ($file['size']) {
            $this->size = $file['size'];
        }
    }
    
    private function makeSecureName($name)
    {
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        return md5($name . time()) . "." . $ext;
    }
    
    /**
     * @param $file_path
     * @throws \Exception
     */
    public function makeFromFile($file_path)
    {
        if (file_exists($file_path)) {
            $this->path = $file_path;
            $this->name = $this->makeSecureName(basename($file_path));
            $this->type = mime_content_type($file_path);
            $this->size = filesize($file_path);
            $this->init = true;
        } else {
            throw new \Exception('Файл несуществует');
        }
    }
    
    public function makeFromBitrix(array $bx_file)
    {
        if ($bx_file['ORIGINAL_NAME']) {
            $this->name = $this->makeSecureName($bx_file['ORIGINAL_NAME']);
        } else {
            throw new \Exception('В полученном массиве не содержится поле [ORIGINAL_NAME]');
        }
        
        if ($bx_file['CONTENT_TYPE']) {
            $this->type = $bx_file['CONTENT_TYPE'];
        } else {
            throw new \Exception('В полученном массиве не содержится поле [CONTENT_TYPE]');
        }
        
        if ($bx_file['FILE_NAME'] && $bx_file['SUBDIR']) {
            $this->path = $_SERVER['DOCUMENT_ROOT'] . '/upload/' . $bx_file['SUBDIR'] . '/' . $bx_file['FILE_NAME'];
        } else {
            throw new \Exception('В полученном массиве не содержится поле [FILE_NAME]');
        }
        
        $this->init = true;
        if ($bx_file['size']) {
            $this->size = $bx_file['size'];
        }
    }
    
    public function isInit()
    {
        return $this->init;
    }
    
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }
    
    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }
}
