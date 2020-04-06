<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 27/10/2018
 * Time: 19:19
 */

class MY_Controller extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        header("Access-Control-Allow-Methods: POST, GET");
        //header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        header('Access-Control-Allow-Origin: '.$this->config->item('controlOrigin'));
    }


}