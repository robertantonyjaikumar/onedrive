<?php
/**
 * Created by PhpStorm.
 * User: processdrive
 * Date: 6/8/18
 * Time: 3:55 PM
 */

namespace onedrive\models;


class onedrive_rest_client
{
    public function consume($paremeter, $json_decode = true)
    {
        $curl = curl_init();
        curl_setopt_array($curl, $paremeter);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($json_decode)
        $response = json_decode($response);
        else {
            var_dump($response);die;
        }

        if ($err) {
            echo "cURL Error #:" . $err;die;
        } else {
            return $response;
        }
    }
}