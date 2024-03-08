<?php
namespace Controllers;

class HomeController{
    public function home() {
        $respons = [
            'statud' => 'ok',
            'message' => 'welcome home'
        ];
        echo json_encode($respons);
    }
}