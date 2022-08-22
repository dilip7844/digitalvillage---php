<?php

namespace App\Controllers;

use App\Models\HomeModel;
use CodeIgniter\API\ResponseTrait;

class Home extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $domain = Common::getDomain();
        \Config\Database::connect('default')->setDatabase($domain);
    }


    public function getHome()
    {
        $model = new HomeModel();
        $result = $model->getHome();
        return $this->respond($result);
    }
}
