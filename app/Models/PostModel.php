<?php

namespace App\Models;

use App\Controllers\Common;
use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = "posts";
    protected $primaryKey = 'post_id';
    protected $allowedFields = [
        'post_id', 'post', 'images', 'liked_by', 'disliked_by', 'is_visible', 'created_by', 'created_on', 'timestamp'
    ];

    var  $id = null;
    var  $userId = null;
    var  $search = null;
    var  $limit = null;
    var  $offset = null;
    var  $isVisible = null;

    var $post = null;
    var $images = null;
    var $result = null;


    public function selectPosts()
    {
        //$fields="*";
        $fields="post_id,post,images,liked_by,disliked_by,posts.created_on,created_by,first_name,last_name,profile_pic";
        $query = 'SELECT '.$fields.' from ' . $this->table . ' LEFT JOIN users ON posts.created_by = users.id';
        //echo $query;
        if ($this->search != null || $this->isVisible != null)
            $query = $query . " where";

        if ($this->isVisible != null) {
            $query = $query . " posts.is_visible=" . $this->isVisible . "";
            if ($this->search != null)
                $query = $query . " and";
        }

        if ($this->search != null) {
            $query = $query . " (posts.post like '%" . $this->search . "%')";
        }
        $query = $query . ' order by post_id desc';

        if ($this->limit != null && $this->offset != null) {
            $query = $query . ' limit ' . $this->limit . ' offset ' . $this->offset;
        }
        //echo $query;

        $this->result = $this->db->query($query);
        $res = $this->result->getResult();
        if ($this->isResultEmpty())
            return Common::createResponse(STATUS_USER_NOT_FOUND, "Post not found");
        return Common::createResponse(STATUS_SUCCESS, "Post Fetched", $res);
    }

    public function insertPost()
    {
        $userModel = new UserModel();
        if ($userModel->hasPermission($this->userId, PERMISSION_ADD_POST)) {
            $data = [
                'post' => $this->post,
                'images'  => '',
                'liked_by'  => '',
                'disliked_by'  => '',
                'is_visible'  => '1',
                'created_by'  => $this->userId,
                'created_on'  => Common::getCurrentTime(),
                'timestamp' => Common::getCurrentTimeStamp()
            ];
            $res = $this->insert($data);
            if ($res) {
                $root = Common::createResponse(STATUS_SUCCESS, "Post Created");
                $insertedId = $this->getInsertID();
                //uploading image if any
                if (!empty($this->images)) {
                    $fileModel = new FileModel($this->images);
                    $imagesPathString = '';
                    for ($i = 0; $i < $fileModel->getFileCount(); $i++) {
                        $newname = $insertedId . '-POST_' . $i . '.png';
                        $location = Common::rootOfPosts() . $newname;
                        if (Common::uploadImage($fileModel->getTempName($i), $location)) {
                            if ($imagesPathString == '')
                                $imagesPathString = $location;
                            else $imagesPathString = $imagesPathString . ',' . $location;
                        }
                    }
                    $this->result = $this->db->query("UPDATE " . $this->table . " set images='" . $imagesPathString . "' where post_id=" . $insertedId);
                }
                $root->data = $this->find($insertedId);
                return $root;
            } else {
                return Common::createResponse(STATUS_FAILED, "Failed to Create Post");
            }
        } else return Common::createResponse(STATUS_NO_PERMISSION, "You don't have permission to create post");
    }

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
