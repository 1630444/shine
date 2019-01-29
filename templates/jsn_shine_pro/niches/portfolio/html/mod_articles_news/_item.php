<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_news
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$item_heading = $params->get('item_heading', 'h4');
$readmore = JText::_('READMORE');
?>
<div class="article-item">
	<div class="create pull-left">
		<span class="date-create"><?php echo JText::sprintf(JHtml::_('date', $item->created, 'd')); ?></span>
		<span class="month-create"><?php echo JText::sprintf(JHtml::_('date', $item->created, 'F')); ?></span>
	</div>
	<div class="content-item pull-left">
	<?php if ($params->get('item_title')) : ?>

		<<?php echo $item_heading; ?> class="newsflash-title<?php echo $params->get('moduleclass_sfx'); ?>">
		<?php if ($params->get('link_titles') && $item->link != '') : ?>
			<a href="<?php echo $item->link; ?>">
				<?php echo $item->title; ?>
			</a>
		<?php else : ?>
			<?php echo $item->title; ?>
		<?php endif; ?>
		</<?php echo $item_heading; ?>>

	<?php endif; ?>

	<?php if (!$params->get('intro_only')) : ?>
		<?php echo $item->afterDisplayTitle; ?>
	<?php endif; ?>

	<?php echo $item->beforeDisplayContent; ?>
	<div class="desc">
		 
		 <?php
		 	$introtext = strip_tags($item->introtext);
		 	echo JHtml::_('string.truncate', $introtext, 140);
		 ?>
	</div>
	<?php echo $item->afterDisplayContent; ?>

	<?php if (isset($item->link) && $item->readmore != 0 && $params->get('readmore')) : ?>
		<?php echo '<a class="readmore" href="' . $item->link . '">' . $readmore . '<i class="fa fa-long-arrow-right"></i></a>'; ?>
	<?php endif; ?>
	</div>
</div>
