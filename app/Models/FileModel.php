<?php

namespace App\Models;

class FileModel
{
    private $files;
    public $isArray = false;
    function __construct($files)
    {
        $this->files = $files;
        $this->isArray = is_array($this->files['name']);
    }

    public function getFileCount()
    {
        return sizeof($this->files['name']);
    }

    public function getName($fileIndex = 0)
    {
        if (!$this->isArray)
            $name = $this->files['name'];
        else  $name = $this->files['name'][$fileIndex];
        return $name;
    }

    public function getTempName($fileIndex = 0)
    {
        if (!$this->isArray)
            $tmpName = $this->files['tmp_name'];
        else $tmpName = $this->files['tmp_name'][$fileIndex];
        return $tmpName;
    }

    public function getType($fileIndex = 0)
    {
        if (!$this->isArray)
            $type = $this->files['type'];
        else  $type = $this->files['type'][$fileIndex];
        return $type;
    }

    public function getSize($fileIndex = 0)
    {
        if (!$this->isArray)
            $size = $this->files['size'];
        else  $size = $this->files['size'][$fileIndex];
        return $size;
    }

    public function getError($fileIndex = 0)
    {
        if (!$this->isArray)
            $error = $this->files['error'];
        else $error = $this->files['error'][$fileIndex];
        return $error;
    }
}
