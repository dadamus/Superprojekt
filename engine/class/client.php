<?php

class Client { // Client

    public $i_type; //Typ  liczbowy
    public $s_type; //Typ tkstowy

    public function __construct($int) {
        $this->i_type = $int;
        switch ($int) {
            case 1:
                $this->s_type = 'F';
                break;
            case 2:
                $this->s_type = 'O';
                break;
        }
    }

}

/**
 * @return array
 */
function getClients() {
    global $db;

    $query = $db->query("SELECT * FROM `clients`");
    return $query->fetchAll();
}

function getClientsShort($query) {
    global $db;

    $query->execute();
    $data = "";
    foreach ($query as $row) {
        $client = new Client($row["type"]);
        $type = $client->s_type;

        $data .= '<tr id="' . $row["id"] . '_' . $row["name"] . '" style="cursor: pointer"><td>' . $row["id"] . '</td><td class="_fname">' . $row["name"] . '</td><td>' . $type . '</td></tr>';
    }
    return $data;
}
