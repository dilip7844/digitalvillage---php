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
define('address', 'address');
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
define('profilePic', 'profilePic');

//Notification
define('sender', 'sender');
define('receivers', 'receivers');
define('title', 'title');
define('message', 'message');
define('icon', 'icon');
define('bigIcon', 'bigIcon');
define('extras', 'extras');

//post && events
define('post', 'post');
define('event', 'event');
define('description', 'description');
define('venue', 'venue');
define('venueLatLng', 'venueLatLng');
define('startDate', 'startDate');
define('endDate', 'endDate');
define('category', 'category');
define('images', 'images');
define('isVisible', 'isVisible');

//STATUS CODES
define('STATUS_SUCCESS', '0');
define('STATUS_FAILED', '1');
define('STATUS_NO_DATA', '2');
define('STATUS_USER_NOT_FOUND', '3');
define('STATUS_USER_INACTIVE', '4');
define('STATUS_USER_UNVERIFIED', '5');
define('STATUS_NO_PERMISSION', '6');

// PERSMISSIONS
define('PERMISSION_VIEW_USER', 'view_user');
define('PERMISSION_ADD_USER', 'add_user');
define('PERMISSION_EDIT_USER', 'edit_user');
define('PERMISSION_ADD_POST', 'add_post');
define('PERMISSION_ADD_EVENT', 'add_event');

//DEFAULT USER PERMISSIONS
define('PERMISSION_DEFAULT', PERMISSION_VIEW_USER . ',' . PERMISSION_ADD_POST);

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

    public static function getDomain()
    {
        // to concat to db initials name with domain
        return '' . Common::getParam(domain);
    }

    public static function getParam($param)
    {
        if (isset($_POST[$param]))
            return $_POST[$param];
        else return null;
    }

    public static function getFile($param)
    {
        if (isset($_FILES[$param]))
            return $_FILES[$param];
        else return null;
    }

    public static function setTimeZone()
    {
        date_default_timezone_set("Asia/Kolkata");
    }

    public static function getCurrentTime($pattern = 'd M Y H:i:s')
    {
        Common::setTimeZone();
        return date($pattern);
    }

    public static function getCurrentTimeStamp()
    {
        Common::setTimeZone();
        return time();
    }

    public static function uploadImage($tmpName, $location)
    {
        return move_uploaded_file($tmpName, $location);
    }

    public static function rootOfData()
    {
        return 'data/';
    }

    public static function rootOfDomain()
    {
        return Common::rootOfData() . Common::getDomain() . '/';
    }

    public static function rootOfUploads()
    {
        return Common::rootOfDomain() . 'uploads/';
    }

    public static function rootOfProfilePictures()
    {
        return Common::rootOfUploads() . 'profile_pictures/';
    }

    public static function rootOfPosts()
    {
        return Common::rootOfUploads() . 'posts/';
    }
    public static function rootOfEvents()
    {
        return Common::rootOfUploads() . 'events/';
    }
    
    
}
