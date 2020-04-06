<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 26/10/2018
 * Time: 13:24
 */

require(APPPATH . '/libraries/REST_Controller.php');

class Signup extends \Restserver\Libraries\REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->model('user_model');
    }

    //MUST DECODE ANY INCOMING RAW INPUT STREAM BEFORE USING DATA

    public function add_post()
    {
        $this->load->library('form_validation');
        $data = [
            'address'=>$this->post('address'),
            'password'=>$this->post('password'),
            'cpass'=>$this->post('cpass'),
            'email'=>$this->post('email'),
            'first_name'=>$this->post('first_name'),
            'last_name'=>$this->post('last_name'),
            'phone'=>$this->post('phone'),
            'username'=>$this->post('username'),
        ];
        $this->form_validation->set_data($data);
        setFormValidationRules([
            [
                'name'=>'address',
                'label'=>'Adresse',
                'rules'=>'trim|required'
            ],
            [
                'name'=>'password',
                'label'=>'Mot de passe',
                'rules'=>'trim|required',
            ],
            [
                'name'=>'cpass',
                'label'=>'Confirmation  de mot de passe',
                'rules'=>'trim|required|matches[password]',
            ],
            [
                'name'=>'email',
                'label'=>'Email',
                'rules'=>'trim|required|valid_email|is_unique[users.email]',
            ],
            [
                'name'=>'first_name',
                'label'=>'Prénom(s)',
                'rules'=>'trim|required',
            ],
            [
                'name'=>'last_name',
                'label'=>'Nom',
                'rules'=>'trim|required',
            ],
            [
                'name'=>'phone',
                'label'=>'Téléphone',
                'rules'=>'trim|required|is_natural_no_zero|is_unique[users.phone]',
            ],
            [
                'name'=>'username',
                'label'=>"Nom d'utilisateur",
                'rules'=>'trim|required|is_natural_no_zero|is_unique[users.username]',
            ],
        ]);
        if($this->form_validation->run()){
            if ($userID = $this->user_model->insert($data)) {
                $this->response([
                    'status' => true,
                    'userID' => $userID,
                    'message' => "Votre compté a été créé avec succès <br> Un mail de notification a été envoyé sur votre adresse mail. Veuillez le confirmer"
                ]);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Une erreur a été rencontrée. Veuillez réessayer plus tard'
                ]);
            }
        }
        $this->response([
            'status'=>false,
            'message'=>setErrorDelimiter()
        ]);
    }

    public function mailExist_get()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data([
            'email'=>$email=$this->get('email')
        ]);
        setFormValidationRules([
            [
                'name'=>'email',
                'label'=>'Email',
                'rules'=>'trim|required|valid_email',
            ]
        ]);
        if($this->form_validation->run()){
            $exist = $this->user_model->mailExist($email);
            if ($exist) {
                $this->response([
                    'status' => false,
                    'message' => 'Adresse mail existe déjà'
                ]);
            }
            $this->response([
                'status' => 1
            ]);
        }
        $this->response([
            'status' => false,
            'message'=>setErrorDelimiter()
        ]);
    }

    public function userNameExist_get()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_data([
            'username'=>$username=$this->get('username')
        ]);
        setFormValidationRules([
            [
                'name'=>'username',
                'label'=>"Nom d'utilisateur",
                'rules'=>'trim|required'
            ]
        ]);
        if($this->form_validation->run()){
            $exist = $this->user_model->userNameExist($username);
            if ($exist) {
                $this->response([
                    'status' => false,
                    'message' => "Nom d'utilisateur existe déjà"
                ]);
            } $this->response([
                'status' => true
            ]);
        }
        $this->response([
            'status' => false,
            'message'=>setErrorDelimiter()
        ]);
    }
}