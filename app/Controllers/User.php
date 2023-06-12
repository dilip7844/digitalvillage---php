<?php

namespace App\Controllers;

use App\Models\ResultModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

// TODO //Need to confirm if we have to close db connection at the end ..

class User extends BaseController
{
    use ResponseTrait;
    var $response = null;

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
        $res = $model->selectUsers();
        if (empty($res))
            $response = Common::createResponse(STATUS_NO_DATA, "No Users");
        else
            $response = Common::createResponse(STATUS_SUCCESS, "Users Fetched", $res);
        return $this->respond($response);
    }

    public function getUser()
    {
        $model = new UserModel();
        $model->id = Common::getParam(id);
        $model->mobile = Common::getParam(mobile);
        $res = $model->getUser();
        if (empty($res))
            $response = Common::createResponse(STATUS_USER_NOT_FOUND, "User not found");
        else
            $response = Common::createResponse(STATUS_SUCCESS, "User Fetched", $res);
        return $this->respond($response);
    }

    public function createUser()
    {
        $model = new UserModel();
        $model->name = Common::getParam(name);
        $model->mobile = Common::getParam(mobile);
        $model->gender = Common::getParam(gender);
        $model->occupationId = Common::getParam(occupationId);
        $model->fcmToken = Common::getParam(fcmToken);
        $model->dob = Common::getParam(dob);
        $model->address = Common::getParam(address);

        $result = $model->insertUser();
        switch ($result->statusCode) {
            case STATUS_SUCCESS:
                $response = Common::createResponse(STATUS_SUCCESS, "User Created Successfully", $result->data);
                break;
            case STATUS_ALREADY_EXIST:
                $response = Common::createResponse(STATUS_ALREADY_EXIST, "User already exist with mobile Number " . $model->mobile);
                break;
         default:
                $response = Common::validateResponse($result->statusCode, "Failed to create user ");
                break;
        }
        return $this->respond($response);
    }

    public function deleteUser()
    {
        $model = new UserModel();
        $id = Common::getParam(id);
        $result = $model->delete($id);
        if ($result) {
            return $this->respond(Common::createResponse(0, "User Deleted"));
        } else
            $this->respond(Common::createResponse(1, "Failed to Delete User"));
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
        $model->userId = Common::getParam(userId);
        $resultCode = $model->changeActiveStatus();
        switch ($resultCode) {
            case STATUS_SUCCESS:
                $response = Common::createResponse(STATUS_SUCCESS, "Status Changed Successfully");
                break;
                default:
                $response=Common::validateResponse($resultCode,"Failed to change status");
                break;
        }
        return $this->respond($response);
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
        $model = new UserModel();
        $model->profilePic = Common::getFile(profilePic);
        $model->userId = Common::getParam(userId);
        $result = $model->updateProfilePic();
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