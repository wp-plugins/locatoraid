<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends My_Controller {
	function __construct()
	{
		parent::__construct();
		$this->data['page_title'] = $this->config->item('nts_app_title') . ' Installation';

	// check if already setup
		if( $this->is_setup() )
		{
			if ( ! $this->auth->logged_in() ){
				ci_redirect('auth/login');
				}
			elseif ( ! $this->auth->is_admin() ){
				$this->session->set_flashdata('message', 'You must be an admin to view this page');
				ci_redirect('auth/login');
				}
		}
	}

	function index()
	{
		$this->data['include'] = 'setup';
		$this->load->view( 'template', $this->data);

		$app = $this->config->item('nts_app');
		$predefined_admin = isset($GLOBALS['NTS_CONFIG'][$app]['PREDEFINED_ADMIN']) ? $GLOBALS['NTS_CONFIG'][$app]['PREDEFINED_ADMIN'] : NULL;
		if( $predefined_admin )
		{
			$this->run( $predefined_admin );
		}
	}

	function run( $predefined_admin = NULL )
	{
		$validation = array(
		   array(
				'field'   => 'email',
				'label'   => 'lang:email',
				'rules'   => 'trim|required|valid_email'
				),
		   array(
				'field'   => 'password',
				'label'   => 'lang:password',
				'rules'   => 'trim|required|matches[password2]'
				),
		   array(
				'field'   => 'password2',
				'label'   => 'lang:password2',
				'rules'   => 'trim|required'
				),
			);
		$this->form_validation->set_rules( $validation );

		if( (! $predefined_admin) && ($this->form_validation->run() == FALSE) )
		{
			$this->data['include'] = 'setup';
			$this->load->view( 'template', $this->data);
		}
		else
		{
			if( (! $predefined_admin) )
			{
				$admin_email = $this->input->post( 'email' );
				$admin_password = $this->input->post( 'password' );
			}
			else
			{
				$admin_email = $predefined_admin;
				$admin_password = mt_rand(1000000, 9999999);
			}

			$tables = array();
			$sth = $this->db->query("SHOW TABLES LIKE '" . NTS_DB_TABLES_PREFIX . "%'");
			foreach ($sth->result_array() as $r) {
				reset( $r );
				foreach( $r as $k => $v ){
					$tables[] = $v;
					}
				}
			reset( $tables );
			foreach( $tables as $t )
			{
				$this->db->query("DROP TABLE " . $t . "");
			}

		$this->load->library('migration');
		if ( ! $this->migration->current()){
			show_error($this->migration->error_string());
			return false;
			}

		$this->load->library( 'app_conf' );

	// create admin
		$this->app_conf->set( 'admin_email', $admin_email );
		$hash_password = $this->auth->hash_password( $admin_password );
		$this->app_conf->set( 'admin_password', $hash_password );

		$this->session->set_flashdata( 'message', lang('ok') );
		ci_redirect( '' );
		}
	}
}

/* End of file setup.php */
/* Location: ./application/controllers/setup.php */