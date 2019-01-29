<?php
/**
 * @version		1.3.1
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2011 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;
?>
<?php defined('_JEXEC') or die('Restricted access'); ?>
<div class="manufacturer-slide <?php echo $params->get('moduleclass_sfx' ); ?>">
	<div class="eshop_manufacturer row-fluid">
		<?php
		foreach ($items as $item)
		{ 
			$viewManufacturerUrl = JRoute::_(EshopRoute::getManufacturerRoute($item->id));
			?>
			<div class="slide">
				<a href="<?php echo $viewManufacturerUrl; ?>" title="<?php echo $item->manufacturer_name; ?>">
					<img src="<?php echo $item->image; ?>" alt="<?php echo $item->manufacturer_name; ?>" />
				</a>
			</div>
			<?php
		}
		?>
	</div>
</div>
<script type="text/javascript">
	Eshop.jQuery(document).ready(function($){
		if($("body").hasClass("sunfw-direction-rtl"))
	        rtl = true;
	    else {
	        rtl = false;
	    }
		$('.eshop_manufacturer').slick({
		  dots: false,
		  speed: 200,
		  arrows: true,
		  slidesToShow: <?php echo $manufacturersShow; ?>,
		  slidesToScroll: 1,
		  touchMove: true,
		  rtl: rtl,
		  responsive: [
		    {
		      breakpoint: 480,
		      settings: {
		        slidesToShow: 1,
		        slidesToScroll: 1
		      }
		    }
		  ]
		});
	});

</script>