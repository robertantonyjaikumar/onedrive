<?php
/**
 * Created by PhpStorm.
 * User: processdrive
 * Date: 6/8/18
 * Time: 2:52 PM
 */

namespace onedrive\models;

/**
 * Interface onedrive
 * @package onedrive\models
 */
interface onedrive {

    /**
     * @return string [URL]
     */
    function get_code_url();

    /**
     * @param $code [string]
     * @return token [object]
     */
    function get_access_token_from_code($code);

    /**
     * @param $code [string]
     * @return token [object]
     */
    function get_access_token_from_refresh_token($refresh_token);

    /**
     * @param null $id
     * @return files [object]
     */
    function get_items($parent_folder_id = null);

    /**
     * @param $parent_folder_id
     * @param $folder_name
     * @return mixed
     */
    function create_folder($folder_name, $parent_folder_id = null);

    /**
     * @param $file_id
     * @return filecontent
     */
    function download_file($file_id);

    /**
     * @param $file_path
     * @param null $parent_id
     * @param null $file_name
     * @return mixed
     */
    function create_file($file_path, $parent_id = null, $file_name = null);

    /**
     * @param $item_id
     * @return mixed
     */
    function delete_item_from_drive($item_id);

    /**
     * @param $item_id
     * @return string [url]
     */
    function get_share_link_of_an_item($item_id);
}