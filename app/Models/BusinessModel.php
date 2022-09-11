<?php

namespace App\Models;

use App\Controllers\Common;
use CodeIgniter\Model;

use function PHPUnit\Framework\isEmpty;

class BusinessModel extends Model
{
    protected $table = "businesses";
    protected $primaryKey = 'business_id';
    protected $allowedFields = [
        'business_id', 'outlet_name', 'owners', 'created_by', 'created_on', 'timestamp', 'is_visible', 'category',
        'images', 'products', 'address', 'contact_number',    'email',    'posts',
        'about', 'opening_time', 'closing_time'
    ];

    var $tableBusinessCategory = "business_categories";

    var  $id = null;
    var  $userId = null;
    var  $search = null;
    var  $limit = null;
    var  $offset = null;
    var  $isVisible = null;

    var $outletName = null;
    var $owners = null;
    var $about = null;
    var $category = null;
    var $products = null;
    var $address = null;
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

    public function createBusiness()
    {
        $userModel = new UserModel();
        if ($userModel->hasPermission($this->userId, PERMISSION_ADD_BUSINESS)) {
            $data = [
                'outlet_name' => $this->outletName,
                'images'  => '',
                'owners'  => $this->owners,
                'products'  => $this->products,
                'address'  => $this->address,
                'contact_number'  => $this->contactNumber,
                'category'  => $this->category,
                'email'  => $this->email,
                'about'  => $this->about,
                'posts'  => $this->posts,
                'opening_time'  => $this->openingTime,
                'closing_time'  => $this->closingTime,
                'is_visible'  => '1',
                'created_by'  => $this->userId,
                'created_on'  => Common::getCurrentTime(),
                'timestamp' => Common::getCurrentTimeStamp()
            ];
            $res = $this->insert($data);
            if ($res) {
                $root = Common::createResponse(STATUS_SUCCESS, "Business Created");
                $insertedId = $this->getInsertID();
                $userModel->id = $this->owners;
                $userModel->addBusiness($insertedId);
                //uploading image if any
                if (!empty($this->images)) {
                    $fileModel = new FileModel($this->images);
                    $imagesPathString = '';
                    for ($i = 0; $i < $fileModel->getFileCount(); $i++) {
                        $newname = $insertedId . '-BUSINESS_' . $i . '.png';
                        $location = Common::rootOfBusinesses() . $newname;
                        if (Common::uploadImage($fileModel->getTempName($i), $location)) {
                            if ($imagesPathString == '')
                                $imagesPathString = $location;
                            else $imagesPathString = $imagesPathString . ',' . $location;
                        }
                    }
                    $this->result = $this->db->query("UPDATE " . $this->table . " set images='" . $imagesPathString . "' where business_id=" . $insertedId);
                }
                $root->data = $this->find($insertedId);
                return $root;
            } else {
                return Common::createResponse(STATUS_FAILED, "Failed to Create Business");
            }
        } else return Common::createResponse(STATUS_NO_PERMISSION, "You don't have permission to create Business");
    }

    public function addOwner()
    {
        if ($this->isOwner($this->userId, $this->id)) {
            $arr = $this->db->query("Select owners from " . $this->table . " where business_id= " . $this->id);
            if ($arr->getNumRows() > 0)
                $res = $arr->getRowArray(0);
            else return "";
            if ($res["owners"] == "")
                $this->db->query("update " . $this->table . " set owners=" . $this->owners . " where business_id= " . $this->id);
            else  $arr = $this->db->query("update " . $this->table . " set owners= concat(owners,'," . $this->owners . "') where business_id= " . $this->id);
            if ($this->db->affectedRows() > 0)
                return Common::createResponse(STATUS_SUCCESS, "Owner Added");
            else return Common::createResponse(STATUS_FAILED, "Unable to add Owner");
        } else return Common::createResponse(STATUS_NO_PERMISSION, "You don't have permission to add owner to this business");
    }

    public function isOwner($userId, $businessId)
    {
        $query = 'SELECT owners from ' . $this->table . ' where business_id=' . $businessId . '';
        $res = $this->db->query($query)->getRowArray(0);
        $array = explode(',', $res['owners']);
        $permissionExists = array_search($userId, $array);
        return $permissionExists != '';
    }

    private function isResultEmpty()
    {
        return $this->result->getNumRows() == 0;
    }
}
