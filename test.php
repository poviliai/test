<?php
    $json = [];

    $hostname = '';
    $username = '';
    $password = '';
    $database = '';

    if(empty($hostname) || empty($username) || empty($password) || empty($database)){
        $json['error'] =  "missing access data for the DB server";
       
    }else{
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $mysqli = new mysqli($hostname,$username,$password,$database);

        // Check connection
        if ($mysqli->connect_errno) {
            $json['error'] = "Failed to connect to MySQL: " . $mysqli->connect_error;
           
        }

    }

    if (!$json && !empty($_FILES['file']['name']) && is_file($_FILES['file']['tmp_name'])){

         $required_key = ['item_id','customer_id','comment'];

        // Sanitize the filename
        $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($_FILES['file']['name'], ENT_QUOTES, 'UTF-8')));

        // Validate the filename length
        if (strlen($filename) > 64) {
            $json['error'] = 'Error file name';
        }

        // Return any upload error
        if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
            $json['error'] = 'Error upload file '. $_FILES['file']['error'];
        }
        //Check file type
        if($_FILES['file']['type'] !== 'text/plain'){
                $json['error'] = 'Error file type';
        }
        //-- processing
        if(!$json){
            $file_array = file($_FILES['file']['tmp_name'], FILE_IGNORE_NEW_LINES);
            if(!$file_array){
                $json['error'] = 'File upload error';
            }else{
                $error = [];
                //start from 1 - skip header 
                for($i=1; $i < count($file_array); $i++){
                   
                    $row = explode(';',$file_array[$i]);
                    if(count($row) === count($required_key)){

                        $values = array_combine($required_key, $row); 
                        //check product_id
                        $result = $mysqli->query("SELECT * FROM merchandise WHERE id='". (int)$values['item_id'] ."'");
                        if(!$result->fetch_assoc()){
                            $error[] =  $file_array[$i] . ';error: -  item_id not found';
                            file_put_contents('error.log', $file_array[$i] ."\n", FILE_APPEND);
                            continue;
                        }
                        //check customer_id 
                        $result = $mysqli->query("SELECT * FROM clients WHERE id='". (int)$values['customer_id'] ."'");
                        if(!$result->fetch_assoc()){
                            $error[] =  $file_array[$i] . ';error: -  customer_id not found';
                            file_put_contents('error.log', $file_array[$i] ."\n", FILE_APPEND);
                            continue;
                        }
                                
                        $mysqli->query("INSERT INTO `orders`(`item_id`,`customer_id`,`comment`) VALUES ('". (int)$values['item_id'] ."','". (int)$values['customer_id']  ."','". $mysqli->real_escape_string(trim($values['comment'])) ."') ");
                       
                    }else{
                        $error[] =  $file_array[$i] . ';error: -  required fields are missing';
                        file_put_contents('error.log', $file_array[$i] ."\n", FILE_APPEND);
                       
                    }
                }
                if(!$error){
                    $json['success'] = 'File upload successful';
                }else{
                    $json['error'] = 'Uploading contains errors - check error.log!';
                    $json['error_description'] = $error;
                }
            }
        }

    } 

   
    header('Content-type: application/json');
    echo json_encode($json);
    
    
?>