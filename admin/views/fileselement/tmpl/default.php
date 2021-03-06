<?php
/**
 * @version 1.5 stable $Id: default.php 1929 2014-07-08 17:04:16Z ggppdk $
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

$tip_class = FLEXI_J30GE ? ' hasTooltip' : ' hasTip';
$btn_class = FLEXI_J30GE ? 'btn' : 'fc_button fcsimple';
$hint_image = JHTML::image ( 'administrator/components/com_flexicontent/assets/images/comment.png', JText::_( 'FLEXI_NOTES' ), 'style="vertical-align:top;"' );
$warn_image = JHTML::image ( 'components/com_flexicontent/assets/images/warning.png', JText::_( 'FLEXI_NOTES' ), 'style="vertical-align:top;"' );

$start_text = '<span class="label">'.JText::_('FLEXI_COLUMNS', true).'</span>';
$end_text = '<div class="icon-arrow-up-2" title="'.JText::_('FLEXI_HIDE').'" style="cursor: pointer;" onclick="fc_toggle_box_via_btn(\\\'mainChooseColBox\\\', document.getElementById(\\\'fc_mainChooseColBox_btn\\\'), \\\'btn-primary\\\');"></div>';
flexicontent_html::jscode_to_showhide_table('mainChooseColBox', 'adminListTableFCfileselement_default'.$this->fieldid, $start_text, $end_text);
$ctrl_task  = FLEXI_J16GE ? 'task=filemanager.'  :  'controller=filemanager&amp;task=';
$del_task   = FLEXI_J16GE ? 'filemanager.remove'  :  'remove';
$session = JFactory::getSession();
$document = JFactory::getDocument();

$close_btn = FLEXI_J30GE ? '<a class="close" data-dismiss="alert">&#215;</a>' : '<a class="fc-close" onclick="this.parentNode.parentNode.removeChild(this.parentNode);">&#215;</a>';
$alert_box = FLEXI_J30GE ? '<div %s class="alert alert-%s %s">'.$close_btn.'%s</div>' : '<div %s class="fc-mssg fc-%s %s">'.$close_btn.'%s</div>';

// Load JS tabber lib
$document->addScript(JURI::root(true).'/components/com_flexicontent/assets/js/tabber-minimized.js');
$document->addStyleSheet(JURI::root(true).'/components/com_flexicontent/assets/css/tabber.css');
$document->addScriptDeclaration(' document.write(\'<style type="text/css">.fctabber{display:none;}<\/style>\'); ');  // temporarily hide the tabbers until javascript runs

$list_total_cols = $this->folder_mode ? 12 : 13;
$flexi_select = JText::_('FLEXI_SELECT');
?>
<script type="text/javascript">

jQuery(document).ready(function() {
	fc_toggle_box_via_btn('uploader_tabset', this, 'btn-primary');
});

// delete active filter
function delFilter(name)
{
	//if(window.console) window.console.log('Clearing filter:'+name);
	var myForm = jQuery('#adminForm');
	var filter = jQuery('#'+name);
	if (filter.length==0) return;
	if (filter.attr('type')=='checkbox')
		filter.checked = '';
	else
		filter.val('');
}

function delAllFilters() {
	delFilter('search');
	delFilter('filter_lang');
	delFilter('filter_uploader');
	delFilter('filter_url');
	delFilter('filter_secure');
	delFilter('filter_ext');
	delFilter('item_id');
}

</script>

<div id="flexicontent" class="flexicontent">

<?php if (!$this->CanUpload) :?>
	<?php echo sprintf( $alert_box, '', 'note', '', JText::_('FLEXI_YOUR_ACCOUNT_CANNOT_UPLOAD') ); ?>
<?php endif; ?>

<div class="fctabber" id="uploader_tabset" style="">
	<?php
	//echo FLEXI_J16GE ? JHtml::_('tabs.start') : $this->pane->startPane( 'stat-pane' );
	?>
	
	<!-- File(s) by uploading -->
	
	<?php if ($this->CanUpload):
		//echo FLEXI_J16GE ?
		//	JHtml::_('tabs.panel', JText::_( 'FLEXI_UPLOAD_LOCAL_FILE' ), 'local' ) :
		//	$this->pane->startPanel( JText::_( 'FLEXI_UPLOAD_LOCAL_FILE' ), 'local' ) ;
	?>
	<div class="tabbertab" style="padding: 0px;" id="local_tab" data-icon-class="icon-upload">
		<h3 class="tabberheading"> <?php echo JText::_( 'FLEXI_UPLOAD_LOCAL_FILE' ); ?> </h3>
	
	<!-- File Upload Form -->
	<fieldset class="filemanager-tab" >
		<?php
		// Configuration
		$upload_maxsize = $this->params->get('upload_maxsize');
		$phpUploadLimit = flexicontent_upload::getPHPuploadLimit();
		$server_limit_exceeded = $phpUploadLimit['value'] < $upload_maxsize;
		
		$conf_limit_class = $server_limit_exceeded ? '' : 'badge-success';
		$conf_limit_style = $server_limit_exceeded ? 'text-decoration: line-through;' : '';
		$conf_lim_image   = $server_limit_exceeded ? $warn_image.$hint_image : $hint_image;
		$sys_limit_class  = $server_limit_exceeded ? 'badge-important' : '';
		
		echo '
		<span class="fc-fileman-upload-limits-box">
			<span class="label label-info">'.JText::_( 'FLEXI_UPLOAD_LIMITS' ).'</span>
			<span class="fc-sys-upload-limit-box">
				<span class="'.$tip_class.'" style="margin-left:24px;" title="'.flexicontent_html::getToolTip('FLEXI_CONF_UPLOAD_MAX_LIMIT', 'FLEXI_CONF_UPLOAD_MAX_LIMIT_DESC', 1, 1).'">'.$conf_lim_image.'</span>
				<span class="badge '.$conf_limit_class.'" style="'.$conf_limit_style.'">'.round($upload_maxsize / (1024*1024), 2).' M </span>
			</span>
			<span class="fc-php-upload-limit-box">
				<span class="'.$tip_class.'" style="margin-left:24px;" title="'.flexicontent_html::getToolTip(JText::_('FLEXI_SERVER_UPLOAD_MAX_LIMIT'), JText::sprintf('FLEXI_SERVER_UPLOAD_MAX_LIMIT_DESC', $phpUploadLimit['name']), 0, 1).'">'.$hint_image.'</span>
				<span class="badge '.$sys_limit_class.'">'.round($phpUploadLimit['value'] / (1024*1024), 2).' M </span>
			</span>
		</span>
		';
		?>
		
		<fieldset class="actions" id="filemanager-1">
			<form action="<?php echo JURI::base(); ?>index.php?option=com_flexicontent&amp;<?php echo $ctrl_task; ?>upload&amp;<?php echo $session->getName().'='.$session->getId(); ?>" name="uploadFileForm" id="uploadFileForm" method="post" enctype="multipart/form-data">
				
				<table class="fc-form-tbl" cellspacing="0" cellpadding="0" border="0" id="file-upload-form-container">
					
					<tr>
						<td id="file-upload-lbl-container" class="key <?php echo $tip_class; ?>" title="<?php echo flexicontent_html::getToolTip('FLEXI_CHOOSE_FILE', 'FLEXI_CHOOSE_FILE_DESC', 1, 1); ?>">
							<label class="label" id="file-upload-lbl" for="file-upload">
							<?php echo JText::_( 'FLEXI_CHOOSE_FILE' ); ?>
							</label>
						</td>
						<td id="file-upload-container">
							<div id="img_preview_msg" style="float:left;"></div>
							<img id="img_preview" src="" style="float:left; display:none;" />
							<input type="file" id="file-upload" name="Filedata" onchange="fc_loadImagePreview(this.id,'img_preview', 'img_preview_msg', 100, 0, '-1');" />
						</td>
						
	<?php if (!$this->folder_mode) { ?>
						<td id="file-title-lbl-container" class="key <?php echo $tip_class; ?>" title="<?php echo flexicontent_html::getToolTip('FLEXI_FILE_DISPLAY_TITLE', 'FLEXI_FILE_DISPLAY_TITLE_DESC', 1, 1); ?>">
							<label class="label" id="file-title-lbl" for="file-title">
							<?php echo JText::_( 'FLEXI_FILE_DISPLAY_TITLE' ); ?>
							</label>
						</td>
						<td id="file-title-container">
							<input type="text" id="file-title" size="44" class="required" name="file-title" />
						</td>
	<?php } ?>
					</tr>
	<?php if (!$this->folder_mode) { ?>
					<tr>
						<td id="file-lang-lbl-container" class="key <?php echo $tip_class; ?>" title="<?php echo flexicontent_html::getToolTip('FLEXI_LANGUAGE', 'FLEXI_FILE_LANGUAGE_DESC', 1, 1); ?>">
							<label class="label" id="file-lang-lbl" for="file-lang">
							<?php echo JText::_( 'FLEXI_LANGUAGE' ); ?>
							</label>
						</td>
						<td id="file-lang-container">
							<?php echo $this->lists['file-lang']; ?>
						</td>
						
						<td id="file-desc-lbl-container" rowspan="2" class="key <?php echo $tip_class; ?>" title="<?php echo flexicontent_html::getToolTip('FLEXI_DESCRIPTION', 'FLEXI_FILE_DESCRIPTION_DESC', 1, 1); ?>">
							<label class="label" id="file-desc-lbl" for="file-desc_uploadFileForm">
							<?php echo JText::_( 'FLEXI_DESCRIPTION' ); ?>
							</label>
						</td>
						<td id="file-desc-container" valign="top" rowspan="2">
							<textarea name="file-desc" cols="24" rows="3" id="file-desc_uploadFileForm"></textarea>
						</td>
					</tr>
					
		<?php if ($this->target_dir==2) : ?>
					<tr>
						<td id="secure-lbl-container" class="key <?php echo $tip_class; ?>" data-placement="bottom" title="<?php echo flexicontent_html::getToolTip('FLEXI_CHOOSE_DIRECTORY', 'FLEXI_CHOOSE_DIRECTORY_DESC', 1, 1); ?>">
							<label class="label" id="secure-lbl">
							<?php echo JText::_( 'FLEXI_TARGET_DIRECTORY' ); ?>
							</label>
						</td>
						<td id="secure-container">
							<?php
							//echo JHTML::_('select.booleanlist', 'secure', 'class="inputbox"', 1, JText::_( 'FLEXI_SECURE' ), JText::_( 'FLEXI_MEDIA' ), 'secure_uploadFileForm' );
							$_options = array();
							$_options['0'] = JText::_( 'FLEXI_MEDIA' );
							$_options['1'] = JText::_( 'FLEXI_SECURE' );
							echo flexicontent_html::buildradiochecklist($_options, 'secure', /*selected*/1, /*type*/1, /*attribs*/'', /*tagid*/'secure_uploadFileForm');
							?>
						</td>
					</tr>
		<?php endif; ?>
	<?php } ?>
				</table>
				
				<input type="submit" id="file-upload-submit" class="fc_button fcsimple" value="<?php echo JText::_( 'FLEXI_START_UPLOAD' ); ?>"/>
				<span id="upload-clear"></span>
				
				<?php echo JHTML::_( 'form.token' ); ?>
				<input type="hidden" name="fieldid" value="<?php echo $this->fieldid; ?>" />
				<input type="hidden" name="u_item_id" value="<?php echo $this->u_item_id; ?>" />
				<input type="hidden" name="folder_mode" value="<?php echo $this->folder_mode; ?>" />
				<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_flexicontent&view=fileselement&tmpl=component&field='.$this->fieldid.'&folder_mode='.$this->folder_mode); ?>" />
			</form>
			
		</fieldset>
		
	</fieldset>
	
	</div>
	<?php
	//echo FLEXI_J16GE ? '' : $this->pane->endPanel();
	?>
	<?php endif; /*CanUpload*/ ?>
	
	
	<!-- File URL by Form -->
	<?php
		//echo FLEXI_J16GE ?
		//	JHtml::_('tabs.panel', JText::_( 'FLEXI_ADD_FILE_BY_URL' ), 'fileurl' ) :
		//	$this->pane->startPanel( JText::_( 'FLEXI_ADD_FILE_BY_URL' ), 'fileurl' ) ;
	?>
	<div class="tabbertab" style="padding: 0px;" id="fileurl_tab" data-icon-class="icon-out">
		<h3 class="tabberheading"> <?php echo JText::_( 'FLEXI_ADD_FILE_BY_URL' ); ?> </h3>
	
	<form action="<?php echo JURI::base(); ?>index.php?option=com_flexicontent&amp;<?php echo $ctrl_task; ?>addurl&amp;<?php echo $session->getName().'='.$session->getId(); ?>&amp;<?php echo (FLEXI_J30GE ? JSession::getFormToken() : JUtility::getToken());?>=1" class="form-validate" name="addUrlForm" id="addUrlForm" method="post">
		<fieldset class="filemanager-tab" >
			<fieldset class="actions" id="filemanager-2">
				
				<table class="fc-form-tbl" cellspacing="0" cellpadding="0" border="0" id="file-url-form-container">
					
					<tr>
						<td class="key <?php echo $tip_class; ?>" title="<?php echo flexicontent_html::getToolTip('FLEXI_FILE_URL', 'FLEXI_FILE_URL_DESC', 1, 1); ?>">
							<label class="label" for="file-url-data">
							<?php echo JText::_( 'FLEXI_FILE_URL' ); ?>
							</label>
						</td>
						<td>
							<input type="text" id="file-url-data" size="44" class="required" name="file-url-data" />
						</td>
						
						<td class="key <?php echo $tip_class; ?>" title="<?php echo flexicontent_html::getToolTip('FLEXI_FILE_DISPLAY_TITLE', 'FLEXI_FILE_DISPLAY_TITLE_DESC', 1, 1); ?>">
							<label class="label" for="file-url-title">
							<?php echo JText::_( 'FLEXI_FILE_DISPLAY_TITLE' ); ?>
							</label>
						</td>
						<td>
							<input type="text" id="file-url-title" size="44" class="required" name="file-url-title" />
						</td>
					</tr>
					
					<tr>
						<td class="key <?php echo $tip_class; ?>" title="<?php echo flexicontent_html::getToolTip('FLEXI_LANGUAGE', 'FLEXI_FILE_LANGUAGE_DESC', 1, 1); ?>">
							<label class="label" id="file-url-lang-lbl" for="file-url-lang">
							<?php echo JText::_( 'FLEXI_LANGUAGE' ); ?>
							</label>
						</td>
						<td>
							<?php echo str_replace('file-lang', 'file-url-lang', $this->lists['file-lang']); ?>
						</td>
						
						<td rowspan="2" class="key <?php echo $tip_class; ?>" title="<?php echo flexicontent_html::getToolTip('FLEXI_DESCRIPTION', 'FLEXI_FILE_DESCRIPTION_DESC', 1, 1); ?>">
							<label class="label" for="file-url-desc">
							<?php echo JText::_( 'FLEXI_DESCRIPTION' ); ?>
							</label>
						</td>
						<td rowspan="2">
							<textarea name="file-url-desc" cols="24" rows="3" id="file-url-desc"></textarea>
						</td>
					</tr>
					
					<tr>
						<td class="key <?php echo $tip_class; ?>" title="<?php echo flexicontent_html::getToolTip('FLEXI_FILEEXT_MIME', 'FLEXI_FILEEXT_MIME_DESC', 1, 1); ?>">
							<label class="label" for="file-url-ext">
							<?php echo JText::_( 'FLEXI_FILEEXT_MIME' ); ?>
							</label>
						</td>
						<td>
							<input type="text" id="file-url-ext" size="5" class="required" name="file-url-ext" />
						</td>
					</tr>
					
				</table>
				
				<input type="submit" id="file-url-submit" class="fc_button fcsimple validate" value="<?php echo JText::_( 'FLEXI_ADD_FILE' ); ?>"/>
			</fieldset>
		</fieldset>
		<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_flexicontent&view=fileselement&field='.$this->fieldid.'&tmpl=component'); ?>" />
	</form>
	<?php /*echo FLEXI_J16GE ? '' : $this->pane->endPanel();*/ ?>
	</div>
	
	
	<?php /*echo FLEXI_J16GE ? JHtml::_('tabs.end') : $this->pane->endPane();*/ ?>
</div><!-- fctabber end -->
	
<div class="fcclear"></div>


<form action="index.php?option=<?php echo $this->option; ?>&view=<?php echo $this->view; ?>&field=<?php echo $this->fieldid?>&amp;tmpl=component" method="post" name="adminForm" id="adminForm">

<?php if (!$this->folder_mode) : ?>
	<div id="fc-filters-header">
		<span class="fc-filter nowrap_box">
			<?php echo $this->lists['scope']; ?>
			<span class="filter-search btn-group">
				<input type="text" name="search" id="search" placeholder="<?php echo JText::_( 'FLEXI_SEARCH' ); ?>" value="<?php echo htmlspecialchars($this->lists['search'], ENT_QUOTES, 'UTF-8'); ?>" class="inputbox" />
			</span>
			<span class="btn-group hidden-phone">
				<button title="<?php echo JText::_('FLEXI_APPLY_FILTERS'); ?>" class="<?php echo $btn_class; ?>" onclick="this.form.submit();"><?php echo FLEXI_J30GE ? '<i class="icon-search"></i>' : JText::_('FLEXI_GO'); ?></button>
				<button title="<?php echo JText::_('FLEXI_RESET_FILTERS'); ?>" class="<?php echo $btn_class; ?>" onclick="delAllFilters();this.form.submit();"><?php echo FLEXI_J30GE ? '<i class="icon-remove"></i>' : JText::_('FLEXI_CLEAR'); ?></button>
			</span>
		</span>
		
		<?php $_class = FLEXI_J30GE ? ' btn' : ' fc_button fcsimple fcsmall'; ?>
		<div class="btn-group" style="margin: 2px 32px 6px -3px; display:inline-block;">
			<input type="button" id="fc_filters_box_btn" class="<?php echo $_class.($this->count_filters ? ' btn-primary' : ''); ?>" onclick="fc_toggle_box_via_btn('fc-filters-box', this, 'btn-primary');" value="<?php echo JText::_( 'FLEXI_FILTERS' ); ?>" />
			<input type="button" id="fc_mainChooseColBox_btn" class="<?php echo $_class; ?>" onclick="fc_toggle_box_via_btn('mainChooseColBox', this, 'btn-primary');" value="<?php echo JText::_( 'FLEXI_COLUMNS' ); ?>" />
			<input type="button" id="fc_upload_box_btn" class="<?php echo $_class; ?> btn-success" onclick="fc_toggle_box_via_btn('uploader_tabset', this, 'btn-primary');" value="<?php echo JText::_( 'FLEXI_UPLOAD' ); ?>" />
		</div>
		
		<div class="fcclear"></div>
		
	<?php if (!$this->folder_mode) : ?>
		<span class="fc-filter nowrap_box">
			<span class="limit nowrap_box" style="display: inline-block;">
				<label class="label">
					<?php echo JText::_(FLEXI_J16GE ? 'JGLOBAL_DISPLAY_NUM' : 'DISPLAY NUM'); ?>
				</label>
				<?php
				$pagination_footer = $this->pagination->getListFooter();
				if (strpos($pagination_footer, '"limit"') === false) echo $this->pagination->getLimitBox();
				?>
			</span>
			
			<span class="fc_item_total_data nowrap_box badge badge-info">
				<?php echo @$this->resultsCounter ? $this->resultsCounter : $this->pagination->getResultsCounter(); // custom Results Counter ?>
			</span>
			
			<?php if (($getPagesCounter = $this->pagination->getPagesCounter())): ?>
			<span class="fc_pages_counter nowrap_box fc-mssg-inline fc-info fc-nobgimage">
				<?php echo $getPagesCounter; ?>
			</span>
			<?php endif; ?>
		</span>
	<?php endif; ?>
		
	<?php if ($this->CanViewAllFiles) : ?>
		<span class="fc-filter nowrap_box fc-mssg-inline">
			<?php echo $this->lists['uploader']; ?>
		</span>
	<?php endif; ?>
		
	</div>
	
	
	<div id="fc-filters-box" <?php if (!$this->count_filters) echo 'style="display:none;"'; ?> class="">
		<!--<span class="label"><?php echo JText::_( 'FLEXI_FILTERS' ); ?></span>-->
		
		<span class="fc-filter nowrap_box">
			<?php echo $this->lists['language']; ?>
		</span>
		
		<span class="fc-filter nowrap_box">
			<?php echo $this->lists['url']; ?>
		</span>
		
		<?php if ($this->target_dir==2) : ?>
		<span class="fc-filter nowrap_box">
			<?php echo $this->lists['secure']; ?>
		</span>
		<?php endif; ?>
		
		<span class="fc-filter nowrap_box">
			<?php echo $this->lists['ext']; ?>
		</span>
		
		<span class="fc-filter nowrap_box">
			<label class="label">Item ID</label> <?php echo $this->lists['item_id']; ?>
		</span>
		
		<div class="icon-arrow-up-2" title="<?php echo JText::_('FLEXI_HIDE'); ?>" style="cursor: pointer;" onclick="fc_toggle_box_via_btn('fc-filters-box', document.getElementById('fc_filters_box_btn'), 'btn-primary');"></div>
	</div>
	
<?php endif; ?>

	<div class="fcclear"></div>
	<div id="mainChooseColBox" class="fc_mini_note_box well well-small" style="display:none;"></div>
	<div class="fcclear"></div>
	
	<table id="adminListTableFCfileselement_default<?php echo $this->fieldid; ?>" class="adminlist fcmanlist">
	<thead>
		<tr>
			<th><?php echo JText::_( 'FLEXI_NUM' ); ?></th>
			<th><input type="checkbox" name="toggle" value="" onclick="<?php echo FLEXI_J30GE ? 'Joomla.checkAll(this);' : 'checkAll('.count( $this->rows).');'; ?>" /></th>
<?php if ($this->folder_mode) { ?>
			<th width="5">&nbsp;</th>
<?php } ?>
			<th class="center hideOnDemandClass"><?php echo JText::_( 'FLEXI_THUMB' ); ?></th>
			<th class="left">
				<?php echo JHTML::_('grid.sort', 'FLEXI_FILENAME', 'f.filename_displayed', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				/
				<?php echo JHTML::_('grid.sort', 'FLEXI_FILE_DISPLAY_TITLE', 'f.altname', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</th>
			<th class="center hideOnDemandClass" nowrap="nowrap"><?php echo JText::_( 'FLEXI_PUBLISHED' ); ?></th>
			<th class="center hideOnDemandClass"><?php echo JText::_( 'FLEXI_ACCESS' ); ?></th>
			<th class="center hideOnDemandClass"><?php echo JText::_( 'FLEXI_LANGUAGE' ); ?></th>
			<th class="center hideOnDemandClass"><?php echo JText::_( 'FLEXI_SIZE' ); ?></th>
			<th class="center hideOnDemandClass"><?php echo JHTML::_('grid.sort', 'FLEXI_HITS', 'f.hits', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="center hideOnDemandClass"><?php echo JHTML::_('grid.sort', 'FLEXI_UPLOADER', 'uploader', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="center hideOnDemandClass"><?php echo JHTML::_('grid.sort', 'FLEXI_UPLOAD_TIME', 'f.uploaded', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
<?php if (!$this->folder_mode) { ?>
			<th class="center hideOnDemandClass" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'FLEXI_ID', 'f.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
<?php } ?>
		</tr>
	</thead>

<?php if (!$this->folder_mode) : ?>
	<tfoot>
		<tr>
			<td colspan="<?php echo $list_total_cols; ?>">
				<?php echo $pagination_footer; ?>
			</td>
		</tr>
	</tfoot>
<?php endif; ?>

	<tbody>
		<?php
		$imageexts = array('jpg','gif','png','bmp','jpeg');
		$index = JRequest::getInt('index', 0);
		$k = 0;
		$i = 0;
		$n = count($this->rows);
		foreach ($this->rows as $row) {
			unset($thumb_or_icon);
			$filename    = str_replace( array("'", "\""), array("\\'", ""), $row->filename );
			$filename_original = $this->folder_mode ? '' : str_replace( array("'", "\""), array("\\'", ""), $row->filename_original );
			$fileid = $this->folder_mode ? '' : $row->id;
			$display_filename  = $filename_original ? $filename_original : $filename;
			
			if ( !in_array(strtolower($row->ext), $imageexts)) $thumb_or_icon = JHTML::image($row->icon, $row->filename);
			
			$checked 	= @ JHTML::_('grid.checkedout', $row, $i );
			
			$path		= $row->secure ? COM_FLEXICONTENT_FILEPATH : COM_FLEXICONTENT_MEDIAPATH;  // JPATH_ROOT . DS . <media_path | file_path>
			$file_path = $row->filename;
			
			if ($this->folder_mode) {
				$file_path = $this->img_folder . DS . $row->filename;
			} else
			
			if (!$row->url && substr($row->filename, 0, 7)!='http://') {
				$file_path = $path . DS . $row->filename;
			} else {
				$thumb_or_icon = 'URL';
			}
			
			$file_path = str_replace('\\', '/', $file_path);
			if ( empty($thumb_or_icon) ) {
				if (file_exists($file_path)){
					$thumb_or_icon = '<img src="'.JURI::root().'components/com_flexicontent/librairies/phpthumb/phpThumb.php?src='.$file_path.'&w=60&h=60" alt="'.$display_filename.'" />';
				} else {
					$thumb_or_icon = '<span class="badge badge-important">'.JText::_('FLEXI_FILE_NOT_FOUND').'</span>';
				}
			}
			$file_preview = JURI::root() . 'components/com_flexicontent/librairies/phpthumb/phpThumb.php?src=' . $file_path . '&w='.$this->thumb_w.'&h='.$this->thumb_h;
			$file_preview2 = JURI::root() . 'components/com_flexicontent/librairies/phpthumb/phpThumb.php?src=' . $file_path . '&w=120&h=90';
			if ($this->folder_mode) {
				$img_assign_link = "window.parent.qmAssignFile".$this->fieldid."('".$this->targetid."', '".$filename."', '".$file_preview."');document.getElementById('file{$i}').className='striketext';";
			} else {
				$img_assign_link = "qffileselementadd(document.getElementById('file".$row->id."'), '".$row->id."', '".$display_filename."');";
			}
   		?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td>
				<?php echo $checked; ?>
			</td>
<?php if ($this->folder_mode) { ?>
			<td>
				<a href="javascript:;" onclick="if (confirm('<?php echo JText::_('FLEXI_SURE_TO_DELETE_FILE'); ?>')) { document.adminForm.filename.value='<?php echo $row->filename;?>'; document.adminForm.controller.value='filemanager'; <?php echo FLEXI_J16GE ? "Joomla." : ""; ?>submitbutton('<?php echo $del_task; ?>'); }" href="#">
				<?php echo JHTML::image('components/com_flexicontent/assets/images/trash.png', JText::_('FLEXI_REMOVE') ); ?>
				</a>
			</td>
<?php } ?>
			<td align="center">
				<a style="cursor:pointer" class="<?php echo $tip_class; ?>" onclick="<?php echo $img_assign_link; ?>" title="<?php echo $flexi_select; ?>">
					<?php echo $thumb_or_icon; ?>
				</a>
			</td>
			<td align="left">
				<?php
					if (JString::strlen($display_filename) > 100) {
						$filename_cut = JString::substr( htmlspecialchars($display_filename, ENT_QUOTES, 'UTF-8'), 0 , 100).'...';
					} else {
						$filename_cut = htmlspecialchars($display_filename, ENT_QUOTES, 'UTF-8');
					}
				?>
					<a style="cursor:pointer" id="file<?php echo $row->id;?>" class="<?php echo $btn_class.' '.$tip_class; ?> btn-small" prv="<?php echo $file_preview2;?>" data-fileid="<?php echo $fileid; ?>" data-filename="<?php echo $filename; ?>" onclick="<?php echo $img_assign_link; ?> "title="<?php echo $flexi_select; ?>">
						<?php echo $filename_cut; ?>
					</a>
				
				<?php
				if (!$this->folder_mode && $row->altname != $row->filename_displayed) {
					if (JString::strlen($row->altname) > 100) {
						echo '<br/><span>'.JString::substr( htmlspecialchars($row->altname, ENT_QUOTES, 'UTF-8'), 0 , 100).'... </span>';
					} else {
						echo '<br/><span>'.htmlspecialchars($row->altname, ENT_QUOTES, 'UTF-8').'</span>';
					}
				}
				?>
			</td>
<?php if (!$this->folder_mode) { ?>
			<td align="center">
				<?php echo JHTML::image('components/com_flexicontent/assets/images/'. ($row->published ? 'tick.png' : 'publish_x.png'), JText::_('FLEXI_REMOVE') ); ?>
			</td>
<?php } ?>
			
			<td align="center">
			<?php
			$is_authorised = $this->CanFiles && ($this->CanViewAllFiles || $user->id == $row->uploaded_by);
			if ($is_authorised) {
				$access = flexicontent_html::userlevel('access['.$row->id.']', $row->access, 'onchange="return listItemTask(\'cb'.$i.'\',\'filemanager.access\')"');
			} else {
				$access = strlen($row->access_level) ? $this->escape($row->access_level) : '-';
			}
			echo $access;
			?>
			</td>
			
<?php if (!$this->folder_mode) { ?>
			<?php
			// Set a row language, even if empty to avoid errors
			$row->language = @$row->language ? $row->language : '*';
   		?>
			<td align="center" class="hasTip col_lang" title="<?php echo JText::_( 'FLEXI_LANGUAGE', true ).'::'.($row->language=='*' ? JText::_("All") : $this->langs->{$row->language}->name); ?>">
				<?php if ( !empty($row->language) && !empty($this->langs->{$row->language}->imgsrc) ) : ?>
					<img src="<?php echo $this->langs->{$row->language}->imgsrc; ?>" alt="<?php echo $row->language; ?>" />
				<?php elseif( !empty($row->language) ) : ?>
					<?php echo $row->language=='*' ? JText::_("FLEXI_ALL") : $row->language;?>
				<?php endif; ?>
			</td>
<?php } ?>
			
			<td align="center"><?php echo $row->size; ?></td>
			<td align="center"><?php echo $row->hits; ?></td>
			<td align="center"><?php echo $row->uploader; ?></td>
			<td align="center"><?php echo JHTML::Date( $row->uploaded, JText::_( 'DATE_FORMAT_LC4' )." H:i:s" ); ?></td>
<?php if (!$this->folder_mode) { ?>
			<td align="center"><?php echo $row->id; ?></td>
<?php } ?>
		</tr>
		<?php 
			$k = 1 - $k;
			$i++;
		} 
		?>
	</tbody>

	</table>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="filemanager" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="file" value="" />
	<input type="hidden" name="files" value="<?php echo $this->files; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<input type="hidden" name="fieldid" value="<?php echo $this->fieldid; ?>" />
	<input type="hidden" name="u_item_id" value="<?php echo $this->u_item_id; ?>" />
	<input type="hidden" name="folder_mode" value="<?php echo $this->folder_mode; ?>" />
	<input type="hidden" name="filename" value="" />
</form>
</div>