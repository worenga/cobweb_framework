<?php
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
?>
<?php

$this->htmlOpen();
?>
<?php

if (! isset($this->data['pageTitle'])) {
    CobWeb::o('Console')->notice('standard.php - No Pagetitle');
    $title = 'Unnamed';
} else {
    $title = $this->data['pageTitle'];
}
$this->htmlHead($title);
?>
<?php

$this->htmlIncludeCss('default.css');
?>
<?php

$this->htmlIncludeJquery();
?>
<?php

$this->htmlIncludeRequiredScripts();
?>
<?php

$this->htmlBody();
?>
<div id="page_wrapper">
	<?php
if ($this->data['notice']) {
    ?>
	<div class="notice">
	<?php
    echo $this->data['notice'];
    ?>
	</div>
	<?php
}
?>
	<?php
echo $this->data['form'];
?>
</div>
<?php
$this->htmlClose();
?>