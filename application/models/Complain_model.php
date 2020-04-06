<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 05/11/2018
 * Time: 10:41
 */

class Complain_model extends CI_Model
{
    private $complainTypeTableName = 'complain_types';
    private $complainViolenceTableName = 'complain_violences';
    private $complainTableName = 'complains';
    private $complainUploadTableName = 'complain_uploads';

    public function __construct()
    {
        parent::__construct();
    }

    private function getMetas(){
        return [
            'phone',
            'text',
            'source'
        ];
    }
    
     function insert_p($nom, $prenom, $email, $age, $phone, $type, $description, $picture) {
        $data1 = array(
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'age' => $age,
            'telephone' => $phone,
            'type' => $type,
            'description' => $description,
            'preuve' => $picture,
        );

        $this->db->insert('plainte', $data1);
    }

    function insert_d($nom, $prenom, $email, $age, $phone, $victime, $type, $tel_vic, $adresse_vic, $description, $picture) {
        $data1 = array(
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'age' => $age,
            'telephone' => $phone,
            'victime' => $victime,
            'type' => $type,
            'tel_vic' => $tel_vic,
            'adresse_vic' => $adresse_vic,
            'description' => $description,
            'preuve' => $picture,
        );

        $this->db->insert('denonce', $data1);
    }


    function insert_t($nom, $prenom,  $age, $profession, $description) {
        $data1 = array(
            'nom' => $nom,
            'prenom' => $prenom, 
            'age' => $age,
            'profession' => $profession,
            'description' => $description,
        );

        $this->db->insert('temoignage', $data1);
    }
    public function getComplainMeta(array $complainArrayData)
    {
        if (!empty($complainArrayData)) {
            $metas = $this->getMetas();
            if (!empty($metas)) {
                foreach ($metas as $meta) {
                    $complainArrayData[$meta] = $this->get_meta($complainArrayData['id'], $meta);
                }
            }
        }
        return $complainArrayData;
    }

    public function insertUploads($data){

    }

    public function getTotalUpload($complainID){
        return $this->db->query("SELECT COUNT(id) as nbr FROM $this->complainUploadTableName where complain_id = $complainID")->row()->nbr;
    }

    public function getComplainModerators($complainID){
        $results= $this->db->query("SELECT users.first_name, users.last_name FROM complain_updates JOIN 
users ON users.id = complain_updates.moderator_id where complain_id = $complainID")->result_array();
        $handlers =[];
        if(!empty($results)){
            foreach ($results as $result){
                $handlers[]= $result['first_name'].' '.$result['last_name'];
            }
            return implode(', ', $handlers);
        }
        return '-';
    }

    public function getAll(){
        //$complains = $this->db->get($this->complainTableName)->result_array();
        $complains = $this->db->query("SELECT complains.*, complain_types.name, users.first_name, users.last_name, users.id as author_id
FROM complains JOIN complain_types ON complains.complain_type_id = complain_types.id INNER JOIN users ON users.id = complains.member_id order by complains.id desc")->result_array();
        if(!empty($complains)){
            $this->load->model('user_model');
            foreach ($complains as $key => $complain){
                $complains[$key] = $this->getComplainMeta($complain);
                $complains[$key]['totalUploads'] = $this->getTotalUpload($complain['id']);
                $complains[$key]['moderators'] = $this->getComplainModerators($complain['id']);
                $complains[$key]['user_photo'] = $this->user_model->get_meta($complain['author_id'], 'user_photo');
                $complains[$key]['cover'] = $this->getOneUploadedImageOrFile($complain['id']);
            }
        }
        return $complains;
    }

    public function getOneUploadedImageOrFile($complainID){
        $uploads = $this->db->query("SELECT link from complain_uploads where complain_id = $complainID")->result_array();
        $imagesExtensions = getImageExtensions();
        if(!empty($uploads)){
            foreach ($uploads as $upload){
               if($info=pathinfo($upload['link'])){
                   if(in_array($info['extension'], $imagesExtensions)){
                       return $upload['link'];
                       break;
                   }
               }
            }
            return 'file-type/'.$info['extension'].'.png';
        }
        return false;
    }

    public function get_meta($complain_id, $key)
    {
        return get_meta($complain_id, $key, 'complain_meta', 'complain_id');
    }

    public function getByID($complainID){
        $complainData = $this->db->query("SELECT complains.*, complain_types.name, users.first_name, users.last_name, users.id as author_id
FROM complains JOIN complain_types ON complains.complain_type_id = complain_types.id INNER JOIN users ON users.id = complains.member_id 
where complains.id=$complainID")->row_array();
        if(!empty($complainData)){
            $this->load->model('user_model');
            $complainData = $this->getComplainMeta($complainData);
            $complainData['uploads']=$this->getUploadsByComplainID($complainID);
            $complainData['user_photo']=$this->user_model->get_meta($complainData['author_id'], 'user_photo');
            $complainData['moderators']=$this->getComplainModerators($complainID);
            $complainData['updates']=$this->getAllUpdatesByComplainID($complainID);
        }
        return $complainData;
    }

    public function getAllUpdatesByComplainID($complainID){
        $updates = $this->db->query("SELECT complain_updates.*, users.first_name, users.last_name FROM complain_updates JOIN users 
ON users.id = complain_updates.moderator_id where complain_id=$complainID order by id desc")->result_array();
        if(!empty($updates)){
            $this->load->model('user_model');
            foreach ($updates as $key=> $update){
                $updates[$key]['user_photo']=$complainData['user_photo']=$this->user_model->get_meta($update['moderator_id'], 'user_photo');
            }
        }
        return $updates;
    }

    public function getUpdateByModeratorIDAndComplainID($complainID, $moderatorID){
        $update = $this->db->query("SELECT * from complain_updates where 
moderator_id=$moderatorID and complain_id=$complainID")->row_array();
        if(empty($update)){
            return [
                'description'=>$this->get_meta($complainID, 'text')
            ];
        }
        return $update;
    }

    public function getUploadsByComplainID($complainID){
        return $this->db->query("SELECT *, users.first_name, users.last_name FROM complain_uploads 
JOIN users ON users.id = complain_uploads.user_id where complain_id = $complainID")->result_array();
    }

    public function insertUpload($data){
        $this->db->insert($this->complainUploadTableName, $data);
    }

    public function insertORUpdateComplainUpdate($complainID, $moderatorID, $description){
        $count = $this->db->query("SELECT id from complain_updates where moderator_id = $moderatorID and complain_id = $complainID")->row();
        if($id = maybe_null_or_empty($count, 'id')){
            $this->db->update('complain_updates', [
                'description'=>$description,
                'updated_at'=>date('Y-m-d G:i:s')
            ], ['id'=>(int) $id]);
        }else{
            $this->db->insert('complain_updates', [
                'complain_id'=>$complainID,
                'moderator_id'=>$moderatorID,
                'description'=>$description,
                'updated_at'=>date('Y-m-d G:i:s')
            ]);
            return $this->db->insert_id();
        }
    }

    public function insert($data){
        $dataToBeInserted=[
            'member_id'=>$data['member_id'],
            'complain_type_id'=>$data['complain_type_id'],
            'status'=>1,
            'created_at'=>$date=date('Y-m-d G:i:s')
        ];
        $this->db->insert($this->complainTableName, $dataToBeInserted);
        $complainID = $this->db->insert_id();
       // $dataToBeInserted=[];
        if($complainID){
            //Insert into uploads table
            if($uploads = maybe_null_or_empty($data, 'uploads')){
                foreach ($uploads as $upload){
                    $dataToBeInserted=[
                        'complain_id'=>$complainID,
                        'user_id'=>$data['member_id'],
                        'link'=>$upload,
                        'created_at'=>$date
                    ];
                    $this->insertUpload($dataToBeInserted);
                    //$this->db->insert($this->complainUploadTableName, $dataToBeInserted);
                }
            }
            // Insert in the metas
            $metasGroups = $this->getMetas();
            if(!empty($metasGroups)){
                foreach ($metasGroups as $meta){
                    if(isset($data[$meta]) && !empty($data[$meta])){
                        update_meta($complainID, $meta, $data[$meta], 'complain_meta', 'complain_id');
                    }
                }
            }
            return $complainID;
        }
        return false;
    }

    public function insertComplainType($data)
    {
        $this->db->insert($this->complainTypeTableName, $data);
        return $this->db->insert_id();
    }

    public function updateComplainType($data, $typeID)
    {
        $this->db->update($this->complainTypeTableName, $data, ['id' => $typeID]);
    }

    public function getTypeByID($id)
    {
        return $this->db->get_where($this->complainTypeTableName, ['id' => $id])->row();
    }

    public function getAllComplainTypes($forSelect= false)
    {
        $results = $this->db->get($this->complainTypeTableName)->result_array();
        if($forSelect){
            $data=[''=>'Sélectionner type de plainte'];
            if(!empty($results)){
                foreach ($results as $result){
                    $data[$result['name']]=$result['name'];
                }
            }
            return $data;
        }
        return $results;
    }

    public function getAllComplainViolences()
    {
        return $this->db->get($this->complainViolenceTableName)->result_array();
    }

    public function getViolenceByID($id)
    {
        return $this->db->get_where($this->complainViolenceTableName, ['id' => $id])->row();
    }

    public function insertComplainViolence($data)
    {
        $this->db->insert($this->complainViolenceTableName, $data);
        return $this->db->insert_id();
    }

    public function updateComplainViolence($data, $typeID)
    {
        $this->db->update($this->complainViolenceTableName, $data, ['id' => $typeID]);
    }



}