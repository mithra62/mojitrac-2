<?php 
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
* @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/PM/View/Helper/ConfirmPageUnload.php
 */

namespace PM\View\Helper;

use Base\View\Helper\BaseViewHelper;

/**
 * PM - Confirm Page Unload View Helper
 *
 * @package 	ViewHelpers\HTML
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/PM/View/Helper/ConfirmPageUnload.php
 */
class ConfirmPageUnload extends BaseViewHelper
{
	public function __invoke($id)
	{
		$doc_id = 'document.'.str_replace('#', '', $id);
		$data = <<<HTML
		
<script type="text/javascript" charset="utf-8">
function setConfirmUnload(on) {
     window.onbeforeunload = (on) ? unloadMessage : null;
}

function unloadMessage() {
     return 'You have entered new data on this page.  If you navigate away from this page without first saving your data, the changes will be lost.';
}

jQuery(document).ready(function($){
	$("$id").submit(function() {
		window.onbeforeunload = null;
	});
	
	$(':input',$doc_id).bind("change", function() {
		setConfirmUnload(true);
	});	
});
</script>		
HTML;
	return $data;

	}
}
?>
