<?php

namespace App\Models;

class FileModel
{
    private $files;
    function __construct($files)
    {
        $this->files = $files;
    }

    public function getName($fileIndex = 0)
    {
        if ($fileIndex == 0)
            $name = $this->files['name'];
        else  $name = $this->files['name'][$fileIndex];
        return $name;
    }

    public function getTempName($fileIndex = 0)
    {
        if ($fileIndex == 0)
            $tmpName = $this->files['tmp_name'];
        else $tmpName = $this->files['tmp_name'][$fileIndex];
        return $tmpName;
    }

    public function getType($fileIndex = 0)
    {
        if ($fileIndex == 0)
            $type = $this->files['type'];
        else  $type = $this->files['type'][$fileIndex];
        return $type;
    }

    public function getSize($fileIndex = 0)
    {
        if ($fileIndex == 0)
            $size = $this->files['size'];
        else  $size = $this->files['size'][$fileIndex];
        return $size;
    }

    public function getError($fileIndex = 0)
    {
        if ($fileIndex == 0)
            $error = $this->files['error'];
        else $error = $this->files['error'][$fileIndex];
        return $error;
    }
}
