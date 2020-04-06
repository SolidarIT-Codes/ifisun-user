<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 09/11/2018
 * Time: 19:54
 */

require(APPPATH . '/libraries/REST_Controller.php');

class Complain extends \Restserver\Libraries\REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('complain_model');
    }

    public function all_get()
    {
        //var_dump_pre($this->complain_model->getAll());exit;
        $this->response([
            'status' => true,
            'message' => 'Fetched',
            'data' => $this->complain_model->getAll()
        ]);
    }

    public function byID_get()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data([
            'complainID' => $complainID=$this->get('complainID')
        ]);
        setFormValidationRules([
            [
                'name' => 'complainID',
                'label' => 'ID Plainte',
                'rules' => 'trim|required|is_natural_no_zero'
            ]
        ]);
        if ($this->form_validation->run()) {
            $this->response([
                'status' => true,
                'data' => $this->complain_model->getByID((int)$complainID),
                'message' => 'Fetched'
            ]);
        }
        echoResponse([
            'status' => false,
            'message' => setErrorDelimiter()
        ]);
    }

    public function moderatorUpdate_get()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data=[
            'complainID'=>$this->get('complainID'),
            'username'=>$this->get('moderatorUsername'),
            'password'=>$this->get('moderatorPassword')
        ]);
        setFormValidationRules([
            [
                'name'=>'complainID',
                'label'=>'ID Plainte',
                'rules'=>'trim|required|is_natural_no_zero'
            ],
            [
                'name'=>'username',
                'label'=>"Nom d'utilisateur",
                'rules'=>'trim|required'
            ],
            [
                'name'=>'password',
                'label'=>"Mot de passe",
                'rules'=>'trim|required'
            ],
        ]);
        if($this->form_validation->run()){
            $this->load->model('user_model');
           $moderatorValidation = $this->user_model->validate($data['username'], $data['password']);
           if($moderatorValidation['status']){
               $moderatorID = maybe_null_or_empty($moderatorValidation['data'], 'id');
               if (!empty($update)) {
                   $this->response([
                       'status' => true,
                       'data' => $this->complain_model->getUpdateByModeratorIDAndComplainID($data['complainID'], $moderatorID),
                       'message' => 'Fetched'
                   ]);
               }
           }else{
               $this->response($moderatorValidation);
           }
        }
        $this->response([
            'status' => false,
            'message' => setErrorDelimiter()
        ]);
    }

    public function setModeratorUpdate_post()
    {
        // echoResponse($this->input->post());
        if (($complainID = (int)$this->input->post('complainID')) && ($userID = (int)$this->input->post('userID'))) {
            if ($files = maybe_null_or_empty($_FILES, 'files')) {
                // echoResponse($files['name']);
                $this->load->helper(['url']);
                $uploadedData = [];
                if (!empty($files)) {
                    $limit = count($files['name']);
                    for ($i = 0; $i < $limit; $i++) {
                        $_FILES['file']['name'] = $files['name'][$i];
                        $_FILES['file']['type'] = $files['type'][$i];
                        $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
                        $_FILES['file']['error'] = $files['error'][$i];
                        $_FILES['file']['size'] = $files['size'][$i];
                        $uploadedData[] = upload_data(array(
                            'upload_path' => FCPATH . 'uploads',
                            'allowed_types' => 'jpg|png|jpeg|mp3|pdf|mp4|docx|doc|txt',
                            'max_size' => 50000), 'file');
                    }
                }
                if (!empty($uploadedData)) {
                    foreach ($uploadedData as $datum) {
                        $dataToBeUpdated = [
                            'complain_id' => $complainID,
                            'user_id' => $userID,
                            'link' => $datum['file_name'],
                            'created_at' => date('Y-m-d G:i:s')
                        ];
                        // echoResponse($dataToBeUpdated);
                        $this->complain_model->insertUpload($dataToBeUpdated);
                    }
                }
            }
            if ($description = $this->input->post('description')) {
                $this->complain_model->insertORUpdateComplainUpdate($complainID, $userID, $description);
            }
            echoResponse([
                'status' => 1,
                'message' => 'Plainte mise à jour avec succès'
            ]);
        }
        echoResponse([
            'status' => 0,
            'message' => 'Erreur rencontrée. Veuillez réessayer plus tard'
        ]);
    }

    public function uploads()
    {
        $this->complain_model->getOneUploadedImageOrFile(4);
    }


}