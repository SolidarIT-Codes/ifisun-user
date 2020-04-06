<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 28/10/2018
 * Time: 21:14
 */
require(APPPATH . '/libraries/REST_Controller.php');

class Users extends \Restserver\Libraries\REST_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->library('form_validation');
    }

    public function getMembersList_get()
    {
        $this->response([
            'status' => true,
            'data' => $this->user_model->getUsersByGroup('members')
        ]);
    }

    //working with frontEnd formdata
    public function getAdminsAndModeratorsList_get()
    {
        $data = [
            'userID' => $this->get('userID')
        ];
        $this->form_validation->set_data($data);
        setFormValidationRules([
            [
                'name' => 'userID',
                'label' => 'ID',
                'rules' => 'trim|required|is_natural_no_zero'
            ]
        ]);
        if ($this->form_validation->run()) {
            $this->response([
                'status' => true,
                'data' => $this->user_model->getUsersByGroup(['admin', 'moderator'], true, $data['userID'])
            ]);
        }
        $this->response([
            'status' => false,
            'message' => setErrorDelimiter()
        ]);
    }

    // working with frontEnd formData
    public function add_post()
    {
        $data = [
            'email' => $this->post('email'),
            'first_name' => $this->post('first_name'),
            'last_name' => $this->post('last_name'),
        ];
        if ($groupArray = $this->post('user_group')) {
            $data['user_group'] = $groupArray;
        }
        if ($photo = $this->post('user_photo')) {
            $data['user_photo'] = $photo;
        }
        $this->form_validation->set_data($data);
        $formValidation = [
            [
                'name' => 'email',
                'label' => 'Email',
                'rules' => 'trim|required|is_unique[users.email]'
            ],
            [
                'name' => 'first_name',
                'label' => 'Prénom(s)',
                'rules' => 'trim|required'
            ],
            [
                'name' => 'last_name',
                'label' => 'Nom',
                'rules' => 'trim|required'
            ],
        ];
        if ($photo) {
            $formValidation[] = [
                'name' => 'user_photo',
                'label' => 'Photo de profil',
                'rules' => 'trim|required'
            ];
        }
        setFormValidationRules($formValidation);
        if ($this->form_validation->run()) {
            unset($data['user_group']);
            $data['user_photo']=uploadBase64($data['user_photo']);
            $userData = $this->user_model->insert($data, $groupArray);
            if (!empty($userData)) {
                $this->response([
                    'status' => true,
                    'message' => "L'utilisateur a été créé avec succès <br> Un mail de notification lui a été envoyé sur son adresse mail"
                ], '', true);
                $userData = (object)$userData;
                if (in_array($this->config->item('adminGroup'), $groupArray)) {
                    $userGroup = $this->user_model->getGroupByName($this->config->item('adminGroup'))->description;
                } else {
                    $userGroup = $this->user_model->getGroupByName($groupArray[0])->description;
                }
                $userGroup = strtolower($userGroup);
                $data = (object)$data;
                $this->load->model('option_model');
                $siteName = $this->option_model->get_option('siteName');
                $mail['title'] = 'Finaliser votre inscription';
                $mail['message'] = "Bonjour Mr/Mme $data->last_name $data->first_name. <br> Vous avez été désigné comme $userGroup de $siteName. 
Veuillez finaliser votre inscription en cliquant sur le bouton ci-dessous";
                $mail['btnLabel'] = 'Finaliser inscription';
                $mail['btnLink'] = $this->config->item('frontEndUrl') . "/confirm/$userData->id/$userData->activation";
                $mail['destination'] = $userData->email;
                sendMail('feminit@solidarit-hub.org', $mail);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Erreur rencontrée. Veuillez réessayer'
                ]);
            }
        } else {
            $this->response([
                'status' => false,
                'message' => setErrorDelimiter()
            ]);
        }
    }

    // working with formdata
    public function edit_post()
    {
        $dataToBeUpdated = [
            'id'=>$this->post('userID'),
            'last_name'=>$this->post('last_name'),
            'first_name'=>$this->post('first_name'),
            'phone'=>$this->post('phone'),
        ];
        if($photo=$this->post('user_photo')){
            $dataToBeUpdated['user_photo']=$photo;
        }
        $this->form_validation->set_data($dataToBeUpdated);
        setFormValidationRules([
            [
                'name'=>'id',
                'label'=>'ID',
                'rules'=>'trim|required|is_natural_no_zero'
            ],
            [
                'name'=>'last_name',
                'label'=>'Nom',
                'rules'=>'trim|required'
            ],
            [
                'name'=>'first_name',
                'label'=>'Prénom',
                'rules'=>'trim|required'
            ],
            [
                'name'=>'phone',
                'label'=>'Téléphone',
                'rules'=>'trim|required|is_natural'
            ],
        ]);
        if($this->form_validation->run()){
            if ($photo) {
                $dataToBeUpdated['user_photo'] = uploadBase64($dataToBeUpdated['user_photo']);
            }
            $this->user_model->update($dataToBeUpdated['id'], $dataToBeUpdated);
            $this->response([
                'status' => true,
                'message' => 'Informations mises à jour avec succès',
                'data' => $this->user_model->getCurrentUser($dataToBeUpdated['id'])
            ]);
        }
        $this->response([
            'status' => false,
            'message' => setErrorDelimiter()
        ]);
    }
    public function banOrActivate_get()
    {
        $this->form_validation->set_data([
            'userID' => $userID = $this->get('userID')
        ]);
        setFormValidationRules([
            [
                'name' => 'userID',
                'label' => 'ID',
                'rules' => 'trim|required|is_natural_no_zero'
            ]
        ]);
        if ($this->form_validation->run()) {
            $active = $this->user_model->getActive($userID);
            //set to 1 if banned or otherwise
            $setActive = $active == 1 ? 2 : 1;
            $this->ion_auth->update((int)$userID, ['active' => $setActive]);
            $this->response([
                'status' => true,
                'message' => $setActive == 2 ? 'Utilisateur banni' : 'Utilisateur activé',
                'data' => $setActive
            ]);
        }
        $this->response([
            'status' => false,
            'message' => setErrorDelimiter()
        ]);
    }
    
    public function editPassword_post()
    {
        $this->form_validation->set_data($dataToBeUpdated=[
            'userID'=>$this->post('userID'),
            'password'=>$this->post('password'),
            'newpassword'=>$this->post('newpassword'),
        ]);
        setFormValidationRules([
            [
                'name'=>'userID',
                'label'=>'ID',
                'rules'=>'trim|required|is_natural_no_zero'
            ],
            [
                'name'=>'password',
                'label'=>'Mot de passe actuel',
                'rules'=>'trim|required|min_length[8]'
            ],
            [
                'name'=>'newpassword',
                'label'=>'Nouveau mot de passe',
                'rules'=>'trim|required|min_length[8]'
            ],
        ]);
        if($this->form_validation->run()){
            $oldPassword = $dataToBeUpdated['password'];
            if ($this->ion_auth->hash_password_db($dataToBeUpdated['userID'], $oldPassword)) {
                $newpassword = $dataToBeUpdated['newpassword'];
                $this->ion_auth->update($dataToBeUpdated['userID'], ['password' => $newpassword]);
                $this->response([
                    'status' => true,
                    'message' => 'Mot de passe mis à jour avec succès'
                ]);
            } else {
                $this->response([
                    'status' => false,
                    'message' => 'Mot de passe actuel pas valide. Veuillez reessayer'
                ]);
            }
        }
        $this->response([
            'status' => false,
            'message' => setErrorDelimiter()
        ]);
    }

    public function getGroupNames_get()
    {
        $this->response([
            'status' => true,
            'data' => $this->user_model->getGroups()
        ]);
    }

    //working with formdata
    public function verifyActivation_get()
    {
        $data = [
            'userID' => $this->get('userID'),
            'activationCode' => $this->get('activationCode')
        ];
        $this->load->library('form_validation');
        $this->form_validation->set_data($data);
        setFormValidationRules([
            [
                'name' => 'userID',
                'label' => 'ID',
                'rules' => 'trim|required|is_natural'
            ], [
                'name' => 'activationCode',
                'label' => 'Code',
                'rules' => 'trim|required'
            ],
        ]);
        if ($this->form_validation->run()) {
            $data = (object)$data;
            $exist = $this->db->query("SELECT COUNT(id) as nbr from users where id=$data->id and activation_code='$data->activationCode' and active=0")->row()->nbr;
            if ($exist) {
                $this->response([
                    'status' => true
                ]);
            }
            $this->response([
                'status' => false,
                'message' => 'Verification échouée'
            ]);
        }
        //verify if user activation data is present and active=0
//        if (($id = $this->input->get('userID')) && ($code = $this->input->get('activationCode'))) {
//
//        }
        $this->response([
            'status' => false,
            'message' => 'Action non autorisée'
        ]);

    }

    //working with formdata
    public function activate_post()
    {
        $this->load->library('form_validation');
        $data = [
            'userID' => $this->post('userID'),
            'activationCode' => $this->post('activationCode'),
            'password' => $this->post('password'),
            'username' => $this->post('username'),
        ];
        $this->form_validation->set_data($data);
        setFormValidationRules([
            [
                'name' => 'userID',
                'label' => 'ID',
                'rules' => 'trim|required|is_natural'
            ],
            [
                'name' => 'activationCode',
                'label' => "Code d'activation",
                'rules' => 'trim|required'
            ],
            [
                'name' => 'password',
                'label' => 'Mot de passe',
                'rules' => 'trim|required'
            ],
            [
                'name' => 'username',
                'label' => "Nom d'utilisateur",
                'rules' => 'trim|required'
            ]
        ]);
        if ($this->form_validation->run()) {
            $activated = $this->ion_auth->activate($data['userID'], $data['activationCode']);
            if ($activated) {
                $this->ion_auth->update($data['userID'], [
                    'password' => $data['password'],
                    'username' => $data['username']
                ]);
                $this->response([
                    'status' => true,
                    'message' => 'Inscription finalisée avec succès<br>Vous pouvez à présent vous connecter'
                ]);
            }
        }
        $this->response([
            'status' => false,
            'message' > "Une erreur a été rencontrée. Votre inscription n'a pas été finalisée <br>Veuillez reessayer"
        ]);
        //TODO activate user and update username and password
        //
    }


}