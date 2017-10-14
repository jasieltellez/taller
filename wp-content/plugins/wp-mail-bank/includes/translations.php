<?php
/**
 * This file is used for translation strings.
 *
 * @author  Tech Banker
 * @package wp-mail-bank/includes
 * @version 2.0.0
 */
if (!defined("ABSPATH")) {
   exit;
}// Exit if accessed directly
if (!is_user_logged_in()) {
   return;
} else {
   $access_granted = false;
   foreach ($user_role_permission as $permission) {
      if (current_user_can($permission)) {
         $access_granted = true;
         break;
      }
   }
   if (!$access_granted) {
      return;
   } else {
      //Disclaimer
      $mb_upgrade = __("Premium Editions", "wp-mail-bank");
      $mb_premium_edition_label = __("Premium Edition", "wp-mail-bank");
      $mb_message_premium_edition = __("This feature is available only in Premium Editions! <br> Kindly Purchase to unlock it!", "wp-mail-bank");

      //wizard
      $mb_wizard_basic_info = __("Basic Info", "wp-mail-bank");
      $mb_wizard_account_setup = __("Account Setup", "wp-mail-bank");
      $mb_wizard_confirm = __("Confirm", "wp-mail-bank");

      // Menus
      $wp_mail_bank = __("Mail Bank", "wp-mail-bank");
      $mb_email_configuration = __("Email Setup", "wp-mail-bank");
      $mb_email_logs = __("Email Logs", "wp-mail-bank");
      $mb_test_email = __("Test Email", "wp-mail-bank");
      $mb_settings = __("Plugin Settings", "wp-mail-bank");
      $mb_system_information = __("System Information", "wp-mail-bank");
      $mb_support_forum = __("Ask For Help", "wp-mail-bank");
      $mb_roles_and_capabilities = __("Roles & Capabilities", "wp-mail-bank");

      // Footer
      $mb_success = __("Success!", "wp-mail-bank");
      $mb_update_email_configuration = __("Email Setup has been updated Successfully", "wp-mail-bank");
      $mb_test_email_sent = __("Test Email was sent Successfully!", "wp-mail-bank");
      $mb_test_email_not_send = __("Test Email was not sent!", "wp-mail-bank");
      $mb_update_settings = __("Plugin Settings have been updated Successfully", "wp-mail-bank");
      $oauth_not_supported = __("The OAuth is not supported by providing SMTP Host, kindly provide username and password", "wp-mail-bank");
      $mb_feature_requests_your_email = __("Your Email", "wp-mail-bank");
      $mb_feature_requests_your_name = __("Your Name", "wp-mail-bank");
      $mb_feature_requests_your_name_placeholder = __("Please provide your Name", "wp-mail-bank");

      // Common Variables
      $mb_status_sent = __("Sent", "wp-mail-bank");
      $mb_status_not_sent = __("Not Sent", "wp-mail-bank");
      $mb_user_access_message = __("You don't have Sufficient Access to this Page. Kindly contact the Administrator for more Privileges", "wp-mail-bank");
      $mb_enable = __("Enable", "wp-mail-bank");
      $mb_disable = __("Disable", "wp-mail-bank");
      $mb_override = __("Override", "wp-mail-bank");
      $mb_dont_override = __("Don't Override", "wp-mail-bank");
      $mb_save_changes = __("Save Settings", "wp-mail-bank");
      $mb_subject = __("Subject", "wp-mail-bank");
      $mb_next_step = __("Next Step", "wp-mail-bank");
      $mb_previous_step = __("Previous Step", "wp-mail-bank");
      $mb_upgrade_kanow_about = __("Know about", "wp-mail-bank");
      $mb_full_features = __("Full Features", "wp-mail-bank");
      $mb_chek_our = __("or check our", "wp-mail-bank");
      $mb_online_demos = __("Online Demos", "wp-mail-bank");

      // Email Setup
      $mb_email_configuration_cc_label = __("CC", "wp-mail-bank");
      $mb_email_configuration_bcc_label = __("BCC", "wp-mail-bank");
      $mb_email_configuration_cc_email_address_tooltip = __("Please provide valid Cc Email Address", "wp-mail-bank");
      $mb_email_configuration_bcc_email_address_tooltip = __("Please provide valid Bcc Email Address", "wp-mail-bank");
      $mb_email_configuration_cc_email_placeholder = __("Please provide Cc Email", "wp-mail-bank");
      $mb_email_configuration_bcc_email_placeholder = __("Please provide Bcc Email", "wp-mail-bank");
      $mb_email_configuration_from_name = __("From Name", "wp-mail-bank");
      $mb_email_configuration_from_name_tooltip = __("From Name is the inbox field that tells your recipient who sent the messages. If you would like to override the pre-configured From Name, then you would need to insert the name in the inbox field", "wp-mail-bank");
      $mb_email_configuration_from_name_placeholder = __("Please provide From Name", "wp-mail-bank");
      $mb_email_configuration_from_email = __("From Email", "wp-mail-bank");
      $mb_email_address_tooltip = __("From Email is the inbox field that tells your recipient from which Email Address the messages are coming. If you would like to override the pre-configured From Email, then you would need to insert an Email Address in the inbox field", "wp-mail-bank");
      $mb_email_configuration_from_email_placeholder = __("Please provide From Email Address", "wp-mail-bank");
      $mb_email_configuration_mailer_type = __("Mailer Type", "wp-mail-bank");
      $mb_email_configuration_mailer_type_tooltip = __("This field provides you an ability to choose a specific option for Mailer Type. If you would like to send an Email via SMTP mailer, you would need to select Send Email via SMTP from the drop down or you could use PHP mail () Function", "wp-mail-bank");
      $mb_email_configuration_send_email_via_smtp = __("Send Email via SMTP", "wp-mail-bank");
      $mb_email_configuration_use_php_mail_function = __("Use The PHP mail() Function", "wp-mail-bank");
      $mb_email_configuration_smtp_host = __("SMTP Host", "wp-mail-bank");
      $mb_email_configuration_smtp_host_tooltip = __("If you would like to send an Email via different host, you would need to insert that specific host name like smtp.gmail.com in the inbox field", "wp-mail-bank");
      $mb_feature_requests_your_email_placeholder = __("Please provide your Email Address", "wp-mail-bank");
      $mb_email_configuration_smtp_host_placeholder = __("Please provide SMTP Host", "wp-mail-bank");
      $mb_email_configuration_smtp_port = __("SMTP Port", "wp-mail-bank");
      $mb_email_configuration_smtp_port_tooltip = __("This inbox field is specified to provide a valid SMTP Port for authentication", "wp-mail-bank");
      $mb_email_configuration_smtp_port_placeholder = __("Please provide SMTP Port", "wp-mail-bank");
      $mb_email_configuration_encryption = __("Encryption", "wp-mail-bank");
      $mb_email_configuration_encryption_tooltip = __("It provides you an ability to choose a specific option for Encryption. If you would like to send an Email with TLS encryption, you would need to select Use TLS Encryption from the drop down or you could use SSL Encryption. For that you would need to select Use SSL Encryption from the drop down. If you would like to send an Email without encryption, you would need to select No Encryption from the drop down", "wp-mail-bank");
      $mb_email_configuration_no_encryption = __("No Encryption", "wp-mail-bank");
      $mb_email_configuration_use_ssl_encryption = __("Use SSL Encryption", "wp-mail-bank");
      $mb_email_configuration_use_tls_encryption = __("Use TLS Encryption", "wp-mail-bank");
      $mb_email_configuration_authentication = __("Authentication", "wp-mail-bank");
      $mb_email_configuration_authentication_tooltip = __("This inbox field allows you to choose an appropriate option for authentication. It provides you the Two-way authentication factor; If you would like to authenticate yourself via Username and Password, you would need to select Use Username and Password from the drop down. You can also authenticate by an OAuth 2.0 protocol, which requires Client Id and Secret Key, for that you would need to select Use OAuth (Client ID and Secret Key) from the drop down. You can easily get Client Id and Secret Key from respective SMTP Server Developers section", "wp-mail-bank");
      $mb_email_configuration_test_email_address = __("Test Email Address", "wp-mail-bank");
      $mb_email_configuration_test_email_address_tooltip = __("In this field, you would need to provide a valid Email Address in the inbox field on which you would like to receive Test email", "wp-mail-bank");
      $mb_email_configuration_test_email_address_placeholder = __("Please provide Test Email Address", "wp-mail-bank");
      $mb_email_configuration_subject_test_tooltip = __("In this field, you would need to provide a subject for Test Email", "wp-mail-bank");
      $mb_email_configuration_subject_test_placeholder = __("Please provide Subject", "wp-mail-bank");
      $mb_email_configuration_content = __("Email Content", "wp-mail-bank");
      $mb_email_configuration_content_tooltip = __("In this field, you would need to provide the content for Test Email", "wp-mail-bank");
      $mb_email_configuration_send_test_email = __("Send Test Email", "wp-mail-bank");
      $mb_email_configuration_smtp_debugging_output = __("SMTP Debugging Output", "wp-mail-bank");
      $mb_email_configuration_send_test_email_textarea = __("Checking your settings", "wp-mail-bank");
      $mb_email_configuration_result = __("Result", "wp-mail-bank");
      $mb_email_configuration_send_another_test_email = __("Send Another Test Email", "wp-mail-bank");
      $mb_email_configuration_enable_from_name = __("From Name Configuration", "wp-mail-bank");
      $mb_email_configuration_enable_from_name_tooltip = __("If you would like to override the pre-configured name which will be used while sending Emails, then you would need to choose Override from the drop down and vice-versa", "wp-mail-bank");
      $mb_email_configuration_enable_from_email = __("From Email Configuration", "wp-mail-bank");
      $mb_email_configuration_enable_from_email_tooltip = __("If you would like to override your pre-configured Email Address which will be used while sending Emails, then you would need to choose Override from the drop down and vice-versa", "wp-mail-bank");
      $mb_email_configuration_username = __("Username", "wp-mail-bank");
      $mb_email_configuration_username_tooltip = __("In this field, you would need to provide a username to authenticate your SMTP details", "wp-mail-bank");
      $mb_email_configuration_username_placeholder = __("Please provide username", "wp-mail-bank");
      $mb_email_configuration_password = __("Password", "wp-mail-bank");
      $mb_email_configuration_password_tooltip = __("In this field, you would need to provide a password to authenticate your SMTP details", "wp-mail-bank");
      $mb_email_configuration_password_placeholder = __("Please provide password", "wp-mail-bank");
      $mb_email_configuration_redirect_uri = __("Redirect URI", "wp-mail-bank");
      $mb_email_configuration_redirect_uri_tooltip = __("Please copy this Redirect URI and Paste into Redirect URI field when creating your app", "wp-mail-bank");
      $mb_email_configuration_use_oauth = __("Use OAuth (Client Id and Secret Key required)", "wp-mail-bank");
      $mb_email_configuration_none = __("None", "wp-mail-bank");
      $mb_email_configuration_use_plain_authentication = __("Plain Authentication", "wp-mail-bank");
      $mb_email_configuration_cram_md5 = __("Cram-MD5", "wp-mail-bank");
      $mb_email_configuration_login = __("Login", "wp-mail-bank");
      $mb_email_configuration_client_id = __("Client Id", "wp-mail-bank");
      $mb_email_configuration_client_secret = __("Secret Key", "wp-mail-bank");
      $mb_email_configuration_client_id_tooltip = __("Please provide valid Client Id issued by your SMTP Host", "wp-mail-bank");
      $mb_email_configuration_client_secret_tooltip = __("Please provide valid Secret Key issued by your SMTP Host", "wp-mail-bank");
      $mb_email_configuration_client_id_placeholder = __("Please provide Client Id", "wp-mail-bank");
      $mb_email_configuration_client_secret_placeholder = __("Please provide Secret Key", "wp-mail-bank");
      $mb_email_configuration_tick_for_sent_mail = __("Yes, automatically send a Test Email upon clicking on the Next Step Button to verify settings", "wp-mail-bank");
      $mb_email_configuration_email_address = __("Email Address", "wp-mail-bank");
      $mb_email_configuration_email_address_tooltip = __("In this field, you would need to add a valid Email Address in the inbox field from which you would like to send Emails", "wp-mail-bank");
      $mb_email_configuration_email_address_placeholder = __("Please provide valid Email Address", "wp-mail-bank");
      $mb_email_configuration_reply_to = __("Reply To", "wp-mail-bank");
      $mb_email_configuration_reply_to_tooltip = __("In this field, you would need to add a valid Email Address that is automatically inserted into the Reply To field when a user replies to an email message", "wp-mail-bank");
      $mb_email_configuration_reply_to_placeholder = __("Please provide Reply To Email Address", "wp-mail-bank");
      $mb_email_configuration_get_google_credentials = __("Get Google Client Id and Secret Key", "wp-mail-bank");
      $mb_email_configuration_get_microsoft_credentials = __("Get Microsoft Client Id and Secret Key", "wp-mail-bank");
      $mb_email_configuration_get_yahoo_credentials = __("Get Yahoo Client Id and Secret Key", "wp-mail-bank");

      // Email Logs
      $mb_start_date_title = __("Start Date", "wp-mail-bank");
      $mb_resend = __("Resend", "wp-mail-bank");
      $mb_start_date_placeholder = __("Please provide Start Date", "wp-mail-bank");
      $mb_start_date_tooltip = __("This field shows starting date of Email Logs", "wp-mail-bank");
      $mb_end_date_title = __("End Date", "wp-mail-bank");
      $mb_limit_records_title = __("Limit Records", "wp-mail-bank");
      $mb_limit_records_tooltip = __("Please choose Limit to view records", "wp-mail-bank");
      $mb_hundred_records = __("100", "wp-mail-bank");
      $mb_five_hundred_records = __("500", "wp-mail-bank");
      $mb_thousand_records = __("1000", "wp-mail-bank");
      $mb_all_records = __("All", "wp-mail-bank");
      $mb_end_date_placeholder = __("Please provide End Date", "wp-mail-bank");
      $mb_end_date_tooltip = __("This field shows ending date of Email Logs", "wp-mail-bank");
      $mb_submit = __("Submit", "wp-mail-bank");
      $mb_email_logs_bulk_action = __("Bulk Action", "wp-mail-bank");
      $mb_email_logs_delete = __("Delete", "wp-mail-bank");
      $mb_email_logs_apply = __("Apply", "wp-mail-bank");
      $mb_email_logs_email_to = __("Email To", "wp-mail-bank");
      $mb_email_logs_actions = __("Action", "wp-mail-bank");
      $mb_email_logs_show_details = __("Show Details", "wp-mail-bank");
      $mb_email_logs_debugging_output = __("Debugging Output", "wp-mail-bank");
      $mb_email_logs_show_outputs = __("Show Debugging Output", "wp-mail-bank");
      $mb_date_time = __("Date/Time", "wp-mail-bank");
      $mb_email_logs_status = __("Status", "wp-mail-bank");

      // Settings
      $mb_settings_debug_mode = __("Debug Mode", "wp-mail-bank");
      $mb_settings_debug_mode_tooltip = __("Please choose a specific option for Debug Mode", "wp-mail-bank");
      $mb_remove_tables_title = __("Remove Tables at Uninstall", "wp-mail-bank");
      $mb_remove_tables_tooltip = __("Please choose a specific option whether to allow Remove Tables at Uninstall", "wp-mail-bank");
      $mb_monitoring_email_log_title = __("Monitoring Email Logs", "wp-mail-bank");
      $mb_monitoring_email_log_tooltip = __("This field is used to allow Email Logs to monitor or not", "wp-mail-bank");

      // Roles and Capabilities
      $mb_roles_capabilities_show_menu = __("Show Mail Bank Menu", "wp-mail-bank");
      $mb_roles_capabilities_show_menu_tooltip = __("Please choose a specific role who can see Sidebar Menu", "wp-mail-bank");
      $mb_roles_capabilities_administrator = __("Administrator", "wp-mail-bank");
      $mb_roles_capabilities_author = __("Author", "wp-mail-bank");
      $mb_roles_capabilities_editor = __("Editor", "wp-mail-bank");
      $mb_roles_capabilities_contributor = __("Contributor", "wp-mail-bank");
      $mb_roles_capabilities_subscriber = __("Subscriber", "wp-mail-bank");
      $mb_roles_capabilities_others = __("Others", "wp-mail-bank");
      $mb_roles_capabilities_topbar_menu = __("Show Mail Bank Top Bar Menu", "wp-mail-bank");
      $mb_roles_capabilities_topbar_menu_tooltip = __("Please choose a specific option from Mail Bank Top Bar Menu", "wp-mail-bank");
      $mb_roles_capabilities_administrator_role = __("An Administrator Role can do the following ", "wp-mail-bank");
      $mb_roles_capabilities_administrator_role_tooltip = __("Please choose specific page available for the Administrator Access", "wp-mail-bank");
      $mb_roles_capabilities_full_control = __("Full Control", "wp-mail-bank");
      $mb_roles_capabilities_author_role = __("An Author Role can do the following ", "wp-mail-bank");
      $mb_roles_capabilities_author_role_tooltip = __("Please choose specific page available for Author Access", "wp-mail-bank");
      $mb_roles_capabilities_editor_role = __("An Editor Role can do the following ", "wp-mail-bank");
      $mb_roles_capabilities_editor_role_tooltip = __("Please choose specific page available for Editor Access", "wp-mail-bank");
      $mb_roles_capabilities_contributor_role = __("A Contributor Role can do the following ", "wp-mail-bank");
      $mb_roles_capabilities_contributor_role_tooltip = __("Please choose specific page available for Contributor Access", "wp-mail-bank");
      $mb_roles_capabilities_other_role = __("Other Roles can do the following ", "wp-mail-bank");
      $mb_roles_capabilities_other_role_tooltip = __("Please choose specific page available for Others Role Access", "wp-mail-bank");
      $mb_roles_capabilities_other_roles_capabilities = __("Please tick the appropriate capabilities for security purposes ", "wp-mail-bank");
      $mb_roles_capabilities_other_roles_capabilities_tooltip = __("Only users with these capabilities can access Mail Bank", "wp-mail-bank");
      $mb_roles_capabilities_subscriber_role = __("A Subscriber Role can do the following", "wp-mail-bank");
      $mb_roles_capabilities_subscriber_role_tooltip = __("Please choose specific page available for Subscriber Access", "wp-mail-bank");

      // Test Email
      $mb_test_email_sending_test_email = __("Sending Test Email to", "wp-mail-bank");
      $mb_test_email_status = __("Email Status", "wp-mail-bank");

      // Connectivity Test
      $mb_connectivity_test = __("Connectivity Test", "wp-mail-bank");
      $mb_transport = __("Transport", "wp-mail-bank");
      $mb_socket = __("Socket", "wp-mail-bank");
      $mb_status = __("Status", "wp-mail-bank");
      $mb_smtp = __("SMTP", "wp-mail-bank");
      $mb_mail_server_host = __("Outgoing Mail Server Hostname", "wp-mail-bank");
      $mb_begin_test = __("Begin Test", "wp-mail-bank");
      $mb_localhost = __("localhost", "wp-mail-bank");
      $mb_mail_server_tooltip = __("In this field, you would need to provide Outgoing Mail Server Hostname", "wp-mail-bank");
   }
}