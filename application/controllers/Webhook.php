<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 05/10/2018
 * Time: 06:33
 */

class Webhook extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        //$this->load->helper('utils');
    }

    public function index()
    {
        require(APPPATH . 'third_party/BotInteractor.php');
        $config = getConfig();
        if ($this->input->get('action') && $this->input->get('action') != '') {
            switch ($this->input->get('action')) {
                case "set_menu":
//$array = array(array("fields" => array("persistent_menu")));
//            custom_send('{"fields":["persistent_menu"]}', $config["page_access_token"]);
                    $data = array('persistent_menu' => array(array(
                        'locale' => 'default',
                        "composer_input_disabled" => false,
                        'call_to_actions' => array(
                            array(
                                'title' => 'Réinitialiser',
                                'type' => 'postback',
                                'payload' => 'botReset'
                            ),

                        )
                    )));
                    custom_send(json_encode($data), $config["page_access_token"]);
                    break;

                case "get_started":
//            $array = '{"fields":["persistent_menu"]';

                    $started = '{"get_started":{"payload":"botReset"}}';
// $started=array("get_started"=>array("GET_STARTED_PAYLOAD"));
                    custom_send($started, $config["page_access_token"]);
                    break;
                case 'greeting':
                    $code = '{"greeting":[{"locale":"default", "text":"Bonjour {{user_first_name}}, Bienvenue sur Ifisun\nIfisun est une plateforme de lutte contre les violences faites aux femmes"}]}';
                    custom_send($code, $config['page_access_token']);
                    break;
                case 'control':
                    //$this->load->model('botMessage_model');
                    $this->load->model('user_model');
                    //var_dump_pre($this->db->query("SELECT * FROM users where bot_subscriber_id = 1889466461169454")->row_array());
                   var_dump_pre($this->user_model->updateMetaForBotSubscriber($this->input->get('value'), 'wait', 0));
                    //var_dump_pre($this->user_model->getUserByKeyword('bot_subscriber_id', 1889466461169454));
                    break;

                case "delete_get_started":
                    $array = '{"fields":["persistent_menu"]';
                    custom_send($array, $config["page_access_token"]);
                    break;
            }
        } else {
            $bot = new BotInteractor($config);
            $bot->run();
            unset($bot);
            die();
        }
        //$this->load->view('welcome_message');
    }
}