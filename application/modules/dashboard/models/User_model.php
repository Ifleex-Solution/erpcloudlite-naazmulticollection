<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 #------------------------------------    
    # Author: Bdtask Ltd
    # Author link: https://www.bdtask.com/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------    

class User_model extends CI_Model {
 
	public function create($data = array())
	{
		$user_id = $this->generator(6);
		 $users = array(
		 	'user_id'    => $user_id,
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'logo'       => $data['image'],
            'status'     => $data['status'],
        );
        $this->db->insert('users', $users);
        $user_login = array(
            'user_id'   => $user_id,
            'username'  => $data['email'],
            'password'  => $data['password'],
            'user_type' => $data['user_type'],
            'status'    => $data['status'],
			'screen'    => $data['screen'],
			'startdate' => $data['startdate'],
            'enddate'   => $data['enddate'],
			'temporary' => $data['temporary'],
        );
        $this->db->insert('user_login', $user_login);
        $inserted_id = $this->db->insert_id();
        if (!empty($data['plain_password'])) {
            $key = Config::$encryption_key;
            $this->db->query(
                "UPDATE user_login SET encrypted_password = AES_ENCRYPT(?, '$key') WHERE id = ?",
                [$data['plain_password'], $inserted_id]
            );
        }
        return true;
	}

	public function read()
	{
		$this->db->select("
				a.*,
				CONCAT_WS(' ', a.first_name, a.last_name) AS fullname,b.*,b.status as status,b.username as email
			")
			->from('users a')
			->join('user_login b','b.user_id = a.user_id')
			->order_by('a.user_id', 'desc')
			->group_by('a.user_id');

		if ($this->session->userdata('user_type') != 1) {
			$this->db->where_not_in('b.user_type', 1);
		}

		$this->db->where("b.user_type NOT IN (SELECT company_id FROM company_information WHERE status = 0)", null, false);

		return $this->db->get()->result();
	}

	public function company_list($id=null)
	{
		$encryption_key = Config::$encryption_key;

		$this->db->select("
         company_id,
         AES_DECRYPT(company_name, '{$encryption_key}') AS company_name
     ")
			->from('company_information');
			if($id==1){
				$this->db->where_not_in('company_id', 2);
			}else{
				$this->db->where_not_in('company_id', 1);
			}


		$this->db->where('status', 1);
		$query = $this->db->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return false;
	}

	public function single($id = null)
	{
		$key = Config::$encryption_key;
		return $this->db->select("
				a.*,a.logo as image,b.*,b.status as status,b.username as email,b.user_type,b.screen,
				b.startdate,b.enddate,b.temporary, AES_DECRYPT(encrypted_password, '{$key}') AS encrypted_password
			")
			->from('users a')
			->join('user_login b','b.user_id = a.user_id')
			->order_by('a.user_id', 'desc')
			->where('a.user_id', $id)
			->get()
			->row();
	}

	public function update($data = array())
	{


		$userdata = array(
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'logo'       => $data['image'],
            'status'     => $data['status']
        );
        $this->db->where('user_id', $data['user_id']);
        $this->db->update('users', $userdata);
        $user_login = array(
            'username'  => $data['email'],
            'password'  => $data['password'],
            'user_type' => $data['user_type'],
            'status'    => $data['status'],
			'screen'    => $data['screen'],
			'startdate' => $data['startdate'],
            'enddate'   => $data['enddate'],
			'temporary' => $data['temporary'],
        );
        $this->db->where('user_id', $data['user_id']);
        $this->db->update('user_login', $user_login);
        if (!empty($data['plain_password'])) {
            $key = Config::$encryption_key;
            $this->db->query(
                "UPDATE user_login SET encrypted_password = AES_ENCRYPT(?, '$key') WHERE user_id = ?",
                [$data['plain_password'], $data['user_id']]
            );
        }
        return true;
	}

	public function delete($id = null)
	{
		 $this->db->where('user_id', $id)
			->delete("users");
		$this->db->where('user_id', $id)
			->delete("user_login");	
		if ($this->db->affected_rows()) {
			return true;
		} else {
			return false;
		}
	}



 public function generator($lenth) {
        $number = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");

        for ($i = 0; $i < $lenth; $i++) {
            $rand_value = rand(0, 9);
            $rand_number = $number["$rand_value"];

            if (empty($con)) {
                $con = $rand_number;
            } else {
                $con = "$con" . "$rand_number";
            }
        }
        return $con;
    }


}
