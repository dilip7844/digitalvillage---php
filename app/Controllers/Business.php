<?php

namespace App\Controllers;

use App\Models\BusinessModel;
use CodeIgniter\API\ResponseTrait;

// TODO //Need to confirm if we have to close db connection at the end ..

class Business extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $domain = Common::getDomain();
        \Config\Database::connect('default')->setDatabase($domain);
    }

    public function getCategories()
    {
        $model = new BusinessModel();
        $result = $model->getCategories();
        $model->owners = Common::getParam(owners);
        $model->id = Common::getParam(id);

        $model->addOwner();
        return $this->respond($result);
    }
    public function addOwner()
    {
        $model = new BusinessModel();
        $model->owners = Common::getParam(owners);
        $model->userId = Common::getParam(userId);
        $model->id = Common::getParam(id);
        $result = $model->addOwner();
        return $this->respond($result);
    }

    public function createBusiness()
    {
        $model = new BusinessModel();
        $model->userId = Common::getParam(userId);
        $model->outletName = Common::getParam(outletName);
        $model->owners = Common::getParam(owners);
        $model->category = Common::getParam(category);
        $model->contactNumber = Common::getParam(contactNumber);
        $model->email = Common::getParam(email);
        $model->about = Common::getParam(about);
        $model->posts = Common::getParam(posts);
        $model->address = Common::getParam(address);
        $model->openingTime = Common::getParam(openingTime);
        $model->closingTime = Common::getParam(closingTime);
        $model->images = Common::getFile(images);

        $result = $model->createBusiness();
        return $this->respond($result);
    }
}
