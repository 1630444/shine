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
<div class="eshop-product-tabs<?php echo $params->get( 'moduleclass_sfx' ); ?>">
	<?php if($headerText){?>
		<div class="eshopheader"><?php echo $headerText; ?></div>
	<?php }?>
	<div class="row-fluid">
		<div class="span12">
			<div class="tabbable <?php echo $tabDerection; ?>">
				<?php
				if ($tabDerection != 'tabs-below')
				{
					?>
					<ul class="nav nav-tabs" id="productTab">
						<?php
						$count = 0;
						foreach ($categories as $key => $category) 
						{
							$products = modEshopProductTabsHelper::getProducts($category->id, $params);
							if (!$products) {
								continue;
							}
							($count == 0) ? $class = 'active' : $class = '';
							?>
							<li class="<?php echo $class;?>"><a href="#category-tab-<?php echo $count.$moduleId; ?>" rel="<?php echo $count; ?>" data="<?php echo $moduleId; ?>" data-toggle="tab"><?php echo $category->category_name; ?></a></li>
							<?php 
							$count++;
						}
						?>
					</ul>
					<?php
				} 
				?>
				<div class="tab-content" id="productContent">
					<?php
					$count = 0;
					foreach ($categories as $key => $category) 
					{
						$products = modEshopProductTabsHelper::getProducts($category->id, $params);
						if (!$products) {
							continue;
						}
						($count == 0) ? $class = 'active' : $class = '';
						?>
						<input type="hidden" id="setcount-<?php echo $count?>" name="setcount-<?php echo $count?>" value="0" />
						<div class="tab-pane <?php echo $class; ?>" id="category-tab-<?php echo $count.$moduleId; ?>">
							<?php
							if ($params->get('show_category_desc'))
							{
								?>
								<div class="category-desc"><?php echo $category->category_desc?></div>
								<?php
							}
							if ($showViewAllLink)
							{
								$categoryUrl = JRoute::_(EshopRoute::getCategoryRoute($category->id));
								?>
								<div class="category-view-all">
									<a href="<?php echo $categoryUrl; ?>" title="<?php echo $category->category_page_title != '' ? $category->category_page_title : $category->category_name; ?>">
										<?php echo JText::_('ESHOP_VIEW_ALL'); ?>
									</a>
								</div>
								<?php
							}
							if (count($products)) 
							{
								?>
								<div id="item-container-<?php echo $count.$moduleId; ?>" class="product-tab-content row">
									<?php
									foreach ($products as $key => $product)
									{
										$viewProductUrl = JRoute::_(EshopRoute::getProductRoute($product->id, EshopHelper::getProductCategory($product->id)));
										// Image
										if ($product->product_image && JFile::exists(JPATH_ROOT.'/media/com_eshop/products/' . $product->product_image))
										{
											$image = EshopHelper::resizeImage($product->product_image, JPATH_ROOT . '/media/com_eshop/products/', $thumbnailWidth, $thumbnailHeight);
										}
										else
										{
											$image = EshopHelper::resizeImage('no-image.png', JPATH_ROOT . '/media/com_eshop/products/', $thumbnailWidth, $thumbnailHeight);
										}
										$image = JURI::base() . 'media/com_eshop/products/resized/' . $image;
										$labels = EshopHelper::getProductLabels($product->id);
										?>
										<div class="eshop-product-tab col-lg-<?php echo intval(12 / $visibleProdcuts);?> col-md-<?php echo intval(12 / $visibleProdcuts);?> col-sm-6 col-xs-12">
											<div class="esbop-image-block">
												<div class="image img-polaroid">
													<a href="<?php echo $viewProductUrl; ?>">
														<?php
														if (count($labels) && $params->get('enable_labels'))
														{
															for ($i = 0; $n = count($labels), $i < $n; $i++)
															{
																$label = $labels[$i];
																if ($label->label_style == 'rotated' && !($label->enable_image && $label->label_image))
																{
																	?>
																	<div class="cut_rotated">
																		<?php
																	}
																	if ($label->enable_image && $label->label_image)
																	{
																		$imageWidth = $label->label_image_width > 0 ? $label->label_image_width : EshopHelper::getConfigValue('label_image_width');
																		if (!$imageWidth)
																			$imageWidth = 50;
																		$imageHeight = $label->label_image_height > 0 ? $label->label_image_height : EshopHelper::getConfigValue('label_image_height');
																		if (!$imageHeight)
																			$imageHeight = 50;
																		?>
																		<span class="horizontal <?php echo $label->label_position; ?> small-db" style="opacity: <?php echo $label->label_opacity; ?>;<?php echo 'background-image: url(' . $label->label_image . ')'; ?>; background-repeat: no-repeat; width: <?php echo $imageWidth; ?>px; height: <?php echo $imageHeight; ?>px; box-shadow: none;"></span>
																		<?php
																	}
																	else 
																	{
																		?>
																		<span class="<?php echo $label->label_style; ?> <?php echo $label->label_position; ?> small-db" style="background-color: <?php echo '#'.$label->label_background_color; ?>; color: <?php echo '#'.$label->label_foreground_color; ?>; opacity: <?php echo $label->label_opacity; ?>;<?php if ($label->label_bold) echo 'font-weight: bold;'; ?>">
																			<?php echo $label->label_name; ?>
																		</span>
																		<?php
																	}
																	if ($label->label_style == 'rotated' && !($label->enable_image && $label->label_image))
																	{
																		?>
																	</div>
																	<?php
																}
															}
														}
														?>
														<img class="img-polaroid" alt="<?php echo $product->product_name; ?>" src="<?php echo $image; ?>">
													</a>
												</div>	
											</div>
											<div class="clear"></div>
											<div class="eshop-info-block">
												<h5>
													<a href="<?php echo $viewProductUrl; ?>" <?php echo $showTooltip == 1 ? 'class="link"' : ''; ?> data-original-title="<?php echo $product->product_short_desc; ?>">
														<?php echo $product->product_name; ?>
													</a>
												</h5>
												<div class="product-review">
													<p>
														<img src="components/com_eshop/themes/jsn_shine/images/stars-<?php echo round(EshopHelper::getProductRating($product->id)); ?>.png" />
													</p>
												</div>
												<?php
												if ($showPrice == 1 && EshopHelper::showPrice() && !$product->product_call_for_price)
												{
													?>
													<div class="price" id="product-price">
														<?php //echo JText::_('ESHOP_PRICE'); ?>
														<?php
														$productPriceArray = EshopHelper::getProductPriceArray($product->id, $product->product_price);
														if ($productPriceArray['salePrice'] >= 0)
														{
															?>
															<span class="base-price"><?php echo $currency->format($tax->calculate($productPriceArray['basePrice'], $product->product_taxclass_id, EshopHelper::getConfigValue('tax'))); ?>
														</span>&nbsp;
														<span class="sale-price"><?php echo $currency->format($tax->calculate($productPriceArray['salePrice'], $product->product_taxclass_id, EshopHelper::getConfigValue('tax'))); ?></span>
														<?php
													}
													else
													{
														?>
														<span class="price"><?php echo $currency->format($tax->calculate($productPriceArray['basePrice'], $product->product_taxclass_id, EshopHelper::getConfigValue('tax'))); ?></span>
														<?php
													}
													?>
													<br />
													<?php
													if ($showTax && EshopHelper::getConfigValue('tax'))
													{
														?>
														<small>
															<?php echo JText::_('ESHOP_EX_TAX'); ?>:
															<?php
															if ($productPriceArray['salePrice'])
															{
																echo $currency->format($productPriceArray['salePrice']);
															}
															else
															{
																echo $currency->format($productPriceArray['basePrice']);
															}
															?>
														</small>
														<?php
													}
													?>
												</div>
												
												<?php
											}
											if ($product->product_call_for_price)
											{
												?>
												<div>
													<div class="product-price">
														<?php echo JText::_('ESHOP_CALL_FOR_PRICE'); ?>: <?php echo EshopHelper::getConfigValue('telephone'); ?>
													</div>
												</div>
												<?php
											}
											?>
										</div>
										<div class="eshop-buttons">
											<a class="btn-wishlist" onclick="addToWishList(<?php echo $product->id; ?>, '<?php echo EshopHelper::getSiteUrl(); ?>')" title="<?php echo JText::_('ESHOP_ADD_TO_WISH_LIST'); ?>">
												<i class="fa fa-heart-o"></i><?php //echo JText::_('ESHOP_ADD_TO_WISH_LIST'); ?>
											</a>
											<?php
											if($showAddcart == 1 && !EshopHelper::getConfigValue('catalog_mode') && !$product->product_call_for_price)
											{ 
												?>
												<div class="eshop-cart-area">
													<input id="add-to-cart-<?php echo $product->id; ?>" type="button" class="btn-cart" onclick="addToCart(<?php echo $product->id; ?>, 1, '<?php echo EshopHelper::getSiteUrl(); ?>', '<?php echo EshopHelper::getAttachedLangLink(); ?>', '<?php echo EshopHelper::getConfigValue('cart_popout')?>', '<?php echo JRoute::_(EshopRoute::getViewRoute('cart')); ?>');" value="<?php echo JText::_('ESHOP_ADD_TO_CART'); ?>" />
												</div>
												<?php 
											}//end if show add to cart
											?>
											<a class="btn-compare" onclick="addToCompare(<?php echo $product->id; ?>, '<?php echo EshopHelper::getSiteUrl(); ?>')" title="<?php echo JText::_('ESHOP_ADD_TO_COMPARE'); ?>">
												<i class="fa fa-exchange"></i><?php //echo JText::_('ESHOP_ADD_TO_COMPARE'); ?>
											</a>

										</div>
									</div>
									<?php
									} //end foreach
									?>
								</div>
								<?php
							}//end if
							?>
							<div class="clearfix"></div>
							<?php
							if (count($products) > $visibleProdcuts) {
								?>
								<div class="pagination pagination-toolbar hidden" style="text-align: center; margin-top: 20px;"> 
									<ul class="<?php echo $moduleId; ?>pagination-list-<?php echo $count.$moduleId; ?>">
									</ul>
								</div>
								<?php
							} 
							?>
						</div>
						<?php
						$count ++ ;
					} 
					?>
				</div>
				<?php
				if ($tabDerection == 'tabs-below')
				{
					?>
					<ul class="nav nav-tabs" id="productTab">
						<?php
						$count = 0;
						foreach ($categories as $key => $category) 
						{
							$products = modEshopProductTabsHelper::getProducts($category->id, $params);
							if (!$products) {
								continue;
							}
							($count == 0) ? $class = 'active' : $class = '';
							?>
							<li class="<?php echo $class;?>"><a href="#category-tab-<?php echo $count.$moduleId; ?>" rel="<?php echo $count; ?>" data="<?php echo $moduleId; ?>" data-toggle="tab"><?php echo $category->category_name; ?></a></li>
							<?php 
							$count++;
						}
						?>
					</ul>
					<?php
				} 
				?>
			</div>
		</div>
	</div>
	<?php if($footerText){ ?>
		<div class="eshopfooter"><?php echo $footerText; ?></div>
	<?php }?>
</div>
<script type="text/javascript">
	Eshop.jQuery(function($){
		$(document).ready(function () {
			loadPagination(0,<?php echo $moduleId;?>);
			$('#setcount-0').attr('value',1);
		});
		$("ul#productTab li a").on('shown.bs.tab', function (e) {
			var getCountData = $(this).attr('rel');
			var getModuleId  = $(this).attr('data');
			if($('#setcount-'+getCountData).val() != 1)
			{
				loadPagination(getCountData, getModuleId);
				$('#setcount-'+getCountData).val(1);
			}
		});
		loadPagination = (function(countData, moduleId){
			$("ul."+moduleId+"pagination-list-"+countData+moduleId).eshopPagination({
				containerID: "item-container-"+countData+moduleId,
				first       : "First",
				last        : "Last",
				perPage: <?php echo $visibleProdcuts; ?>,
				keyBrowse: true,
				pause: <?php echo $autoPlay == 1 ? 4000 : 0; ?>,
				clickStop   : true,
				scrollBrowse: false,
				animation: "<?php echo $animation; ?>",
			});
		})

	})
</script>
