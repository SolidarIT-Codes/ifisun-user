<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 08/11/2018
 * Time: 11:43
 */

class BotImage extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        header("Access-Control-Allow-Methods: GET, POST");
        //header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header('Access-Control-Allow-Origin: ' . $this->config->item('controlOrigin'));
        $this->load->model('botImage_model');
    }

    public function get()
    {
        $data = $this->botImage_model->getAll();
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
            'value'=>uploadBase64($data['image']),
        ];
        $imageID = $this->botImage_model->insert($dataToBeInserted);
        if($imageID){
            echoResponse([
                'status'=>1,
                'data'=>$imageID,
                'message'=>'Image ajoutée avec succès'
            ]);
        }
        echoResponse([
            'status'=>0,
            'message'=>'Erreur rencontrée. Veuillez réessayer'
        ]);
    }

    public function getByID(){
        $data = (array)json_decode($this->input->raw_input_stream);
        $imageID = (int) maybe_null_or_empty($data, 'botImageID');
        if($imageID){
            echoResponse([
                'status'=>1,
                'data'=>$this->botImage_model->get($imageID),
                'message'=>'Fetched'
            ]);
        }
        echoResponse([
            'status'=>0,
            'message'=>'Erreur rencontrée. Veuillez réessayer'
        ]);
    }

    public function delete(){
        $data = (array)json_decode($this->input->raw_input_stream);
        $imageID = (int) maybe_null_or_empty($data, 'imageID');
        if($imageID){
            $this->botImage_model->delete($imageID);
            echoResponse([
                'status'=>1,
                'message'=>'Image supprimée avec succès'
            ]);
        }
        echoResponse([
            'status'=>0,
            'message'=>'Erreur rencontrée. Veuillez réessayer'
        ]);
    }

    public function edit(){
        $data = (array)json_decode($this->input->raw_input_stream);
        $imageID = (int) maybe_null_or_empty($data, 'id');
        $dataToBeUpdated = [
            'key'=>$data['keyword']
        ];
        if($image=maybe_null_or_empty($data, 'image')){
            $dataToBeUpdated['value']=uploadBase64($image);
        }
        if($imageID){
            $this->botImage_model->update($imageID, $dataToBeUpdated);
            echoResponse([
                'status'=>1,
                'message'=>'Image mise à jour avec succès'
            ]);
        }
        echoResponse([
            'status'=>0,
            'message'=>'Erreur rencontrée. Veuillez réessayer'
        ]);
    }
}