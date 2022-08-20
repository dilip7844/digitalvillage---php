<?php

namespace App\Models;

use App\Controllers\Common;
use CodeIgniter\Model;

define('PERMISSION_VIEW_USER', 'view_user');
define('PERMISSION_ADD_USER', 'add_user');
define('PERMISSION_EDIT_USER', 'edit_user');
define('PERMISSION_ADD_POST', 'add_post');

define('PERMISSION_DEFAULT', PERMISSION_VIEW_USER . ',' . PERMISSION_ADD_POST);

class UserModel extends Model
{
    protected $table = "users";
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id', 'first_name', 'middle_name', 'last_name', 'mobile',
        'gender', 'dob', 'occupation', 'is_authority', 'permissions',
        'is_verified', 'is_active', 'service', 'profile_pic', 'fcm_token', 'created_on','timestamp'
    ];

    protected $tableOccupation = "occupations";
    var  $search = null;
    var  $id = null;
    var  $userId = null;
    var  $mobile = null;
    var  $limit = null;
    var  $offset = null;
    var  $isActive = null;
    var  $isVerified = null;
    var  $isAuthority = null;
    var  $permissions = null;
    var  $fcmToken = null;
    var  $profilePic = null;

    var $result = null;

    public function selectUsers()
    {
        // if($this->hasPermission($this->userId,PERMISSION_VIEW_USER)){
        $query = 'SELECT * from ' . $this->table . ' LEFT JOIN occupations ON users.occupation = occupations.occupation_id';
        //echo $query;
        if ($this->search != null || $this->isActive != null || $this->isVerified != null || $this->isAuthority != null)
            $query = $query . " where";

        // if ($this->search != null || $this->isActive != null || $this->isVerified != null || $this->isAuthority != null)
        //     $query = $query . " where";

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
            $query = $query . " is_authority=" . $this->isAuthority  . "";
            if ($this->search != null)
                $query = $query . " and";
        }

        if ($this->search != null) {
            $query = $query . " (first_name like '%" . $this->search . "%' OR last_name like '%" . $this->search . "%' OR mobile like '%" . $this->search . "%')";
        }
        $query = $query . ' order by id desc';

        if ($this->limit != null && $this->offset != null) {
            $query = $query . ' limit ' . $this->limit . ' offset ' . $this->offset;
        }
        //echo $query;

        $this->result = $this->db->query($query);
        $res = $this->result->getResult();
        if ($this->isResultEmpty())
            return Common::createResponse(STATUS_USER_NOT_FOUND, "Users not found");
        return Common::createResponse(STATUS_SUCCESS, "Users Fetched", $res);
        //}else return Common::createResponse(1, "Sorry !! You don't have permission to view users. Please contact Authority");
    }

    public function getUser()
    {
        $where = null;
        if ($this->id != null)
            $where = 'id=' . $this->id;
        if ($this->mobile != null)
            $where = 'mobile=' . $this->mobile;
        $this->result = $this->db->query('SELECT * from ' . $this->table . ' LEFT JOIN occupations ON users.occupation = occupations.occupation_id where ' . $where);
        $res = $this->result->getResult();
        if ($this->isResultEmpty())
            return Common::createResponse(STATUS_USER_NOT_FOUND, "User not found");
        return Common::createResponse(STATUS_SUCCESS, "User Fetched", $res);
    }


    public function insertUser($data)
    {
        if ($this->isExist('mobile', $data[mobile])) {
            return Common::createResponse(1, "User already exist with mobile Number " . $data[mobile]);
        } else {
            $res = $this->insert($data);
            if ($res) {
                $root = Common::createResponse(STATUS_SUCCESS, "User Created");
                $root->data = $this->find($this->getInsertID());
                return $root;
            } else {
                return Common::createResponse(STATUS_FAILED, "Failed to Create User");
            }
        }
    }

    public function modifyPermissions()
    {
        if ($this->isExist("id", $this->id)) {
            if ($this->isUserActive($this->id)) {
                $this->result = $this->db->query("UPDATE " . $this->table . " set permissions= '" . $this->permissions . "' where id=" . $this->id);
                if ($this->db->affectedRows() > 0)
                    return Common::createResponse(STATUS_SUCCESS, "Permission Modified");
                else return Common::createResponse(STATUS_FAILED, "Unable to Modify Permissions");
            } else return Common::createResponse(STATUS_USER_INACTIVE, "User is Deactive, can't modify permission.");
        } else return Common::createResponse(STATUS_USER_NOT_FOUND, "User not found");
    }

    public function changeActiveStatus()
    {
        if ($this->isExist("id", $this->id)) {
            if ($this->isUserActive($this->id) && $this->isActive == '1') {
                return Common::createResponse(1, "User is already Active");
            } else if (!$this->isUserActive($this->id) && $this->isActive == '0') {
                return Common::createResponse(1, "User is already Deactive");
            } else {
                $this->result = $this->db->query("UPDATE " . $this->table . " set is_active=" . $this->isActive . " where id=" . $this->id);
                if ($this->db->affectedRows() > 0) {
                    $status = "Deactivated";
                    if ($this->isActive == '1')
                        $status = "Activated";
                    return Common::createResponse(STATUS_SUCCESS, "User is " . $status);
                } else return Common::createResponse(STATUS_FAILED, "Unable to change active status");
            }
        } else return Common::createResponse(STATUS_USER_NOT_FOUND, "User not found");
    }

    public function updateUser()
    {
        if ($this->userId != $this->id) { // User is updating other's profile ,
            if ($this->hasPermission($this->userId, PERMISSION_EDIT_USER)) { //Check if he has the permission

            } else return Common::createResponse(1, "You don't have permission to update user's information");
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
            } else return Common::createResponse(STATUS_FAILED, "Failed to upload Profile Pic");
        } else return Common::createResponse(STATUS_USER_NOT_FOUND, "User not found");
    }

    public function updateFCMToken()
    {
        if ($this->isExist("id", $this->id)) {
            if ($this->isUserActive($this->id)) {
                $this->result = $this->db->query("UPDATE " . $this->table . " set fcm_token= '" . $this->fcmToken . "' where id=" . $this->id);
                if ($this->db->affectedRows() > 0)
                    return Common::createResponse(STATUS_SUCCESS, "Token Updated");
                else return Common::createResponse(STATUS_FAILED, "Unable to update token");
            } else return Common::createResponse(STATUS_USER_INACTIVE, "User is Deactive, can't update token.");
        } else return Common::createResponse(STATUS_USER_NOT_FOUND, "User not found");
    }

    public function getOccupations()
    {
        $this->result = $this->db->query('SELECT * from ' . $this->tableOccupation);
        $res = $this->result->getResult();
        if ($this->isResultEmpty())
            return Common::createResponse(STATUS_NO_DATA, "No Occupations added by Admin");
        return Common::createResponse(STATUS_SUCCESS, "Occupations Fetched", $res);
    }

    private function hasPermission($id, $strPermission)
    {
        $query = 'SELECT permissions from ' . $this->table . ' where id=' . $id . '';
        $res = $this->db->query($query)->getRowArray(0);
        $array = explode(',', $res['permissions']);
        $permissionExists = array_search($strPermission, $array);
        return $permissionExists != '';
    }

    private function isExist($key, $value)
    {
        $query = 'SELECT id from ' . $this->table . ' where ' . $key . '=' . $value . '';
        $res = $this->db->query($query);
        return $res->getNumRows() > 0;
    }

    private function getMobile($userId)
    {
        $query = 'SELECT mobile from ' . $this->table . ' where id=' . $userId . '';
        $arr = $this->db->query($query);
        if ($arr->getNumRows() > 0)
            $res = $arr->getRowArray(0);
        else return "";
        return $res["mobile"];
    }

    private function isUserActive($id)
    {
        $query = 'SELECT is_active from ' . $this->table . ' where id=' . $id . '';
        $res = $this->db->query($query)->getRowArray(0);
        return $res['is_active'] == 1;
    }

    private function isResultEmpty()
    {
        return $this->result->getNumRows() == 0;
    }
}
