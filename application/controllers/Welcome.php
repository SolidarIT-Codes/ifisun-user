<?php
defined('BASEPATH') OR exit('No direct script access allowed');


header('Access-Control-Allow-Origin: *'); 
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-with");
class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	

	public function index()
	{

	    $this->load->helper(['url', 'form']);
	    $data['assetsUrl']=base_url('assets/');

        $this->load->model('complain_model', 'admin');
	    $data['complainTypes']=$this->admin->getAllComplainTypes(true);
	    if($complain = $this->input->post('complain')){
	        $this->load->library('form_validation');
	        $this->form_validation->set_rules('complain[last_name]', 'Nom', 'trim|required');
	        $this->form_validation->set_rules('complain[first_name]', 'Prénom', 'trim|required');
	        $this->form_validation->set_rules('complain[email]', 'Email', 'trim|required');
	        $this->form_validation->set_rules('complain[age]', 'Age', 'trim|required');
	        $this->form_validation->set_rules('complain[phone]', 'Phone', 'trim|required');
	        $this->form_validation->set_rules('complain[complain_type_id]', 'Type de plainte', 'trim|required');
	        $this->form_validation->set_rules('complain[text]', 'Description', 'trim|required');
	        if($this->form_validation->run()){

                //Check whether user upload picture
            if(!empty($_FILES['picture']['name'])){
                $config['upload_path'] = 'uploads/images/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif|mp3|mp4|avi|pdf';
                $config['file_name'] = $_FILES['picture']['name'];
                $config['max_size'] = '1000';
                
                //Load upload library and initialize configuration
                
                $this->load->library('upload', $config);
                
                if($this->upload->do_upload('picture')){
                    $uploadData = $this->upload->data();
                    $picture = $uploadData['file_name'];
                }else{
                    $picture = 'yo';
                }
            }else{
                $picture = 'allo';
            }

            $nom = $this->input->post('complain[last_name]');
            $prenom = $this->input->post('complain[first_name]');
            $email = $this->input->post('complain[email]');
            $age = $this->input->post('complain[age]');
            $phone = $this->input->post('complain[phone]');
            $type = $this->input->post('complain[complain_type_id]');
            $description = $this->input->post('complain[text]');
            $picture = $picture;
            /*$hasrdv = $this->input->post('hasrdv');
            $collaborateurfkiduser = $this->input->post('collaborateur');
            $descriptionvisite = $this->input->post('descriptionvisite');*/

            $this->db->trans_begin();

            $this->admin->insert_p($nom, $prenom, $email, $age, $phone, $type, $description, $picture);
            redirect('welcome/success');
            }else{
                redirect('welcome/success');
            }

        }
		$this->load->view('welcome_message', $data);
	}


			public function denonce(){
       					$this->load->helper(['url', 'form']);
	    $data['assetsUrl']=base_url('assets/');

        $this->load->model('complain_model', 'admin');
	    $data['complainTypes']=$this->admin->getAllComplainTypes(true);
	    if($complain = $this->input->post('complain')){
	        $this->load->library('form_validation');
	        $this->form_validation->set_rules('complain[last_name]', 'Nom', 'trim|required');
	        $this->form_validation->set_rules('complain[first_name]', 'Prénom', 'trim|required');
	        $this->form_validation->set_rules('complain[email]', 'Email', 'trim|required');
	        $this->form_validation->set_rules('complain[age]', 'Age', 'trim|required');
	        $this->form_validation->set_rules('complain[phone]', 'Phone', 'trim|required');
	        $this->form_validation->set_rules('complain[complain_type_id]', 'Type de plainte', 'trim|required');
	        $this->form_validation->set_rules('complain[text]', 'Description', 'trim|required');
	        if($this->form_validation->run()){
                
                    //Check whether user upload picture
            if(!empty($_FILES['picture']['name'])){
                $config['upload_path'] = 'uploads/images/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif|mp3|mp4|avi|pdf';
                $config['file_name'] = $_FILES['picture']['name'];
                $config['max_size'] = '1000';
                
                //Load upload library and initialize configuration
                
                $this->load->library('upload', $config);
                
                if($this->upload->do_upload('picture')){
                    $uploadData = $this->upload->data();
                    $picture = $uploadData['file_name'];
                }else{
                    $picture = 'yo';
                }
            }else{
                $picture = 'allo';
            }

            $nom = $this->input->post('complain[last_name]');
            $prenom = $this->input->post('complain[first_name]');
            $email = $this->input->post('complain[email]');
            $age = $this->input->post('complain[age]');
            $phone = $this->input->post('complain[phone]');
            $victime = $this->input->post('complain[vic_name]');
            $type = $this->input->post('complain[complain_type_id]');
            $tel_vic = $this->input->post('complain[vic_tel]');
            $adresse_vic = $this->input->post('complain[vic_adress]');
            $description = $this->input->post('complain[text]');
            $picture = $picture;
            $this->db->trans_begin();

            $this->admin->insert_d($nom, $prenom, $email, $age, $phone, $victime, $type, $tel_vic, $adresse_vic, $description, $picture);
            redirect('welcome/success');
            }else{
                redirect('welcome/success');
            }

        }
		$this->load->view('denonce', $data);
   					 }

   					 	public function temoigner(){
       					$this->load->helper(['url', 'form']);
	    $data['assetsUrl']=base_url('assets/');

        $this->load->model('complain_model', 'admin');
	    $data['complainTypes']=$this->admin->getAllComplainTypes(true);
	    if($complain = $this->input->post('complain')){
	        $this->load->library('form_validation');
	        $this->form_validation->set_rules('complain[last_name]', 'Nom', 'trim|required');
	        $this->form_validation->set_rules('complain[first_name]', 'Prénom', 'trim|required');
	        $this->form_validation->set_rules('complain[age]', 'Age', 'trim|required');
	        $this->form_validation->set_rules('complain[profession]', 'Profession', 'trim|required');
	        $this->form_validation->set_rules('complain[text]', 'Description', 'trim|required');
	        if($this->form_validation->run()){
                
            $nom = $this->input->post('complain[last_name]');
            $prenom = $this->input->post('complain[first_name]');
            $age = $this->input->post('complain[age]');
            $profession = $this->input->post('complain[profession]');
            $description = $this->input->post('complain[text]');

            $this->db->trans_begin();

            $this->admin->insert_t($nom, $prenom, $age, $profession, $description);
            redirect('welcome/success');
            }else{
                redirect('welcome/success');
            }

        }
		$this->load->view('temoigner', $data);
   					 }

	public function success(){
        $this->load->helper(['url']);
        $data['assetsUrl']=base_url('assets/');
        $this->load->view('success', $data);
    }
}
