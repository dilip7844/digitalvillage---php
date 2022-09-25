<?php

namespace App\Models;

use App\Controllers\Common;
use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table = "posts";
    protected $primaryKey = 'post_id';
    protected $allowedFields = [
        'post_id', 'post', 'images', 'liked_by', 'disliked_by', 'is_visible', 'business_id', 'is_business_post', 'created_by', 'created_on', 'timestamp'
    ];

    var  $id = null;
    var  $userId = null;
    var  $search = null;
    var  $businessId = null;
    var  $limit = null;
    var  $offset = null;
    var  $isVisible = null;

    var $post = null;
    var $images = null;
    var $result = null;


    public function selectPosts()
    {
        //$fields="*";
        $fields = "post_id,post,images,liked_by,disliked_by,posts.created_on,created_by,first_name,last_name,profile_pic";
        $query = 'SELECT ' . $fields . ' from ' . $this->table . ' LEFT JOIN users ON posts.created_by = users.id';
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
            return Common::createResponse(STATUS_NO_DATA, "No Posts");
        return Common::createResponse(STATUS_SUCCESS, "Posts Fetched", $res);
    }

    public function insertPost()
    {
        $userModel = new UserModel();
        if ($userModel->hasPermission($this->userId, PERMISSION_ADD_POST)) {
            $isBusinessPost = '0';
            if ($this->businessId != null)
                $isBusinessPost = '1';
            $data = [
                'post' => $this->post,
                'images'  => '',
                'liked_by'  => '',
                'disliked_by'  => '',
                'is_visible'  => '1',
                'is_business_post'  => $isBusinessPost,
                'business_id'  => $this->businessId,
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
        $this->result = $this->db->query('SELECT * from ' . $this->table . ' LEFT JOIN users ON posts.created_by = users.id where post_id=' . $this->id);
        $res = $this->result->getResult();
        if ($this->isResultEmpty())
            return Common::createResponse(STATUS_NO_DATA, "Post not found");
        return Common::createResponse(STATUS_SUCCESS, "Post Fetched", $res);
    }

    public function likePost()
    {
        if (!$this->isExistInArray('liked_by', $this->userId, "post_id=" . $this->id)) {
            // if not liked . Do like
            $arr = $this->db->query("Select liked_by from " . $this->table . " where post_id= " . $this->id);
            if ($arr->getNumRows() > 0)
                $res = $arr->getRowArray(0);
            else return "";
            if ($res["liked_by"] == "")
                $this->db->query("update " . $this->table . " set liked_by=" . $this->userId . " where post_id= " . $this->id);
            else  $this->db->query("update " . $this->table . " set liked_by= concat(liked_by,'," . $this->userId . "') where post_id= " . $this->id);

            if ($this->db->affectedRows() > 0) {
                $msg = "";
                /// Removing dislike
                if ($this->isExistInArray('disliked_by', $this->userId, "post_id=" . $this->id)) {
                    $newDislikeString = $this->removeItemFromArray('disliked_by', $this->userId, "post_id=" . $this->id);
                    $this->db->query("update " . $this->table . " set disliked_by='" . $newDislikeString . "' where post_id= " . $this->id);
                    $msg = " & Removed Dislike";
                }
                return Common::createResponse(STATUS_ACTION_POSITIVE, "Post Liked " . $msg);
            } else return Common::createResponse(STATUS_FAILED, "Post Liked Failed");
        } else { // remove like
            $newLikeString = $this->removeItemFromArray('liked_by', $this->userId, "post_id=" . $this->id);
            $this->db->query("update " . $this->table . " set liked_by='" . $newLikeString . "' where post_id= " . $this->id);
            if ($this->db->affectedRows() > 0)
                return Common::createResponse(STATUS_ACTION_NEGATIVE, "Post Like Removed");
            else return Common::createResponse(STATUS_FAILED, "Unable to remove Post Like");
        }
    }

    public function dislikePost()
    {
        if (!$this->isExistInArray('disliked_by', $this->userId, "post_id=" . $this->id)) {
            // if not disliked . Do Dislike
            $arr = $this->db->query("Select disliked_by from " . $this->table . " where post_id= " . $this->id);
            if ($arr->getNumRows() > 0)
                $res = $arr->getRowArray(0);
            else return "";
            if ($res["disliked_by"] == "")
                $this->db->query("update " . $this->table . " set disliked_by=" . $this->userId . " where post_id= " . $this->id);
            else  $this->db->query("update " . $this->table . " set disliked_by= concat(disliked_by,'," . $this->userId . "') where post_id= " . $this->id);

            if ($this->db->affectedRows() > 0) {
                $msg = "";
                /// Removing dislike
                if ($this->isExistInArray('liked_by', $this->userId, "post_id=" . $this->id)) {
                    $newDislikeString = $this->removeItemFromArray('liked_by', $this->userId, "post_id=" . $this->id);
                    $this->db->query("update " . $this->table . " set liked_by='" . $newDislikeString . "' where post_id= " . $this->id);
                    $msg = " & Removed Like";
                }
                return Common::createResponse(STATUS_ACTION_POSITIVE, "Post Disliked " . $msg);
            } else return Common::createResponse(STATUS_FAILED, "Post Disliked Failed");
        } else { // remove like
            $newLikeString = $this->removeItemFromArray('disliked_by', $this->userId, "post_id=" . $this->id);
            $this->db->query("update " . $this->table . " set disliked_by='" . $newLikeString . "' where post_id= " . $this->id);
            if ($this->db->affectedRows() > 0)
                return Common::createResponse(STATUS_ACTION_NEGATIVE, "Post Dislike Removed");
            else return Common::createResponse(STATUS_FAILED, "Unable to remove Post Dislike");
        }
    }

    private function isExistInArray($field, $contains, $where)
    {
        $query = 'SELECT ' . $field . ' from ' . $this->table . ' where ' . $where . '';
        $res = $this->db->query($query)->getRowArray(0);
        $array = explode(',', $res[$field]);
        $isExists = array_search($contains, $array);
        return $isExists != '';
    }

    private function removeItemFromArray($field, $item, $where) // returns updated String
    {
        $query = 'SELECT ' . $field . ' from ' . $this->table . ' where ' . $where . '';
        $res = $this->db->query($query)->getRowArray(0);
        $array = explode(',', $res[$field]);
        $isExists = array_search($item, $array);
        if ($isExists !== FALSE) {
            unset($array[$isExists]);
        }
        $str = implode(',', $array);
        return $str;
    }

    public function remove()
    {
    }

    private function isResultEmpty()
    {
        return $this->result->getNumRows() == 0;
    }
}
