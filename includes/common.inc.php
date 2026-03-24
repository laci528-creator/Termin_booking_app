<?php
function ta(mixed $in):void {
	if(TESTBETRIEB) {
		echo('<pre class="ta">');
		print_r($in);
		echo('</pre>');
	}
}
?>