<?php

require_once __DIR__ . '/../mainController.php';

class MaterialCardLogController extends mainController
{
    public function __construct()
    {
        $this->setViewPath(__DIR__ . '/view/');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function showAction() {
        return $this->render('logView.php');
    }
}