<?php
/**
 * Created by PhpStorm.
 * User: processdrive
 * Date: 6/8/18
 * Time: 3:01 PM
 */

namespace onedrive\models;

/**
 * Class onedrive_business
 * @package onedrive\models
 */
class onedrive_business extends onedrive_abstract
{
    /**
     * onedrive_business constructor.
     */
    function __construct()
    {
        parent::__construct();

        $this->baseurl_onedrive_action = "https://graph.microsoft.com/v1.0/me/drive/";
        $this->baseurl_onedrive_auth = "https://login.microsoftonline.com/common/oauth2/";
        $this->key = "@microsoft.graph.downloadUrl";
        $this->share_key = "/createLink";
    }

    /**
     * @return string
     */
    public function get_code_url()
    {
        return $this->baseurl_onedrive_auth."authorize?".$this->onedrive_client_id."&response_type=code&".$this->onedrive_redirect_url;
    }

    /**
     * @param $code
     * @return onedrive_abstract|token
     */
    public function get_access_token_from_code($code)
    {
        $this->url = $this->baseurl_onedrive_auth."token";
        $this->parameter = "resource=https%3A%2F%2Fgraph.microsoft.com%2F&";

        return parent::get_access_token_from_code($code);
    }

    /**
     * @param $refresh_token
     * @return onedrive_abstract|token
     */
    public function get_access_token_from_refresh_token($refresh_token)
    {
        $this->url = $this->baseurl_onedrive_auth."token";
        $this->parameter = "resource=https%3A%2F%2Fgraph.microsoft.com&";

        return parent::get_access_token_from_refresh_token($refresh_token);
    }
}