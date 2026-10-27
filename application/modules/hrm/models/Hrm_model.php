<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hrm_model extends CI_Model {

   public function create_designation($data = [])
    {    
        return $this->db->insert('designation',$data);
    }

    public function update_designation($data = [])
    {
        return $this->db->where('id',$data['id'])
            ->update('designation',$data); 
    } 


     public function single_designation_data($id){
        return $this->db->select('*')
            ->from('designation')
            ->where('id', $id)
            ->get()
            ->row();
    }

    public function delete_designation($id){
        $this->db->where('id', $id)
            ->delete("designation");
        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    }
    
public function check_designation_in_use($designation_id)
{
    $this->db->select('COUNT(*) as count');
    $this->db->from('employee_history');
    $this->db->where('designation', $designation_id);
    $result = $this->db->get()->row();
    
    return ($result->count > 0);
}


     public function designation_list(){
        return $this->db->select('*')
                        ->from('designation')
                        ->get()
                        ->result_array();
     }

     public function getDesignationList($postData)
{
    $response = array();

    ## Read value
    $draw = $postData['draw'];
    $start = $postData['start'];
    $rowperpage = $postData['length']; // Rows display per page
    $columnIndex = $postData['order'][0]['column']; // Column index
    $columnName = $postData['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
    $searchValue = $postData['search']['value']; // Search value

    ## Search 
    $searchQuery = "";
    if($searchValue != ''){
        $searchQuery = " (designation like '%".$searchValue."%' OR details like '%".$searchValue."%')";
    }

    ## Total number of records without filtering
    $this->db->select('count(*) as allcount');
    $records = $this->db->get('designation')->row();
    $totalRecords = $records->allcount;

    ## Total number of record with filtering
    $this->db->select('count(*) as allcount');
    if($searchQuery != '')
        $this->db->where($searchQuery);
    $records = $this->db->get('designation')->row();
    $totalRecordwithFilter = $records->allcount;

    ## Fetch records
    $this->db->select('*');
    if($searchQuery != '')
        $this->db->where($searchQuery);
    $this->db->order_by($columnName, $columnSortOrder);
    $this->db->limit($rowperpage, $start);
    $records = $this->db->get('designation')->result();

    $data = array();
    $i = $start + 1;

    
    foreach($records as $record ){
        $status = isset($record->status) ? $record->status : 1;
        if($status == 1) {
            $status_label = '<span class="label label-success">Active</span>';
        } else {
            $status_label = '<span class="label label-danger">Inactive</span>';
        }

        $button = '';
        if($this->permission1->method('manage_designation','update')->access()){
            $button .= '<a href="'.base_url('designation_form/'.$record->id).'" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="left" title="Update"><i class="fa fa-pencil" aria-hidden="true"></i></a> ';
        }
        if($this->permission1->method('manage_designation','delete')->access()){
            $button .= '<a href="'.base_url('hrm/hrm/bdtask_deletedesignation/'.$record->id).'" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure?\')" data-toggle="tooltip" data-placement="right" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
        }

        $data[] = array( 
            "sl"            => $i,
            "designation"   => html_escape($record->designation),
            "details"       => html_escape($record->details),
            "status_label"  => $status_label,
            "button"        => $button,
        ); 
        $i++;
    }

    ## Response
    $response = array(
        "draw"                 => intval($draw),
        "iTotalRecords"        => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData"               => $data
    );

    return $response; 
}

    

     public function check_employee_in_use($employee_id)
{
    // Check in sale table
    $this->db->select('COUNT(*) as count');
    $this->db->from('sale');
    $this->db->where('employee_id', $employee_id);
    $sale_result = $this->db->get()->row();
    
    if ($sale_result->count > 0) {
        return true;
    }

     // Check in service table
    $this->db->select('COUNT(*) as count');
    $this->db->from('service');
    $this->db->where('employee_id', $employee_id);
    $service_result = $this->db->get()->row();
    
    if ($service_result->count > 0) {
        return true;
    }

    // Check in service_order table
    $this->db->select('COUNT(*) as count');
    $this->db->from('service_order');
    $this->db->where('employee_id', $employee_id);
    $service_order_result = $this->db->get()->row();
    
    if ($service_order_result->count > 0) {
        return true;
    }
    
    // Check in quotation table
    $this->db->select('COUNT(*) as count');
    $this->db->from('sales_order');
    $this->db->where('employee_id', $employee_id);
    $quotation_result = $this->db->get()->row();
    
    if ($quotation_result->count > 0) {
        return true;
    }
    
    return false;
}

 /*employee part*/

     public function single_employee_data($id){
        return $this->db->select('*')
            ->from('employee_history')
            ->where('id', $id)
            ->get()
            ->row();
     }

     public function create_employee($data = []){

        
          $this->db->insert('employee_history',$data);

    //       $id =$this->db->insert_id();
    //   $coa = $this->headcode();
     
    //        if($coa->HeadCode!=NULL){
    //             $headcode=$coa->HeadCode+1;
    //        }else{
    //             $headcode="2116000001";
    //         }
    // $c_acc=$id.'-'.$data['first_name'].''.$data['last_name'];
    // $createby=$this->session->userdata('id');
    // $createdate=date('Y-m-d H:i:s');
    // $employee_coa = [
    //          'HeadCode'         => $headcode,
    //          'HeadName'         => $c_acc,
    //          'PHeadName'        => 'Employee Ledger',
    //          'HeadLevel'        => '4',
    //          'IsActive'         => '1',
    //          'IsTransaction'    => '1',
    //          'IsGL'             => '0',
    //          'HeadType'         => 'L',
    //          'IsBudget'         => '0',
    //          'IsDepreciation'   => '0',
    //          'DepreciationRate' => '0',
    //          'CreateBy'         => $createby,
    //          'CreateDate'       => $createdate,
    //     ];
    //     $this->db->insert('acc_coa',$employee_coa);
        return true;
     }

        public function headcode(){
        $query=$this->db->query("SELECT MAX(HeadCode) as HeadCode FROM acc_coa WHERE HeadLevel='4' And HeadCode LIKE '2116000%'");
        return $query->row();

    }

      public function designation_dropdown(){
    $this->db->select('*');
    $this->db->from('designation');
    $this->db->where('status', 1); // Only show active designations
    $query=$this->db->get();
    $data=$query->result();
    $list[''] = display('select_option');
    if(!empty($data)){
        foreach ($data as  $value) {
            $list[$value->id]=$value->designation;
        }
    }
    return $list;
}


    public function update_employee($data = []){
         return $this->db->where('id',$data['id'])
            ->update('employee_history',$data); 
            
    }

       // Employee list
      public function employee_list(){
    $this->db->select('a.*, b.designation');
    $this->db->from('employee_history a');
    $this->db->join('designation b','a.designation = b.id', 'left');
    $this->db->where_not_in('a.id', 1);

    $this->db->order_by('a.id', 'DESC');
    $query = $this->db->get();

    if ($query->num_rows() > 0) {
        return $query->result_array();
    }
    return false;
}

      public function employee_details($id){
    $this->db->select('a.*, b.designation');
    $this->db->from('employee_history a');
    $this->db->join('designation b','a.designation = b.id', 'left');
    $this->db->where('a.id', $id);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->result_array();
    }
    return false;
}

     public function delete_employee($id){
        $this->db->where('id', $id)
            ->delete("employee_history");
        if ($this->db->affected_rows()) {
            return true;
        } else {
            return false;
        }
    }

    // Add this method after delete_employee() for active employee dropdown
public function active_employee_dropdown(){
    $this->db->select('id, first_name, last_name');
    $this->db->from('employee_history');
    $this->db->where('status', 1); // Only active employees
    $this->db->order_by('first_name', 'ASC');
    $query = $this->db->get();
    $data = $query->result();
    
    $list[''] = display('select_option');
    if(!empty($data)){
        foreach ($data as $value) {
            $list[$value->id] = $value->first_name . ' ' . $value->last_name;
        }
    }
    return $list;
}

}