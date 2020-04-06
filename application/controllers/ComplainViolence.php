<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 05/11/2018
 * Time: 19:58
 */

require(APPPATH . '/libraries/REST_Controller.php');
class ComplainViolence extends \Restserver\Libraries\REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('complain_model');
        $this->load->library('form_validation');
    }

    public function add_post()
    {
        $this->form_validation->set_data($data=[
            'name'=>$this->post('name'),
            'description'=>$this->post('description'),
            'photo'=>$this->post('photo'),
        ]);
        setFormValidationRules([
            [
                'name'=>'name',
                'label'=>'Nom',
                'rules'=>'trim|required|is_unique[complain_violences.name]',
            ],
            [
                'name'=>'description',
                'label'=>'Description',
                'rules'=>'trim|required',
            ],
            [
                'name'=>'photo',
                'label'=>'Image symbolique',
                'rules'=>'trim|required',
            ],
        ]);
        if($this->form_validation->run()){
            $data['photo'] = uploadBase64($data['photo']);
            $complainViolenceID = $this->complain_model->insertComplainViolence($data);
            if ($complainViolenceID) {
                $this->response([
                    'status' => true,
                    'message' => 'Type de violence ajouté avec succès',
                    'data' => $complainViolenceID
                ]);
            }
        }
        $this->response([
            'status' => false,
            'message' => setErrorDelimiter(),
        ]);
    }

    public function getByID_get()
    {
        $this->form_validation->set_data([
            'complainViolenceID'=>$violenceID=$this->get('complainViolenceID')
        ]);
        setFormValidationRules([
            [
                'name'=>'complainViolenceID',
                'label'=>'ID',
                'rules'=>'trim|required|is_natural_no_zero'
            ]
        ]);
        if($this->form_validation->run()){
            $this->response([
                'status' => true,
                'data' => $this->complain_model->getViolenceByID($violenceID),
                'message' => 'Success'
            ]);
        }
        $this->response([
            'status' => false,
            'message' => setErrorDelimiter()
        ]);

    }

    public function edit_post()
    {
        $data = [
            'id'=>$violenceID=$this->post('id'),
            'name'=>$this->post('name'),
            'description'=>$this->post('description'),
        ];
        if($photo = $this->post('photo')){
            $data['photo']=uploadBase64($photo);
        }
        $this->form_validation->set_data($data);
        $formValidation=[
            [
                'name'=>'id',
                'label'=>'ID',
                'rules'=>'trim|required|is_natural_no_zero'
            ],
            [
                'name'=>'name',
                'label'=>'Nom',
                'rules'=>"trim|required|callback_is_unique_on_update[complain_violences.name.$violenceID]"
            ],
            [
                'name'=>'description',
                'label'=>'Description',
                'rules'=>"trim|required"
            ],
        ];
        if($photo){
            $formValidation[]=[
                'name'=>'photo',
                'label'=>'Image symbolique',
                'rules'=>'trim|required',
            ];
        }
        setFormValidationRules($formValidation);
        if($this->form_validation->run()){
            $this->complain_model->updateComplainViolence($data, $data['id']);
            $this->response([
                'status' => true,
                'message' => 'Type de violence mis à jour'
            ]);
        }
        $this->response([
            'status' => false,
            'message' => setErrorDelimiter()
        ]);
    }

    public function is_unique_on_update($field_value, $args)
    {
        return control_unique_on_update($field_value, $args);
    }

    public function all_get()
    {
        $this->response([
            'status' => true,
            'data' => $this->complain_model->getAllComplainViolences()
        ]);
    }
}