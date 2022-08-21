<?php

namespace App\Controllers;

use App\Models\EventModel as MEventModel;
use CodeIgniter\API\ResponseTrait;

class Event extends BaseController
{
    use ResponseTrait;
    public function __construct()
    {
        $domain = Common::getDomain();
        \Config\Database::connect('default')->setDatabase($domain);
    }

    public function getAllEvents()
    {
        $model = new MEventModel();
        $model->limit = Common::getParam(limit);
        $model->offset = Common::getParam(offset);
        $model->search = Common::getParam(search);
        $model->isVisible = Common::getParam(isVisible);

        //  $desc = Common::getParam(desc);
        $result = $model->selectEvents();
        return $this->respond($result);
    }


    public function createEvent()
    {
        $model = new MEventModel();
        $model->event = Common::getParam(event);
        $model->venue = Common::getParam(venue);
        $model->venueLatLng = Common::getParam(venueLatLng);
        $model->description = Common::getParam(description);
        $model->startDate = Common::getParam(startDate);
        $model->endDate = Common::getParam(endDate);
        $model->userId = Common::getParam(userId);
        $model->images = Common::getFile(images);
        $result = $model->insertEvent();
        return $this->respond($result);
    }

    public function getEvent()
    {
        $model = new MEventModel();
        $model->id = Common::getParam(id);
        $model->userId = Common::getParam(userId);
        $result = $model->getEvent();
        return $this->respond($result);
    }
}
