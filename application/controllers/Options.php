<?php

/**

 * Created by PhpStorm.

 * User: MikOnCode

 * Date: 28/10/2018

 * Time: 02:57

 */



require(APPPATH . '/libraries/REST_Controller.php');

class Options extends \Restserver\Libraries\REST_Controller

{


  //  public function __construct()

    {

         parent::__construct();

        $this->load->model('option_model');

    }



    public function set_post()

    {

        $this->load->library('form_validation');

        $data=[

            'siteName'=>$this->post('siteName'),

            'siteDescription'=>$this->post('siteDescription'),

        ];

        $this->form_validation->set_data($data);

        setFormValidationRules([

            [

                'name'=>'siteName',

                'label'=>'Nom du site',

                'rules'=>'trim|required'

            ],

            [

                'name'=>'siteDescription',

                'label'=>'Description du site',

                'rules'=>'trim|required'

            ]

        ]);

        if($this->form_validation->run()){

            if($logo=$this->post('siteLogo')){

                $data['siteLogo'] = uploadBase64($logo);

            }

            if($logo=$this->post('siteAvatar')){

                $data['siteAvatar'] = uploadBase64($logo);

            }

            if($logo=$this->post('siteBackgroundImage')){

                $data['siteBackgroundImage'] = uploadBase64($logo);

            }

            if($logo=$this->post('siteDefaultComplainCover')){

                $data['siteDefaultComplainCover'] = uploadBase64($logo);

            }

            $this->option_model->update_all_options($data);

            $this->response([

                'status' => true,

                'data' => $this->option_model->get_options(),

                'message' => 'Paramètres généraux mis à jour avec succès'

            ]);

        }

        $this->response([

            'status'=>false,

            'message'=>'Erreur rencontrée. Veuillez réessayer'

        ]);



    }



    public function getAll_get()

    {
        

        $this->response($this->option_model->get_options());

    }



}