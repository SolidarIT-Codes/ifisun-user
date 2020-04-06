<?php

/**
 * Created by PhpStorm.
 * User: MICHAEL
 * Date: 26/10/2017
 * Time: 12:40
 */

require 'botMaster/BotApp.class.php';

class BotInteractor extends BotApp
{
    private $ci;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->ci =& get_instance();
        $this->ci->load->model('user_model');
        $this->ci->load->model('botMessage_model');
        $this->ci->load->model('complain_model');
        /*$this->ci->load->model('message_model');
        $this->ci->load->model('subscriber_model');
        $this->ci->load->model('image_model');*/
    }

    public function botReset()
    {
        if (!$this->subscriberExist()) {
            $subscriberData = $this->getFaceBookUserData($this->sender_id);
            $subscriberData['bot_subscriber_id'] = $subscriberData['id'];
            $subscriberData['user_photo']=saveFile($subscriberData['profile_pic'], 'uploads/');
            unset($subscriberData['id']);
            $this->insert($subscriberData);
        }
        $this->cancelWait();
        $this->sendMyText($this->getMessage('welcome-msg'));
        $this->botStart();
    }

    public function botStart()
    {
        // $this->cancelWait();
        $this->setWait('botStart');
        $this->sendYesOrNoQuickReply($this->getMessage('can-we-start-msg'), 'botStarted', 'botStartCanceled');
    }

    public function botStartCanceled()
    {
        $this->setWait('botReset');
        $this->sendMyText($this->getMessage('acceptance-msg'));
        $this->sendMyText($this->getMessage('come-back-again-msg'));
    }

    public function botStarted()
    {
        $this->sendMyText($this->getMessage('happy-msg'));
        $this->botChooseComplainType();
    }


    public function botChooseComplainType()
    {
        $this->setWait('botChooseComplainType');
        $complainTypes = $this->ci->complain_model->getAllComplainTypes();
        $elements = [];
        $quickReplies = [];
        if (!empty($complainTypes)) {
            // $beforeTitle = $this->getMessage('i-want').' ';
            $uploadPath = get_upload_path();
            foreach ($complainTypes as $type) {
                $elements[] = new Element($type['name'], $type['description'], $uploadPath . $type['photo'], [
                    new Button('postback', $type['name'], maybe_serialize([
                        'func' => 'choosedComplainType',
                        'param' => $type['id']
                    ]))
                ]);
                /*
                $quickReplies[]= new QuickReply($beforeTitle.$type['name'], maybe_serialize([
                    'func'=>'choosedComplainType',
                    'param'=>$type['id']
                ]), $uploadPath.$type['photo']);
                */
            }
        }
        $this->sendMyText($this->getMessage('what-can-i-do-for-u'));
        $this->sendTemplate(new GenericTemplate($elements));
        //$this->sendQuickReply($this->getMessage('what-can-i-do-for-u'), $quickReplies);

    }

    public function choosedComplainType($param)
    {
        $this->cancelWait();
        $this->emptyTemp();
        $this->addToTemp([
            'complain_type_id' => (int)$param
        ]);
        $this->complainUploadStart();

    }

    public function complainUploadStart()
    {
        $this->setWait('complainUploadStarted');
        $this->sendMyText($this->getMessage('acceptance-msg'));
        $this->sendMyText($this->getMessage('u-can-upload-text-data'));
        $this->sendMyText($this->getMessage('awaiting-msg'));
    }

    public function complainUploadStarted($param)
    {
        $existingTemp = $this->getTemp();
        if ($param['type'] !== 'text') {
            $existingTemp['url_uploads'][] = $param['value'];
        } else {
            $existingTemp['text_uploads'][] = $param['value'];
        }
        $this->setTemp($existingTemp);
        $quickReply = [
            new QuickReply($this->getMessage('i-finished'), 'complainUploadFinish', '')
        ];
        $this->sendQuickReply($this->getMessage('have-u-finished'), $quickReply);
    }

    public function complainUploadFinish()
    {
        $this->setWait('complainUploadFinish');
        $this->sendYesOrNoQuickReply($this->getMessage('have-u-really-finished'), 'setPhoneNumberStart', 'complainUploadContinue');
    }

    public function complainUploadContinue()
    {
        $this->setWait('complainUploadStarted');
        $this->sendText($this->getMessage('continue-uploading-complain'));
        $this->sendMyText($this->getMessage('awaiting-msg'));
    }

    public function setPhoneNumberStart()
    {
        $this->setWait('setPhoneNumberStarted');
        $this->sendMyText($this->getMessage('happy-msg'));
        $this->sendMyText($this->getMessage('send-ur-phone-number'));
    }

    public function errorPhoneNumber()
    {
        $this->setWait('setPhoneNumberStarted');
        $this->sendMyText($this->getMessage('incorrect-phone-number'));
    }

    public function setPhoneNumberStarted($param)
    {
        $this->cancelWait();
        if (is_string($param)) {
            if (validate_phone_number($param)) {
                $this->addToTemp(['phone' => $param]);
                $this->complainUploadSuccess();
            } else {
                $this->errorPhoneNumber();
            }
        } else {
            $this->errorPhoneNumber();
        }
    }


    public function complainUploadSuccess()
    {
        $this->cancelWait();
        $this->sendMyText($this->getMessage('wait-while-storing'));
        $this->sendAction('typing_on');
        $tempData = $this->getTemp();
        $uploadedData = [];
        $uploadedText = '';
        $phone="";
        if ($uploads = maybe_null_or_empty($tempData, 'url_uploads')) {
            foreach ($uploads as $upload) {
                $uploadedData[] = saveFile($upload, 'uploads/');
            }
        }
        if($uploadedText = maybe_null_or_empty($tempData, 'text_uploads')){
            $uploadedText = implode('<br>', $uploadedText);
        }
        $phone=maybe_null_or_empty($tempData, 'phone');
        $memberID = (int) $this->ci->user_model->getUserIDByKeyword('bot_subscriber_id', $this->sender_id);
        $complainTypeID = (int) maybe_null_or_empty($tempData, 'complain_type_id');
        $data = [
            'member_id'=>$memberID,
            'complain_type_id'=>$complainTypeID,
            'uploads'=>$uploadedData,
            'text'=>$uploadedText,
            'phone'=>$phone,
            'source'=>'chatbot'
        ];

        $this->ci->load->model('complain_model');
        $complainID = $this->ci->complain_model->insert($data);
        $this->sendAction('typing_off');
        if($complainID){
            $this->complainStoredSuccesful();
        }else{
            $this->complainStoreError();
        }

    }

    public function complainStoredSuccesful(){
        $this->cancelWait();
        $this->sendMyText($this->getMessage('complain-stored-success'));
    }

    public function complainStoreError(){
        $this->cancelWait();
        $this->sendMyText($this->getMessage('complain-stored-error'));
    }

    public function getMessage($keyword, $additional = [])
    {
        return $this->ci->botMessage_model->getDecryptedMessage($keyword, $this->sender_id);
    }


    public function emptyTemp()
    {
        $this->ci->user_model->updateMetaForBotSubscriber($this->sender_id, 'botTempData', null);
    }


    public function addToTemp(array $data)
    {
        $temp = $this->getTemp();
        if (!$temp) {
            $temp = [];
        }
        $temp = array_merge($temp, $data);
        $this->setTemp($temp);
    }

    private function actionate($entrantPayload)
    {
        $entrantPayload = maybe_unserialize($entrantPayload);
        $this->sendAction("typing_on");
        if (isset($entrantPayload['func'])) {
            if (isset($entrantPayload['param']) && !empty($entrantPayload['param'])) {
                $this->caller([$this, $entrantPayload['func']], [$entrantPayload['param']]);
            } else {
                $this->caller([$this, $entrantPayload['func']]);
            }
        } else {
            $this->caller([$this, $entrantPayload]);
        }

        $this->sendAction("typing_off");
    }


    private function caller(callable $func, $param = [])
    {
        return call_user_func_array($func, $param);
    }


    private function getTemp()
    {
        //return $this->_subscriberClass->getTempData($this->sender_id);
        $data = $this->ci->user_model->getMetaForBotSubscriber($this->sender_id, 'botTempData');
        return maybe_unserialize($data);
    }

    private function setTemp($data)
    {
        //$this->_subscriberClass->storeTempData($this->sender_id, $data);
        $this->ci->user_model->updateMetaForBotSubscriber($this->sender_id, 'botTempData', maybe_serialize($data));
    }


    public function getCutText($txt, $limit = 640)
    {
        $txt = strip_tags($txt);
        if (strlen(trim($txt)) == 0)
            return;
        if (strlen($txt) <= $limit) {
            return $txt;
        } else {
            $txt = wordwrap($txt, $limit, "_flag_mission_");
            $txt = explode("_flag_mission_", $txt);
            return $txt;
        }
    }

    public function sendLongText($txt)
    {
        $txt = $this->getCutText($txt);
        if (is_string($txt)) {
            $this->sendAction('typing_on');
            $this->sendText($txt);
            $this->sendAction('typing_off');
        }
        if (is_array($txt)) {
            foreach ($txt as $t) {
                $this->sendAction('typing_on');
                $this->sendText($t);
                $this->sendAction('typing_off');
            }
        }
    }

    public function sendMyText($text, $glue = '*')
    {
        if (strpos($text, $glue)) {
            $cuts = explode($glue, $text);
            foreach ($cuts as $cut) {
                $this->sendAction('typing_on');
                $this->sendText($cut);
                $this->sendAction('typing_off');
            }
        } else {
            $this->sendAction('typing_on');
            $this->sendText($text);
            $this->sendAction('typing_off');
        }
    }

    public function receivedQuickreply($msg)
    {
        $this->sendAction("typing_on");
        $entrantPayload = $msg->message->quick_reply->payload;
        $this->actionate($entrantPayload);
    }

    public function receivedLocation($msg)
    {
        $wait = $this->getWait();
        //$this->sendLongText(json_encode($msg->message->attachments[0]->payload->coordinates));
        $this->actionate(['func' => $wait, 'param' => ['latitude' => $msg->message->attachments[0]->payload->coordinates->lat, 'longitude' => $msg->message->attachments[0]->payload->coordinates->long]]);
    }


    public function receivedPostback($msg)
    {
//        $this->sendAction("typing_on");
        $entrantPayload = $msg->postback->payload;
        $this->actionate($entrantPayload);
    }

    public function receivedMessage($msg)
    {
        $msg = $msg->message->text;
        if ($msg != 'RESET') {
            if ($wait = $this->getWait()) {
                if ($wait == 'complainUploadStarted') {
                    $this->actionate(array('func' => $wait, 'param' => [
                        'value' => $msg,
                        'type' => 'text'
                    ]));
                } else {
                    $this->actionate(['func' => $wait, 'param' => $msg]);
                }
            }
        } else {
            $this->botReset();
        }
    }

    public function receivedImage($msg)
    {
//        $subscriber_id = self::get_current_subscriber_data('id');
        $wait = $this->getWait();
        //        $this->sendLongText(json_encode($urls));
        if ($wait) {
            $urls = $msg->message->attachments[0]->payload->url;
            if ($wait == 'complainUploadStarted') {
                $this->actionate(array('func' => $wait, 'param' => [
                    'value' => $urls,
                    'type' => 'image'
                ]));
            } else {
                $this->actionate(array('func' => $wait, 'param' => $urls));
            }

        }
    }

    public function receivedFile($msg)
    {
        $wait = $this->getWait();
        //        $this->sendLongText(json_encode($urls));
        if ($wait) {
            $urls = $msg->message->attachments[0]->payload->url;
            if ($wait == 'complainUploadStarted') {
                $this->actionate(array('func' => $wait, 'param' => [
                    'value' => $urls,
                    'type' => 'file'
                ]));
            } else {
                $this->actionate(array('func' => $wait, 'param' => $urls));
            }
        }
    }

    public function receivedVideo($msg)
    {
        $wait = $this->getWait();
        //        $this->sendLongText(json_encode($urls));
        if ($wait) {
            $urls = $msg->message->attachments[0]->payload->url;
            if ($wait == 'complainUploadStarted') {
                $this->actionate(array('func' => $wait, 'param' => [
                    'value' => $urls,
                    'type' => 'video'
                ]));
            } else {
                $this->actionate(array('func' => $wait, 'param' => $urls));
            }
        }
    }

    public function receivedAudio($msg)
    {
        $wait = $this->getWait();
        if ($wait) {
            $urls = $msg->message->attachments[0]->payload->url;
            if ($wait == 'complainUploadStarted') {
                $this->actionate(array('func' => $wait, 'param' => [
                    'value' => $urls,
                    'type' => 'audio'
                ]));
            } else {
                $this->actionate(array('func' => $wait, 'param' => $urls));
            }
        }
    }

    public function subscriberExist()
    {
        return $this->ci->user_model->botSubscriberExist($this->sender_id);
    }

    public function insert($data)
    {
        $this->ci->user_model->insert($data);
    }

    public function getImage($keyword)
    {
        // return $this->ci->image_model->getByKeyword($keyword);
    }

    private function cancelWait()
    {
        $this->ci->user_model->updateMetaForBotSubscriber($this->sender_id, 'wait', 0);
    }

    private function setWait($waitFlag)
    {
        $this->ci->user_model->updateMetaForBotSubscriber($this->sender_id, 'wait', 1);
        $this->ci->user_model->updateMetaForBotSubscriber($this->sender_id, 'wait_for', $waitFlag);
    }

    private function getWait()
    {
        if ((int)$this->ci->user_model->getMetaForBotSubscriber($this->sender_id, 'wait')) {
            return $this->ci->user_model->getMetaForBotSubscriber($this->sender_id, 'wait_for');
        }
        return false;
    }

    public function sendYesOrNoQuickReply($quickReplyText, $yesPayload, $noPayload)
    {
        $quickReply[] = new QuickReply($this->getMessage('yes'), $yesPayload, '');
        $quickReply[] = new QuickReply($this->getMessage('no'), $noPayload, '');
        $this->sendQuickReply($quickReplyText, $quickReply);
    }

    /* public function sendQuickReplyWIthLocation($text, $quickReplies=[]){
         $quickReplies[]=new QuickReply(null, null, null, 'location');
         $this->sendQuickReply($text, $quickReplies);
     }*/

    private function updateMeta($key, $value)
    {
        //  $this->ci->subscriber_model->update_meta($this->sender_id, $key, $value);
    }

    private function getMeta($key)
    {
        //  return $this->ci->subscriber_model->get_meta($this->sender_id, $key);
    }

    private function getAllMeta()
    {
        //  return $this->ci->subscriber_model->getSubscriberMeta($this->sender_id);
    }

}