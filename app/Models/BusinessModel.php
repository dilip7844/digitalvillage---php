<?php

namespace App\Models;

use App\Controllers\Common;
use CodeIgniter\Model;

class BusinessModel extends Model
{
    protected $table = "businesses";
    protected $primaryKey = 'business_id';
    protected $allowedFields = [
        'business_id', 'outlet_name', 'owner', 'created_by', 'created_on', 'timestamp','is_visible','category',
        'images', 'products', 'address', 'contact_number',    'email',    'posts',
        'about','opening_time','closing_time'
    ];

    var $tableBusinessCategory = "business_categories";

    var  $id = null;
    var  $userId = null;
    var  $search = null;
    var  $limit = null;
    var  $offset = null;
    var  $isVisible = null;

    var $outletName = null;
    var $about = null;
    var $category = null;
    var $images = null;
    var $posts = null;
    var $openingTime = null;
    var $closingTime = null;
    var $contactNumber = null;
    var $email = null;

    var $result = null;


    public function getCategories()
    {
        $this->result = $this->db->query('SELECT * from ' . $this->tableBusinessCategory);
        $res = $this->result->getResult();
        if ($this->isResultEmpty())
            return Common::createResponse(STATUS_NO_DATA, "No Business Category Found");
        return Common::createResponse(STATUS_SUCCESS, "Business Category Fetched", $res);
    }

    private function isResultEmpty()
    {
        return $this->result->getNumRows() == 0;
    }
}
