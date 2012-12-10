//Reload Button for Captcha Images
$(document).ready(function() {
	$(".cw_captcha_regenerate").click(
			function() {
				elementId = this.id;
				fieldId = elementId.replace(/cw_regenerate_/, '');
				imgId = 'cw_captchaimg_' + fieldId;
				imgSrc = $('#' + imgId).attr('src').match(/(.*)\//);
				$('#' + imgId).attr('src',
						imgSrc[0] + '?ts=' + (new Date()).getTime());
				$('#' + imgId).load(function() {
					$('#' + elementId).show();
					$('#cw_regenerate_wait_' + fieldId).hide();
				});
				$('#' + elementId).hide();
				$('#cw_regenerate_wait_' + fieldId).show();
			});
});