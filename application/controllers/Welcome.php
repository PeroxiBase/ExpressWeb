<?php
/**
* The Expression Database .
*
*@copyright Laboratoire de Recherche en Sciences Vegetales 2016-2020
*@author Bruno SAVELLI<savelli@lrsv.ups-tlse.fr>
*@author Sylvain PICARD<picard.sylvain3@gmail.com>
*@version 1.0
*@package expressionWeb
*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	 function __construct()
	{
		parent::__construct();
		#$auth_db = $this->load->database('cluster_db',TRUE);
		if ($this->db->database == "")
		{
                    #header('location:install/');
                    redirect('installer');
                } 
                else
                {
                    if (is_dir('install')) 
                    {
                        echo '<i>Please delete or rename <b>Install</b> folder</i>';
                        exit;
                    }
                }
		$this->output->enable_profiler(true);
		$this->load->helper(array('url','language'));
		$this->lang->load('auth');
	}
	
	public function index()
	{
		//$this->load->view('welcome_message');
		$id = $this->session->user_id  ;
		$username = $this->session->username;
		
		if (!$this->ion_auth->logged_in())
		{
		     $data = array(
                      'title'=>"$this->header_name: Welcome",
                      'footer_title' => $this->footer_title,
                      'contents' => "start",
                      'username' => '',
                      'groups' => '',
                      'tables' => ''
                      );
		}
		else
		{
		    $groups = $this->session->groups;
		    $tables = $this->generic->get_table_members($id);
                    $data = array(
                      'title'=>"$this->header_name: Welcome $username",
                      'footer_title' => $this->footer_title,
                      'contents' => "start",
                      'username' => $username,
                      'groups' => $groups,
                      'tables' => $tables
                      );
                }
            $this->load->view("templates/template", $data);
	}
	
	public function request_account()
        {
            $this->data['title'] = "Create User";
            $this->load->library('form_validation');
            
           if (isset($_POST) && !empty($_POST))
           {
                $identity = $this->input->post('identity');
                $tables = $this->config->item('tables','ion_auth');
                $identity_column = $this->config->item('identity','ion_auth');
                $this->data['identity_column'] = $identity_column;
        
                // validate form input
                $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required');
                $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required');
                if($identity_column!=='email')
                {
                    print "create_user:: form valid $identity_column<br/>";
                    $this->form_validation->set_rules('identity',$this->lang->line('create_user_validation_identity_label'),'required');
                    if(strtolower($identity) !='demo')
                        $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email');
                }
                else
                {
                    $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique[' . $tables['users'] . '.email]');
                }
               // $this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
                $this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
                $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
                $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');
                
                 
                 if ($this->form_validation->run() == true)
                 {
                        $email    = strtolower($this->input->post('email'));
                        $password = $this->input->post('password');
                         print "create_user::form valid  457  true identity $identity <br/>";
                        $additional_data = array(
                            'first_name' => $this->input->post('first_name'),
                            'last_name'  => $this->input->post('last_name'),
                            'company'    => $this->input->post('company'),
                            'phone'      => $this->input->post('phone'),
                        );
                }
                else
                {
                    
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    redirect("welcome/request_account", 'refresh');
                }
                if ($this->form_validation->run() == true && $this->ion_auth->register($identity, $password, $email, $additional_data))
                {
                    // check to see if we are creating the user
                    // redirect them back to the admin page
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    redirect("auth", 'refresh');
                }
           }
            else
            {
                $min_password_length = $this->config->item('min_password_length','ion_auth');
                $max_password_length = $this->config->item('max_password_length','ion_auth');
                
                 // display the create user form
                // set the flash data error message if there is one
                $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
                
                $this->data['first_name'] = array(
                    'name'  => 'first_name',
                    'id'    => 'first_name',
                    'type'  => 'text',
                    'value' => $this->form_validation->set_value('first_name'),
                    'required' => 'required'
                );
                $this->data['last_name'] = array(
                    'name'  => 'last_name',
                    'id'    => 'last_name',
                    'type'  => 'text',
                    'value' => $this->form_validation->set_value('last_name'),
                    'required' => 'required'
                );
                $this->data['identity'] = array(
                    'name'  => 'identity',
                    'id'    => 'identity',
                    'type'  => 'text',
                    'value' => $this->form_validation->set_value('identity'),
                    'required' => 'required'
                );
                $this->data['email'] = array(
                    'name'  => 'email',
                    'id'    => 'email',
                    'type'  => 'text',
                    'size' => 50,
                    'value' => $this->form_validation->set_value('email'),
                    'required' => 'required'
                );
                $this->data['company'] = array(
                    'name'  => 'company',
                    'id'    => 'company',
                    'type'  => 'text',
                    'value' => $this->form_validation->set_value('company'),
                    'required' => 'required'
                );
               /* $this->data['phone'] = array(
                    'name'  => 'phone',
                    'id'    => 'phone',
                    'type'  => 'text',
                    'pattern' =>"[0-9]{10}" ,
                    'title' =>"Phone number: no space no -",
                    'value' => $this->form_validation->set_value('phone'),
                );*/
                $this->data['password'] = array(
                    'name'  => 'password',
                    'id'    => 'password',
                    'type'  => 'password',
                    'value' => $this->form_validation->set_value('password'),
                    'required' => 'required',
                     'title' => "At least $min_password_length characters, max $max_password_length"
                );
                $this->data['password_confirm'] = array(
                    'name'  => 'password_confirm',
                    'id'    => 'password_confirm',
                    'type'  => 'password',
                    'value' => $this->form_validation->set_value('password_confirm'),
                    'required' => 'required',
                     'title' => "At least $min_password_length characters, max $max_password_length"
                );
                
                $this->data['config'] = array(
                    'min_password_length' => $min_password_length,
                    'max_password_length' => $max_password_length ,
                    );
                $this->_render_page("auth/public/request_account", $this->data);
            }
           
        }

       public function _render_page($view, $data=null, $returnhtml=false)//I think this makes more sense
	{
		$this->viewdata = (empty($data)) ? $this->data: $data;
		
                            $this->load->view("templates/header",$this->viewdata);
                            $this->load->view("templates/menu");                           
		$view_html = $this->load->view($view, $this->viewdata, $returnhtml);
		 $this->load->view("templates/footer");

		if ($returnhtml) return $view_html;//This will return html on 3rd argument being true
	}
	
        public function doc()
        {
            $data = array(
               'title'=>"The ExpressDB: ",
               'contents' => 'documentation/index.html',
              );
            $this->load->view('templates/template', $data);
        }

        /**
        * function 
        * 
        * @staticvar integer $staticvar 
        * @param string $param1 
        * @param string $param2 
        * @return integer 
        */  
        public function restricted()
        {
            $data = array(
               'title'=>"The peroxidase: ",
               'contents' => 'restricted',
              );
            $this->load->view('templates/template', $data);
        }
}
