<?php
$append_search = $this->app_conf->get( 'append_search' );
$products_label = $this->app_conf->get( 'form_products' );
if( ! strlen($products_label) )
	$products_label = lang('front_product_search')
?>
<?php echo form_open('search', array('id' => 'lpr-search-form', 'class' => 'form-horizontal form-condensed')); ?>

<div class="control-group">
	<label class="control-label" style="text-align: left;" for="search">
		<?php echo lang('front_address_or_zip'); ?>
	</label>
	<div class="controls">
		<?php echo form_input( array('name' => 'search', 'id' => 'lpr-search-address', 'class' => 'input-medium'), set_value('search', $search) ); ?>
		<div id="lpr-current-location" style="display: none;">
			<strong><?php echo lang('front_current_location'); ?></strong> 
			<a href="#" id="lpr-skip-current-location"><?php echo lang('front_enter_address'); ?></a>
		</div>
	</div>
</div>

<div class="control-group">
	<div class="controls">
		<a href="#" id="lpr-autodetect"><?php echo lang('front_autodetect'); ?></a>
	</div>

	<?php if( count($within_options) > 1 ) : ?>
		<?php
		$measure = $this->app_conf->get( 'measurement' );
		$measure_title = lang('conf_measurement_') . $measure;
		$dropdown_within = array();
		foreach( $within_options as $wo )
		{
			$dropdown_within[ $wo ] = $wo;
		}
		?>
		<label class="control-label" style="text-align: left;" for="within">
			<?php echo lang('front_search_within'); ?>
		</label>
		<div class="controls">
			<?php echo form_dropdown( 'within', $dropdown_within, '', 'id="lpr-search-within" class="input-small"' ); ?> <?php echo $measure_title; ?>
		</div>
	<?php endif; ?>

	<div class="controls" id="lpr-search-controls">
		<?php if( ! $product_options ) : ?>
			<?php echo form_button( array('name' => 'submit', 'type' => 'submit', 'class' => 'btn', 'id' => 'lpr-search-button'), lang('common_search'));?>
			<?php echo form_hidden( 'search2', '' ); ?>
		<?php endif; ?>
	</div>
</div>

<?php if( $product_options ) : ?>
<div class="control-group">
<label style="text-align: left;" class="control-label" for="search2"><?php echo $products_label; ?></label>
<div class="controls">
<?php
$do_options = array(
	' '	=> ' - ' . lang('common_any') . ' - ',
	);
foreach( $product_options as $po )
{
	$do_options[ $po ] = $po;
}
?>
<?php echo form_dropdown( 'search2', $do_options, set_value('search2', $search2) ); ?>
</div>
</div>
<div class="controls" id="lpr-search-controls">
<?php echo form_button( array('name' => 'submit', 'type' => 'submit', 'class' => 'btn', 'id' => 'lpr-search-button'), lang('common_search'));?>
</div>
<?php endif; ?>

<?php echo form_close(); ?>

<div id="lpr-results" class="row-fluid" style="width: 90%; border: #ccc 1px solid;">
<?php if( $show_sidebar ) : ?>
	<div id="lpr-map" class="span8"></div>
	<div id="lpr-locations" class="span4"></div>
<?php else : ?>
	<div id="lpr-map"></div>
<?php endif; ?>	
	<div class="clearfix"></div>
</div>

<script language="JavaScript">
var url_prefix = "<?php echo ci_site_url('front/get'); ?>";
var lpr_map;
var lpr_loc;
var lpr_infowindow;
var lpr_offset = 0;
var lpr_limit = 5;
var lpr_geocoder;
var lpr_start_marker;
var lpr_directions_display;
var lpr_directions_service;
var lpr_log_it;

var lpr_meta;
if( document.createElement && (lpr_meta = document.createElement('meta')) )
{
	lpr_meta.name = "viewport";
	lpr_meta.content = "width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no";
	document.getElementsByTagName('head').item(0).appendChild( lpr_meta );
}

google.maps.LatLng.prototype.ntsDistanceFrom = function(latlng)
{
	var lat = [this.lat(), latlng.lat()]
	var lng = [this.lng(), latlng.lng()]
	var R = 6378137;
	var dLat = (lat[1]-lat[0]) * Math.PI / 180;
	var dLng = (lng[1]-lng[0]) * Math.PI / 180;
	var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
	Math.cos(lat[0] * Math.PI / 180 ) * Math.cos(lat[1] * Math.PI / 180 ) *
	Math.sin(dLng/2) * Math.sin(dLng/2);
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	var d = R * c;
	return Math.round(d);
}

var lpr_allow_empty = false;
jQuery(document).ready( function()
{
	/* check if current location is supported */
	if( ! navigator.geolocation )
	{
		jQuery( '#lpr-autodetect' ).parent().hide();
	}
	else
	{
<?php if( $this->app_conf->get('trigger_autodetect') ) : ?>
		jQuery('#lpr-autodetect').trigger('click');
<?php endif; ?>
	}

	lpr_allow_empty = true;
	lpr_geocoder = new google.maps.Geocoder();

	lpr_map = new google.maps.Map( document.getElementById("lpr-map"), {zoom:15, mapTypeId:google.maps.MapTypeId.ROADMAP} );

	lpr_infowindow = new google.maps.InfoWindow();
	google.maps.event.addListener(lpr_map, 'click', function(){
		lpr_infowindow.close();
		}); 

	var boxText = document.createElement("div");
	var myOptions = {
		boxStyle: {
			background: "#fff url('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/examples/tipbox.gif') no-repeat",
			opacity: 0.9,
			width: "280px",
			padding: "10px 10px 10px 10px"
			},
		closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
		infoBoxClearance: new google.maps.Size(1, 1),
		zIndex: null,
		isHidden: false,
		pane: "floatPane",
		enableEventPropagation: true,
		disableAutoPan: false,
		maxWidth: 0,
		pixelOffset: new google.maps.Size(-140, 0)
		};
	lpr_infowindow = new InfoBox(myOptions);

	var address = jQuery('#lpr-search-form').find('[name=search]').val();
<?php if( $append_search ) : ?>
	var append_search = "<?php echo $append_search; ?>";
	// check if it already ends with append
	if( address.substr(address.length - append_search.length).toLowerCase() != append_search.toLowerCase() )
	{
		address = address + ' ' + append_search;
	}
<?php endif; ?>

	var my_hash = window.location.hash;
	if( my_hash )
	{
		my_hash = my_hash.slice(1);
		my_hash = decodeURIComponent( my_hash );
		var my_select = jQuery('#lpr-search-form select[name=search2]');
		if( my_select.length )
		{
			my_select.find('option[value="' + my_hash + '"]').attr('selected', 'selected');
		}
	}

	var search2 = jQuery('#lpr-search-form').find('[name=search2]').val();
	var within = jQuery('#lpr-search-form').find('[name=within]').val();

<?php if( $start_listing ) : ?>
	lpr_log_it = 0;
	lpr_front_process_search( address, search2, lpr_allow_empty, within );
<?php else : ?>
	lpr_log_it = 1;
	jQuery('#lpr-results').hide();
<?php endif; ?>

	lpr_allow_empty = false;

	lpr_directions_service = new google.maps.DirectionsService();
	lpr_directions_display = new google.maps.DirectionsRenderer();
	lpr_directions_display.setMap( lpr_map );
});

jQuery('#lpr-search-form').live( 'submit', function(event) {
	event.preventDefault();
	var address = jQuery( this ).find('[name=search]').val();
<?php if( $append_search ) : ?>
	var append_search = "<?php echo $append_search; ?>";
	// check if it already ends with append
	if( address.substr(address.length - append_search.length).toLowerCase() != append_search.toLowerCase() )
	{
		address = address + ' ' + append_search;
	}
<?php endif; ?>

	var search2 = jQuery( this ).find('[name=search2]').val();
	var within = jQuery( this ).find('[name=within]').val();

	if( jQuery('#lpr-results').is(':hidden') )
	{
		if( address.length || (search2.length && (search2 != ' ')) )
		{
			jQuery('#lpr-results').show();
			lpr_map = new google.maps.Map( document.getElementById("lpr-map"), {zoom:15, mapTypeId:google.maps.MapTypeId.ROADMAP} );
		}
	}

	allow_empty = (typeof allow_empty === "undefined") ? false : allow_empty;
	if( (! allow_empty) && (address.length < 1) && (search2.length < 1) )
		return false;

	var target_div = jQuery( '#lpr-locations' );
	if( target_div )
		target_div.html( '' );

	lpr_directions_display.setMap( null );
	lpr_front_process_search( address, search2, lpr_allow_empty, within );
	});

<?php if( $product_options ) : ?>
jQuery('#lpr-search-form select').live( 'change', function(event) {
	jQuery('#lpr-search-form').submit();
	});
<?php endif; ?>
</script>

<script language="JavaScript">
jQuery(document).ready( function()
{
	var my_hash = window.location.hash;
	if( my_hash )
	{
		my_hash = my_hash.slice(1);
		my_hash = decodeURIComponent( my_hash );
		var my_select = jQuery('#lpr-search-form select[name=search2]');
		if( my_select.length )
		{
			my_select.find('option[value="' + my_hash + '"]').attr('selected', 'selected');
		}
	}
});
</script>