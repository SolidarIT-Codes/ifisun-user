<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 08/11/2018
 * Time: 11:45
 */

class BotImage_model extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        $this->dbName='bot_images';
    }

    public function getAll(){
        $this->db->order_by('id', 'DESC');
        return $this->db->get($this->dbName)->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->dbName, $data);
        return $this->db->insert_id();
    }
    public function get($id)
    {
        return $this->db->get_where($this->dbName, ['id' => $id])->row_array();
    }

    public function getByKeyword($keyword){
        $results=$this->db->get_where($this->dbName, ['key'=>$keyword])->row();
        if(!empty($results)){
            return $results->value;
        }
        return '';
    }
    public function update($id, $data)
    {
        $this->db->update($this->dbName, $data, ['id' => $id]);
        return $id;
    }

    public function delete($id){
        $this->db->delete($this->dbName, ['id'=>$id]);
    }
}