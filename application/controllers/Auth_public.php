<?php
/**
* The Expression Database.
*
*  Auth_public Class 
*
* From codeigniter-auth
* [Ben Edmunds](http://benedmunds.com)
*
* This version drops any backwards compatibility and makes things even more
* awesome then you could expect.
*
* Documentation is located at http://benedmunds.com/ion_auth/
*
* Authentification class. allow single user to manage their profile
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@version 1.0
*@package        ExpressWeb
*@subpackage     Controller
*@category       Libraries
*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_public extends MY_Controller 
{

    public function __construct()
    {
            parent::__construct();
            $this->output->enable_profiler(false);
            $this->load->library('expression_lib');
            $this->load->library('form_validation');
            $this->load->helper('language');
            $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
            $this->lang->load('auth');
            if (!$this->ion_auth->logged_in())
            {
                // redirect them to the login page
                redirect("auth/login", 'refresh');
            }
    }

    // redirect if needed, otherwise display the user list
    public function index()
    {
        if (!$this->ion_auth->logged_in())
        {
            // redirect them to the login page
            redirect("auth/login", 'refresh');
        }            
        elseif (!$this->ion_auth->is_admin()) // remove this elseif if you want to enable this for non-admins
        {
            // redirect them to the home page because they must be an administrator to view this
            return show_error('You must be an administrator to view this page.');
        } 
        else
        {
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            //list the users
            $this->data['users'] = $this->ion_auth->users()->result();
            $this->data['title'] = "$this->header_name: Manage account";
            foreach ($this->data['users'] as $k => $user)
            {
                $this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
            }

            $this->_render_page("auth/index", $this->data);
        }
    }

    // create a new user
    public function update_account()
    {
        
        $data = array(
          'title'=> "$this->header_name: User account",
          'contents' => 'auth/public/dashboard_view',
          'message' => '',
          'footer_title' => $this->footer_title
          );
        $this->load->view("templates/template", $data);
    }
    
    public function edit_user()
    {
        $id= $this->session->user_id;
        $Msg= urldecode($this->uri->segment(3));
        if($Msg == "fl") $this->data['first_login'] = 1;
        else $this->data['first_login'] = 0;
        $this->data['title'] = "$this->header_name: Edit User";

        if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id)))
        {
                redirect("auth", 'refresh');
        }

        $user = $this->ion_auth->user($id)->row();
        $groups=$this->ion_auth->groups()->result_array();
        $currentGroups = $this->ion_auth->get_users_groups($id)->result();

        // validate form input
        $this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'trim|required');
        $this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'trim|required');
        $this->form_validation->set_rules('email', $this->lang->line('edit_user_validation_email_label'), 'trim|valid_email|required');
        $this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'trim|required');

        if (isset($_POST) && !empty($_POST))
        {
            // do we have a valid request?
            if ($this->_valid_csrf_nonce() === FALSE || $id != $this->input->post('id'))
            {
                print "error : id $id  post_id: ".$this->input->post('id')." valid_csrf: ".$this->_valid_csrf_nonce()."<br>POST: ".print_r($_POST,1).""; 
                    show_error($this->lang->line('error_csrf'));
            }

            // update the password if it was posted
            if ($this->input->post('password'))
            {
                $this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
                $this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
            }

            if ($this->form_validation->run() === TRUE)
            {
                $data = array(
                        'first_name' => $this->input->post('first_name'),
                        'last_name'  => $this->input->post('last_name'),
			'email'  => $this->input->post('email'),
                        'company'    => $this->input->post('company'),
                        'username'   => $this->input->post('username'),
                );

                // update the password if it was posted
                if ($this->input->post('password'))
                {
                        $data['password'] = $this->input->post('password');
                }
                
                // Only allow updating groups if user is admin
                if ($this->ion_auth->is_admin())
                {
                    //Update the groups user belongs to
                    $groupData = $this->input->post('groups');

                    if (isset($groupData) && !empty($groupData)) 
                    {
                        $this->ion_auth->remove_from_group('', $id);

                        foreach ($groupData as $grp) 
                        {
                                $this->ion_auth->add_to_group($grp, $id);
                        }
                    }
                }

            // check to see if we are updating the user
               if($this->ion_auth->update($user->id, $data))
               {
                    // redirect them back to the admin page if admin, or to the base url if non admin
                    $this->session->set_flashdata('message', $this->ion_auth->messages() );
                    if ($this->ion_auth->is_admin())
                    {
                        redirect("auth", 'refresh');
                    }
                    else
                    {
                        redirect("welcome", 'refresh');
                    }
                }
                else
                {
                    // redirect them back to the admin page if admin, or to the base url if non admin
                    $this->session->set_flashdata('message', $this->ion_auth->errors() );
                    if ($this->ion_auth->is_admin())
                    {
                        redirect("auth", 'refresh');
                    }
                    else
                    {
                        redirect("welcome", 'refresh');
                    }
                }
            }
        } // End if (isset($_POST)

        // display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();
        
        // set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

        // pass the user to the view
        $this->data['user'] = $user;
        $this->data['groups'] = $groups;
        $this->data['currentGroups'] = $currentGroups;

        $this->data['first_name'] = array(
                'name'  => 'first_name',
                'id'    => 'first_name',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('first_name', $user->first_name),
                'required' =>  'required'
        );
        $this->data['last_name'] = array(
                'name'  => 'last_name',
                'id'    => 'last_name',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('last_name', $user->last_name),
                'required' =>  'required'
        );
        $this->data['username'] = array(
                'name'  => 'username',
                'id'    => 'username',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('username', $user->username),
                'required' =>  'required'
        );
        $this->data['email'] = array(
                'name'  => 'email',
                'id'    => 'email',
                'type'  => 'email',
                'value' => $this->form_validation->set_value('email', $user->email),
                'size' => 50,
                'required' =>  'required'
        );
        $this->data['company'] = array(
                'name'  => 'company',
                'id'    => 'company',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('company', $user->company),
                'required' =>  'required'
        );
        $this->data['phone'] = array(
                'name'  => 'phone',
                'id'    => 'phone',
                'type'  => 'text',
                'value' => $this->form_validation->set_value('phone', $user->phone),
        );
        $this->data['password'] = array(
                'name' => 'password',
                'id'   => 'password',
                'type' => 'password'
        );
       
        $this->data['password_confirm'] = array(
                'name' => 'password_confirm',
                'id'   => 'password_confirm',
                'type' => 'password'
        );
         if($this->data['first_login'] ==1) 
         {
                $this->data['password']['required'] =  'required';
                $this->data['password_confirm']['required'] =  'required';
         }
        $this->data['Id'] = $id;
        
        $this->_render_page("auth/public/edit_user", $this->data);
    }
    
     
    #####################################
    /**
    * function get_members_files()
    *   display clustering result.
    *   Show all files for admin
    *   Show files in group user for non admin
    */  
    public function get_members_files()
    {
        // Get user id from session to use in the update function as a primary key.
        
        $user_id = $this->session->user_id;
        $Directory = $this->session->working_path;
        $UserGroups = $this->session->groups;
        $username  = $this->session->username;
        $Admin_name = $this->config->item('admin_name'); ### name of super admin in db
        $userTables=$this->generic->get_table_members($user_id); #### see Generic.php model
        #### Load user folder. 
        if(!$Directory)
        {
           $GetWS=$this->expression_lib->working_space('','Upload'); 
           if(isset($GetWS->Path))
           {
                $pid = $GetWS->pid;
                $this->session->set_userdata('pid',$pid);
           }
        }
        $tables=$tables_tmp=array();
        $option_div =  "";
        $web_path = $this->config->item('web_path');
        $web_pathLen= strlen($web_path);
        $out_network = $this->config->item('network');
        $out_network = substr($out_network,$web_pathLen);
        $out_similarity = $this->config->item('similarity');
        $out_similarity = substr($out_similarity,$web_pathLen);
        ####################################################
        $File_list = "";
        if($userTables->nbr >0)
        {
            $File_list .= "<table class=\"table table-hover table-condensed table-bordered\" >\n";
            $File_list .= "   <thead>\n";
            $File_list .= "      <tr><th>TableName</th><th>version</th><th>MasterGroup</th><th>Threshold</th><th>Network</th><th>Similarity</th><th>Date</th></tr>\n";
            $File_list .= "   </thead>\n";
            $File_list .= "   <tbody>\n";
            ###### for each $userTables search for precomputed Cluster
            ###### result will be displayed on select in main view
            foreach($userTables->result as $row)
            {
                $IdTables = $row['IdTables'];
                $TableName = $row['TableName'];
                $Root = $row['Root'];
                $version = $row['version'];
                $group_name = $row['group_name'];
                ##### similarity data
                
                
                $SimilarityFile =  anchor( $out_similarity."".$TableName."_Similarity",$TableName."_Similarity") ;
                $get_child = $this->generic->get_child($IdTables);
                if($get_child->nbr >0)
                {
                    $tbl_seuil = 
                    $rowspan = 0; 
                    $File_list_tmp = "";
                    foreach($get_child->result as $row)
                    {$NetworkFile = "";
                        $IdTables2 = $row->IdTables;
                        $tableName2 = $row->TableName;
                        
                        $tbl_seuil = trim(preg_replace("/$TableName|_Cluster/","",$tableName2),"_");
                        $dot_seuil = preg_replace("/_/",".",$tbl_seuil);
                        if(file_exists(".".$out_network."Edges".$TableName."_".$tbl_seuil.".json"))
                        {
                            $date = date ("F d Y H:i:s.", filemtime(".".$out_network."Edges".$TableName."_".$tbl_seuil.".json"));
                            $NetworkFile .= anchor( $out_network."Edges".$TableName."_".$tbl_seuil.".json","Edges $TableName $dot_seuil")."<br />\n" ;
                            $NetworkFile .= anchor( $out_network."Nodes".$TableName."_".$tbl_seuil.".json","Nodes $TableName $dot_seuil")."<br />\n" ;
                            if($rowspan==0) $File_list_tmp .=  "<td>$tbl_seuil</td><td>$NetworkFile</td><td>$SimilarityFile</td><td>$date</td></tr>\n";
                            else $File_list_tmp .=  "           <tr><td>$tbl_seuil</td><td>$NetworkFile</td><td>$SimilarityFile</td><td>$date</td></tr>\n";
                            $rowspan++;
                        }
                        else
                        {
                            $File_list_tmp .=  "                <td>$tbl_seuil</td><td colspan=3>oops! not computed files found</td></tr>\n";
                        }
                    }                    
                }
                ###### combine result
                
                $File_list .=  "    <tr>\n";
                if($Root)
                {
                    if(in_array($group_name,$UserGroups) OR $username =="$Admin_name" )
                    {
                        $File_list .=  "            <th class=info rowspan=\"$rowspan\">$TableName</th><td rowspan=\"$rowspan\">$version</td><td rowspan=\"$rowspan\">$group_name</td>\n";
                        $File_list .= $File_list_tmp;
                    }
                }
                else
                {
                        $File_list .=  "            <td rowspan=\"$rowspan\">$TableName</td><td rowspan=\"$rowspan\">$version</td><td rowspan=\"$rowspan\">$group_name</td>\n";
                        $File_list .= $File_list_tmp;
                        
                }
            }
            $File_list .= "   </tbody>\n";
            $File_list .= "</table>\n"; 
        }
        ##Report_Files for admin
        $Report_Files = $HReport_Files = "";
        
        if($this->ion_auth->is_admin())
        {
            $HReport_Files .= "<table class=\"table table-hover table-condensed table-bordered\" >\n";
            $HReport_Files .= "   <thead>\n";
            $HReport_Files .= "      <tr><th>Filename</th><th>Size</th><th>Date</th><th>Operation</th></tr>\n";
            $HReport_Files .= "   </thead>\n";
            $HReport_Files .= "   <tbody>\n";
            $d = dir(".$out_network");
            while (false !== ($entry = $d->read())) 
            {
                if(preg_match("/Report|EndJob/",$entry))
                {
                    $file_url= ".$out_network$entry";
                    $file_size = filesize($file_url);
                    $file_date = date ("Y/m/d H:i:s.", filemtime($file_url));
                    $FileName  = anchor($file_url,$entry,"target='_blank'");
                    $ope="<button class=\"delFile btn btn-primary\" value=\"$file_url\" />Delete</button>";
                   
                    $Report_Files  .= "<tr><td>$FileName</td><td>$file_size</td><td>$file_date</td><td>$ope</td></tr>\n";
                }
            }
            $d->close();
            if($Report_Files !="")
            {
                $HReport_Files .= $Report_Files;
                $Report_Files .= "   </tbody>\n";
                $Report_Files .= "</table>\n"; 
            }
        }
        // Set any returned status/error messages.
        $message = "";
        $data = array(
            'title' =>  'Project Information',
            'contents' =>  'auth/public/project_detail_view',
            'footer_title' =>  $this->footer_title,
            'message' =>  $message,
            'username' => $username,
            'File_list' => $File_list,
            'Report_Files' => $Report_Files,
            'Directory' => $Directory
         );
        $this->load->view('templates/template', $data);
        }
 
    public function delete_file(){
          $FileName=$_POST['FileName'];
          $work_scripts  = $this->config->item('work_scripts');
          $cmd = exec("rm  $FileName",$st,$code);
          if($code ==0)
          $html= "FileName  $FileName deleted";
          else $html= "FileName  $FileName not deleted. error :$code";
          #log_message('debug',"L411 auth_public: $html ");
          return $html;
    }
    
    public function _get_csrf_nonce()
    {
            $this->load->helper('string');
            $key   = random_string('alnum', 8);
            $value = random_string('alnum', 20);
            $this->session->set_flashdata('csrfkey', $key);
            $this->session->set_flashdata('csrfvalue', $value);

            return array($key => $value);
    }

    public function _valid_csrf_nonce()
    {
            if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
                    $this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
            {
                    return TRUE;
            }
            else
            {
                    return FALSE;
            }
    }

    public function _render_page($view, $data=null, $returnhtml=false)//I think this makes more sense
    {
            $this->viewdata = (empty($data)) ? $this->data: $data;
            
            $this->load->view("templates/header",$this->viewdata);
            $this->load->view("templates/menu");                           
            $view_html = $this->load->view($view, $this->viewdata, $returnhtml);
            $data['footer_title'] = $this->footer_title;
            $this->load->view("templates/footer",$data);

            if ($returnhtml) return $view_html;//This will return html on 3rd argument being true
    }
	
}
