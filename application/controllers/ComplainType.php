<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 05/11/2018
 * Time: 10:40
 */

require(APPPATH . '/libraries/REST_Controller.php');
class ComplainType extends \Restserver\Libraries\REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('complain_model');
    }

    public function add_post()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data($data=[
            'name'=>$this->post('name'),
            'description'=>$this->post('description'),
            'photo'=>$this->post('photo'),
        ]);
        setFormValidationRules([
            [
                'name'=>'name',
                'label'=>'Libellé',
                'rules'=>'trim|required|is_unique[complain_types.name]'
            ],
            [
                'name'=>'description',
                'label'=>'Description',
                'rules'=>'trim|required'
            ],
            [
                'name'=>'photo',
                'label'=>'Image symbolique',
                'rules'=>'trim|required'
            ],
        ]);
        if($this->form_validation->run()){
            $data['photo'] = uploadBase64($data['photo']);
            $complainTypeID = $this->complain_model->insertComplainType($data);
            if ($complainTypeID) {
                $this->response([
                    'status' => true,
                    'message' => 'Type de plainte ajouté avec succès',
                    'data' => $complainTypeID
                ]);
            }
            $this->response([
                'status'=>false,
                'message'=>'Erreur rencontrée. Veuillez réessayer'
            ]);
        }
        $this->response([
            'status' => false,
            'message' => setErrorDelimiter()
        ]);
    }

    public function getByID_get() {
        $this->load->library('form_validation');
        $this->form_validation->set_data([
            'complainTypeID'=>$typeID=$this->get('complainTypeID')
        ]);
        setFormValidationRules([
            [
                'name'=>'complainTypeID',
                'label'=>'ID',
                'rules'=>'trim|required|is_natural_no_zero'
            ]
        ]);
        if($this->form_validation->run()){
            $typeData = $this->complain_model->getTypeByID($typeID);
            $this->response([
                'status'=>true,
                'data'=>$typeData,
                'message'=>'Success'
            ]);
        }
        $this->response([
            'status'=>false,
            'message'=>setErrorDelimiter()
        ]);

    }

    public function is_unique_on_update($field_value, $args)
    {
        return control_unique_on_update($field_value, $args);
    }

    public function edit_post() {
        $this->load->library('form_validation');
        $data = [
            'id'=>$typeID=$this->post('id'),
            'name'=>$name=$this->post('name'),
            'description'=>$this->post('description')
        ];
        if($photo=$this->post('photo')){
            $data['photo']=$photo;
        }
        $this->form_validation->set_data($data);
        $formValidation=[

            [
                'name'=>'id',
                'label'=>'ID',
                'rules'=>"trim|required|is_natural_no_zero",
            ],[
                'name'=>'name',
                'label'=>'Libellé',
                'rules'=>"trim|required|callback_is_unique_on_update[complain_types.name.$typeID]",
            ],
            [
                'name'=>'description',
                'label'=>'Description',
                'rules'=>'trim|required'
            ]
        ];
        if($photo){
            $formValidation[]=[
                'name'=>'photo',
                'label'=>'Image symbolique',
                'rules'=>'trim|required'
            ];
        }
        setFormValidationRules($formValidation);
        if($this->form_validation->run()){
            if($photo){
                $data['photo']=uploadBase64($data['photo']);
            }
            $this->complain_model->updateComplainType($data, $data['id']);
            $this->response([
                'status'=>true,
                'message'=>'Type de plainte mis à jour'
            ]);
        }
        $this->response([
            'status'=>false,
            'message'=>setErrorDelimiter()
        ]);
    }

    public function all_get(){
        $this->response([
            'status'=>true,
            'data'=>$this->complain_model->getAllComplainTypes()
        ]);
    }

}