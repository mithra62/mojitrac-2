<?php
class Zend_View_Helper_ConfirmPageUnload
{
	function ConfirmPageUnload($id)
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
