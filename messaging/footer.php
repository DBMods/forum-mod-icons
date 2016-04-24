<?php
if (count(get_included_files()) == 1) {
	die('Insufficient permissions');
}
?>
</div>
<div id='nav'>
	<a class='button wide blue' href='https://www.techgeek01.com/dropboxextplus/compose.php'>Compose</a><br>
	<?php
	//getMessages();
	echo linkActivity('', true, 'Inbox' . $countBadge, 'index.php') . '<br>';
	//echo linkActivity('sent.php', true, 'Sent') . '<br>';
	//echo linkActivity('archive.php', true, 'Archive' . $archBadge) . '<br>';
	//echo linkActivity('settings.php', true, 'Settings') . '<br>';
	//echo linkActivity('stats.php', true, 'Stats');
	if ($userIsAdmin) {
		echo '<br>' . linkActivity('admin/', false, 'Admin');
	}
	?>
</div>
<div class='clearfix'></div>
</div>
<footer class='columnbox'>
	<div class='column'>
		<strong>Dropbox</strong><br>
		<a href='https://www.dropbox.com'>Dropbox</a><br>
		<a href='https://www.dropboxforum.com'>Dropbox Forums</a>
	</div>
	<div class='column'>
		<strong>Extender+</strong><br>
		<a href='https://www.dropboxforum.com/hc/en-us/community/posts/201168809-Dropbox-Forum-Extender-for-Greasemonkey'>Official Thread</a><br>
		<a href='https://www.github.com/DBMods/forum-extender-plus'>GitHub</a>
	</div>
</footer>
</div>
<div id='attrib' class='tab blue'>
	<div id='attribmore'>
		This messaging system has been designed and coded with love from the DBMods team!<br>
		<small><br>Forum Extender+ - <small>EST.</small> some time in the past <span style='color:#ccc'>(May 23, 2013)</span></small>
	</div>
	<footer>
		Proudly developed by <a href="http://techgeek01.com" target='_blank'>Andy Y.</a> and <a href="http://nathancheek.com" target='_blank'>Nathan C.</a>
		<!--<form action="compose.php" method="post" class="form-link">
			<input type="hidden" name="msgto" value="TechGeek01" />
			<input type="hidden" name="subject" value="Bug report: " />
			<button type="submit" class="btn-link">Problem?</button>
		</form>-->
	</footer>
</div>

<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'></script>
<script>
	var pageUrl = '<?php echo $pageName; ?>';
	var archAct;

	var selectedList = [];

	if (pageUrl == 'index.php' || pageUrl == 'archive.php') {
		archAct = pageUrl == 'index.php' ? 'arch' : 'unarch';
		$('#archForm input[name="action"]').val(archAct);

		//initialize buttons
		$('#viewBtn, #repBtn, #fwdBtn').addClass('grayed');

		//Select message when clicking on message
		$('table tr td:not(.check)').on('click', function() {
			var id = $(this).parent().attr('data-id');

			//Highlight selection
			$('table tr.highlighted .check input').prop('checked', false);
			$('table tr.highlighted').removeClass('highlighted');
			$(this).parent().addClass('highlighted');
			$(this).parent().find('.check input').prop('checked', 'checked');

			selectedList = [id];

			//Add ID to forms for manipulation
			$('#viewForm input[name="msgid"], #replyForm input[name="msgid"], #archForm input[name="msgid"], #delForm input[name="msgid"]').val(selectedList);

			//Enable buttons
			$('#viewBtn, #repBtn, #fwdBtn').removeClass('grayed');
			$('#messageActionButtons').show();
		});

		//Add message to selection when clicked on checkbox
		$('table tr .check input').on('click', function() {
			var id = $(this).parent().parent().attr('data-id');

			//Highlight message appropriately, and change selection list
			if ($(this).prop('checked')) {
				$(this).parent().parent().addClass('highlighted');
				selectedList.push(id);
			} else {
				$(this).parent().parent().removeClass('highlighted');
				selectedList.splice(selectedList.indexOf(id), 1);
			}

			//Add ID to forms for manipulation
			$('#viewForm input[name="msgid"], #replyForm input[name="msgid"], #archForm input[name="msgid"], #readForm input[name="msgid"], #unreadForm input[name="msgid"], #delForm input[name="msgid"]').val(selectedList);

			//Manage meta bar buttons
			if (selectedList.length == 0) {
				$('#messageActionButtons').hide();
			} else if (selectedList.length == 1) {
				$('#viewBtn, #repBtn, #fwdBtn').removeClass('grayed');
				$('#messageActionButtons').show();
			} else if (selectedList.length > 1) {
				$('#viewBtn, #repBtn, #fwdBtn').addClass('grayed');
				$('#messageActionButtons').show();
			}
		});

	} else if (pageUrl == 'view.php') {
		archAct = '<?php echo $buttonMetaArch; ?>';
		$('#archBtn').html(archAct[0].toUpperCase() + archAct.substr(1, archAct.length) + 'ive');
		$('#archForm input[name="action"]').val(archAct);

		//Add ID to forms for manipulation
		$('#replyForm input[name="msgid"], #forwardForm input[name="msgid"], #archForm input[name="msgid"], #readForm input[name="msgid"], #unreadForm input[name="msgid"], #delForm input[name="msgid"]').val(['<?php echo $buttonMetaId ?>']);

		//Change meta forms to throw back to the inbox to avoid displaying errors to the user after message is moved or deleted
		$('#delForm, #archForm').attr('action', 'index.php');

		//Enable buttons
		$('#viewBtn').remove();
		$('#messageActionButtons').show();
	} else if (pageUrl == 'admin/userdata.php') {
		//Manage modals on user table
		$('.changepass').click(function() {
			var $userform = $(this).parent().find('form');
			if ($userform.find('.dashmodpassword').val() != '') {
				modal('Reset password', 'You are about to reset the password of the user with UID ' + $userform.find('.dashuserid').val() + '.', 'reset password', 'Reset password', $userform);
			} else {
				modal('Clear password', 'You are about to clear the password of the user with UID ' + $userform.find('.dashuserid').val() + '. This is <strong>extremely insecure</strong> and not recommended.', 'Clear password', 'Clear password', $userform);
			}
		});
	}

	//Main function buttons
	$('#cancelBtn').on('click', function() {
		$('#cancelForm').submit();
	});
	$('#sendBtn').on('click', function() {
		$('#messageform').submit();
	});

	/*
	 * Effects
	 */

	$('#attrib').hover(function() {
		$('#attribmore').stop(true).slideDown();
	}, function() {
		$('#attribmore').stop(true).slideUp();
	});

	/*
	 * Functions
	 */

	function modal(title, message, confirmText, button, $form) {
		$('#modal .modalTitle').html(title);
		$('#modal .modalContent .message').html(message);
		$('#modal .modalConfirmText').html(confirmText.toUpperCase());
		$('#modalConfirmButton').html(button);
		$('#modalConfirmBox').val('');

		$('.modalClose').click(function() {
			$('#modal, #modalShade').hide();
		});

		$('#modal').css('visibility', 'hidden').show();

		//Style Modal
		$('#modal').css('margin-top', '-' + (parseInt($('#modal').css('height')) / 2 - 14) + 'px');
		$('#modalConfirmBox').css('width', (parseInt($('#modal').css('width')) - 2 * parseInt($('.modalContent').css('padding-left')) - parseInt($('#modalConfirmButton').css('width')) - 11) + 'px');

		$('#modal, #modalShade').css('visibility', 'visible').show();

		$('#modalConfirmButton').click(function() {
			if ($('#modalConfirmBox').val().toLowerCase() == confirmText.toLowerCase()) {
				$('#modal, #modalShade').hide();
				$form.attr('onsubmit', '');
				$form.submit();
			} else {
				$('#modalConfirmBox').val('');
				$('#modal').shake(4);
			}
		});
	}

	$.fn.shake = function(times){
		var interval = 100;
		var distance = 10;

		var base = parseInt(this.css('margin-left'));

		for(var iter = 0; iter < times + 1; iter++){
			this.animate({
				'margin-left': base - (iter % 2 == 0 ? distance : distance * -1)
			}, interval);
		}

		this.animate({'margin-left': base}, interval);
	}
</script>

<script src='https://www.techgeek01.com/dropboxextplus/js/bootstrap.js'></script>
<script>
	window.setTimeout(function() {
		$('#alert-fade').addClass('fade');
	}, 3000);
	$('.open-alertDelete').click(function(sendID) {
		sendID.preventDefault();
		var _self = $(this);
		$('#msgid').val(_self.data('id'));
		$(_self.attr('href')).modal('show');
	});
</script>
</body>
</html>
