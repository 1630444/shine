<?php
/**
 * @package     RedSHOP.Frontend
 * @subpackage  Template
 *
 * @copyright   Copyright (C) 2008 - 2016 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * $displayData extract
 *
 * @param   object  $form        A JForm object
 * @param   int     $product_id  Id current product
 * @param   int     $modal       Flag use form in modal
 */
extract($displayData);

?>

<div class="row">
	<label class="col-xs-5"><?php echo $extra_field_label;?>:</label>
	<div class="col-xs-7"><?php echo $extra_field_value;?></div>
</div>
