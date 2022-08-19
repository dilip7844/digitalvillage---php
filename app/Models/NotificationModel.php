<?php

namespace App\Models;

use App\Controllers\Common;
use CodeIgniter\Model;
use PDO;


class NotificationModel extends Model
{
    protected $table = "notifications";
    protected $primaryKey = 'notification_id';
    protected $allowedFields = [
        'notification_id', 'title', 'message', 'sender', 'receivers', 'icon', 'big_icon', 'extras', 'date'
    ];

    var  $id = null;
    var $sender = null;
    var $title = null;
    var $message = null;
    var $receivers = null;
    var $extras = null;
    var $icon = null;
    var $bigIcon = null;

    var $result = null;

    public function getNotifications()
    {
        $this->result = $this->db->query('SELECT * from ' . $this->table);
        $res = $this->result->getResult();
        if ($this->isResultEmpty())
            return Common::createResponse(STATUS_NO_DATA, "No Notifications");
        return Common::createResponse(STATUS_SUCCESS, "Notifications Fetched", $res);
    }

    public function sendNotification()
    {
        $tokenList = $this->getTokens($this->receivers);
        $url = "https://fcm.googleapis.com/fcm/send";
        $serverKey = 'AAAAGw4kAy0:APA91bGKwzb3ytk8X-sRHv6WQyE4ZDv3xk72yJ8NGKaN7qVdrivGHzAosjj1m5ObtZGyMsqcPCt_SQc7vIma-NEA1JjGA0sIBB5k6arlJOz5qetODVG6aGLQVr6Ypn-AWGUVLqzpzdjs';
        $notification = array('title' => $this->title, 'body' => $this->message, 'sound' => 'default', 'badge' => '1');
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //print_r($tokenList[1] . fcmToken);
        $foundToken = 0;
        $successCount = 0;
        $failureCount = 0;
        foreach ($tokenList as $row) {
            $token = $row['fcm_token'];
            if ($token != '') {
                $arrayToSend = array('to' => $token, 'notification' => $notification, 'priority' => 'high');
                $json = json_encode($arrayToSend);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                //Send the request
                $response = curl_exec($ch);
                $result = json_decode($response);
                if ($result->success == '1')
                    ++$successCount;
                else ++$failureCount;
                // print_r($result);
                ++$foundToken;
            }
        }
        curl_close($ch);
        $data = array();
        $data["success_count"] = $successCount;
        $data["failure_count"] = $failureCount;
        $data["token_missing"] = count($tokenList) - $foundToken;
        $data["token_present"] = $foundToken;
        if ($successCount > $failureCount) {
            $res = $this->insertNotification();
            return Common::createResponse(STATUS_SUCCESS, "Notification Sent Successfully", $data);
        } else {
            return Common::createResponse(STATUS_FAILED, "Failed to send notification", $data);
        }
    }

    private function getTokens($ids)
    {
        $query = 'SELECT fcm_token from users where id in (' . $ids  . ')';
        $this->result = $this->db->query($query);
        $res = $this->result->getResultArray();
        return $res; // returns list of FCM tokens
    }

    private function insertNotification()
    {
        $data = [
            'title' => $this->title,
            'message' => $this->message,
            'icon' => $this->icon,
            'big_icon' => $this->bigIcon,
            'extras' => $this->extras,
            'sender' => $this->sender,
            'receivers' => $this->receivers,
            'date' => Common::getCurrentTime()
        ];

        $res = $this->insert($data);
        if ($res)
            return true;
        else return false;
    }

    private function isResultEmpty()
    {
        return $this->result->getNumRows() == 0;
    }
}
