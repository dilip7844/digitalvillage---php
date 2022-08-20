<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

// TODO //Need to confirm if we have to close db connection at the end ..

class User extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $domain = Common::getDomain();
         \Config\Database::connect('default')->setDatabase($domain);
    }

    public function getAllUsers()
    {
        $model = new UserModel();
        $model->limit = Common::getParam(limit);
        $model->offset = Common::getParam(offset);
        $model->search = Common::getParam(search);
        $model->isActive = Common::getParam(isActive);
        $model->isVerified = Common::getParam(isVerified);
        $model->isAuthority = Common::getParam(isAuthority);
        //  $desc = Common::getParam(desc);
        $result = $model->selectUsers();
        return $this->respond($result);
    }

    public function getUser()
    {
        $model = new UserModel();
        $model->id = Common::getParam(id);
        $model->mobile = Common::getParam(mobile);
        $result = $model->getUser();
        return $this->respond($result);
    }

    public function createUser()
    {
        $model = new UserModel();
        $firstName = Common::getParam(firstName);
        $middleName = Common::getParam(middleName);
        $lastName = Common::getParam(lastName);
        $mobile = Common::getParam(mobile);
        $gender = Common::getParam(gender);
        $occupation = Common::getParam(occupation);
        $fcmToken = Common::getParam(fcmToken);
        $dob = Common::getParam(dob);
        $date = Common::getCurrentTime();
        $timestamp=Common::getCurrentTimeStamp();
        $data = [
            'first_name' => $firstName,
            'middle_name'  => $middleName,
            'last_name'  => $lastName,
            'mobile'  => $mobile,
            'gender'  => $gender,
            'dob'  => $dob,
            'occupation'  => $occupation,
            'is_authority'  => '0',
            'permissions'  => PERMISSION_DEFAULT,
            'is_verified'  => '0',
            'is_active'  => '1',
            'service'  => '',
            'created_on'  => $date,
            'profile_pic' => '',
            'fcm_token' => $fcmToken,
            'timestamp'=> $timestamp
        ];
        $result = $model->insertUser($data);
        return $this->respond($result);
    }

    public function deleteUser()
    {
        $model = new UserModel();
        $id = Common::getParam(id);
        $result = $model->delete($id);
        if ($result) {
            return $this->respond(Common::createResponse(0, "User Deleted"));
        } else $this->respond(Common::createResponse(1, "Failed to Delete User"));
    }

    public function updateFCMToken()
    {
        $model = new UserModel();
        $model->fcmToken = Common::getParam(fcmToken);
        $model->id = Common::getParam(id);
        $result = $model->updateFCMToken();
        return $this->respond($result);
    }

    public function modifyPermissions()
    {
        $model = new UserModel();
        $model->id = Common::getParam(id);
        $model->permissions = Common::getParam(permissions);
        $result = $model->modifyPermissions();
        return $this->respond($result);
    }

    public function changeActiveStatus()
    {
        $model = new UserModel();
        $model->id = Common::getParam(id);
        $model->isActive = Common::getParam(isActive);
        $result = $model->changeActiveStatus();
        return $this->respond($result);
    }

    public function updateUser()
    {
        $model = new UserModel();
        $model->id = Common::getParam(id);
        $model->userId = Common::getParam(userId);
        $result = $model->updateUser();
        return $this->respond($result);
    }

    public function updateProfilePic()
    {
        $model=new UserModel();
        $model->profilePic=Common::getFile(profilePic);
        $model->userId = Common::getParam(userId);
        $result=$model->updateProfilePic();
        return $this->respond($result);
    }

    public function getOccupations()
    {
        $model = new UserModel();
        $model->id = Common::getParam(id);
        $model->userId = Common::getParam(userId);
        $result = $model->getOccupations();
        return $this->respond($result);
    }
}
