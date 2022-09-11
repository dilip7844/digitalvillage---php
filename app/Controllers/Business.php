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
        return $this->respond($result);
    }


     
     

     

    

   
}
