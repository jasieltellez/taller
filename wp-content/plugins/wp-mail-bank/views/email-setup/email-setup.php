<?php
/**
 * This Template is used for email setup.
 *
 * @author  Tech Banker
 * @package wp-mail-bank/views/email-setup
 * @version 2.0.0
 */
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
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
   } else if (email_configuration_mail_bank == "1") {
      $oauth_redirect_url = admin_url("admin-ajax.php");
      $mail_bank_set_hostname_port = wp_create_nonce("mail_bank_set_hostname_port");
      $mail_bank_email_configuration_settings = wp_create_nonce("mail_bank_email_configuration_settings");
      $mail_bank_test_email_configuration = wp_create_nonce("mail_bank_test_email_configuration");
      ?>
      <div class="page-bar">
         <ul class="page-breadcrumb">
            <li>
               <i class="icon-custom-home"></i>
               <a href="admin.php?page=mb_email_configuration">
                  <?php echo $wp_mail_bank; ?>
               </a>
               <span>></span>
            </li>
            <li>
               <span>
                  <?php echo $mb_email_configuration; ?>
               </span>
            </li>
         </ul>
      </div>
      <div class="row">
         <div class="col-md-12">
            <div class="portlet box vivid-green">
               <div class="portlet-title">
                  <div class="caption">
                     <i class="icon-custom-envelope-open"></i>
                     <?php echo $mb_email_configuration; ?>
                  </div>
                  <p class="premium-editions">
                     <?php echo $mb_upgrade_kanow_about ?> <a href="https://mail-bank.tech-banker.com/" target="_blank" class="premium-edition-text"><?php echo $mb_full_features ?></a> <?php echo $mb_chek_our; ?> <a href="https://mail-bank.tech-banker.com/backend-demos/" target="_blank" class="premium-edition-text"><?php echo $mb_online_demos; ?></a>
                  </p>
               </div>
               <div class="portlet-body form">
                  <div class="form-body">
                     <div class="form-wizard" id="ux_div_frm_wizard">
                        <ul class="nav nav-pills nav-justified steps">
                           <li class="active">
                              <a aria-expanded="true" href="javascript:void(0);" class="step">
                                 <span class="number"> 1 </span>
                                 <span class="desc"> <?php echo $mb_wizard_basic_info; ?> </span>
                              </a>
                           </li>
                           <li>
                              <a href="javascript:void(0);" class="step">
                                 <span class="number"> 2 </span>
                                 <span class="desc"><?php echo $mb_wizard_account_setup; ?> </span>
                              </a>
                           </li>
                           <li>
                              <a href="javascript:void(0);" class="step">
                                 <span class="number"> 3 </span>
                                 <span class="desc"><?php echo $mb_wizard_confirm; ?> </span>
                              </a>
                           </li>
                        </ul>
                     </div>
                     <div id="ux_div_step_progres_bar" class="progress progress-striped" role="progressbar">
                        <div id="ux_div_step_progres_bar_width" style="width: 33%;" class="progress-bar progress-bar-success"></div>
                     </div>
                     <div class="line-separator"></div>
                     <div class="tab-content" id="mailer_settings">
                        <form id="ux_frm_email_configuration">
                           <div id="ux_div_first_step">
                              <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label class="control-label">
                                          <?php echo $mb_email_configuration_enable_from_name; ?> :
                                          <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_enable_from_name_tooltip; ?>" data-placement="right"></i>
                                          <span class="required" aria-required="true">*</span>
                                       </label>
                                       <select name="ux_ddl_from_name" id="ux_ddl_from_name" class="form-control" onchange="mail_bank_from_name_override()">
                                          <option value="override"><?php echo $mb_override; ?></option>
                                          <option value="dont_override"><?php echo $mb_dont_override; ?></option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label class="control-label">
                                          <?php echo $mb_email_configuration_from_name; ?> :
                                          <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_from_name_tooltip; ?>" data-placement="right"></i>
                                          <span class="required" aria-required="true">*</span>
                                       </label>
                                       <input type="text" class="form-control" name="ux_txt_mb_from_name" id="ux_txt_mb_from_name" value="<?php echo isset($email_configuration_array["sender_name"]) ? esc_html($email_configuration_array["sender_name"]) : ""; ?>" placeholder="<?php echo $mb_email_configuration_from_name_placeholder; ?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label class="control-label">
                                          <?php echo $mb_email_configuration_enable_from_email; ?> :
                                          <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_enable_from_email_tooltip; ?>" data-placement="right"></i>
                                          <span class="required" aria-required="true">*</span>
                                       </label>
                                       <select name="ux_ddl_from_email" id="ux_ddl_from_email" class="form-control" onchange="mail_bank_from_email_override()">
                                          <option value="override"><?php echo $mb_override; ?></option>
                                          <option value="dont_override"><?php echo $mb_dont_override; ?></option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label class="control-label">
                                          <?php echo $mb_email_configuration_from_email; ?> :
                                          <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_address_tooltip; ?>" data-placement="right"></i>
                                          <span class="required" aria-required="true">*</span>
                                       </label>
                                       <input type="text" class="form-control" name="ux_txt_mb_from_email_configuration" id="ux_txt_mb_from_email_configuration" value="<?php echo isset($email_configuration_array["sender_email"]) ? esc_html($email_configuration_array["sender_email"]) : ""; ?>" placeholder="<?php echo $mb_email_configuration_from_email_placeholder; ?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="line-separator"></div>
                              <div class="form-actions">
                                 <div class="pull-right">
                                    <button class="btn vivid-green" name="ux_btn_next_step_second" id="ux_btn_next_step_second" onclick="mail_bank_move_to_second_step();"><?php echo $mb_next_step; ?> >> </button>
                                 </div>
                              </div>
                           </div>
                           <div id="ux_div_second_step" style="display:none">
                              <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label class="control-label">
                                          <?php echo $mb_email_configuration_email_address; ?> :
                                          <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_email_address_tooltip; ?>" data-placement="right"></i>
                                          <span class="required" aria-required="true">*</span>
                                       </label>
                                       <input type="text" class="form-control" name="ux_txt_email_address" id="ux_txt_email_address" value="<?php echo isset($email_configuration_array["email_address"]) ? esc_html($email_configuration_array["email_address"]) : ""; ?>" placeholder="<?php echo $mb_email_configuration_email_address_placeholder; ?>" onblur="mail_bank_get_host_port()">
                                    </div>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label class="control-label">
                                          <?php echo $mb_email_configuration_reply_to; ?> :
                                          <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_reply_to_tooltip; ?>" data-placement="right"></i>
                                          <span class="required" aria-required="true"> ( <?php echo $mb_premium_edition_label; ?> )</span>
                                       </label>
                                       <input type="text" class="form-control" name="ux_txt_reply_to" id="ux_txt_reply_to" value="<?php echo isset($email_configuration_array["reply_to"]) ? esc_html($email_configuration_array["reply_to"]) : ""; ?>" disabled="disabled" placeholder="<?php echo $mb_email_configuration_reply_to_placeholder; ?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label class="control-label">
                                          <?php echo $mb_email_configuration_cc_label; ?> :
                                          <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_cc_email_address_tooltip; ?>" data-placement="right"></i>
                                          <span class="required" aria-required="true"> ( <?php echo $mb_premium_edition_label; ?> ) </span>
                                       </label>
                                       <input type="text" class="form-control" name="ux_txt_cc" id="ux_txt_cc" value="<?php echo isset($email_configuration_array["cc"]) ? esc_html($email_configuration_array["cc"]) : "" ?>" disabled="disabled" placeholder="<?php echo $mb_email_configuration_cc_email_placeholder; ?>">
                                    </div>
                                 </div>
                                 <div class="col-md-6">
                                    <div class="form-group">
                                       <label class="control-label">
                                          <?php echo $mb_email_configuration_bcc_label; ?> :
                                          <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_bcc_email_address_tooltip; ?>" data-placement="right"></i>
                                          <span class="required" aria-required="true"> ( <?php echo $mb_premium_edition_label; ?> ) </span>
                                       </label>
                                       <input type="text" class="form-control" name="ux_txt_bcc" id="ux_txt_bcc" value="<?php echo isset($email_configuration_array["bcc"]) ? esc_html($email_configuration_array["bcc"]) : "" ?>" disabled="disabled" placeholder="<?php echo $mb_email_configuration_bcc_email_placeholder; ?>">
                                    </div>
                                 </div>
                              </div>
                              <div class="form-group">
                                 <label class="control-label">
                                    <?php echo $mb_email_configuration_mailer_type; ?> :
                                    <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_mailer_type_tooltip; ?>" data-placement="right"></i>
                                    <span class="required" aria-required="true">*</span>
                                 </label>
                                 <select name="ux_ddl_type" id="ux_ddl_type" class="form-control" onchange="change_settings_mail_bank()">
                                    <option value="php_mail_function" selected="selected"><?php echo $mb_email_configuration_use_php_mail_function; ?></option>
                                    <option value="smtp"><?php echo $mb_email_configuration_send_email_via_smtp; ?></option>
                                 </select>
                              </div>
                              <div id="ux_div_smtp_mail_function">
                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                          <label class="control-label">
                                             <?php echo $mb_email_configuration_smtp_host; ?> :
                                             <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_smtp_host_tooltip; ?>" data-placement="right"></i>
                                             <span class="required" aria-required="true">*</span>
                                          </label>
                                          <input type="text" class="form-control" name="ux_txt_host" id="ux_txt_host" value="<?php echo isset($email_configuration_array["hostname"]) ? esc_html($email_configuration_array["hostname"]) : ""; ?>" placeholder="<?php echo $mb_email_configuration_smtp_host_placeholder; ?>" onblur="change_link_content_mail_bank();">
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="form-group">
                                          <label class="control-label">
                                             <?php echo $mb_email_configuration_encryption; ?> :
                                             <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_encryption_tooltip; ?>" data-placement="right"></i>
                                             <span class="required" aria-required="true">*</span>
                                          </label>
                                          <select name="ux_ddl_encryption" id="ux_ddl_encryption" class="form-control" onchange="mail_bank_select_port()">
                                             <option value="none"><?php echo $mb_email_configuration_no_encryption; ?></option>
                                             <option value="tls"><?php echo $mb_email_configuration_use_tls_encryption; ?></option>
                                             <option value="ssl"><?php echo $mb_email_configuration_use_ssl_encryption; ?></option>
                                          </select>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-md-6">
                                       <div class="form-group">
                                          <label class="control-label">
                                             <?php echo $mb_email_configuration_smtp_port ?> :
                                             <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_smtp_port_tooltip; ?>" data-placement="right"></i>
                                             <span class="required" aria-required="true">*</span>
                                          </label>
                                          <input type="text" class="form-control" name="ux_txt_port" id="ux_txt_port" value="<?php echo isset($email_configuration_array["port"]) ? esc_html($email_configuration_array["port"]) : ""; ?>" placeholder="<?php echo $mb_email_configuration_smtp_port_placeholder; ?>" onfocus="paste_only_digits_mail_bank(this.id);">
                                       </div>
                                    </div>
                                    <div class="col-md-6">
                                       <div class="form-group">
                                          <label class="control-label">
                                             <?php echo $mb_email_configuration_authentication; ?> :
                                             <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_authentication_tooltip; ?>" data-placement="right"></i>
                                             <span class="required" aria-required="true">*</span>
                                          </label>
                                          <select name="ux_ddl_mb_authentication" id="ux_ddl_mb_authentication" class="form-control" onchange="select_credentials_mail_bank()">
                                             <option value="none"><?php echo $mb_email_configuration_none; ?></option>
                                             <option value="oauth2"><?php echo $mb_email_configuration_use_oauth; ?></option>
                                             <option value="crammd5"><?php echo $mb_email_configuration_cram_md5; ?></option>
                                             <option value="login"><?php echo $mb_email_configuration_login; ?></option>
                                             <option value="plain"><?php echo $mb_email_configuration_use_plain_authentication; ?></option>
                                          </select>
                                       </div>
                                    </div>
                                 </div>
                                 <div id="ux_div_oauth_authentication">
                                    <div class="row">
                                       <div class="col-md-6">
                                          <div class="form-group">
                                             <label class="control-label">
                                                <?php echo $mb_email_configuration_client_id; ?> <a href="" target="_blank" id="ux_link_reference"><span id="ux_link_content"></span></a>:
                                                <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_client_id_tooltip; ?>" data-placement="right"></i>
                                                <span class="required" aria-required="true">*</span>
                                             </label>
                                             <input type="text" class="form-control" name="ux_txt_client_id" id="ux_txt_client_id" value="<?php echo isset($email_configuration_array["client_id"]) ? esc_html($email_configuration_array["client_id"]) : ""; ?>" placeholder="<?php echo $mb_email_configuration_client_id_placeholder; ?>" onclick="this.select()">
                                          </div>
                                       </div>
                                       <div class="col-md-6">
                                          <div class="form-group">
                                             <label class="control-label">
                                                <?php echo $mb_email_configuration_client_secret; ?> :
                                                <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_client_secret_tooltip; ?>" data-placement="right"></i>
                                                <span class="required" aria-required="true">*</span>
                                             </label>
                                             <input type="text" class="form-control" name="ux_txt_client_secret" id="ux_txt_client_secret" value="<?php echo isset($email_configuration_array["client_secret"]) ? esc_html($email_configuration_array["client_secret"]) : ""; ?>" placeholder="<?php echo $mb_email_configuration_client_secret_placeholder; ?>" onclick="this.select()">
                                          </div>
                                       </div>
                                    </div>
                                    <div class="form-group">
                                       <label class="control-label">
                                          <?php echo $mb_email_configuration_redirect_uri; ?> :
                                          <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_redirect_uri_tooltip; ?>" data-placement="right"></i>
                                          <span class="required" aria-required="true">*</span>
                                       </label>
                                       <input type="text" name="ux_txt_redirect_uri" id="ux_txt_redirect_uri" readonly="readonly" class="form-control" value="<?php echo $oauth_redirect_url; ?>" onclick="this.select()">
                                    </div>
                                 </div>
                                 <div id="ux_div_username_password_authentication">
                                    <div class="row">
                                       <div class="col-md-6">
                                          <div class="form-group">
                                             <label class="control-label">
                                                <?php echo $mb_email_configuration_username; ?> :
                                                <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_username_tooltip; ?>" data-placement="right"></i>
                                                <span class="required" aria-required="true">*</span>
                                             </label>
                                             <input type="text" class="form-control" name="ux_txt_username" id="ux_txt_username" value="<?php echo isset($email_configuration_array["username"]) ? esc_html($email_configuration_array["username"]) : ""; ?>" placeholder="<?php echo $mb_email_configuration_username_placeholder; ?>">
                                          </div>
                                       </div>
                                       <div class="col-md-6">
                                          <div class="form-group">
                                             <label class="control-label">
                                                <?php echo $mb_email_configuration_password; ?> :
                                                <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_password_tooltip; ?>" data-placement="right"></i>
                                                <span class="required" aria-required="true">*</span>
                                             </label>
                                             <input type="password" class="form-control" name="ux_txt_password" id="ux_txt_password" value="<?php echo isset($email_configuration_array["password"]) ? str_repeat('*', strlen(base64_decode(esc_html($email_configuration_array["password"])))) : ""; ?>" placeholder="<?php echo $mb_email_configuration_password_placeholder; ?>">
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div id="ux_div_automatic_mail">
                                 <div class="form-group">
                                    <label class="control-label">
                                       <input type="checkbox"  name="ux_chk_automatic_sent_mail" id="ux_chk_automatic_sent_mail" value="1" checked="checked">
                                       <strong><?php echo $mb_email_configuration_tick_for_sent_mail; ?></strong>
                                    </label>
                                 </div>
                              </div>
                              <div class="line-separator"></div>
                              <div class="form-actions">
                                 <div class="pull-left">
                                    <button type="button" class="btn vivid-green" name="ux_btn_previsious_step_first" id="ux_btn_previsious_step_first" onclick="mail_bank_move_to_first_step()"> << <?php echo $mb_previous_step; ?></button>
                                 </div>
                                 <div class="pull-right">
                                    <button  class="btn vivid-green" name="ux_btn_next_step_third" id="ux_btn_next_step_third" onclick="mail_bank_move_to_third_step();"><?php echo $mb_next_step; ?> >></button>
                                 </div>
                              </div>
                           </div>
                        </form>
                        <div id="test_email" style="display:none">
                           <form id="ux_frm_test_email_configuration">
                              <div id="ux_div_test_mail">
                                 <div class="form-group">
                                    <label class="control-label">
                                       <?php echo $mb_email_configuration_test_email_address; ?> :
                                       <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_test_email_address_tooltip; ?>" data-placement="right"></i>
                                       <span class="required" aria-required="true">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="ux_txt_email" id="ux_txt_email" value="<?php
                                    $admin_email = get_option("admin_email");
                                    echo $admin_email;
                                    ?>" placeholder="<?php echo $mb_email_configuration_test_email_address_placeholder; ?>">
                                 </div>
                                 <div class="form-group">
                                    <label class="control-label">
                                       <?php echo $mb_subject; ?> :
                                       <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_subject_test_tooltip; ?>" data-placement="right"></i>
                                       <span class="required" aria-required="true">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="ux_txt_subject" id="ux_txt_subject" value="Test Email - Mail Bank" placeholder="<?php echo $mb_email_configuration_subject_test_placeholder; ?>">
                                 </div>
                                 <div class="form-group">
                                    <label class="control-label">
                                       <?php echo $mb_email_configuration_content; ?> :
                                       <i class="icon-custom-question tooltips" data-original-title="<?php echo $mb_email_configuration_content_tooltip; ?>" data-placement="right"></i>
                                       <span class="required" aria-required="true">*</span>
                                    </label>
                                    <?php
                                    $email_configuration = "This is a demo Test Email for Email Setup - Mail Bank";
                                    wp_editor($email_configuration, 'ux_content', array('teeny' => TRUE, 'textarea_name' => 'description', 'media_buttons' => FALSE, 'textarea_rows' => 5));
                                    ?>
                                    <textarea id="ux_email_configuration_text_area" name="ux_email_configuration_text_area" style="display: none;"></textarea>
                                 </div>
                                 <div class="line-separator"></div>
                                 <div class="form-actions">
                                    <div class="pull-left">
                                       <button type="button" class="btn vivid-green" name="ux_btn_previsious_step_second" id="ux_btn_previsious_step_second" onclick="mail_bank_second_step_settings()"> << <?php echo $mb_previous_step; ?></button>
                                    </div>
                                    <div class="pull-right">
                                       <button class="btn vivid-green" name="ux_btn_save_test_email"  id="ux_btn_save_test_email" onclick="mail_bank_send_test_mail()"><?php echo $mb_email_configuration_send_test_email; ?></button>
                                       <button type="button" class="btn vivid-green" name="ux_btn_save_changes" id="ux_btn_save_changes" onclick="mail_bank_save_changes()"> <?php echo $mb_save_changes; ?></button>
                                    </div>
                                 </div>
                              </div>
                              <div id="console_log_div" style="display: none;">
                                 <div class="form-group">
                                    <label class="control-label"><?php echo $mb_email_configuration_smtp_debugging_output; ?> :</label>
                                    <textarea name="ux_txtarea_console_log" class="form-control" id="ux_txtarea_console_log" rows="15" readonly="readonly"><?php echo $mb_email_configuration_send_test_email_textarea; ?></textarea>
                                 </div>
                              </div>
                              <div id="ux_div_mail_console" style="display: none;">
                                 <div id="result_div">
                                    <div class="form-group">
                                       <label class="control-label"><?php echo $mb_email_configuration_result; ?>:</label>
                                       <textarea name="ux_txtarea_result_log" id="ux_txtarea_result_log" class="form-control" rows="16"  readonly="readonly" ></textarea>
                                    </div>
                                 </div>
                                 <div class="line-separator"></div>
                                 <div class="form-actions">
                                    <div class="pull-left">
                                       <button type="button" class="btn vivid-green" name="ux_btn_previsious_step_second" id="ux_btn_previsious_step_second" onclick="mail_bank_second_step_settings()">  << <?php echo $mb_previous_step; ?></button>
                                    </div>
                                    <div class="pull-right">
                                       <input type="button" class="btn vivid-green" name="ux_btn_another_test_email" onclick="another_test_email_mail_bank();" id="ux_btn_another_test_email" value="<?php echo $mb_email_configuration_send_another_test_email; ?>">
                                       <button type="button" class="btn vivid-green" name="ux_btn_save_changes_on_another_mail" id="ux_btn_save_changes_on_another_mail" onclick="mail_bank_save_changes()"> <?php echo $mb_save_changes; ?></button>
                                    </div>
                                 </div>
                              </div>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php
   } else {
      ?>
      <div class="page-bar">
         <ul class="page-breadcrumb">
            <li>
               <i class="icon-custom-home"></i>
               <a href="admin.php?page=mb_email_configuration">
                  <?php echo $wp_mail_bank; ?>
               </a>
               <span>></span>
            </li>
            <li>
               <span>
                  <?php echo $mb_email_configuration; ?>
               </span>
            </li>
         </ul>
      </div>
      <div class="row">
         <div class="col-md-12">
            <div class="portlet box vivid-green">
               <div class="portlet-title">
                  <div class="caption">
                     <i class="icon-custom-envelope-open"></i>
                     <?php echo $mb_email_configuration; ?>
                  </div>
               </div>
               <div class="portlet-body form">
                  <div class="form-body">
                     <strong><?php echo $mb_user_access_message; ?></strong>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php
   }
}