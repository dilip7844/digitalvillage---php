<?php

namespace App\Models;

use App\Controllers\Common;
use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table = "events";
    protected $primaryKey = 'event_id';
    protected $allowedFields = [
        'event_id', 'event', 'description','category','venue','venue_lat_lng','images', 'interested', 'going',
        'is_visible','start_date','end_date', 'created_by', 'created_on', 'timestamp'
    ];

    var  $id = null;
    var  $userId = null;
    var  $search = null;
    var  $limit = null;
    var  $offset = null;
    var  $isVisible = null;

    var $event = null;
    var $description = null;
    var $category = null;
    var $images = null;
    var $startDate = null;
    var $endDate = null;
    var $venue = null;
    var $venueLatLng = null;

    var $result = null;


    public function selectEvents()
    {
        //$fields="*";
        $fields="event_id,event,venue,description,category,images,interested,going,events.created_on,created_by,first_name,last_name,profile_pic";
        $query = 'SELECT '.$fields.' from ' . $this->table . ' LEFT JOIN users ON events.created_by = users.id';
        //echo $query;
        if ($this->search != null || $this->isVisible != null)
            $query = $query . " where";

        if ($this->isVisible != null) {
            $query = $query . " events.is_visible=" . $this->isVisible . "";
            if ($this->search != null)
                $query = $query . " and";
        }

        if ($this->search != null) {
            $query = $query . " (events.event like '%" . $this->search . "%')";
        }
        $query = $query . ' order by event_id desc';

        if ($this->limit != null && $this->offset != null) {
            $query = $query . ' limit ' . $this->limit . ' offset ' . $this->offset;
        }
        //echo $query;

        $this->result = $this->db->query($query);
        $res = $this->result->getResult();
        if ($this->isResultEmpty())
            return Common::createResponse(STATUS_NO_DATA, "No Events");
        return Common::createResponse(STATUS_SUCCESS, "Event Fetched", $res);
    }

    public function insertEvent()
    {
        $userModel = new UserModel();
        if ($userModel->hasPermission($this->userId, PERMISSION_ADD_EVENT)) {
            $data = [
                'event' => $this->event,
                'description' => $this->description,
                'images'  => '',
                'venue'  => $this->venue,
                'start_date'  => $this->startDate,
                'end_date'  => $this->endDate,
                'venue_lat_lng'  => $this->venueLatLng,
                'interested'  => '',
                'going'  => '',
                'is_visible'  => '1',
                'created_by'  => $this->userId,
                'created_on'  => Common::getCurrentTime(),
                'timestamp' => Common::getCurrentTimeStamp()
            ];
            $res = $this->insert($data);
            if ($res) {
                $root = Common::createResponse(STATUS_SUCCESS, "Event Created");
                $insertedId = $this->getInsertID();
                //uploading image if any
                if (!empty($this->images)) {
                    $fileModel = new FileModel($this->images);
                    $imagesPathString = '';
                    for ($i = 0; $i < $fileModel->getFileCount(); $i++) {
                        $newname = $insertedId . '-EVENT_' . $i . '.png';
                        $location = Common::rootOfEvents() . $newname;
                        if (Common::uploadImage($fileModel->getTempName($i), $location)) {
                            if ($imagesPathString == '')
                                $imagesPathString = $location;
                            else $imagesPathString = $imagesPathString . ',' . $location;
                        }
                    }
                    $this->result = $this->db->query("UPDATE " . $this->table . " set images='" . $imagesPathString . "' where event_id=" . $insertedId);
                }
                $root->data = $this->find($insertedId);
                return $root;
            } else {
                return Common::createResponse(STATUS_FAILED, "Failed to Create Event");
            }
        } else return Common::createResponse(STATUS_NO_PERMISSION, "You don't have permission to create Event");
    }

    public function getEvent()
    {
        $this->result = $this->db->query('SELECT * from ' . $this->table . ' LEFT JOIN users ON events.created_by = users.id where event_id=' . $this->id);
        $res = $this->result->getResult();
        if ($this->isResultEmpty())
            return Common::createResponse(STATUS_NO_DATA, "Event not found");
        return Common::createResponse(STATUS_SUCCESS, "Event Fetched", $res);
    }

    private function isResultEmpty()
    {
        return $this->result->getNumRows() == 0;
    }
}
