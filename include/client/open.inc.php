<?php
if(!defined('OSTCLIENTINC')) die('Access Denied!');
$info=array();
if($thisclient && $thisclient->isValid()) {
    $info=array('name'=>$thisclient->getName(),
                'email'=>$thisclient->getEmail(),
                'phone'=>$thisclient->getPhoneNumber());
}

$info=($_POST && $errors)?Format::htmlchars($_POST):$info;

$form = null;
if (!$info['topicId'])
    $info['topicId'] = $cfg->getDefaultTopicId();

$forms = array();
if ($info['topicId'] && ($topic=Topic::lookup($info['topicId']))) {
    foreach ($topic->getForms() as $F) {
        if (!$F->hasAnyVisibleFields())
            continue;
        if ($_POST) {
            $F = $F->instanciate();
            $F->isValidForClient();
        }
        $forms[] = $F;
    }
}

?>
<div class="row">
<div class="page-title">
	<h1><?php echo __('Open a New Ticket');?></h1>
	<div><?php echo __('Please fill in the form below to open a new ticket.');?></div>
</div>
</div>
<form id="ticketForm" method="post" action="open.php" enctype="multipart/form-data">
  <?php csrf_token(); ?>
  <input type="hidden" name="a" value="open">

        <div class="panel panel-default">

            <div class="panel-heading">
			<div class="form-header" style="margin-bottom:0.5em">
                <h3 class="panel-title"> <?php echo __('Help Topic');?> </h3>
				</div>
                <em>  <?php echo __('Select the Relevant Topic');?>  </em>
            </div>
            <div class="panel-body">
                <select class="form-control" id="topicId" name="topicId" onchange="javascript:
                        var data = $(':input[name]', '#dynamic-form').serialize();
                        $.ajax(
                          'ajax.php/form/help-topic/' + this.value,
                          {
                            data: data,
                            dataType: 'json',
                            success: function(json) {
                              $('#dynamic-form').empty().append(json.html);
                              $(document.head).append(json.media);
                            }
                          });">
                    <option value="" selected="selected">&mdash; <?php echo __('Select a Help Topic');?> &mdash;</option>
                    <?php
                    if($topics=Topic::getPublicHelpTopics()) {
                        foreach($topics as $id =>$name) {
                            echo sprintf('<option value="%d" %s>%s</option>',
                                    $id, ($info['topicId']==$id)?'selected="selected"':'', $name);
                        }
                    } else { ?>
                        <option value="0" ><?php echo __('General Inquiry');?></option>
                    <?php
                    } ?>
                </select>
                <span class="error"><?php echo $errors['topicId']; ?></span>
            </div>
        </div>
    <div id="dynamic-form">
        <?php foreach ($forms as $form) {
            include(CLIENTINC_DIR . 'templates/dynamic-form.tmpl.php');
        } ?>
    </div>

    <?php
    if($cfg && $cfg->isCaptchaEnabled() && (!$thisclient || !$thisclient->isValid())) {
        if($_POST && $errors && !$errors['captcha'])
            $errors['captcha']=__('Please re-enter the text again');
        ?>

        <div class="row">
            <div class="col-sm-12">
                <div class="captchaRow">
                    <span class="required"><?php echo __('CAPTCHA Text');?>:</span>
                    <span>
                        <span class="captcha"><img src="captcha.php" border="0" align="left"></span>
                        &nbsp;&nbsp;
                        <input id="captcha" type="text" name="captcha" size="6" autocomplete="off">
                        <small><?php echo __('Enter the text shown on the image.');?></small>
                        <span class="error">*&nbsp;<?php echo $errors['captcha']; ?></span>
                    </span>
                </div>
            </div>
        </div>
    <?php
    } ?>
    <br />
    <div class="row">
        <div class="col-md-2 col-sm-12">
            <input class="btn btn-success btn-block col-md-2 col-sm-12" type="submit" value="<?php echo __('Create Ticket');?>">
        </div>
        <div class="col-md-2 col-sm-12">
            <input class="btn btn-warning btn-block col-md-2 col-sm-12" type="reset" name="reset" value="<?php echo __('Reset');?>">
        </div>
        <div class="col-md-2 col-sm-12">
            <input class="btn btn-default btn-block col-md-2 col-sm-12" type="button" name="cancel" value="<?php echo __('Cancel'); ?>" onclick="javascript:
                $('.richtext').each(function() {
                    var redactor = $(this).data('redactor');
                    if (redactor && redactor.opts.draftDelete)
                        redactor.deleteDraft();
                });
                window.location.href='index.php';">
        </div>
    </div>
</form>
<div class="clearfix"></div>
