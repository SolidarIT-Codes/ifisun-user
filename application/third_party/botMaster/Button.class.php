<?php

class Button {

    public $type;
    public $title;

    public function __construct($type, $title = "", $payload = "") {

        if ($type != 'web_url' && $type != 'postback' && $type != 'phone_number' && $type != 'element_share')
            throw new Exception('Invalid type');
       
        $this->type = $type;

        switch ($type) {
            case "web_url":
                if (!filter_var($payload, FILTER_VALIDATE_URL))
                    throw new Exception('Invalid URL');
                $this->url = $payload;
                $this->title = $title;
                break;

            case "element_share":
                $this->type = $type;
                break;

            default:
                if (empty($payload))
                    throw new Exception('Empty payload');
                $this->payload = $payload;
                $this->title = $title;
                break;
        }
    }

// __construct
}

// Button
