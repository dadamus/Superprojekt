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

function getClients() {
    global $db;

    $query = $db->prepare("SELECT * FROM `clients`");
    $query->execute();

    $data = "";
    foreach ($query as $row) {
        $data .= "<tr class='gradeA' style='cursor: pointer;' id='" . $row['id'] . "_id'>";

        $data .= "<td>" . $row['id'] . "</td>";
        $data .= "<td>" . $row['name'] . "</td>";
        $data .= "<td>" . $row['email'] . "</td>";
        $data .= "<td>" . $row['phone'] . "</td>";

        $date = explode(" ", $row['date']);
        $data .= "<td>" . $date[0] . "</td>";

        $data .= "<td>" . $row['person'] . "</td>";
        $data .= "<td>" . $row['nip'] . "</td>";
        $data .= "<td>" . $row['address'] . "</td>";

        $client = new Client($row["type"]);
        $data .= "<td>$client->s_type</td>";

        $data .= '<td><a href="#clienteForm" data-toggle="modal" class="cEdit btn btn-info"  id="' . $row["id"] . '_eid">Edytuj</a></td>';
            
        $data .= "</tr></a>";
    }
    return $data;
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
