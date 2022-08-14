<?php

namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use stdClass;

//User
define('id', 'id'); //int
define('firstName', 'firstName');
define('middleName', 'middleName');
define('lastName', 'lastName');
define('dob', 'dob');
define('mobile', 'mobile');
define('gender', 'gender');
define('occupation', 'occupation');
define('isAuthority', 'isAuthority');
define('isVerified', 'isVerified');
define('permissions', 'permissions');
define('fcmToken', 'fcmToken');
define('isActive', 'isActive');
define('domain', 'domain');
define('limit', 'limit');
define('offset', 'offset');
define('search', 'search');
define('desc', 'desc');  //boolean
define('userId', 'userId');

//Notification
define('sender', 'sender');
define('receivers', 'receivers');
define('title', 'title');
define('message', 'message');
define('icon', 'icon');
define('bigIcon', 'bigIcon');
define('extras', 'extras');



define('STATUS_SUCCESS', '0');
define('STATUS_FAILED', '1');
define('STATUS_NO_DATA', '2');
define('STATUS_USER_NOT_FOUND', '3');
define('STATUS_USER_INACTIVE', '4');
define('STATUS_USER_UNVERIFIED', '5');



class Common
{
    use ResponseTrait;

    public static function createResponse($status, $message, $data = null)
    {
        $obj = new stdClass();
        $obj->status = $status;
        $obj->message = $message;
        if ($data != null) {
            $obj->data = $data;
        }
        return $obj;
    }

    public static function getParam($param)
    {
        if (isset($_POST[$param]))
            return $_POST[$param];
        else return null;
    }

    public static function getCurrentTime($pattern = 'd-m-Y H:i:s')
    {
        return date($pattern);
    }
}
