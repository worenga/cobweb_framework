<?php 
if (! defined('cw_inc')) {
    echo 'CobWeb: Cannot load files from the outdoor application context. Exit';
    exit(1);
}
?>
<?php $this->htmlOpen(); ?>
<?php $this->htmlHead('CobWeb Message'); ?>
<link rel="stylesheet" type="text/css" media="screen"
    href="/<?php
                echo cw_webroot;
                ?>framework/meta/cobweb_internal.css" />
<?php $this->htmlBody() ?>
<div class="welcome_wrapper">
<div class="logo"><img
    src="/<?php
                echo cw_webroot;
                ?>framework/meta/cw_logo_big.jpg"
    alt="CobWeb Framework" /></div>
<div class="alert">Controller wurde nicht gefunden!</div>
</div>

<?php $this->htmlClose() ?>