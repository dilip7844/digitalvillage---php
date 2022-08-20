<?php

namespace App\Models;

use App\Controllers\Common;
use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = "posts";
    protected $primaryKey = 'post_id';
    protected $allowedFields = [
        'post_id', 'post', 'images', 'liked_by', 'disliked_by', 'created_by', 'created_on', 'timestamp'
    ];

    var  $id = null;
    var  $userId = null;

    var $result = null;

    public function getPost()
    {
        $this->result = $this->db->query('SELECT * from ' . $this->table . ' where post_id=' . $this->id);
        $res = $this->result->getResult();
        if ($this->isResultEmpty())
            return Common::createResponse(STATUS_NO_DATA, "Post not found");
        return Common::createResponse(STATUS_SUCCESS, "Post Fetched", $res);
    }

    private function isResultEmpty()
    {
        return $this->result->getNumRows() == 0;
    }
}
