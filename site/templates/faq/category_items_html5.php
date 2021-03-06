<?php
/**
 * HTML5 Template
 * @version 1.5 stable $Id: category_items_html5.php 0001 2012-09-23 14:00:28Z Rehne $
 * @package Joomla
 * @subpackage FLEXIcontent
 * @copyright (C) 2009 Emmanuel Danan - www.vistamedia.fr
 * @license GNU/GPL v2
 * 
 * FLEXIcontent is a derivative work of the excellent QuickFAQ component
 * @copyright (C) 2008 Christoph Lukes
 * see www.schlu.net for more information
 *
 * FLEXIcontent is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
// first define the template name
$tmpl = $this->tmpl;
$user = JFactory::getUser();
?>

<?php
	ob_start();
	
	// Form for (a) Text search, Field Filters, Alpha-Index, Items Total Statistics, Selectors(e.g. per page, orderby)
	// If customizing via CSS rules or JS scripts is not enough, then please copy the following file here to customize the HTML too
	include(JPATH_SITE.DS.'components'.DS.'com_flexicontent'.DS.'tmpl_common'.DS.'listings_filter_form_html5.php');
	
	$filter_form_html = trim(ob_get_contents());
	ob_end_clean();
	if ( $filter_form_html ) {
		echo '<aside class="group">'."\n".$filter_form_html."\n".'</aside>';
	}
?>

<div class="clear"></div>

<?php
$items = & $this->items;

// -- Check matching items found
if (!$items) {
	// No items exist
	if ($this->getModel()->getState('limit')) {
		// Not creating a category view without items
		echo '<div class="noitems group">' . JText::_( 'FLEXI_NO_ITEMS_FOUND' ) . '</div>';
	}
	return;
}


// -- Decide whether to show the item edit options
if ( $user->id ) :
	
	$show_editbutton = $this->params->get('show_editbutton', 1);
	foreach ($items as $item) :
	
		if ( $show_editbutton ) :
			if ($item->editbutton = flexicontent_html::editbutton( $item, $this->params )) :
				$item->editbutton = '<div class="fc_edit_link_nopad">'.$item->editbutton.'</div>';
			endif;
			if ($item->statebutton = flexicontent_html::statebutton( $item, $this->params )) :
				$item->statebutton = '<div class="fc_state_toggle_link_nopad">'.$item->statebutton.'</div>';
			endif;
		endif;
		
		if ($item->approvalbutton = flexicontent_html::approvalbutton( $item, $this->params )) :
			$item->approvalbutton = '<div class="fc_approval_request_link_nopad">'.$item->approvalbutton.'</div>';
		endif;
		
	endforeach;
	
endif;

// -- Find all categories used by items
$currcatid = $this->category->id;
$cat_items[$currcatid] = array();
$sub_cats[$currcatid] = & $this->category;
foreach ($this->categories as $subindex => $sub) :
	$cat_items[$sub->id] = array();
	$sub_cats[$sub->id] = & $this->categories[$subindex];
endforeach;

// -- Group items into categories
for ($i=0; $i<count($items); $i++) :
	foreach ($items[$i]->cats as $cat) :
		if (isset($cat_items[$cat->id])) :
			$cat_items[$cat->id][] = & $items[$i];
		endif;
	endforeach;
endfor;


// -- Decide CSS classes
$tmpl_cols = $this->params->get('tmpl_cols', 2);
$tmpl_cols_classes = array(1=>'one',2=>'two',3=>'three',4=>'four');
$classnum = $tmpl_cols_classes[$tmpl_cols];
// bootstrap span
$tmpl_cols_spanclasses = array(1=>'span12',2=>'span6',3=>'span4',4=>'span3');
$classspan = $tmpl_cols_spanclasses[$tmpl_cols];
?>

<ul class="faqblock <?php echo $classnum; ?> group row">	

<?php
$show_itemcount   = $this->params->get('show_itemcount', 1);
$show_subcatcount = $this->params->get('show_subcatcount', 0);
$itemcount_label   = ($show_itemcount==2   ? JText::_('FLEXI_ITEM_S') : '');
$subcatcount_label = ($show_subcatcount==2 ? JText::_('FLEXI_CATEGORIES') : '');

$tooltip_class = FLEXI_J30GE ? 'hasTooltip' : 'hasTip';
$_comments_container_params = 'class="fc_comments_count_nopad '.$tooltip_class.'" title="'.flexicontent_html::getToolTip('FLEXI_NUM_OF_COMMENTS', 'FLEXI_NUM_OF_COMMENTS_TIP', 1, 1).'"';

global $globalcats;
$count_cat = -1;
foreach ($cat_items as $catid => $items) :
	$sub = & $sub_cats[$catid];
	if (count($items)==0) continue;
	if ($catid!=$currcatid) $count_cat++;
?>

<li class="<?php echo $catid==$currcatid ? 'full' : ($count_cat%2 ? 'even' : 'odd'); ?> <?php echo $classspan; ?>">
	
	<section class="group">	
		
		<header class="flexi-cat group">

			<?php if (!empty($sub->image) && $this->params->get(($catid!=$currcatid? 'show_description_image_subcat' : 'show_description_image'), 1)) : ?>
				<!-- BOF subcategory image -->
				<figure class="catimg">
					<?php echo $sub->image; ?>
				</figure>
				<!-- EOF subcategory image -->
			<?php endif; ?>

			<?php if ($catid!=$currcatid) { ?> <a class='fc_cat_title' href="<?php echo JRoute::_( FlexicontentHelperRoute::getCategoryRoute($sub->slug) ); ?>"> <?php } else { echo "<span class='fc_cat_title'>"; } ?>
				<!-- BOF subcategory title -->
				<?php echo $sub->title; ?>
				<!-- EOF subcategory title -->
			<?php if ($catid!=$currcatid) { ?> </a> <?php } else { echo "</span>"; } ?>

			<?php if ($catid!=$currcatid) : ?>
				<!-- BOF subcategory assigned/subcats_count  -->
				<?php
				$infocount_str = '';
				if ($show_itemcount)   $infocount_str .= (int) $sub->assigneditems . $itemcount_label;
				if ($show_subcatcount) $infocount_str .= ($show_itemcount ? ' / ' : '').count($sub->subcats) . $subcatcount_label;
				if ($infocount_str) $infocount_str = ' (' . $infocount_str . ')';
				?>
				<!-- EOF subcategory assigned/subcats_count -->
			<?php endif; ?>

			<?php if ($this->params->get(($catid!=$currcatid? 'show_description_subcat' : 'show_description'), 1)) : ?>
				<!-- BOF subcategory description  -->
				<div class="catdescription group">
					<?php	echo flexicontent_html::striptagsandcut( $sub->description, $this->params->get(($catid!=$currcatid? 'description_cut_text_subcat' : 'description_cut_text'), 120) ); ?>
				</div>
				<!-- EOF subcategory description -->
			<?php endif; ?>
			
		</header>


		<?php if ( $items ) : ?>
			<!-- BOF subcategory items -->
			<div class="group">
				<ul class="flexi-itemlist">
			
				<?php foreach ($items as $i => $item) : ?>
					<?php
					$fc_item_classes = 'flexi-item';
					
					$markup_tags = '<span class="fc_mublock">';
					foreach($item->css_markups as $grp => $css_markups) {
						if ( empty($css_markups) )  continue;
						$fc_item_classes .= ' fc'.implode(' fc', $css_markups);
						
						$ecss_markups  = $item->ecss_markups[$grp];
						$title_markups = $item->title_markups[$grp];
						foreach($css_markups as $mui => $css_markup) {
							$markup_tags .= '<span class="fc_markup mu' . $css_markups[$mui] . $ecss_markups[$mui] .'">' .$title_markups[$mui]. '</span>';
						}
					}
					$markup_tags .= '</span>';
					?>
					<li id="faqlist_cat_<?php echo $catid; ?>item_<?php echo $i; ?>" class="<?php echo $fc_item_classes; ?>">
						
					  <?php if ($item->event->beforeDisplayContent) : ?>
					  <!-- BOF beforeDisplayContent -->
						<aside class="fc_beforeDisplayContent group">
							<?php echo $item->event->beforeDisplayContent; ?>
						</aside>
					  <!-- EOF beforeDisplayContent -->
						<?php endif; ?>

					
						<ul class="flexi-fieldlist">
				   		<li class="flexi-field flexi-title">
								<i class="icon-arrow-right"></i>

				   			<?php echo @ $item->editbutton; ?>
				   			<?php echo @ $item->statebutton; ?>
				   			<?php echo @ $item->approvalbutton; ?>
								
								<?php if ($this->params->get('show_comments_count')) : ?>
									<?php if ( isset($this->comments[ $item->id ]->total) ) : ?>
									<div <?php echo $_comments_container_params; ?> >
										<?php echo $this->comments[ $item->id ]->total; ?>
									</div>
									<?php endif; ?>
								<?php endif; ?>
						
								<?php if ($this->params->get('show_title', 1)) : ?>
									<!-- BOF item title -->
									<?php if ($this->params->get('link_titles', 0)) : ?>
						   			<a class="fc_item_title" href="<?php echo JRoute::_(FlexicontentHelperRoute::getItemRoute($item->slug, $item->categoryslug, 0, $item)); ?>">
											<?php echo $item->title; ?>
										</a>
					   			<?php else : ?>
										<?php echo $item->title; ?>
									<?php endif; ?>
									<!-- BOF item title -->
								<?php endif; ?>
								
								<div class="clear"></div>
								<?php echo $markup_tags; ?>
						
								<?php if ($item->event->afterDisplayTitle) : ?>
									<!-- BOF afterDisplayTitle -->
									<div class="fc_afterDisplayTitle group">
										<?php echo $item->event->afterDisplayTitle; ?>
									</div>
									<!-- EOF afterDisplayTitle -->
								<?php endif; ?>
								
							</li>
							
							<?php if (isset($item->positions['aftertitle'])) : ?>
								<!-- BOF aftertitle block -->
								<?php foreach ($item->positions['aftertitle'] as $field) : ?>
								<li class="flexi-field">
									<?php if ($field->label) : ?>
									<span class="flexi label field_<?php echo $field->name; ?>"><?php echo $field->label; ?></span>
									<?php endif; ?>
									<div class="flexi value field_<?php echo $field->name; ?>"><?php echo $field->display; ?></div>
								</li>
								<?php endforeach; ?>
								<!-- EOF aftertitle block -->
							<?php endif; ?>
							
							
							<?php if (isset($item->positions['aftertitle_nolabel'])) : ?>
								<!-- BOF aftertitle_nolabel block -->
								<?php foreach ($item->positions['aftertitle_nolabel'] as $field) : ?>
								<li class="flexi-field">
									<div class="flexi value field_<?php echo $field->name; ?>"><?php echo $field->display; ?></div>
								</li>
								<?php endforeach; ?>
								<!-- EOF aftertitle_nolabel block -->
							<?php endif; ?>
							
							
							<?php if (isset($item->positions['aftertitle2'])) : ?>
								<!-- BOF aftertitle block -->
								<?php foreach ($item->positions['aftertitle2'] as $field) : ?>
								<li class="flexi-field">
									<?php if ($field->label) : ?>
									<span class="flexi label field_<?php echo $field->name; ?>"><?php echo $field->label; ?></span>
									<?php endif; ?>
									<div class="flexi value field_<?php echo $field->name; ?>"><?php echo $field->display; ?></div>
								</li>
								<?php endforeach; ?>
								<!-- EOF aftertitle block -->
							<?php endif; ?>
							
							
							<?php if (isset($item->positions['aftertitle_nolabel2'])) : ?>
								<!-- BOF aftertitle_nolabel block -->
								<?php foreach ($item->positions['aftertitle_nolabel2'] as $field) : ?>
								<li class="flexi-field">
									<div class="flexi value field_<?php echo $field->name; ?>"><?php echo $field->display; ?></div>
								</li>
								<?php endforeach; ?>
								<!-- EOF aftertitle_nolabel block -->
							<?php endif; ?>
							
							
							<?php if (isset($item->positions['aftertitle3'])) : ?>
								<!-- BOF aftertitle block -->
								<?php foreach ($item->positions['aftertitle3'] as $field) : ?>
								<li class="flexi-field">
									<?php if ($field->label) : ?>
									<span class="flexi label field_<?php echo $field->name; ?>"><?php echo $field->label; ?></span>
									<?php endif; ?>
									<div class="flexi value field_<?php echo $field->name; ?>"><?php echo $field->display; ?></div>
								</li>
								<?php endforeach; ?>
								<!-- EOF aftertitle block -->
							<?php endif; ?>
							
							
							<?php if (isset($item->positions['aftertitle_nolabel3'])) : ?>
								<!-- BOF aftertitle_nolabel block -->
								<?php foreach ($item->positions['aftertitle_nolabel3'] as $field) : ?>
								<li class="flexi-field">
									<div class="flexi value field_<?php echo $field->name; ?>"><?php echo $field->display; ?></div>
								</li>
								<?php endforeach; ?>
								<!-- EOF aftertitle_nolabel block -->
							<?php endif; ?>
							
						</ul>


						<?php if ($item->event->afterDisplayContent) : ?>
							<!-- BOF afterDisplayContent -->
							<aside class="fc_afterDisplayContent group">
								<?php echo $item->event->afterDisplayContent; ?>
							</aside>
							<!-- EOF afterDisplayContent -->
						<?php endif; ?>

					</li>
				<?php endforeach; ?>

				</ul>
			</div>
			<!-- EOF subcategory items -->
		<?php endif; ?>

	</section>
</li>

		
<?php endforeach; ?>

</ul>

