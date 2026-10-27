<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 #------------------------------------    
    # Author: Bdtask Ltd
    # Author link: https://www.bdtask.com/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------    
require_once("./vendor/Config.php");
class User extends MX_Controller {
     
     public function __construct()
     {
         parent::__construct();
         $this->load->model(array(
             'user_model'  
         ));
         
        // if (! $this->session->userdata('isAdmin'))
        //     redirect('login');
     }
 
    
    public function bdtask_userlist() {
    if (!$this->permission1->method('manage_user', 'read')->access()) {
        $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
        redirect($previous_url);
    }
    
    $data['title']    = display('user_list');
    $data['user']     = $this->user_model->read();
    
    // Load Config class for encryption key
    require_once APPPATH . '../vendor/Config.php';
    $encryption_key = Config::$encryption_key;
    
    foreach ($data['user'] as &$user) {
        // Get roles
        $user->roles = $this->db->select('sec_role.type')
            ->from('sec_userrole')
            ->join('sec_role', 'sec_role.id = sec_userrole.roleid')
            ->where('sec_userrole.user_id', $user->user_id)
            ->get()
            ->result();
        
        // Get branches with proper decryption
        $branch_query = "
            SELECT 
                sec_branch.default,
                CAST(AES_DECRYPT(branch.name, ?) AS CHAR(255)) as decrypted_name,
                branch.code
            FROM sec_branch
            JOIN branch ON branch.id = sec_branch.branchid
            WHERE sec_branch.userid = ?
        ";
        
        $branch_data = $this->db->query($branch_query, array($encryption_key, $user->user_id))->result();
        
        $user->branches = array();
        foreach ($branch_data as $branch) {
            $branchObj = new stdClass();
            $branchObj->default = $branch->default;
            $branchObj->name = !empty($branch->decrypted_name) ? $branch->decrypted_name : $branch->code;
            
            $user->branches[] = $branchObj;
        }
        
        // Get stores
        $user->stores = $this->db->select('store.name, sec_store.default')
            ->from('sec_store')
            ->join('store', 'store.id = sec_store.storeid')
            ->where('sec_store.userid', $user->user_id)
            ->get()
            ->result();
    }
    
    $data['module']   = "dashboard";  
    $data['page']     = "user/list";   
    echo Modules::run('template/layout', $data); 
}



    public function email_check($email, $id)
    { 
        $emailExists = $this->db->select('email')
                            ->where('email',$email) 
                            ->where_not_in('id',$id) 
                            ->get('user')
                            ->num_rows();

        if ($emailExists > 0) {
            $this->form_validation->set_message('email_check', 'The {field} is already registered.');
            return false;
        } else {
            return true;
        }
    } 

        public function bdtask_userform($id = null)
    { 

        if (!$this->permission1->method('manage_user', 'create')->access()) {
            $previous_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url();
            redirect($previous_url);
        }
        $data['title']    = display('add_user');
        /*-----------------------------------*/
       
         
        /*-----------------------------------*/
        $data['user'] = (object)$userLevelData = array(
            'user_id'       => $id,
            'id'            => $id,
            'first_name'    => $this->input->post('firstname'),
            'last_name'     => $this->input->post('lastname'),
            'email'         => $this->input->post('email'),
            'password'      => (!empty($this->input->post('password'))?md5('gef'.$this->input->post('password')):$this->input->post('oldpassword')),
            'plain_password'=> $this->input->post('password'),
            'image'         => (!empty($image)?$image:$this->input->post('old_image')),
            'status'        => $this->input->post('status'),
            'screen'        => $this->input->post('screen'),
            'user_type'     => $this->input->post('user_type'),
            'startdate'     => $this->input->post('startdate'),
            'enddate'       => $this->input->post('enddate'),
            'temporary'     => $this->input->post('temporary'),
        );

        /*-----------------------------------*/
        if ($this->form_validation->run()) {
           


        } else {
            $data['module'] = "dashboard";  
            $data['page']   = "user/form"; 
            if(!empty($id)){
            $data['title']  = display('edit_user');
            $data['user']   = $this->user_model->single($id);

            }
            $data['company_list']   = $this->user_model->company_list($id);


            
            echo Modules::run('template/layout', $data);
        }
    }

    public function  save_user($id = null){
        $base_url = base_url();
         $image = $this->fileupload->do_upload(
            './assets/img/user/', 
            'image'

        );

        $data['user'] = (object)$userLevelData = array(
            'user_id'       => $id,
            'id'            => $id,
            'first_name'    => $this->input->post('firstname'),
            'last_name'     => $this->input->post('lastname'),
            'email'         => $this->input->post('email'),
            'password'      => (!empty($this->input->post('password'))?md5('gef'.$this->input->post('password')):$this->input->post('oldpassword')),
            'plain_password'=> $this->input->post('password'),
            'image'         => (!empty($image)?$image:$this->input->post('old_image')),
            'status'        => $this->input->post('status'),
            'screen'        => $this->input->post('screen'),
            'user_type'     => $this->input->post('user_type'),
            'startdate'     => $this->input->post('startdate'),
            'enddate'       => $this->input->post('enddate'),
            'temporary'     => $this->input->post('temporary'),
        );
        if (empty($id)) {
            if ($this->user_model->create($userLevelData)) {
                // $this->session->set_flashdata('message', display('save_successfully'));
                echo '
            <script type="text/javascript">
            alert("User Details Saved successfully");
            window.location.href = "' . $base_url . 'user_list";
           </script>';
            } else {
                $this->session->set_flashdata('exception', display('please_try_again'));
            }
        } else {
            if ($this->user_model->update($userLevelData)) {
                // $this->session->set_flashdata('message', display('update_successfully'));
                echo '
            <script type="text/javascript">
            alert("User Details Updated successfully");
            window.location.href = "' . $base_url . 'user_list";
           </script>';
            } else {
                $this->session->set_flashdata('exception', display('please_try_again'));
            }

            // redirect("add_user/$id");
        }
    }



     public function bdtask_deleteuser($id = null) {
        if ($this->user_model->delete($id)) {
      $this->session->set_flashdata('message', display('delete_successfully'));
        } else {
       $this->session->set_flashdata('exception', display('please_try_again'));
        }

        redirect("user_list");
    }

}