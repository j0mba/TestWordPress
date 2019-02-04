<script type="text/template" id="happyforms-form-setup-template">
	<div class="happyforms-stack-view">
		<div class="customize-control">
			<label for="happyforms-confirmation-message" class="customize-control-title"><?php _e( 'Confirmation message', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="confirmation_message"></i></label>
			<div data-pointer-target>
				<textarea name="" id="happyforms-confirmation-message" cols="34" rows="3" data-attribute="confirmation_message"><%= confirmation_message %></textarea>
			</div>
		</div>
		<div class="customize-control customize-control-checkbox">
			<div class="customize-inside-control-row">
				<input type="checkbox" id="happyforms-receive-email-alerts" value="1" <% if ( receive_email_alerts ) { %>checked="checked"<% } %> data-attribute="receive_email_alerts" />
				<label for="happyforms-receive-email-alerts"><?php _e( 'Receive submission alerts', 'happyforms' ); ?></label>
			</div>
		</div>
		<div id="happyforms-alert-email-settings"<% if ( receive_email_alerts ) { %> style="display: block"<% } %>>
			<div class="customize-control">
				<label for="form_email_recipient" class="customize-control-title"><?php _e( 'Email address', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="email_recipient"></i></label>
				<input type="text" id="form_email_recipient" value="<%= email_recipient %>" data-attribute="email_recipient" data-pointer-target />
			</div>
			<div class="customize-control">
				<label for="form_alert_email_subject" class="customize-control-title"><?php _e( 'Email subject', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="alert_email_subject"></i></label>
				<input type="text" id="form_alert_email_subject" value="<%= alert_email_subject %>" data-attribute="alert_email_subject" data-pointer-target />
			</div>
			<div class="customize-control customize-control-checkbox">
				<div class="customize-inside-control-row" data-pointer-target>
					<input type="checkbox" value="1" id="happyforms-email-mark-and-reply" <% if ( email_mark_and_reply ) { %>checked="checked"<% } %> data-attribute="email_mark_and_reply" />
					<label for="happyforms-email-mark-and-reply"><?php _e( 'Include mark and reply link', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="email_mark_and_reply"></i></label>
				</div>
			</div>
		</div>
		<div class="customize-control customize-control-checkbox">
			<div class="customize-inside-control-row">
				<input type="checkbox" id="happyforms-send-confirmation-email" value="1" <% if ( send_confirmation_email ) { %>checked="checked"<% } %> data-attribute="send_confirmation_email" />
				<label for="happyforms-send-confirmation-email"><?php _e( 'Send confirmation email', 'happyforms' ); ?></label>
			</div>
		</div>
		<div id="happyforms-confirmation-email-settings"<% if ( send_confirmation_email ) { %> style="display: block"<% } %>>
			<div class="customize-control" >
				<label for="confirmation_email_from_name" class="customize-control-title"><?php _e( 'Email display name', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="confirmation_email_from_name"></i></label>
				<input type="text" id="confirmation_email_from_name" value="<%= confirmation_email_from_name %>" data-attribute="confirmation_email_from_name" data-pointer-target />
			</div>
			<div class="customize-control" >
				<label for="form_confirmation_email_subject" class="customize-control-title"><?php _e( 'Email subject', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="confirmation_email_subject"></i></label>
				<input type="text" id="form_confirmation_email_subject" value="<%= confirmation_email_subject %>" data-attribute="confirmation_email_subject" data-pointer-target />
			</div>
			<div class="customize-control">
				<label for="happyforms-confirmation-email-content" class="customize-control-title"><?php _e( 'Email content', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="confirmation_email_content"></i></label>
				<div data-pointer-target>
					<textarea name="" id="happyforms-confirmation-email-content" cols="34" rows="3" data-attribute="confirmation_email_content"><%= confirmation_email_content %></textarea>
				</div>
			</div>
		</div>
		<div class="customize-control">
			<label for="form_redirect_url" class="customize-control-title"><?php _e( 'On complete redirect link', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="redirect_url"></i></label>
			<input type="text" id="form_redirect_url" value="<%= redirect_url %>" data-attribute="redirect_url" data-pointer-target />
		</div>
		<div class="customize-control">
			<label for="form_submit_button_label" class="customize-control-title"><?php _e( 'Submit button label', 'happyforms' ); ?></label>
			<input type="text" id="form_submit_button_label" value="<%= submit_button_label %>" data-attribute="submit_button_label" data-pointer-target />
		</div>
		<div class="customize-control customize-control-checkbox">
			<div class="customize-inside-control-row" data-pointer-target>
				<input type="checkbox" id="happyforms-spam-prevention" value="1" <% if ( spam_prevention ) { %>checked="checked"<% } %> data-attribute="spam_prevention" />
				<label for="happyforms-spam-prevention"><?php _e( 'Spam prevention', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="spam_prevention"></i></label>
			</div>
		</div>
		<div class="customize-control customize-control-checkbox">
			<div class="customize-inside-control-row" data-pointer-target>
				<input type="checkbox" id="happyforms-use-captcha" value="1" <% if ( captcha ) { %>checked="checked"<% } %> data-attribute="captcha" />
				<label for="happyforms-use-captcha"><?php _e( 'Use', 'happyforms' ); ?> <a href="https://www.google.com/recaptcha" target="_blank" class="external"><?php _e( 'Google ReCaptcha', 'happyforms' ); ?></a> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="captcha"></i></label>
			</div>
			<div id="happyforms-captcha-settings" <% if ( captcha ) { %>style="display: block;"<% } %>>
				<p>
					<label for="form_captcha_site_key" class="customize-control-title"><?php _e( 'ReCaptcha site key', 'happyforms' ); ?></label>
					<input type="text" id="form_captcha_site_key" value="<%= captcha_site_key %>" data-attribute="captcha_site_key" />
				</p>
				<p>
					<label for="form_captcha_secret_key" class="customize-control-title"><?php _e( 'ReCaptcha secret key', 'happyforms' ); ?></label>
					<input type="text" id="form_captcha_secret_key" value="<%= captcha_secret_key %>" data-attribute="captcha_secret_key" />
				</p>
			</div>
		</div>
		<div class="customize-control customize-control-checkbox">
			<div class="customize-inside-control-row" data-pointer-target>
				<input type="checkbox" value="1" id="happyforms-save-entries" <% if ( save_entries ) { %>checked="checked"<% } %> data-attribute="save_entries" />
				<label for="happyforms-save-entries"><?php _e( 'Save responses for this form', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="save_entries"></i></label>
			</div>
		</div>
		<div class="customize-control customize-control-checkbox">
			<div class="customize-inside-control-row" data-pointer-target>
				<input type="checkbox" value="1" id="happyforms-unique-id" <% if ( unique_id ) { %>checked="checked"<% } %> data-attribute="unique_id" />
				<label for="happyforms-unique-id"><?php _e( 'Add identifying number to responses', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="unique_id"></i></label>
			</div>
		</div>
		<div id="happyforms-unique-id-settings" <% if ( unique_id ) { %>style="display: block;"<% } %>>
			<div class="customize-control">
				<label for="unique_id_start_from" class="customize-control-title"><?php _e( 'Start counter from', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="unique_id_start_from"></i></label>
				<input type="number" id="unique_id_start_from" value="<%= unique_id_start_from %>" data-attribute="unique_id_start_from"  data-pointer-target />
			</div>
			<div class="customize-control happyforms-customize-controls-wrap--side-by-side">
				<div>
					<label for="unique_id_prefix" class="customize-control-title"><?php _e( 'Prefix', 'happyforms' ); ?></label>
					<input type="text" id="unique_id_prefix" value="<%= unique_id_prefix %>" data-attribute="unique_id_prefix" />
				</div>
				<div>
					<label for="unique_id_suffix" class="customize-control-title"><?php _e( 'Suffix', 'happyforms' ); ?></label>
					<input type="text" id="unique_id_suffix" value="<%= unique_id_suffix %>" data-attribute="unique_id_suffix" />
				</div>
			</div>
		</div>
		<div class="customize-control customize-control-checkbox">
			<div class="customize-inside-control-row" data-pointer-target>
				<input type="checkbox" value="1" id="happyforms-preview-before-submit" <% if ( preview_before_submit ) { %>checked="checked"<% } %> data-attribute="preview_before_submit" />
				<label for="happyforms-preview-before-submit"><?php _e( 'Preview values before submission', 'happyforms' ); ?> <i class="fa fa-question-circle" aria-hidden="true" data-pointer="preview_before_submit"></i></label>
			</div>
		</div>
		<div id="happyforms-review-button-label-settings" <% if ( preview_before_submit ) { %>style="display: block;"<% } %>>
			<div class="customize-control">
				<label for="form_review_button_label" class="customize-control-title"><?php _e( 'Review button text', 'happyforms' ); ?></label>
				<input type="text" id="form_review_button_label" value="<%= review_button_label %>" data-attribute="review_button_label" data-pointer-target />
			</div>
		</div>
	</ul>
</script>
<script type="text/template" id="happyforms-pointer-confirmation_message">
<?php _e( 'This is the message your users will see after succesfully submitting your form.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-email_recipient">
<?php _e( 'Add your email address here to receive a confirmation email for each form response. You can add multiple email addresses by separating each address with a comma.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-alert_email_subject">
<?php _e( 'Each time a user submits a message, you\'ll receive an email with this subject.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-confirmation_email_content">
<?php _e( 'If your form contains an email field, recipients will receive an email with this content.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-confirmation_email_from_name">
<?php _e( 'If your form contains an email field, recipients will receive an email with this sender name.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-confirmation_email_subject">
<?php _e( 'If your form contains an email field, recipients will receive an email with this subject.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-redirect_url">
<?php _e( 'By default, recipients will be redirected to the post or page displaying this form. To set a custom redirect webpage, add a link here.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-spam_prevention">
<?php _e( 'Protect your form against bots by using HoneyPot security.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-captcha">
<?php _e( 'Protect your form against bots using your Google ReCaptcha credentials.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-save_entries">
<?php _e( 'Keep recipients responses stored in your WordPress database.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-preview_before_submit">
<?php _e( 'Let your users review their submission before confirming it.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-unique_id">
<?php _e( 'Tag responses with a unique, incremental identifier.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-unique_id_start_from">
<?php _e( 'Your next submission will be tagged with this identifier.', 'happyforms' ); ?>
</script>
<script type="text/template" id="happyforms-pointer-email_mark_and_reply">
<?php _e( 'Reply to your users and mark their submission as read in one click.', 'happyforms' ); ?>
</script>

