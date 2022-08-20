<?php

namespace App\Controllers;

use App\Models\PostModel;
use CodeIgniter\API\ResponseTrait;

class Post extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $domain = Common::getDomain();
        \Config\Database::connect('default')->setDatabase($domain);
    }

    public function createPost()
    {
        echo "create post";
    }

    public function getPost()
    {
        $model = new PostModel();
        $model->id = Common::getParam(id);
        $model->userId = Common::getParam(userId);
        $result = $model->getPost();
        return $this->respond($result);
    }
}
