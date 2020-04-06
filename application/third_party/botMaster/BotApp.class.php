<?php

// Templates
require 'Template.class.php';
require 'ButtonTemplate.class.php';
require 'GenericTemplate.class.php';

// Elements
require 'Element.class.php';
require 'Button.class.php';

// Misc classes
require 'Attachment.class.php';
require 'QuickReply.class.php';
require 'Payload.class.php';

class BotApp
{

    public $sender_id;
    private $verify_token;
    private $page_access_token;
    private $session;
    private $json;
    private $request;

    public function __construct(array $config)
    {
        //var_dump('vadd');
        if (!isset($config['verify_token']))
            throw new Exception('Missing verify_token in configuration');
        if (!isset($config['page_access_token']))
            throw new Exception('Missing page_access_token in configuration');
        if (!function_exists('curl_init'))
            throw new Exception('cURL extension is needed');

        $this->verify_token = $config['verify_token'];
        $this->page_access_token = $config['page_access_token'];
        $this->debug = (isset($config['debug']) && $config['debug']);
        $this->log_file = (isset($config['log_file'])) ? $config['log_file'] : 'bot.log';

        $this->checkValidation();

        // message parsing and handling
        $ci =& get_instance();
        $this->json = $ci->input->raw_input_stream;
        $this->request = json_decode($this->json);
        if (!is_object($this->request)):
            $this->log('Unknown stuff received: ' . $this->json);
            exit;
        endif;

        // Manual session management - they don't give cookies :(
        if (isset($config['sender_id'])) {
            $this->sender_id = $config['sender_id'];
        } else {
            $this->sender_id = $this->request->entry[0]->messaging[0]->sender->id; //FIXME: Messy, messy code
        }
        //$this->update_subscriber_activity();
        /*session_id(get_class($this) . $this->sender_id);
        //session_start();
        $this->session = $ci->session->userdata();
        if (empty($this->session)):
            $this->session = array();
        endif;*/
        //$this->update_subscriber_activity();
    }

    private function checkValidation()
    {
        $ci =& get_instance();
        //var_dump($ci->input->get());
        if ($ci->input->get('hub_mode') && $ci->input->get('hub_mode') == 'subscribe'):
            if (!$ci->input->get('hub_verify_token') || !$ci->input->get('hub_challenge'))
                exit;
            if ($ci->input->get('hub_verify_token') == $this->verify_token):
                //$this->log($ci->input->get('hub_challenge'));
                print $ci->input->get('hub_challenge');
                exit;
            endif;
        endif;
    }


// __construct
    //Récurère les informations de l'abonné

    protected function log($txt)
    {

        if (!$this->debug)
            return;

        $fd = fopen($this->log_file, 'a');
        fwrite($fd, date('r') . "\n");
        fwrite($fd, $txt . "\n\n");
        fclose($fd);
    }

    /* destructor. saves the session. */

    public function __destruct()
    {

        $_SESSION = $this->session;
        session_write_close();
    }

// __destruct


    /* Bot webhook validation */

    public function getSession($key)
    {

        if (!isset($this->session[$key]))
            return null;
        return $this->session[$key];
    }

// checkValidation

    public function setSession($key, $val)
    {

        $this->session[$key] = $val;
    }

// getSession

    public function run()
    {
        // dispatch the request to the appropriate handler
        if ($this->request->object == 'page'):

            foreach ($this->request->entry as $entry):
                $page_id = $entry->id;
                $ts = $entry->time;
                foreach ($entry->messaging as $msg):
                    //$this->sendText(json_encode($msg));
                    if (isset($msg->message)):
                        if (isset($msg->message->is_echo) && $msg->message->is_echo):
                            $this->log("Received ECHO: " . json_encode($msg));
                            $this->receivedEcho($msg);
                        elseif (isset($msg->message->quick_reply)):
                            $this->log("Received QUICKREPLY: " . json_encode($msg));
                            $this->receivedQuickreply($msg);
                        elseif (!isset($msg->message->attachments)):
                            $this->log("Received TEXT: " . json_encode($msg));
                            //$this->sendText('text received');
                            $this->receivedMessage($msg);
                        elseif (isset($msg->message->attachments)):
                            foreach ($msg->message->attachments as $att):
                                switch ($att->type):
                                    case 'image':
                                        $this->log("Received IMAGE: " . $att->payload->url);
                                        $this->receivedImage($msg);
                                        break;
                                    case 'video':
                                        $this->log("Received VIDEO: " . $att->payload->url);
                                        $this->receivedVideo($msg);
                                        break;
                                    case 'audio':
                                        $this->log("Received AUDIO: " . $att->payload->url);
                                        $this->receivedAudio($msg);
                                        break;
                                    case 'file':
                                        $this->log("Received FILE: " . $att->payload->url);
                                        $this->receivedFile($msg);
                                        break;
                                    case 'location':
                                        $this->log("Received COORDINATES: " . $att->payload->coordinates->lat . "," . $att->payload->coordinates->long);
                                        $this->receivedLocation($msg);
                                        break;
                                endswitch;
                            endforeach;
                        endif;
                    elseif (isset($msg->optin)):
                        //$this->update_subscriber_activity();
                        $this->log("Received OPTIN: " . json_encode($msg));
                        $this->receivedAuthentication($msg);
                    elseif (isset($msg->delivery)):
                        $this->log("Received DELIVERY: " . json_encode($msg));
                        $this->receivedDelivery($msg);
                    elseif (isset($msg->postback)):
                        $this->update_subscriber_activity();
                        $this->log("Received POSTBACK: " . json_encode($msg));
                        //$this->sendText(json_encode($msg));
                        $this->receivedPostback($msg);
                    else:
                        //$this->update_subscriber_activity();
                        $this->log("Received UNKNOWN: " . json_encode($msg));
                        $this->receivedUnknown($msg);
                    endif;
                endforeach;
            endforeach;
        endif;
    }

// setSession

    public function update_subscriber_activity()
    {
        $ci =& get_instance();
        /*$ci->load->model('subscriber_model');
        if (!$ci->subscriber_model->exist($this->sender_id)) {
            $ci->subscriber_model->insert($subscriberData=(array)$this->getFaceBookUserData($this->sender_id));
        }*/
        //$ci->session->set_userdata($subscriberData);
    }

// run


    /* low-level sending method */

    public function get_subscriber_profil($user_id)
    {

        $url = "https://graph.facebook.com/v2.6/" . $user_id . "?fields=first_name,last_name,profile_pic&access_token=" . $this->page_access_token;

        $json = file_get_contents($url);
        $this->sendText(($json));
        $obj = json_decode($json);
        return $obj;
    }

    public function getFaceBookUserData($userID)
    {
        // Get cURL resource
        $curl = curl_init();

        $url = "https://graph.facebook.com/v2.6/" . $userID . "?fields=first_name,last_name,profile_pic,locale,timezone,gender&access_token=" . $this->page_access_token;

// Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'Codular Sample cURL Request'
        ));
// Send the request & save response to $resp
        $resp = curl_exec($curl);
// Close request to clear up some resources
        curl_close($curl);
        return (array) json_decode($resp);
    }

// send


    /* content can be a string or an Attachment object */

    public function sendText($txt)
    {

        $obj = new StdClass();
        $obj->recipient = new StdClass();
//        if ($sender_id == '')
        $obj->recipient->id = $this->sender_id;
//        else
//            $obj->recipient->id = $sender_id;
        $obj->message = new StdClass();
        $obj->message->text = $txt;
        $json = json_encode($obj);

        $this->send($json);
    }

// sendQuickReply

    private function send($json)
    {

        $this->log("SENDING: {$json}");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v2.6/me/messages?access_token=' . $this->page_access_token);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch);
        $this->log("RESPONSE: {$ret}");
    }

    // send user_action

    public function receivedEcho($msg)
    {

    }

// sendText

    public function receivedQuickreply($msg)
    {

    }

// sendTemplate

    public function receivedMessage($msg)
    {

    }

// sendAttachment

    public function receivedImage($msg)
    {

    }

// sendFile

    public function receivedVideo($msg)
    {

    }

// sendAudio

    public function receivedAudio($msg)
    {

    }

// sendVideo

    public function receivedFile($msg)
    {

    }

// sendImage

    public function receivedLocation($msg)
    {

    }

// log


    /* these functions should be implemented in the child class */

    public function receivedAuthentication($msg)
    {

    }

    public function receivedDelivery($msg)
    {

    }

    public function receivedPostback($msg)
    {

    }

    public function receivedUnknown($msg)
    {

    }

    public function sendQuickReply($text, $quick_replies)
    {

        $obj = new StdClass();
        $obj->recipient = new StdClass();
        $obj->recipient->id = $this->sender_id;
        $obj->message = new StdClass();
//        if ($content instanceof Attachment):
//            $obj->message->attachment = $content;
//        else:
//            $obj->message->text = "Tooo";
//        endif;
        $obj->message->text = $text;
        $obj->message->quick_replies = $quick_replies;
        $json = json_encode($obj);

        $this->send($json);
    }

    public function sendAction($type)
    {

        $obj = new StdClass();
        $obj->recipient = new StdClass();
        $obj->recipient->id = $this->sender_id;
        $obj->sender_action = $type;
        $json = json_encode($obj);

        $this->send($json);
    }

    public function sendTemplate(Template $template)
    {

        $obj = new StdClass();
        $obj->recipient = new StdClass();
        $obj->recipient->id = $this->sender_id;

        $obj->message = new StdClass();
//        $payload=new stdClass();
//        $payload->elements=$template;
//        $payload->image_aspect_ratio='square';
        $obj->message->attachment = new Attachment(
            'template', $template
        );
        $json = json_encode($obj);

        $this->send($json);
    }

    public function sendFile($url)
    {

        $att = new Attachment('file', new Payload($url));
        $this->sendAttachment($att);
    }

    public function sendAttachment(Attachment $att)
    {

        $obj = new StdClass();
        $obj->recipient = new StdClass();
        $obj->recipient->id = $this->sender_id;
        $obj->message = new StdClass();
        $obj->message->attachment = $att;
        $json = json_encode($obj);

        $this->send($json);
    }

    public function sendAudio($url)
    {

        $att = new Attachment('audio', new Payload($url));
        $this->sendAttachment($att);
    }

    public function sendVideo($url)
    {

        $att = new Attachment('video', new Payload($url));
        $this->sendAttachment($att);
    }

    public function sendImage($url)
    {

        $att = new Attachment('image', new Payload($url));
        $this->sendAttachment($att);
    }

}

// BotApp
