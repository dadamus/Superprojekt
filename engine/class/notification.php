<?php

function getNotification() {
    global $db;

    $leng = 0;
    $content = "";

    $dateRange = date("Y-m-d 00:00:00", strtotime("-2 DAY"));
    $emails = $db->query("SELECT `id`, `type`, `send_date`, `pid`, `done` FROM `email` WHERE send_date > '$dateRange' ORDER BY `id` DESC");
    foreach ($emails as $email) {
        if ($email["done"] == 1 && $email["type"] != 3) {
            continue;
        }
        $leng++;

        $l_type = "label-warning";
        $l_i = "fa fa-bell-o";
        if ($email["type"] == 2 || $email["type"] == 4) {
            $l_type = "label-danger";
            $l_i = "fa fa-warning";
        }

        //Time counting
        $s_date = strtotime($email["send_date"]);
        $d = strtotime(date("Y-m-d H:i:s")) - $s_date;

        $time_s = "teraz";
        if ($d <= 60) {
            $time_s = "teraz";
        } else if ($d <= 60 * 60) {
            $time_s = floor($d / 60) . " min";
        } else if ($d <= 24 * 60 * 60) {
            $time_s = floor($d / 60 / 60) . " godz";
        } else {
            $time_s = floor($d / 60 / 60 / 24) . "dni";
        }

        //Text
        switch ($email["type"]) {
            case 1:
                if ($email["pid"] == 0) {
                    $text = "Brak programu!";
                } else {
                    $text = "Program wycięty.";
                    $l_type = "label-success";
                    $l_i = "fa fa-info-circle";
                }
                break;
            case 2:
                $text = "Niezakończony poprawnie!";
                break;
            case 3:
                $text = "Wyłączenie zasilania.";
                $l_type = "label-danger";
                $l_i = "fa fa-bolt";
                break;
            case 4:
                $text = "Program zatrzymany!";
                break;
            case 5:
                $text = "Alarm!";
                break;
            case 404:
                $text = "Nieznany błąd!";
                break;
        }

        $content .= '<li id="' . $email["id"] . '_eid"><a href = "javascript:;"> <span class="details"><span class="label label-sm label-icon ' . $l_type . ' md-skip"><i class="' . $l_i . '"></i></span>' . $text . '</span><span class="time">' . $time_s . '</span></a></li>'."\r\n";
    }

    return array("size" => $leng, "content" => $content);
}
