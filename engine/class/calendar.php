<?php

class calendar {

    public $menu = [];

    public function __construct($site_path) {
        $this->menu = [
            [
                "text" => "To do",
                "link" => $site_path . "/site/200/kalendarz-todo",
            ],
            [
                "text" => "Prywatny",
                "link" => $site_path . "/site/201/kalendarz-prywatny",
            ],
            [
                "text" => "Projekty",
                "link" => $site_path . "/site/202/kalendarz-projekty",
            ],
            [
                "text" => "Projektownia",
                "link" => $site_path . "/site/203/kalendarz-projektownia",
            ],
            [
                "text" => "Cięcie",
                "link" => $site_path . "/site/204/kalendarz-ciecie",
            ], [
                "text" => "Gięcie",
                "link" => $site_path . "/site/205/kalendarz-giecie",
            ], [
                "text" => "Marketing",
                "link" => $site_path . "/site/206/kalendarz-marketing",
            ],
            [
                "text" => "Konserwacja",
                "link" => $site_path . "/site/207/kalendarz-konserwacja",
            ],
            [
                "text" => "Dostawy",
                "link" => $site_path . "/site/208/kalendarz-dostawy",
            ],
            [
                "text" => "Odbiór",
                "link" => $site_path . "/site/209/kalendarz-odbior",
            ],
            [
                "text" => "Inne",
                "link" => $site_path . "/site/210/kalendarz-inne",
            ],
        ];
    }

}
