<?php

class Imap {

    public $imap;
    public $ip, $user, $pass;
    public $subject_types;

    public function __construct($ip, $user, $pass) {
        $this->ip = $ip;
        $this->user = $user;
        $this->pass = $pass;


        $st = array();
        $st[1] = "A program was finished.";
        $st[2] = "A program was unusually finished.";
        $st[3] = "Power supply off management was started.";
        $st[4] = "A program stopped.";
        $st[5] = "Alarm occurred.";

        $this->subject_types = $st;

        $this->imap = imap_open($ip, $user, $pass);
        if (!$this->imap) {
            die("Brak połączenia IMAP");
        }
    }

    public function getMail($date = '22 August 2016') {
        $content = array();
        $emails = imap_search($this->imap, 'SINCE "' . $date . '"');
        if (is_array($emails) || is_object($emails)) {
            foreach ($emails as $eid) {
                $header = imap_fetch_overview($this->imap, $eid, 0);
                mb_internal_encoding("UTF-8");

                array_push($content, array("uid" => $eid, "subject" => mb_decode_mimeheader($header[0]->subject), "content" => imap_body($this->imap, $eid)));
            }
        }
        return $content;
    }

}
