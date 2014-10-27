<?php echo form_open('search', array('id' => 'lpr-search-form', 'class' => 'form-horizontal form-condensed')); ?>

<ul class="list-unstyled list-margin-v">
	<?php if( $conf_trigger_autodetect ) : ?>
		<li>
			<button type="button" class="btn" id="lpr-autodetect"><?php echo lang('front_autodetect'); ?></button>
		</li>
	<?php endif; ?>

	<li id="lpr-current-location" style="display: none;">
		<ul class="list-inline list-margin-v list-margin-h">
			<li>
				<strong><?php echo lang('front_current_location'); ?></strong> 
			</li>
			<li>
				<button type="button" class="btn" id="lpr-skip-current-location"><?php echo lang('front_enter_address'); ?></button>
			</li>
		</ul>
	</li>

	<li>
		<ul class="list-inline list-margin-v list-margin-h">
			<li>
				<?php echo form_input( array('name' => 'search', 'id' => 'lpr-search-address', 'class' => '', 'placeholder' => lang('front_address_or_zip')), set_value('search', $search) ); ?>
			</li>

			<?php if( $countries_options ) : ?>
				<li>
					<?php echo form_dropdown( 'country', $countries_options, set_value('country', $country), 'title="' . lang('location_country') . '" id="lpr-countries-dropdown" style="width: 8em;"' ); ?>
				</li>
			<?php else : ?>
				<?php echo form_hidden( 'country', '' ); ?>
			<?php endif; ?>


			<?php if( $product_options ) : ?>
				<li>
					<?php echo form_dropdown( 'search2', $do_options, set_value('search2', $search2), 'id="lpr-products-dropdown"' ); ?>
				</li>
			<?php else : ?>
				<?php echo form_hidden( 'search2', '' ); ?>
			<?php endif; ?>

			<?php if( count($within_options) > 1 ) : ?>
				<li>
					<?php echo form_dropdown( 'within', $dropdown_within, '', 'id="lpr-search-within" class="input-small"' ); ?>
				</li>
			<?php endif; ?>

			<li>
				<input type="submit" name="submit" class="btn" id="lpr-search-button" value="<?php echo lang('common_search'); ?>">
			</li>
		</ul>
	</li>
</ul>

<?php echo form_close(); ?>

<div id="lpr-results" class="row-fluid">
	<?php if( $show_sidebar ) : ?>
		<div id="lpr-map" class="span8" style="margin-bottom: 2em;"></div>
		<div id="lpr-locations" class="span4"></div>
	<?php else : ?>
		<div id="lpr-map"></div>
	<?php endif; ?>	
	<div class="clearfix"></div>
</div>