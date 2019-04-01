<?php
class YTOExpress{
    // const BASE_URL = "http://localhost:9999/api/yto";
    const BASE_URL = "www.kdt188.com/index.php/api/yto";

    function __construct(){
        //TODO:
    }

    public function sendYTORequest($reqData){
        return $this->sendYTO($reqData);
    }

    private function sendYTO($reqData){

        // prepare request
        date_default_timezone_set("Asia/Shanghai");

        // build request string
        $fields_string = "";
        foreach ($reqData as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');

        $con = curl_init();
        curl_setopt($con, CURLOPT_URL, self::BASE_URL);
        curl_setopt($con, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($con, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($con, CURLOPT_HEADER, 0);
        curl_setopt($con, CURLOPT_POST, 1);
        curl_setopt($con, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($con, CURLOPT_POSTFIELDS, $fields_string);
        // return $fields_string;


        $result = curl_exec($con);
        // file_put_contents('filename', $result);
        curl_close($con);

        $result_object = json_decode($result);

        if (!empty($result_object)) {
            return $result_object;
        }else{
            error_log('YTO send error :' . $result);
            return false;
        }

    }
}

?>