<?php

namespace App\Models;

use App\Controllers\Common;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = "users";
    protected $primaryKey = 'user_id';
    protected $allowedFields = [
        'user_id',
        'name',
        'mobile',
        'address',
        'business_id',
        'gender',
        'dob',
        'occupation_id',
        'is_authority',
        'permissions',
        'is_verified',
        'is_active',
        'service',
        'profile_pic',
        'fcm_token',
        'created_on',
        'timestamp'
    ];

    var $tableOccupation = "occupations";

    var $search = null;
    var $id = null;
    var $userId = null;
    var $mobile = null;
    var $limit = null;
    var $offset = null;
    var $isActive = null;
    var $isVerified = null;
    var $isAuthority = null;
    var $permissions = null;
    var $fcmToken = null;
    var $profilePic = null;

    var $name = null;
    var $dob = null;
    var $gender = null;
    var $address = null;
    var $occupationId = null;


    var $result = null;

    public function selectUsers()
    {
        // if($this->hasPermission($this->userId,PERMISSION_VIEW_USER)){
        $query = 'SELECT * from ' . $this->table . ' LEFT JOIN occupations ON users.occupation_id = occupations.occupation_id';
        //echo $query;
        if ($this->search != null || $this->isActive != null || $this->isVerified != null || $this->isAuthority != null)
            $query = $query . " where";

        if ($this->isActive != null) {
            $query = $query . " is_active=" . $this->isActive . "";
            if ($this->search != null || $this->isVerified != null || $this->isAuthority != null)
                $query = $query . " and";
        }

        if ($this->isVerified != null) {
            $query = $query . " is_verified=" . $this->isVerified . "";
            if ($this->search != null || $this->isAuthority != null)
                $query = $query . " and";
        }

        if ($this->isAuthority != null) {
            $query = $query . " is_authority=" . $this->isAuthority . "";
            if ($this->search != null)
                $query = $query . " and";
        }

        if ($this->search != null) {
            $query = $query . " (name like '%" . $this->search . "%' OR mobile like '%" . $this->search . "%')";
        }
        $query = $query . ' order by user_id desc';

        if ($this->limit != null && $this->offset != null) {
            $query = $query . ' limit ' . $this->limit . ' offset ' . $this->offset;
        }
        //echo $query;

        $this->result = $this->db->query($query);
        $res = $this->result->getResult();
        return $res;
        //}else return Common::createResponse(1, "Sorry !! You don't have permission to view users. Please contact Authority");
    }

    public function getUser()
    {
        $res = $this->select('users.*,occupations.*')
            ->join('occupations', 'occupations.occupation_id = users.occupation_id', 'left')
            ->orWhere("user_id", $this->id)->orWhere("mobile", $this->mobile)->first();
        return $res;
    }


    public function insertUser()
    {
        $result = new ResultModel();
        if ($this->isExist('mobile', $this->mobile)) {
            $result->statusCode = STATUS_ALREADY_EXIST;
            return $result;
        } else {
            $data = [
                'name' => $this->name,
                'mobile' => $this->mobile,
                'gender' => $this->gender,
                'address' => $this->address,
                'business_id' => 0,
                'dob' => $this->dob,
                'occupation_id' => $this->occupationId,
                'is_authority' => 'No',
                //'permissions' => PERMISSION_DEFAULT,
                'is_verified' => 'Yes',
                'is_active' => 'Yes',
                'service' => '0',
                'profile_pic' => 'da',
                'fcm_token' => $this->fcmToken,
                'created_on' => Common::getCurrentTime(),
                'timestamp' => Common::getCurrentTimeStamp()
            ];
            //print_r($data) ;
            $res = $this->insert($data);
            if ($res) {
                $result->statusCode = STATUS_SUCCESS;
                $result->data = $this->find($this->getInsertID());
            } else {
                $result->statusCode = STATUS_FAILED;
            }
            return $result;
        }
    }

    public function modifyPermissions()
    {
        if ($this->isExist("id", $this->id)) {
            if ($this->isUserActive($this->id)) {
                $this->result = $this->db->query("UPDATE " . $this->table . " set permissions= '" . $this->permissions . "' where id=" . $this->id);
                if ($this->db->affectedRows() > 0)
                    return Common::createResponse(STATUS_SUCCESS, "Permission Modified");
                else
                    return Common::createResponse(STATUS_FAILED, "Unable to Modify Permissions");
            } else
                return Common::createResponse(STATUS_USER_INACTIVE, "User is Deactive, can't modify permission.");
        } else
            return Common::createResponse(STATUS_USER_NOT_FOUND, "User not found");
    }

    public function changeActiveStatus()
    {
        if ($this->isExist("user_id", $this->id)) {
            $nextStatus = 'No';
            if (!$this->isUserActive($this->id))
                $nextStatus = 'Yes';
            $data = [
                'is_active' => $nextStatus
            ];
            $result = $this->update($this->id,$data);
            if ($result)
                return STATUS_SUCCESS;
            else
                return STATUS_FAILED;
        } else
            return STATUS_USER_NOT_FOUND;
    }

    public function updateUser()
    {
        if ($this->userId != $this->id) { // User is updating other's profile ,
            if ($this->hasPermission($this->userId, PERMISSION_EDIT_USER)) { //Check if he has the permission

            } else
                return Common::createResponse(STATUS_NO_PERMISSION, "You don't have permission to update user's information");
        } else { //User is Updating self profile

        }
    }

    public function updateProfilePic()
    {
        $files = $this->profilePic;
        $fileModel = new FileModel($files);
        $mobile = $this->getMobile($this->userId);
        if (!empty($mobile)) {
            $newname = $this->userId . '-' . $mobile . '.png';
            $location = Common::rootOfProfilePictures() . $newname;
            if (Common::uploadImage($fileModel->getTempName(), $location)) {
                $this->db->query("UPDATE " . $this->table . " set profile_pic= '" . $location . "' where id=" . $this->userId);
                return Common::createResponse(STATUS_SUCCESS, "Profile Pic Updated Successfully");
            } else
                return Common::createResponse(STATUS_FAILED, "Failed to upload Profile Pic");
        } else
            return Common::createResponse(STATUS_USER_NOT_FOUND, "User not found");
    }

    public function updateFCMToken()
    {
        if ($this->isExist("user_id", $this->id)) {
            if ($this->isUserActive($this->id)) {
                $this->result = $this->db->query("UPDATE " . $this->table . " set fcm_token= '" . $this->fcmToken . "' where id=" . $this->id);
                if ($this->db->affectedRows() > 0)
                    return Common::createResponse(STATUS_SUCCESS, "Token Updated");
                else
                    return Common::createResponse(STATUS_FAILED, "Unable to update token");
            } else
                return Common::createResponse(STATUS_USER_INACTIVE, "User is Deactive, can't update token.");
        } else
            return Common::createResponse(STATUS_USER_NOT_FOUND, "User not found");
    }

    public function getOccupations()
    {
        $this->result = $this->db->query('SELECT * from ' . $this->tableOccupation);
        $res = $this->result->getResult();
        if ($this->isResultEmpty())
            return Common::createResponse(STATUS_NO_DATA, "No Occupations added by Admin");
        return Common::createResponse(STATUS_SUCCESS, "Occupations Fetched", $res);
    }

    public function hasPermission($id, $strPermission)
    {
        $query = 'SELECT permissions from ' . $this->table . ' where user_id=' . $id . '';
        $res = $this->db->query($query)->getRowArray(0);
        $array = explode(',', $res['permissions']);
        $permissionExists = array_search($strPermission, $array);
        return $permissionExists != '';
    }

    private function isExist($key, $value)
    {
        $query = 'SELECT user_id from ' . $this->table . ' where ' . $key . '=' . $value . '';
        $res = $this->db->query($query);
        return $res->getNumRows() > 0;
    }
    private function getMobile($userId)
    {
        $res = $this->find($userId);
        return $res["mobile"];
    }

    private function isUserActive($userId)
    {
        $res = $this->find($userId);
        return $res["is_active"] == 'Yes';
    }

    public function addBusiness($businessId)
    {
        $arr = $this->db->query("Select business from " . $this->table . " where user_id= " . $this->id);
        if ($arr->getNumRows() > 0)
            $res = $arr->getRowArray(0);
        else
            return "";
        if ($res["business"] == "")
            $this->db->query("update " . $this->table . " set business=" . $businessId . " where id= " . $this->id);
        else
            $this->db->query("update " . $this->table . " set business= concat(business,'," . $businessId . "') where id= " . $this->id);
        if ($this->db->affectedRows() > 0)
            return Common::createResponse(STATUS_SUCCESS, "Updated User's Business");
        else
            return Common::createResponse(STATUS_FAILED, "Unable to update users Business");
    }

    private function isResultEmpty()
    {
        return $this->result->getNumRows() == 0;
    }
}