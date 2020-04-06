<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 26/10/2018
 * Time: 23:30
 */

class User_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
    }

    private function get_metas_group()
    {
        return array(
            'user_photo',
            'profile_pic',
            'locale',
            'timezone',
            'gender',
            'wait',
            'wait_for',
        );
    }

    public function getUserByKeyword($keyword, $value){
        /*$data=$this->db->query("SELECT * FROM users where bot_subscriber_id = $value")->row_array();
        var_dump_pre($this->db->last_query());
        var_dump_pre($data);exit;*/
        $data = $this->db->get_where('users', [$keyword=>$value])->row_array();
        if(!empty($data)){
            return $this->getUserMeta($data);
        }
        return $data;
    }

    public function getUserIDByKeyword($keyword, $value){
        /*$data=$this->db->query("SELECT * FROM users where bot_subscriber_id = $value")->row_array();
        var_dump_pre($this->db->last_query());
        var_dump_pre($data);exit;*/
        $this->db->select('id');
        $this->db->where([$keyword=>$value]);
        $result= $this->db->get('users')->row_array();
        return maybe_null_or_empty($result, 'id');
    }

    public function getUserMeta(array $userArrayData)
    {
        if (!empty($userArrayData)) {
            $metas = $this->get_metas_group();
            if (!empty($metas)) {
                foreach ($metas as $meta) {
                    $userArrayData[$meta] = $this->get_meta($userArrayData['id'], $meta);
                }
            }
        }
        return $userArrayData;
    }

    public function updateMetaForBotSubscriber($subscriberID, $key, $value){
        $result = $this->db->query("SELECT id from users where bot_subscriber_id = $subscriberID")->row();
        if($userID = (int) maybe_null_or_empty($result, 'id')){
            $this->update_meta($userID, $key, $value);
        }
    }

    public function getMetaForBotSubscriber($subscriberID, $key){
        $result = $this->db->query("SELECT id from users where bot_subscriber_id = $subscriberID")->row();
        if($userID = (int) maybe_null_or_empty($result, 'id')){
            return $this->get_meta($userID, $key);
        }
        return false;
    }

    public function get_meta($user_id, $key)
    {
        return get_meta($user_id, $key, 'user_meta', 'user_id');
    }

    public function update_meta($user_id, $key, $value)
    {
       update_meta($user_id, $key, $value, 'user_meta', 'user_id');
    }

    public function update($userID, $data){
        $metaGroups = $this->get_metas_group();
        $metaData=[];
        if(!empty($metaGroups)){
            foreach ($metaGroups as $group){
                if(isset($data[$group])){
                    $metaData[$group]=$data[$group];
                    unset($data[$group]);
                }
            }
        }
        $this->ion_auth->update($userID, $data);
        if(!empty($metaData)){
            foreach ($metaData as $key=>$datum){
                $this->update_meta($userID, $key, $datum);
            }
        }
    }


    public function getGroupByName($groupName)
    {
        return $this->db->get_where('groups', ['name' => $groupName])->row();

    }

    public function getActivationCode($userID){
        return $this->db->query("SELECT activation_code as code from users where id=$userID")->row()->code;
    }

    public function insert($data, $groupNames='members')
    {
        if(!$groupNames){
            return false;
        }
        $username=isset($data['username']) ? $data['username'] : $data['first_name'].$data['last_name'].uniqid();
        $email=isset($data['email']) ? $data['email'] : uniqid().'@ifisun_fb.com';
        if(!$this->mailExist($email)){
            $password=isset($data['password']) ? $data['password'] : uniqid();
            $metaGroups=$this->get_metas_group();
            $metas=[];
            if(!empty($metaGroups)){
                foreach ($metaGroups as $metaGroup){
                    if(isset($data[$metaGroup])){
                        $metas[$metaGroup]=$data[$metaGroup];
                        unset($data[$metaGroup]);
                    }
                }
            }
            unset($data['username'], $data['email'], $data['password'], $data['cpass'], $data['adress']);
            $groupArray=[];
            if(is_array($groupNames) && !empty($groupNames)){
                foreach ($groupNames as $groupName){
                    $groupArray[]= (int) $this->getGroupByName($groupName)->id;
                }
            }else{
                $groupArray = [(int) $this->getGroupByName($groupNames)->id];
            }

            $userData=$this->ion_auth->register($username, $password, $email, $data, $groupArray);
            if ($userData && !empty($metas)){
                foreach ($metas as $key => $meta) {
                    $this->update_meta((int) $userData['id'], $key, $meta);
                }
            }
            return $userData;
        }else{
            return false;
        }

    }

    public function getGroups($except='members'){
        return $this->db->query("SELECT * from groups where name<>'$except'")->result_array();
    }



    public function mailExist($email){
        return (bool) $this->db->query("SELECT COUNT(id) as nbr from users where email = '$email'")->row()->nbr;
    }
    public function userNameExist($userName){
        return (bool) $this->db->query("SELECT COUNT(id) as nbr from users where username = '$userName'")->row()->nbr;
    }

    public function getIDByMail($email){
        $result = $this->db->query("SELECT id from users where email = '$email'")->row();
        return maybe_null_or_empty($result, 'id');
    }

    public function botSubscriberExist($subscriberID){
        return (bool) $this->db->query("SELECT COUNT(id) as nbr from users where bot_subscriber_id = $subscriberID")->row()->nbr;
    }

    public function getUsersByGroup($groupNames, $onlyActiveUsers=false, $exceptUserID=''){
        $sqlCond="";
        $activeUserCond="";
        if(is_array($groupNames) && !empty($groupNames)){
            foreach ($groupNames as $key => $groupName){
                $sqlCond.= ($key!=0 ? ' or ':'')."name='$groupName'";
            }
        }else{
            $sqlCond="name='$groupNames'";
        }
        if($onlyActiveUsers){
            $activeUserCond=" (users.active=1 or users.active=2) and ";
        }
        if($exceptUserID && $exceptUserID!=''){
            $activeUserCond.=" users.id <> $exceptUserID and";
        }

        $users = $this->db->query("SELECT * FROM users where$activeUserCond users.id IN (SELECT user_id from 
users_groups where group_id in (SELECT id from groups where $sqlCond))")->result_array();
        if(!empty($users)){
            foreach ($users as $key=> $user){
                $users[$key]['created_on'] = getDateByTime($user['created_on']);
                $users[$key]['last_login'] = getDateByTime($user['last_login']);
                $users[$key]=$this->getUserMeta($users[$key]);
                $statusArray=$this->ion_auth->get_users_groups($user['id'])->result();
                $temp=[];
                if(!empty($statusArray)){
                    foreach ($statusArray as $status){
                        $temp[]=$status->description;
                    }
                }
                $users[$key]['roles']=implode(', ', $temp);
                /*if(user_can('admin', $user['id'])){
                    $users[$key]['status'] = $this->getGroupByName('admin')->description;
                }elseif (user_can('moderator', $user['id'])){
                    $users[$key]['status'] = $this->getGroupByName('moderator')->description;
                }else{
                    $users[$key]['status'] = $this->getGroupByName('members')->description;
                }*/
            }
        }
        return $users;
    }

    public function getActive($userID){
        $this->db->select('active');
        return $this->db->get_where('users', ['id'=>$userID])->row()->active;
    }

    public function getCurrentUser($userID=null){
       $user = $this->ion_auth->user($userID)->row_array();
       $user = $this->getUserMeta($user);
       // Obtaining user groups
       $user['groups']=$this->ion_auth->get_users_groups()->result();
       return $user;
    }

    public function validate($username, $hashedPassword, /*Because of status 1 and 0*/$mustBeConfirmed = true)
    {
        $user = $this->db->get_where('users', ['username' => $username, 'password' => $hashedPassword])->row();
        if (!empty($user)) {
            switch (maybe_null_or_empty($user, 'active')) {
                case 0:
                    if(!$mustBeConfirmed){
                        return [
                            'status'=>true,
                            'data'=>$user
                        ];
                    }else{
                        return [
                            'status' => false,
                            'message' => 'Utilisateur non confirmé'
                        ];
                    }
                    break;
                case 1:
                    return [
                        'status'=>true,
                        'data'=>$user
                    ];
                case 2:
                    return [
                        'status' => false,
                        'message' => 'Utilisateur banni'
                    ];
            }
        }
        return [
            'status' => false,
            'message' => 'Données utilisateurs incorrects'
        ];
    }

}