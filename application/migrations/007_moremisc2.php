<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_moremisc2 extends CI_Migration {
	public function up()
	{
		$this->dbforge->add_column(
			'locations',
			array(
				'misc6' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),
				'misc7' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),
				'misc8' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),
				'misc9' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),
				'misc10' => array(
					'type' => 'TEXT',
					'null' => TRUE,
					),
				)
			);
	}

	public function down()
	{
		$this->dbforge->drop_column('locations', 'misc6');
		$this->dbforge->drop_column('locations', 'misc7');
		$this->dbforge->drop_column('locations', 'misc8');
		$this->dbforge->drop_column('locations', 'misc9');
		$this->dbforge->drop_column('locations', 'misc10');
	}
}
