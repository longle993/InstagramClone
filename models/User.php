<?php

namespace models;
use classes\{Hash, Config, Session, DB, Cookie};
class User implements \JsonSerializable {
    private $db,
        $sessionName,
        $cookieName,
        $id,
        $username='',
        $email='',
        $password='',
        $salt='',
        $firstname='',
        $lastname='',
        $joined='',
        $user_type=1,
        $bio='',
        $cover='',
        $picture='',
        $private=-1,
        $last_active_update='',
        $isLoggedIn=false;
    public function __construct() {
        $this->db = DB::getInstance();
        $this->sessionName = Config::get('session/session_name');
        $this->cookieName = Config::get('remember/cookie_name');
        if(Session::exists($this->sessionName)) {
            $dt = Session::get($this->sessionName);
            if($this->fetchUser("id", $dt)) {
                $this->isLoggedIn = true;
            }
        }
    }
    public function getPropertyValue($propertyName) {
        return $this->$propertyName;
    }
    public function setPropertyValue($propertyName, $propertyValue) {
        $this->$propertyName = $propertyValue;
    }
    // public function get_metadata($label="") {
    //     $metadata = array();
    //     $values = array($this->id);
    //     $query = "SELECT * FROM user_metadata WHERE `user_id` = ?";
    //     if(!empty($label)) {
    //         $query .= " AND `label` = ?";
    //         $values[] = $label;
    //     }
    //     $this->db->query($query, $values);
    //     return $this->db->results();
    // }
    // public function get_metadata_items_number() {
    //     $this->db->query("SELECT COUNT(*) as number_of_labels FROM user_metadata WHERE `user_id` = ?", array($this->id));
    //     if(count($this->db->results()) > 0) {
    //         return $this->db->results()[0]->number_of_labels;
    //     }
    //     return array();
    // }
    // public function metadata_exists($label) {
    //     $this->db->query("SELECT COUNT(*) as number_of_labels FROM user_metadata WHERE `label`=?  AND `user_id`=?", array(
    //         $label,
    //         $this->id
    //     ));
    //     if($this->db->results()[0]->number_of_labels != 0) {
    //         return true;
    //     }
    //     return false;
    // }
    // public function add_metadata($label, $content) {
    //     if($this->get_metadata_items_number() < 6 && $content != "") {
    //         $this->db->query("INSERT INTO user_metadata (`label`, `content`, `user_id`) values(?, ?, ?);", array(
    //             $label,
    //             $content,
    //             $this->id
    //         ));
    //         return true;
    //     }
    //     return false;
    // }
    // public function update_metadata($label, $content) {
    //     $this->db->query("UPDATE user_metadata SET `content`=? WHERE `label`=? AND `user_id`=?", array(
    //         $content,
    //         $label,
    //         $this->id
    //     ));
    //     return $this->db->error() == false ? true : false;
    // }
    // public function set_metadata($metadata) {
    //     foreach($metadata as $mdata) {
    //         if($this->metadata_exists($mdata["label"])) {
    //             $this->update_metadata($mdata["label"], $mdata["content"]);
    //         } else {
    //             $this->add_metadata($mdata["label"], $mdata["content"]);
    //         }
    //     }
    // }
    public static function user_exists($field, $value) {
        DB::getInstance()->query("SELECT * FROM user_info WHERE $field = ?", array($value));
        if(DB::getInstance()->count() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function fetchUser($field_name, $field_value) {
        $this->db->query("SELECT * FROM user_info WHERE $field_name = ?", array($field_value));
        if($this->db->count() > 0) {
            $fetchedUser = $this->db->results()[0];
            $this->id = $fetchedUser->id;
            $this->username = $fetchedUser->username;
            $this->email = $fetchedUser->email;
            $this->password = $fetchedUser->password;
            $this->salt = $fetchedUser->salt;
            $this->firstname = $fetchedUser->firstname;
            $this->lastname = $fetchedUser->lastname;
            $this->joined = $fetchedUser->joined;
            $this->user_type = $fetchedUser->user_type;
            $this->bio = $fetchedUser->bio;
            $this->picture = $fetchedUser->picture;
            $this->last_active_update = $fetchedUser->last_active_update;
            return $this;
        }
        return false;
    }
    public function setData($data = array()) {
        $this->id = $data["id"];
        $this->username = $data["username"];
        $this->email = $data["email"];
        $this->password = $data["password"];
        $this->salt = $data["salt"];
        $this->firstname = $data["firstname"];
        $this->lastname = $data["lastname"];
        $this->joined = isset($data["joined"]) ? $data["joined"] : date("Y/m/d h:i:s");
        $this->user_type = $data["user_type"];
        $this->bio = $data["bio"];
        $this->picture = $data["picture"];
    }
    public function add() {
        $this->db->query("INSERT INTO user_info 
        (username, email, password, salt, firstname, lastname, joined, user_type) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)", array(
            $this->username,
            $this->email,
            $this->password,
            $this->salt,
            $this->firstname,
            $this->lastname,
            $this->joined,
            $this->user_type
        ));
        return $this->db->error() == false ? true : false;
    }
    public function update() {
        $this->db->query("UPDATE user_info SET username=?, email=?, password=?, salt=?, firstname=?, lastname=?, joined=?, user_type=?, bio=?, picture=? WHERE id=?",
        array(
            $this->username,
            $this->email,
            $this->password,
            $this->salt,
            $this->firstname,
            $this->lastname,
            $this->joined,
            $this->user_type,
            $this->bio,
            $this->picture,
            $this->id
        ));
        return ($this->db->error()) ? false : true;
    }
    public function update_property($property, $new_value) {
        $this->db->query("UPDATE user_info SET $property=? WHERE id=?",
        array(
            $new_value,
            $this->id
        ));
        return ($this->db->error()) ? false : true;
    }
    public function delete() {
        $this->db->query("DELETE FROM user_info WHERE id = ?", array($this->id));
        return ($this->db->error()) ? false : true;
    }
    public static function search($keyword) {
        if(empty($keyword)) {
            return array();
        }
        $keywords = strtolower($keyword);
        $keywords = htmlspecialchars($keywords);
        $keywords = trim($keywords);
        $keywords = explode(' ', $keyword);
        if($keywords[0] == '') {
            $query = "";
        } else {
            $query = "SELECT * FROM user_info ";
            for($i=0;$i<count($keywords);$i++) {
                $k = $keywords[$i];
                if($i==0)
                    $query .= "WHERE username LIKE '%$k%' OR firstname LIKE '%$k%' OR lastname LIKE '%$k%' ";
                else
                    $query .= "OR username LIKE '%$k%' OR firstname LIKE '%$k%' OR lastname LIKE '%$k%' ";
            }
        }
        DB::getInstance()->query($query);
        return DB::getInstance()->results();
    }
    public static function search_by_username($username) {
        if(empty($username)) {
            return array();
        }
        $keyword = strtolower($username);
        $keyword = htmlspecialchars($username);
        $keyword = trim($username);
        DB::getInstance()->query("SELECT * FROM user_info WHERE username LIKE '$keyword%'");
        return DB::getInstance()->results();
    }
    public function login($email_or_username='', $password='', $remember=false) {
        if($this->id) {
            Session::put($this->sessionName, $this->id);
            $this->isLoggedIn = true;
            return true;
        } else {
            $fetchBy = "username";
            if(strpos($email_or_username, "@")) {
                $fetchBy = "email";
            }
            if($this->fetchUser($fetchBy, $email_or_username)) {
                if($this->password === Hash::make($password, $this->salt)) {
                    Session::put($this->sessionName, $this->id);
                    if($remember) {
                        $this->db->query("SELECT * FROM users_session WHERE user_id = ?",
                            array($this->id));
                        if(!$this->db->count()) {
                            $hash = Hash::unique();
                            $this->db->query('INSERT INTO users_session (user_id, hash) VALUES (?, ?)', 
                                array($this->id, $hash));
                        } else {
                            $hash = $this->db->results()[0]->hash;
                        }
                        Cookie::put($this->cookieName, $hash, Config::get("remember/cookie_expiry"));
                    }
                    return true;
                }
            }
        }
        return false;
    }
    public function logout() {
        $this->db->query("DELETE FROM users_session WHERE user_id = ?", array($this->id));
        Session::delete($this->sessionName);
        Session::delete(Config::get("session/tokens/logout"));
        Cookie::delete($this->cookieName);
    }
    public function update_active() {
        $this->db->query("UPDATE user_info SET last_active_update=? WHERE id=?",
        array(
            date("Y/m/d h:i:s A"),
            $this->id
        ));
        return ($this->db->error()) ? false : true;
    }
    public function isLoggedIn() {
        return $this->isLoggedIn;
    }
    public function jsonSerialize()
    {
        $vars = array(
            "id"=>$this->id,
            "username"=>$this->username
        );
        return $vars;
    }
    public static function get_friends($user_id) {
        DB::getInstance()->query("SELECT * FROM user_info WHERE `id` != ?",
        array(
            $user_id
        ));
        $relations = DB::getInstance()->results();
        $friends = array();
        foreach($relations as $relation) {
            $friend_id = $relation->id;
            $user = new User();
            $user->fetchUser("id", $friend_id);
            $friends[] = $user;
        }
        return $friends;
    }
}