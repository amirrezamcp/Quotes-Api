<?php
namespace Controllers;
class quotesController{
    public function index($params){
        echo $params[1];
    }
}