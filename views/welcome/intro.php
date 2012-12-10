<?php
$this->htmlOpen ();
$this->htmlHead ( 'Welcome to CobWeb Framework ' . cw_version );
?>
<link rel="stylesheet" type="text/css" media="screen"
    href="/<?php
				echo cw_webroot;
				?>framework/meta/cobweb_internal.css" />
<?php
$this->htmlBody ();
?>
<div class="welcome_wrapper">
<div class="logo"><img
    src="/<?php
				echo cw_webroot;
				?>framework/meta/cw_logo_big.jpg"
    alt="CobWeb Framework" /></div>
<div class="introtext">
<h1>Welcome to CobWeb Framework <?php
echo cw_version?></h1>
<p>If you can see this message, it means CobWeb is successfully running
in your Environment. If this is the first time you're seeing this page
skip to diagnostics to see whether there are problems with your CobWeb
installation</p>
<h1>What to do next?</h1>
<p>Now you can...</p>
<ul>
    <li>Create CobWeb controllers, appclasses or views</li>
    <li>Specify routes to your controllers in <span class="code">cw.routes.php</span></li>
    <li>Adjust your configuration in <span class="code">cw.configuration.php</span></li>
    <li>Learn more about CobWeb</li>
</ul>
</div>
<div class="diagnostics">
<h1>Diagnostics</h1>
<ul>
    <li>.htaccess files integrity - <?php
				if (isset ( $this->data ['diagnostics'] ['integrity'] )) {
					?><span class="failed">FAILED</span><?php
				} else {
					?><span class="passed">check passed</span><?php
				}
				?></li>
    <li><span class="code">cache/</span> and <span class="code">log/</span>
    dir and <span class="code">configuration.php</span> writeable - <?php
				if (isset ( $this->data ['diagnostics'] ['permission'] )) {
					?><span class="failed">FAILED</span><?php
				} else {
					?><span class="passed">check passed</span><?php
				}
				?></li>
    <li>Security Checks - <?php
				if (isset ( $this->data ['diagnostics'] ['security'] )) {
					?><span class="failed">FAILED</span><?php
				} else {
					?><span class="passed">check passed</span><?php
				}
				?></li>
</ul>
</div>
</div>
<?php
$this->htmlClose ()?>