<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 05/10/2018
 * Time: 11:05
 */

class BotMessage_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->dbName='bot_messages';
    }

    private function getReplacers($subscriberID, $message="")
    {
        $this->load->model('user_model');
        $temp['{{bot-name}}']='Ifisun';
        if(strpos($message, '{{')!== false){
            $subscriberData = $this->user_model->getUserByKeyword('bot_subscriber_id',$subscriberID);
            if (!empty($subscriberData)) {
                foreach ($subscriberData as $key => $datum) {
                    $temp['{{user_' . $key . '}}'] = $datum;
                }
            }
        }
        return $temp;
    }


    public function getAll()
    {
        $this->db->order_by('id', 'DESC');
        return $this->db->get($this->dbName)->result();
    }

    public function getDecryptedMessage($keyword, $subscriberID)
    {

        $messsage = $this->getByKeyword($keyword);
        if ($messsage) {
            $replacers = $this->getReplacers($subscriberID, $messsage);
            if (!empty($replacers)) {
                foreach ($replacers as $key => $replacer) {
                    $messsage = str_replace($key, $replacer, $messsage);
                }
                return $messsage;
            }
        }
        return '';
    }

    public function get($id)
    {
        $this->db->order_by('id', 'ASC');
        return $this->db->get_where($this->dbName, ['id' => $id])->row_array();
    }

    public function getByKeyword($keyword)
    {
        $message= $this->db->get_where($this->dbName, ['key' => $keyword])->row();
        if(!empty($message)){
            $message=$message->value;
            $message=explode('|', $message);
            return $message[array_rand($message)];
        }
        return false;
    }

    public function insert($data)
    {
        $this->db->insert($this->dbName, $data);
        return $this->db->insert_id();
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