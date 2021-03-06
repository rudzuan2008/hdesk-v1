<?php
if(!defined('KTKADMININC') || !$thisuser->isadmin()) die(_('Access Denied'));
$info=null;
$pmenu=_('DEPARTMENTS');

if($dept && $_REQUEST['a']!='new'){
    //Editing Department.
    $title=_('Update Department');
    $action='update';
    $info=$dept->getInfo();
}else {
    $title=_('New Department');
    $action='create';
    $info['ispublic']=isset($info['ispublic'])?$info['ispublic']:1;
    $info['ticket_auto_response']=isset($info['ticket_auto_response'])?$info['ticket_auto_response']:1;
    $info['message_auto_response']=isset($info['message_auto_response'])?$info['message_auto_response']:1;
    $info['company_id']=Sys::getCompanyId();

}
$companies = db_query('SELECT company_id, name FROM '.COMPANY_TABLE);

//Sys::console_log('debug', 'company id:'.$info['company_id']);
//$info['company_id']=$thisuser->getCompanyId();
$info=($errors && $_POST)?Format::input($_POST):Format::htmlchars($info);

?>
<div class="msg"><?=$title?></div>
<form class="rz-form" action="admin.php?t=dept&id=<?=$info['dept_id']?>" method="POST" name="dept">
  <input type="hidden" name="do" value="<?=$action?>">
  <input type="hidden" name="a" value="<?=Format::htmlchars($_REQUEST['a'])?>">
  <input type="hidden" name="t" value="dept">
  <input type="hidden" name="dept_id" value="<?=$info['dept_id']?>">
  <input type="hidden" name="menu" value="<?=$pmenu?>">
  <input type="hidden" name="randcheck" value="<?= $rand ?>">
  <input type="hidden" name="sel_company_id" value="<?= $info['company_id'] ?>">

  <table style="width:100%; border: 0px solid" class="tform">
      <tr class="header"><td colspan=2><?= _('Department') ?></td></tr>
      <tr class="subheader"><td colspan=2 ><?= _('Dept depends on email &amp; help topics settings for incoming tickets.') ?></td></tr>
      <tr><th><?= _('Dept Name:') ?></th>
          <td><input type="text" name="dept_name" size=25 value="<?=$info['dept_name']?>">
              &nbsp;<font class="error">*&nbsp;<?=$errors['dept_name']?></font>

          </td>
      </tr>
      <tr>
          <th><?= _('Company:') ?></th>
          <td>
          <?php if ($thisuser->getUserName()=="admin") {?>
          		<select name="company_id">
                  <option value=0><?= _('Select Company') ?></option>
                  <?php
                  while (list($id,$name) = db_fetch_row($companies)){
                      $selected = ($info['company_id']==$id)?'selected':''; ?>
                  <option value="<?=$id?>" <?=$selected?>><?=$name?> </option>
                  <?php
                  }?>
              </select>

          <?php }else{?>
          	<?= Sys::getCompany() ?><input type="hidden" name="company_id" value="<?=$rep['company_id']?>">
          <?php }?>
          &nbsp;<font class="error">*&nbsp;<?=$errors['company_id']?></font></td>
      </tr>
      <tr>
          <th><?= _('Dept Email:') ?></th>
          <td>
              <select name="email_id">
                  <option value=""><?= _('Select One') ?></option>
                  <?php
                  $emails=db_query('SELECT email_id,email,name,smtp_active FROM '.EMAIL_TABLE);
                  while (list($id,$email,$name,$smtp) = db_fetch_row($emails)){
                      $email=$name?"$name &lt;$email&gt;":$email;
                      if($smtp)
                          $email.=' (SMTP)';
                      ?>
                   <option value="<?=$id?>"<?=($info['email_id']==$id)?'selected':''?>><?=$email?></option>
                  <?php
                  }?>
               </select>
               &nbsp;<font class="error">*&nbsp;<?=$errors['email_id']?></font>&nbsp;(<?=_('outgoing email')?>)
          </td>
      </tr>
      <?php if($info['dept_id']) { //update
          $users= db_query('SELECT staff_id,CONCAT_WS(" ",firstname,lastname) as name FROM '.STAFF_TABLE.' WHERE dept_id='.db_input($info['dept_id']));
          ?>
      <tr>
          <th><?= _('Dept Manager:') ?></th>
          <td>
              <?php if($users && db_num_rows($users)) { ?>
              <select name="manager_id">
                  <option value=0 ><?= _('-------None-------') ?></option>
                  <option value=0 disabled ><?= _('Select Manager (optional)') ?></option>
                   <?php
                   while (list($id,$name) = db_fetch_row($users)){ ?>
                      <option value="<?=$id?>"<?=($info['manager_id']==$id)?'selected':''?>><?=$name?></option>
                   <?php } ?>

              </select>
               <?php }else { ?>
              <?= _('No Users in this dept. (Add Users)') ?>
                     <input type="hidden" name="manager_id"  value="0" />
               <?php } ?>
                  &nbsp;<font class="error">&nbsp;<?=$errors['manager_id']?></font>
          </td>
      </tr>
      <?php } ?>
      <tr><th><?= _('Dept Type') ?></th>
          <td>
              <input type="radio" name="ispublic"  value="1"   <?=$info['ispublic']?'checked':''?> /><?= _('Public') ?>
              <input type="radio" name="ispublic"  value="0"   <?=!$info['ispublic']?'checked':''?> /><?= _('Private (Hidden to the user/client)') ?>
              &nbsp;<font class="error"><?=$errors['ispublic']?></font>
          </td>
      </tr>
      <tr>
          <th><br /><?= _('Dept Signature:') ?></th>
          <td>
              <i><?= _('Required when Dept is public') ?></i>&nbsp;&nbsp;&nbsp;<font class="error"><?=$errors['dept_signature']?></font><br/>
              <textarea name="dept_signature" cols="21" rows="5" style="width: 60%;"><?=$info['dept_signature']?></textarea>
              <br>
              <input type="checkbox" name="can_append_signature" <?=$info['can_append_signature'] ?'checked':''?> >
              <?= _('can be appended to responses.&nbsp;(available as a choice for public departments)') ?>
          </td>
      </tr>
      <tr><th><?= _('Email Templates:') ?></th>
          <td>
              <select name="tpl_id">
                  <option value=0 disabled><?= _('Select Template') ?></option>
                  <option value="0" selected="selected"><?= _('System Default') ?></option>
                  <?php
                  $templates=db_query('SELECT tpl_id,name FROM '.EMAIL_TEMPLATE_TABLE.' WHERE tpl_id!='.db_input($cfg->getDefaultTemplateId()));
                  while (list($id,$name) = db_fetch_row($templates)){
                      $selected = ($info['tpl_id']==$id)?'SELECTED':''; ?>
                      <option value="<?=$id?>"<?=$selected?>><?=Format::htmlchars($name)?></option>
                  <?php
                  }?>
              </select><font class="error">&nbsp;<?=$errors['tpl_id']?></font><br/>
              <i><?= _('Used for outgoing emails,alerts and notices to user and staff.') ?></i>
          </td>
      </tr>
      <tr class="header"><td colspan=2><?= _('Autoresponders') ?></td></tr>
      <tr class="subheader"><td colspan=2>
              <?= _('Global auto-response settings in preference section must be enabled for Dept \'Enable\' setting to take effect.') ?>
          </td>
      </tr>
      <tr><th><?= _('New Ticket:') ?></th>
          <td>
              <input type="radio" name="ticket_auto_response"  value="1"   <?=$info['ticket_auto_response']?'checked':''?> /><?= _('Enable') ?>
              <input type="radio" name="ticket_auto_response"  value="0"   <?=!$info['ticket_auto_response']?'checked':''?> /><?= _('Disable') ?>
          </td>
      </tr>
      <tr><th><?= _('New Message:') ?></th>
          <td>
              <input type="radio" name="message_auto_response"  value="1"   <?=$info['message_auto_response']?'checked':''?> /><?= _('Enable') ?>
              <input type="radio" name="message_auto_response"  value="0"   <?=!$info['message_auto_response']?'checked':''?> /><?= _('Disable') ?>
          </td>
      </tr>
      <tr>
          <th><?= _('Auto Response Email:') ?></th>
          <td>
              <select name="autoresp_email_id">
                  <option value="0" disabled><?= _('Select One') ?></option>
                  <option value="0" selected="selected"><?= _('Dept Email (above)') ?></option>
                  <?php
                  $emails=db_query('SELECT email_id,email,name,smtp_active FROM '.EMAIL_TABLE.' WHERE email_id!='.db_input($info['email_id']));
                  if($emails && db_num_rows($emails)) {
                      while (list($id,$email,$name,$smtp) = db_fetch_row($emails)){
                          $email=$name?"$name &lt;$email&gt;":$email;
                          if($smtp)
                              $email.=' (SMTP)';
                          ?>
                          <option value="<?=$id?>"<?=($info['autoresp_email_id']==$id)?'selected':''?>><?=$email?></option>
                      <?php
                      }
                  }?>
               </select>
               &nbsp;<font class="error">&nbsp;<?=$errors['autoresp_email_id']?></font>&nbsp;<br/>
               <i><?= _('Email address used to send auto-responses, if enabled.') ?></i>
          </td>
      </tr>
  </table>
  <div class="centered">
    <input class="button" type="submit" name="submit" value="<?= _('Submit') ?>">
    <input class="button" type="reset" name="reset" value="<?= _('Reset') ?>">
    <input class="button" type="button" name="cancel" value="<?= _('Cancel') ?>" onClick='window.location.href="admin.php?t=dept&menu=<?=$pmenu?>&sid=<?=$info['company_id']?>"'>
  </div>
</form>
