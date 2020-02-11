<?php
    //Importacion de las caveseras Cors
    include 'class/Cors.php';
    //Importacion de la conexion
    include 'class/MySQL.php';

    //Intasncias
    $conn = new MySQL();
    $response = array();
    $status = null;

    //Ejecucion de la peticion GET
    if ($_SERVER['REQUEST_METHOD'] === "GET") {
        if (!empty($_GET)) {
            //Se recupera el parametro por metodo GET y establesco la sentencias SQL
            $idTask = $_GET['id'];
            $sql = "SELECT * FROM tasks WHERE idTask = :idTask";

            //Preparo la sentencia SQL y se cambia los parametros de la sentencia SQL
            $state = $conn->getConnection()->prepare($sql);
            $state->bindParam(':idTask', $idTask);

            //Ejecuto la sentencia y veo que respuesta me trae.
            if (!$state->execute()) {
                //Si la sentencia regresa un error de sixtaxis y manda la respuesta
                $status = 400;
                    
                http_response_code($status);
                $response = array(
                    "status" => $status,
                    "message" => "The query is bad!",
                    "response" => false
                );
            } else {
                
                $json = array();
                while ($row = $state->fetch(PDO::FETCH_ASSOC)) {
                    $json[] = array(
                        "idTask" => $row['idTask'],
                        "task" => $row['task'],
                        "description" => $row['description'],
                        "done" => $row['done'],
                        "date_finish" => $row['date_finish']
                    );
                }

                $response = array(
                    "response" => true,
                    "message" => "The query is well",
                    "body" => $json
                );
            }
        } else {
            //Establesco el status del servidor
            $status = 400;
            //Establesco la respuesta del servidor
            http_response_code($status);
            $response = array(
                "status" => $status,
                "message" => "The http param is empty",
                "response" => false
            );
        }
    } else {
        //Establesco el status de la espuesta del servidor
        $status = 400;
        //Establesco la respuesta del servidor
        http_response_code($status);
        $response = array(
            "status" => $status,
            "message" => "The http request is invalid",
            "response" => false
        );
    }
    
    $jsonString = json_encode($response);
    echo $jsonString;