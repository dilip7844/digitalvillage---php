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

    public function getAllPosts()
    {
        $model = new PostModel();
        $model->limit = Common::getParam(limit);
        $model->offset = Common::getParam(offset);
        $model->search = Common::getParam(search);
        $model->isVisible = Common::getParam(isVisible);

        //  $desc = Common::getParam(desc);
        $result = $model->selectPosts();
        return $this->respond($result);
    }


    public function createPost()
    {
        $model = new PostModel();
        $model->post = Common::getParam(post);
        $model->businessId = Common::getParam(businessId);
        $model->images = Common::getFile(images);
        $model->userId = Common::getParam(userId);
        $result = $model->insertPost();
        return $this->respond($result);
    }

    public function getPost()
    {
        $model = new PostModel();
        $model->id = Common::getParam(id);
        $model->userId = Common::getParam(userId);
        $result = $model->getPost();
        return $this->respond($result);
    }

    public function likePost()
    {
        $model = new PostModel();
        $model->id = Common::getParam(id);
        $model->userId = Common::getParam(userId);
        $result = $model->likePost();
        return $this->respond($result);
    }

    public function dislikePost()
    {
        $model = new PostModel();
        $model->id = Common::getParam(id);
        $model->userId = Common::getParam(userId);
        $result = $model->dislikePost();
        return $this->respond($result);
    }
}
