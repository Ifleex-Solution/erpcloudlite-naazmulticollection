<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/*
*
* ---------------how to use-----------------
* ------------------------------------------
* Developed by <sourav.diubd@gmail.com>
*
* $autoload['helper'] =  array('lang');

* display a language
* echo display('helloworld'); 

* display language list
* $lang = languageList(); 
* ------------------------------------------
*
*/


if (!function_exists('display')) {

    function display($text = null)
    {
        $ci =& get_instance();
        $ci->load->database();
        $table  = 'language';
        $phrase = 'phrase';
        $setting_table = 'web_setting';
        $default_lang  = 'english';

        //set language  
        $data = $ci->db->get($setting_table)->row();
        if (!empty($data->language)) {
            $language = $data->language; 
        } else {
            $language = $default_lang; 
        } 
 
        if (!empty($text)) {

            if ($ci->db->table_exists($table)) { 

                if ($ci->db->field_exists($phrase, $table)) { 

                    if ($ci->db->field_exists($language, $table)) {

                        $row = $ci->db->select($language)
                              ->from($table)
                              ->where($phrase, $text)
                              ->get()
                              ->row(); 

                        if (!empty($row->$language)) {
                            return $row->$language;
                        } else {
                            return false;
                        }

                    } else {
                        return false;
                    }

                } else {
                    return false;
                }

            } else {
                return false;
            }            
        } else {
            return false;
        }  

    }
 
}

function dd($array){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
    exit;
}

if (!function_exists('addActivityLog')) {

    function addActivityLog($type, $action_name, $id, $table, $status=0, $data = null){

        $ci =& get_instance();
        $ci->load->database();

        $postData = (empty($_POST))?array():$_POST;
        $actionData = array(
          'user_id'   => $ci->session->userdata('id'),
          'type'      => $type, 
          'action'    => $action_name, 
          'action_id' => $id, 
          'table_name'=>$table,
          'slug'      => uri_string(),
          'form_data' => ($data==null)?json_encode($postData):json_encode($data),
          'status'    => $status
        );

        $ci->db->insert('activity_logs',$actionData);

    }

}

// web_setting.logo/invoice_logo/favicon now hold either an external image
// URL (pasted by the admin) or, for installs that predate that change, a
// local relative path — this returns something directly usable in src=
// either way, instead of every call site blindly prefixing base_url().
if (!function_exists('logo_url')) {
    function logo_url($value, $default = '')
    {
        if (empty($value)) {
            return $default !== '' ? base_url($default) : '';
        }
        if (preg_match('#^(https?:)?//#i', $value)) {
            return $value;
        }
        return base_url($value);
    }
}

function financial_year(){
    $ci =& get_instance();
    $ci->load->database();
    $ci->db->select("COUNT(id) as num,id");
    $ci->db->from("financial_year");
    $ci->db->where("startDate<=",date("Y-m-d"));
    $ci->db->where("endDate>=",date("Y-m-d"));
    $ci->db->where("status",1);
    $query = $ci->db->get();
    $row = $query->row();
    return $row->id;
}  

