<?php
/**
 * Created by PhpStorm.
 * User: processdrive
 * Date: 6/8/18
 * Time: 2:56 PM
 */

namespace onedrive\models;


class onedrive_personal extends onedrive_abstract
{
    function __construct()
    {
        parent::__construct();

        $this->baseurl_onedrive_auth = "https://login.live.com/";
        $this->baseurl_onedrive_action = "https://api.onedrive.com/v1.0/drive/";
        $this->key = "@content.downloadUrl";
        $this->share_key = "/action.createLink";
    }

    public function get_code_url()
    {
        return $this->baseurl_onedrive_auth."oauth20_authorize.srf?".$this->onedrive_client_id."&response_type=code&".$this->onedrive_redirect_url."&state=personal&scope=onedrive.readwrite%20offline_access%20Files.ReadWrite.AppFolder%20User.ReadWrite";
    }

    public function get_access_token_from_code($code)
    {
        $this->url = $this->baseurl_onedrive_auth."oauth20_token.srf?";
        return parent::get_access_token_from_code($code);
    }

    public function get_access_token_from_refresh_token($refresh_token)
    {
        $this->url = $this->baseurl_onedrive_auth."oauth20_token.srf?";
        return parent::get_access_token_from_refresh_token($refresh_token);
    }
}