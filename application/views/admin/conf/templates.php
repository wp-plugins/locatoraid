<?php
$fields = array();

foreach( $template_fields as $tf => $tf_title ){
	$fields[] = array(
		'name' 		=> $tf,
 		'title'		=> lang('conf_template_list'),
		'type'		=> 'checkbox',
		'help'		=> lang('conf_default_search_help'),
		);
}
reset( $fields );
?>

<div class="page-header">
<h2><?php echo lang('menu_conf_templates');?></h2>
</div>

<p>
<?php echo lang('conf_templates_help'); ?>
</p>

<?php echo form_open('', array('class' => 'form-horizontal form-condensed')); ?>

<table class="table table-condensed table-striped" style="width: auto;">
	<tr>
		<th style="width: 12em;"></td>
		<th style="width: 10em;"><?php echo lang('conf_locations_map'); ?></th>
		<th style="width: 10em;"><?php echo lang('conf_locations_list'); ?></th>
	</tr>

	<?php foreach( $template_fields as $tf => $tf_title) : ?>
	<?php
		if( $tf == 'website') {
			$tf_title = lang('location_website');
		}
	?>
		<tr>
			<td>
			<?php echo $tf_title; ?>
			</td>

			<td>
				<?php
				$tf_name = 'form_' . $tf . '_map_hide';
				$f = array(
					'name' 		=> $tf_name,
					'title'		=> $tf_title,
					'type'		=> 'checkbox',
					'help'		=> lang('conf_default_search_help'),
					);

				$chb_value = isset($defaults[$f['name']]) ? $defaults[$f['name']] ? FALSE : TRUE : TRUE;

				switch( $f['type'] ){
					case 'textarea':
						echo form_textarea( $f['name'], set_value($f['name'], isset($defaults[$f['name']]) ? $defaults[$f['name']] : ''), 'style="width: 20em; height: 8em;"' );
						break;
					case 'checkbox':
						echo form_checkbox($f['name'], 1, set_checkbox($f['name'], 1, $chb_value));
						break;
					default:
						echo form_input($f, set_value($f['name'], isset($defaults[$f['name']]) ? $defaults[$f['name']] : ''));
						break;
				}
				?>
			</td>

			<td>
				<?php
				$tf_name = 'form_' . $tf . '_hide';

				$f = array(
					'name' 		=> $tf_name,
					'title'		=> $tf_title,
					'type'		=> 'checkbox',
					'help'		=> lang('conf_default_search_help'),
					);

				$chb_value = isset($defaults[$f['name']]) ? $defaults[$f['name']] ? FALSE : TRUE : TRUE;

				switch( $f['type'] ){
					case 'textarea':
						echo form_textarea( $f['name'], set_value($f['name'], isset($defaults[$f['name']]) ? $defaults[$f['name']] : ''), 'style="width: 20em; height: 8em;"' );
						break;
					case 'checkbox':
						echo form_checkbox($f['name'], 1, set_checkbox($f['name'], 1, $chb_value));
						break;
					default:
						echo form_input($f, set_value($f['name'], isset($defaults[$f['name']]) ? $defaults[$f['name']] : ''));
						break;
				}
				?>
			</td>

		</tr>
	<?php endforeach; ?>
</table>

<div class="controls">
<?php echo form_submit( array('name' => 'submit', 'class' => 'btn btn-primary'), lang('common_save'));?>
</div>

<?php echo form_close();?>
