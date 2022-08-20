<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\API\ResponseTrait;
use PDO;
use stdClass;

// TODO //Need to confirm if we have to close db connection at the end ..


class Notification extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $domain = Common::getDomain();
        \Config\Database::connect('default')->setDatabase($domain);
    }

    public function getNotifications()
    {
        $model = new NotificationModel();
        $result = $model->getNotifications();
        return $this->respond($result);
    }

    public function sendNotification()
    {
        $model = new NotificationModel();
        $model->sender = Common::getParam(sender);
        $model->receivers = Common::getParam(receivers);
        $model->title = Common::getParam(title);
        $model->message = Common::getParam(message);
        $model->icon = Common::getParam(icon);
        $model->bigIcon = Common::getParam(bigIcon);
        $model->extras = Common::getParam(extras);
        $result = $model->sendNotification();
        if ($result)
            return $this->respond($result);
    }
}
