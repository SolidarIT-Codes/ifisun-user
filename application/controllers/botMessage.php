<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 06/11/2018
 * Time: 13:26
 */

class BotMessage extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('botMessage_model');
    }

    public function get()
    {
        $data = $this->botMessage_model->getAll();
        if (!empty($data)) {
            echoResponse([
                'status' => 1,
                'data' => $data
            ]);
        }
        echoResponse([
            'status' => 0,
        ]);

    }

    public function add(){
        $data = (array)json_decode($this->input->raw_input_stream);
        $dataToBeInserted=[
            'key'=>$data['keyword'],
            'value'=>$data['message'],
        ];
        $messageID = $this->botMessage_model->insert($dataToBeInserted);
        if($messageID){
            echoResponse([
                'status'=>1,
                'data'=>$messageID,
                'message'=>'Message ajouté avec succès'
            ]);
        }
        echoResponse([
            'status'=>0,
            'message'=>'Erreur rencontrée. Veuillez réessayer'
        ]);
    }

    //working with formdata
    public function getByID(){
        if($messageID = (int) $this->input->get('botMessageID')){
            echoResponse([
                'status'=>1,
                'data'=>$this->botMessage_model->get($messageID),
                'message'=>'Fetched'
            ]);
        }
        echoResponse([
            'status'=>0,
            'message'=>'Erreur rencontrée. Veuillez réessayer'
        ]);
    }

    //wworking with formdata
    public function delete(){
        if($messageID = (int) $this->input->get('messageID')){
            $this->botMessage_model->delete($messageID);
            echoResponse([
                'status'=>1,
                'message'=>'Message supprimé avec succès'
            ]);
        }
        echoResponse([
            'status'=>0,
            'message'=>'Erreur rencontrée. Veuillez réessayer'
        ]);
    }

    //working with formdata
    public function edit(){
        if($data=$this->input->post('bot')){
            $messageID = (int) maybe_null_or_empty($data, 'id');
            $dataToBeUpdated = [
                'key'=>$data['keyword'],
                'value'=>$data['message']
            ];
            if($messageID){
                $this->botMessage_model->update($messageID, $dataToBeUpdated);
                echoResponse([
                    'status'=>1,
                    'message'=>'Message mis à jour avec succès'
                ]);
            }
        }
        echoResponse([
            'status'=>0,
            'message'=>'Erreur rencontrée. Veuillez réessayer'
        ]);
    }
}