<?php
class MY_Install {
    public function __construct() {
        $CI =& get_instance();
        $CI->load->database();
        if ($CI->db->database == "") {
            #header('location:installer');
            redirect('installer');
        } else {
            if (is_dir('install')) {
                echo '<i>Please delete or rename <b>Install</b> folder</i>';
                exit;
            }
        }
    }
    
    
  
}
