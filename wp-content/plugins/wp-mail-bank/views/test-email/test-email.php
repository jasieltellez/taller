<?php
/**
 * This Template is used for sending Test Email.
 *
 * @author  Tech Banker
 * @package wp-mail-bank/views/test-email
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
   } else if (test_email_mail_bank == "1") {
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
                  <?php echo $mb_test_email; ?>
               </span>
            </li>
         </ul>
      </div>
      <div class="row">
         <div class="col-md-12">
            <div class="portlet box vivid-green">
               <div class="portlet-title">
                  <div class="caption">
                     <i class="icon-custom-envelope"></i>
                     <?php echo $mb_test_email; ?>
                  </div>
                  <p class="premium-editions">
                     <?php echo $mb_upgrade_kanow_about ?> <a href="https://mail-bank.tech-banker.com/" target="_blank" class="premium-edition-text"><?php echo $mb_full_features ?></a> <?php echo $mb_chek_our; ?> <a href="https://mail-bank.tech-banker.com/backend-demos/" target="_blank" class="premium-edition-text"><?php echo $mb_online_demos; ?></a>
                  </p>
               </div>
               <div class="portlet-body form">
                  <div class="form-body">
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
                              <div class="pull-right">
                                 <button class="btn vivid-green" name="ux_btn_save_test_email"  id="ux_btn_save_test_email" onclick="mail_bank_send_test_mail()"><?php echo $mb_email_configuration_send_test_email; ?></button>
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
                              <div class="pull-right">
                                 <input type="button" class="btn vivid-green" name="ux_btn_another_test_email" <?php echo!extension_loaded("openssl") ? "disabled=disabled" : ""; ?> onclick="another_test_email_mail_bank();" id="ux_btn_another_test_email" value="<?php echo $mb_email_configuration_send_another_test_email; ?>">
                              </div>
                           </div>
                        </div>
                     </form>
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
                  <?php echo $mb_test_email; ?>
               </span>
            </li>
         </ul>
      </div>
      <div class="row">
         <div class="col-md-12">
            <div class="portlet box vivid-green">
               <div class="portlet-title">
                  <div class="caption">
                     <i class="icon-custom-envelope"></i>
                     <?php echo $mb_test_email; ?>
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