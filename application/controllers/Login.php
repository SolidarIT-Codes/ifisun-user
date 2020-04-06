<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 27/10/2018
 * Time: 19:14
 */
require(APPPATH . '/libraries/REST_Controller.php');
class Login extends \Restserver\Libraries\REST_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library(['ion_auth', 'form_validation']);
        $this->load->model('user_model');
    }

    public function set_post() {
        $data=[
            'username'=>$this->post('username'),
            'password'=>$this->post('password'),
            'remember'=>$this->post('remember'),
        ];
        $this->form_validation->set_data($data);
        setFormValidationRules([
            [
                'name'=>'username',
                'label'=>"Nom d'utilisateur",
                'rules'=>'trim|required'
            ],
            [
                'name'=>'password',
                'label'=>'Mot de passe',
                'rules'=>'trim|required'
            ],
            [
                'name'=>'remember',
                'label'=>'Se rappeler de moi',
                'rules'=>'trim|is_natural'
            ],
        ]);
        if($this->form_validation->run()){
            if($this->ion_auth->login($data['username'], $data['password'], $data['remember'])){
                $userData = $this->user_model->getCurrentUser();
                $this->ion_auth->logout();
                if($userData['active'] == 2){
                    // Si utilisateur a été bannis
                    $this->response([
                        'status'=>false,
                        'message'=>'Erreur de connexion <br> Vous avez été bannis'
                    ]);
                }
                if($this->ion_auth->in_group('members')){
                    // In case user is a member logout
                    $this->response([
                        'status'=>false,
                        'message'=>"Action non autorisée"
                    ]);
                }else{
                    // success
                    $this->response([
                        'status'=>true,
                        'message'=>'Utilisateur connecté',
                        'data'=>$userData
                    ]);
                }

            }else{
                $this->response([
                    'status'=>false,
                    'message'=>'Les informations saisies sont incorrectes'
                ]);
            }
        }
        setErrorDelimiter();
        $this->response([
            'status'=>false,
            'message'=>$this->form_validation->error_string()
        ]);
    }
}