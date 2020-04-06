<?php

class QuickReply {

    public $content_type;
    public $title;
    public $payload;
    public $image_url;

    public function __construct($title, $payload, $image_url, $content_type = "text") {
        $this->content_type = $content_type;
        if ($content_type != "location") {
            $this->title = $title;
            $this->payload = $payload;
            $this->image_url = $image_url;
        }
    }

// __construct
}

// QuickReply
