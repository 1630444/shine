<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2011 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

if (count($tags))
{
	?>
	<div class="tag-cloud">
		<?php
		foreach ($tags as $tag)
		{
			?>
			<a title="<?php echo $tag->tag_name; ?>" href="<?php echo $tag->link; ?>" style="font-size: <?php echo $tag->size;?>%;">
				<?php echo $tag->tag_name;?>
			</a>&nbsp;
			<?php
		}
		?>
	</div>
	<?php
}