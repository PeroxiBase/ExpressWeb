<?php
/**
* The Expression Database.
*
*  Auth_public Class 
*
* Authentification class. allow single user to manage their profile
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<sylvain.picard@lrsv.ups-tlse.fr>
*@version 1.0
*@package        ExpressWeb
*@subpackage     Controller
*@category       Libraries
*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_public extends MY_Controller {

	function __construct()
	{
		parent::__construct();
		#$auth_db = $this->load->database('cluster_db',TRUE);
		$this->output->enable_profiler(true);
		$this->load->library(array('ion_auth','form_validation'));
		$this->load->helper(array('url','language'));
		$this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));
		$this->load->model('generic');
		$this->lang->load('auth');
		#$this->view_path ='sylvain';
		if (!$this->ion_auth->logged_in())
		{
			// redirect them to the login page
			redirect("auth/login", 'refresh');
		}
	}

	// redirect if needed, otherwise display the user list
	function index()
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
              'title'=>"The Expression Database: User account",
              'contents' => 'auth/public/dashboard_view',
              'message' => ''
              );
            $this->load->view("templates/template", $data);
        }
        
        public function edit_user()
        {
            $id= $this->session->user_id;
            $this->data['title'] = "Edit User";

            if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id)))
            {
                    redirect("auth", 'refresh');
            }

            $user = $this->ion_auth->user($id)->row();
            $groups=$this->ion_auth->groups()->result_array();
            $currentGroups = $this->ion_auth->get_users_groups($id)->result();

            // validate form input
            $this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required');
            $this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required');
            //$this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required');
            $this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'required');

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
                                    'company'    => $this->input->post('company'),
                                    'username'      => $this->input->post('username'),
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

                                    if (isset($groupData) && !empty($groupData)) {

                                            $this->ion_auth->remove_from_group('', $id);

                                            foreach ($groupData as $grp) {
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
            }

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
            );
            $this->data['last_name'] = array(
                    'name'  => 'last_name',
                    'id'    => 'last_name',
                    'type'  => 'text',
                    'value' => $this->form_validation->set_value('last_name', $user->last_name),
            );
            $this->data['username'] = array(
                    'name'  => 'username',
                    'id'    => 'username',
                    'type'  => 'text',
                    'value' => $this->form_validation->set_value('username', $user->username),
            );
            $this->data['email'] = array(
                    'name'  => 'email',
                    'id'    => 'email',
                    'type'  => 'email',
                    'value' => $this->form_validation->set_value('email', $user->email),
                    'size' => 50
            );
            $this->data['company'] = array(
                    'name'  => 'company',
                    'id'    => 'company',
                    'type'  => 'text',
                    'value' => $this->form_validation->set_value('company', $user->company),
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
            $this->data['Id'] = $id;
            $this->_render_page("auth/public/edit_user", $this->data);
        }
        
        public function update_email()
        {        
            $data = array(
              'title'=>"The Expression Database: Email",
              'contents' => 'auth/public/email_update',
              'message' => ''
              );
            $this->load->view("templates/template", $data);
        }
        
        public function manage_project()
        {        
            $user_id = $this->session->user_id;
            $projects = $this->generic->check_project($user_id);
            $data = array(
              'title'=>"The Expression Database: Manage project",
              'contents' => 'auth/public/project_view',
              'message' => '',
              'projects' => $projects->result_array()
              );
            $this->load->view("templates/template", $data);
        }
        
        public function insert_project()
	{
            // If 'Add Address' form has been submitted, then insert the new address details to the logged in users address book.
            if ($this->input->post('insert_project_submit')) 
            {		
                $this->load->library('form_validation');
                // Set validation rules.
                $validation_rules = array(
                        array('field' => 'insert_project_name', 'label' => 'Project Name', 'rules' => 'trim|required'),
                        array('field' => 'insert_creator', 'label' => 'Creator', 'rules' => 'trim|required'),
                        array('field' => 'insert_project_comment', 'label' => 'Comment', 'rules' => 'trim|required'),
                        array('field' => 'insert_start_Y', 'label' => 'Start Year', 'rules' => 'required'),
                        array('field' => 'insert_start_M', 'label' => 'Start Month', 'rules' => 'required'),
                        array('field' => 'insert_start_D', 'label' => 'Start Day', 'rules' => 'required'),
                );
                
                $this->form_validation->set_rules($validation_rules);
    
                // Run the validation.
                if ($this->form_validation->run())
                {
                    // Get user id from session to use in the insert function as a primary key.
                    $user_id = $this->session->user_id;
                    
                    // Get user project data from input.
                    // You can add whatever columns you need to custom user tables.
                    $project_data = array(
                            'uprj_uacc_fk' => $user_id,
                            'uprj_project_name' => $this->input->post('insert_project_name'),
                            'uprj_project_comment' => $this->input->post('insert_project_comment'),
                            'uprj_project_visibility' => $this->input->post('insert_visible'),
                            'uprj_project_shared' => $this->input->post('insert_shared'),
                            'uprj_project_date_start' => $this->input->post('insert_start_Y').'-'.
                              $this->input->post('insert_start_M').'-'.$this->input->post('insert_start_D'),
                            'uprj_project_date_end' => $this->input->post('insert_end_Y').'-'.
                              $this->input->post('insert_end_M').'-'.$this->input->post('insert_end_D'),
                    );
                     $response = $this->generic->insert_project($project_data);
                    // Save any public status or error messages (Whilst suppressing any admin messages) to CI's flash session data.
                    $this->session->set_flashdata('message', $reponse);
                    
                    // Redirect user.
                    if ($response) 
                    {
                      
                      # $this->genes_functions->working_space();
                      redirect('auth_public/manage_project') ;
                    }
                    else
                    {
                        
                      redirect('auth_public/insert_project');
                    }
                }
                else
                {
                        // Set validation errors.
                         $this->data['message'] = validation_errors('<p class="error_msg">', '</p>');
                        $this->data['contents'] = 'auth/public/project_insert_view';
                        $this->data['title'] = 'Insert Project';
                        $this->data['debug'] = $_POST;
                        $this->load->view('templates/template', $this->data);
                        return FALSE;
                }
            }
            else
            {
                // Set any returned status/error messages.
                $this->data['message'] = (! isset($this->data['message'])) ? $this->session->flashdata('message') : $this->data['message'];
                
                $this->data['contents'] = 'auth/public/project_insert_view';
                $this->data['title'] = 'Insert Project';
                $this->data['debug'] = '';
                $this->load->view('templates/template', $this->data);	
            }
            
	}
	
        function update_project($address_id = FALSE)
	{
		// Check the url parameter is a valid address id, else redirect to the dashboard.
		if (! is_numeric($address_id))
		{
			redirect('auth_public/dashboard');
		}
		// If 'Update Address' form has been submitted, then update the address details.
		else if ($this->input->post('update_project')) 
		{			
			$this->load->model('auth_model');
			$this->auth_model->update_project($address_id);
		}
		
		// Get user id from session to use in the update function as a primary key.
		$user_id = $this->ion_auth->get_user_id();
		$sql_where = array('uprj_id' => $address_id, 'uprj_uacc_fk' => $user_id);
		$this->data['project'] = $this->flexi_auth->get_users_row_array(FALSE, $sql_where);
		
		// Set any returned status/error messages.
		$this->data['message'] = (! isset($this->data['message'])) ? $this->session->flashdata('message') : $this->data['message'];
		
		$this->data['contents'] = 'auth/public/project_update_view';
		$this->data['title'] = 'Update Project';
		$this->load->view('templates/template', $this->data);
	}
		
	function view_project($address_id = FALSE)
	{
		// Check the url parameter is a valid address id, else redirect to the dashboard.
		if (! is_numeric($address_id))
		{
			redirect('auth/public/dashboard');
		}
				
		// Get user id from session to use in the update function as a primary key.
		$user_id = $this->session->user_id;
		/*$sql_where = array('uprj_id' => $address_id, 'uprj_uacc_fk' => $user_id);
		$this->data['project'] = $this->flexi_auth->get_users_row_array(FALSE, $sql_where);
		$query=$this->display_analysis( $this->data['project']['uprj_id']);
		*/
		$query = $this->generic->get_analysis($user_id);
		$get_project_list = $this->generic->check_project($user_id);
		$project=array();
		foreach($get_project_list->result_array() as $row)
		{
		     foreach($row as $key=>$value)
		     {
		         $project[$key]=$value;
		     }
		}
		
		// Set any returned status/error messages.
		$this->data['message'] = (! isset($this->data['message'])) ? $this->session->flashdata('message') : $this->data['message'];
		$this->data['project'] =$project;
		$this->data['query'] =$query;
		$this->data['contents'] = 'auth/public/project_detail_view';
		$this->data['title'] = 'Project Information';
		$this->load->view('templates/template', $this->data);
	}



	function display_analysis($project_id)
	{
        $sql_query="SELECT distinct(pid) as pid, `project_id`, `uacc_username` as user, `section`, `action`, 
              DATE(FROM_UNIXTIME(`when`))as Date, `uri`, `working_path` 
              FROM statistics inner join user_accounts on uacc_id=user_id
              WHERE `project_id`='$project_id'
              order by date,pid";
        $query = $this->db->query($sql_query);
        return $query;
      }
  
        public function manage_files()
        {        
            $data = array(
              'title'=>"The Expression Database: USer account",
              'contents' => 'auth/public/manage_files',
              'message' => ''
              );
            $this->load->view("templates/template", $data);
        }

        
	function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key   = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	function _valid_csrf_nonce()
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

	function _render_page($view, $data=null, $returnhtml=false)//I think this makes more sense
	{
		$this->viewdata = (empty($data)) ? $this->data: $data;
		
                            $this->load->view("templates/header",$this->viewdata);
                            $this->load->view("templates/menu");                           
		$view_html = $this->load->view($view, $this->viewdata, $returnhtml);
		 $this->load->view("templates/footer");

		if ($returnhtml) return $view_html;//This will return html on 3rd argument being true
	}
     
	
}
