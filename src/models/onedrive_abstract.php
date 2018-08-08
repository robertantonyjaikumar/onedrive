<?php
/**
 * Created by PhpStorm.
 * User: processdrive
 * Date: 6/8/18
 * Time: 3:17 PM
 */

namespace onedrive\models;


abstract class onedrive_abstract implements onedrive
{
    protected $baseurl_onedrive_auth;
    protected $baseurl_onedrive_action;
    protected $key;
    protected $share_key;
    protected $code;
    public $access_token;
    public $refresh_token;
    protected $onedrive_rest_client;
    protected $onedrive_client_id = "client_id=";
    protected $onedrive_secret_id = "client_secret=";
    protected $onedrive_redirect_url = "redirect_uri=";

    protected $url;
    protected $parameter;
    protected $method;
    protected $header;

    /**
     * onedrive_abstract constructor.
     * @param $onedrive_client_id
     * @param $onedrive_secret_id
     * @param $onedrive_redirect_url
     */
    function __construct()
    {
        $onedrive = require __DIR__."/../config/onedrive.php";

        $this->onedrive_client_id .= $onedrive["onedrive_client_id"];
        $this->onedrive_secret_id .= urlencode($onedrive["onedrive_secret_id"]);
        $this->onedrive_redirect_url .= $onedrive["onedrive_redirect_url"];
        $this->onedrive_rest_client = new onedrive_rest_client();
    }

    /**
     * @param $code [string]
     * @return onedrive_abstract|token
     */
    public function get_access_token_from_code($code)
    {
        $this->parameter .= $this->onedrive_client_id."&".$this->onedrive_redirect_url."&".$this->onedrive_secret_id."&code=".$code."&grant_type=authorization_code";

        return $this->token_functionality();
    }

    /**
     * @param $refresh_token [string]
     * @return onedrive_abstract|token
     */
    public function get_access_token_from_refresh_token($refresh_token)
    {
        $this->parameter .= $this->onedrive_client_id."&".$this->onedrive_redirect_url."&".$this->onedrive_secret_id."&refresh_token=".$refresh_token."&grant_type=refresh_token";

        return $this->token_functionality();
    }

    /**
     * @param null $parent_folder_id
     * @return mixed|files
     */
    public function get_items($parent_folder_id = null)
    {
        $this->url = $this->getUrl($parent_folder_id);
        $this->header = ["authorization : bearer ".$this->access_token];
        $this->method = "GET";

        return $this->onedrive_rest_client->consume($this->construct_service_data());
    }

    /**
     * @param $folder_name
     * @param null $parent_folder_id
     * @return json [response]
     */
    public function create_folder($folder_name, $parent_folder_id = null)
    {
        $this->url = $this->getUrl($parent_folder_id);
        $this->header = [
                            "authorization : bearer ".$this->access_token,
                            "content-type: application/json"
                        ];
        $this->parameter = "\r\n{\r\n  \"name\": \"".$folder_name."\",\r\n  \"folder\": { }\r\n}";
        $this->method = "POST";

        return $this->onedrive_rest_client->consume($this->construct_service_data());
    }

    /**
     * @param $file_id
     * @return filecontent|void
     */
    public function download_file($file_id)
    {
        $this->url = $this->baseurl_onedrive_action."items/".$file_id."/content";
        $this->header = ["Authorization : Bearer ".$this->access_token];
        $this->method = "GET";
        $this->parameter = "";

        return $this->onedrive_rest_client->consume($this->construct_service_data(), false);
    }

    /**
     * @param $file_path
     * @param null $parent_id
     * @param null $file_name
     * @return mixed|null
     */
    public function create_file($file_path, $parent_id = null, $file_name = null)
    {
        try {
            $file_name = $file_name ? $file_name : basename($file_path);
            $file =  pathinfo($file_name);
            $file_name = urlencode($file['filename']).".".$file['extension'];
            if (!is_file($file_path)) {
                die("File Not found");
            }
            $chunkSizeBytes = 50 * 1024 * 1024; //50MB file
            $file_size = filesize($file_path);
            //no of parts to split
            $parts = $file_size / $chunkSizeBytes;
            $handle = fopen($file_path,"rb");

            if ($handle === FALSE) die("unable_to_upload_file");

            if ($file_size <= $chunkSizeBytes || strpos(get_class($this), 'onedrive_personal') !== false) {
                return $this->upload_load_file_less_than_four_mb_or_onedrive_personal ($parts, $handle, $chunkSizeBytes, $parent_id, $file_name);
            } else {
                return $this->upload_file_is_greator_than_four_mb($parts, $handle, $chunkSizeBytes, $parent_id, $file_name, $file_size);
            }
        } catch (Exception $ex) {
            return null;
        }
    }

    public function delete_item_from_drive($item_id)
    {
        $this->url = $this->baseurl_onedrive_action."items/".$item_id;
        $this->method = "DELETE";
        $this->header = ["authorization : bearer ".$this->access_token];

        return $this->onedrive_rest_client->consume($this->construct_service_data(), false);
    }

    /**
     *  Helper function
     * @return array
     */
    protected  function construct_service_data()
    {
        return [
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30000,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_POSTFIELDS => $this->parameter,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $this->header
        ];
    }

    /**
     * @param bool $parent_folder_id
     * @return string [url]
     */
    protected function getUrl($parent_folder_id = false)
    {
        return (@$parent_folder_id) ? $this->baseurl_onedrive_action."items/".$parent_folder_id."/children" : $this->baseurl_onedrive_action."root/children";
    }

    /**
     * Creating Token
     * @return $this
     */
    protected function token_functionality ()
    {
        $this->method = "POST";
        $this->header = ["content-type" => "application/x-www-form-urlencoded"];
        $response = $this->onedrive_rest_client->consume($this->construct_service_data());
        $this->refresh_token = $response->refresh_token;
        $this->access_token = $response->access_token;

        return $this;
    }

    protected function upload_load_file_less_than_four_mb_or_onedrive_personal($parts, $handle, $chunkSizeBytes, $parent_id, $file_name) {
        for ($i=0;$i < $parts;$i++)
        {
            $file_part = fread($handle, $chunkSizeBytes);
            $newArray = $file_part;
        }

        fclose($handle);

        $this->header = [
            'authorization: bearer '.$this->access_token,
            "content-type: application/octet-stream"
        ];

        $this->url = $this->getUrl($parent_id)."/".$file_name."/content";
        $this->method = "PUT";
        $this->parameter = $newArray;

        return $this->onedrive_rest_client->consume($this->construct_service_data());
    }

    protected function upload_file_is_greator_than_four_mb($parts, $handle, $chunkSizeBytes, $parent_id, $file_name, $file_size) {
        $contentLen = $chunkSizeBytes -1;
        $this->url = $this->baseurl_onedrive_action ."/items/".$parent_id.":/".$file_name.":/createUploadSession";
        $this->method = "POST";
        $this->parameter = "{\n  \"item\": {\n    \"@microsoft.graph.conflictBehavior\": \"rename\"\n  }\n}";
        $this->header = [
            'authorization: bearer '.$this->access_token,
            "content-type: application/json"
        ];

        $response = $this->onedrive_rest_client->consume($this->construct_service_data());

        $upload_url = $response->uploadUrl;

        $i = 0;
        $temp = 0;
        while( $i < $parts)
        {
            if ($i == (int)$parts)
            {
                $finalChunkByte = $file_size - ($chunkSizeBytes * (int)$parts);
                $file_part = fread($handle, $finalChunkByte);
                $newArray[$i] = $file_part;
                $contentrange = "bytes " .($contentLen - $chunkSizeBytes) ."-".($file_size -1) ."/".($file_size) ;

                return $this->file_put_helper($upload_url, $finalChunkByte, $contentrange, $newArray[$i]);
            }
            else
            {
                $file_part = fread($handle, $chunkSizeBytes);
                $newArray[$i] = $file_part;
                if ($i == 0) {
                    $contentrange = "bytes 0-".($contentLen)."/".($file_size);
                    $temp = $contentLen = $chunkSizeBytes;
                } else {
                    $contentrange = "bytes " .($contentLen - $chunkSizeBytes) ."-".($contentLen-1)."/".($file_size);
                }
                $this->file_put_helper($upload_url, $temp, $contentrange, $newArray[$i]);
            }
            $i = $i + 1;
            $contentLen = $contentLen + $chunkSizeBytes;
        }
        fclose($handle);
    }

    protected function file_put_helper ($upload_url, $finalChunkByte, $contentrange, $data) {
        $this->method = "PUT";
        $this->url = $upload_url;
        $this->header = [
            'authorization: bearer '.$this->access_token,
            "Content-Length:".$finalChunkByte,
            "Content-Range:".$contentrange
        ];
        $this->parameter = $data;

        return $this->onedrive_rest_client->consume($this->construct_service_data());
    }
}