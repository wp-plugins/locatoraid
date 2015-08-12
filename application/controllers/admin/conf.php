<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Conf extends Admin_controller
{
	private $params = array();

	function __construct()
	{
		parent::__construct();

		$this->params = array(
			'settings' => array(
				'default_search',
				'search_label',
				'search_button',
				'append_search',
				'autodetect_button',
				'your_location_label',
				'start_listing',
				'csv_separator',
				'measurement',
				'search_within',
				'map_no_scrollwheel',
				'show_sidebar',
				'show_distance',
				'trigger_autodetect',
				'show_print_link',
				'show_matched_locations',
				'not_found_text',
				'group_output',
				'limit_output',
				'language',
				'choose_country',
				'directions_label',
				),
			'form' => array(
				'form_misc1',
				'form_misc2',
				'form_misc3',
				'form_misc4',
				'form_misc5',
				'form_misc6',
				'form_misc7',
				'form_misc8',
				'form_misc9',
				'form_misc10',
				'form_misc1_hide',
				'form_misc2_hide',
				'form_misc3_hide',
				'form_misc4_hide',
				'form_misc5_hide',
				'form_misc6_hide',
				'form_misc7_hide',
				'form_misc8_hide',
				'form_misc9_hide',
				'form_misc10_hide',
				'form_products',
				),
			);

	/* checkboxes to hide views in front end */
		$templates_fields = array();
		$templates_fields['distance'] = lang('location_distance');
		// $templates_fields['directions'] = lang('front_directions');
		$templates_fields['directions'] = 'Directions';

		$this->load->model('Location_model');
		$model_all_fields = $this->Location_model->get_fields();

		$address_fields = array( 'street1', 'street2', 'city', 'state', 'zip', 'country' );
		$skip_fields = array('priority');
		foreach( $model_all_fields as $mf ){
			if( in_array($mf['name'], $skip_fields) ){
				continue;
			}

			if( in_array($mf['name'], $address_fields) ){
				if( ! isset($templates_fields['address']) ){
					$templates_fields['address'] = lang('location_address');
				}
				continue;
			}
			$templates_fields[$mf['name']] = $mf['title'];
		}
		$this->params['template_fields'] = $templates_fields;
		$this->data['template_fields'] = $templates_fields;

		$this->params['templates'] = array();
		foreach( $templates_fields as $tf => $tf_label ){
			$this->params['templates'][] = 'form_' . $tf . '_hide';
			$this->params['templates'][] = 'form_' . $tf . '_map_hide';
		}
		$this->params['templates'][] = 'form_' . 'distance' . '_hide';
		$this->params['templates'][] = 'form_' . 'distance' . '_hide';

		$this->data['defaults'] = array();
		reset( $this->params );
		foreach( $this->params as $pk => $pa ){
			reset( $pa );
			foreach( $pa as $p ){
				$this->data['defaults'][$p] = $this->app_conf->get($p);
				}
			}
	}

	function reset( $what )
	{
		// update
		reset( $this->params[$what] );
		foreach( $this->params[$what] as $p ){
			$this->app_conf->reset( $p, $v );
			}

	// redirect back
		$this->session->set_flashdata( 'message', lang('common_ok') );
		ci_redirect( 'admin/conf/' . $what );
	}

	function resetproducts()
	{
		$this->app_conf->set('products', '');
		$this->session->set_flashdata( 'message', lang('common_ok') );
		ci_redirect( 'admin/conf/settings' );
	}

	function index( $what = 'settings' )
	{
		if( $what == 'resetproducts' )
		{
			return $this->resetproducts();
		}

		if( $this->form_validation->run('conf-' . $what) == false ){
		// display the form
			$this->data['include'] = 'admin/conf/' . $what;
			$this->load->view( $this->template, $this->data );
			}
		else {
		// update
			reset( $this->params[$what] );
			foreach( $this->params[$what] as $p ){
				$v = $this->input->post( $p );
				if( $what == 'templates' ){
					$v = $v ? 0 : 1;
				}
				$this->app_conf->set( $p, $v );
				}

		// redirect back
			$msg = lang('common_update') . ': ' . lang('common_ok');
			$this->session->set_flashdata( 'message', $msg );
			ci_redirect( 'admin/conf/' . $what );
			}
	}
}

/* End of file customers.php */
/* Location: ./application/controllers/admin/categories.php */