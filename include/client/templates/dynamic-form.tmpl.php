<script type="text/javascript">
$(document).ready(function(){
    $('[data-toggle="popover"]').popover({
		html: true, 
        placement : 'top',
        trigger : 'hover'
    });

	$('input[type=text]').addClass('form-control');
    $('input[type=email]').addClass('form-control');
    $('input[type=tel]').addClass('form-control');
	$('select').addClass('form-control');
});

</script>
 <div class="panel panel-default">
    <div class="panel-heading">
        <?php print ($form instanceof DynamicFormEntry) 
            ? $form->getForm()->getMedia() : $form->getMedia(); ?>

	<div class="form-header" style="margin-bottom:0.5em">
        <h3 class="panel-title"><?php echo Format::htmlchars($form->getTitle()); ?></h3>
        <em><?php echo Format::display($form->getInstructions()); ?></em>
	</div>
    </div>
    <div class="panel-body">
    <?php
	if ($form->getTitle() == 'Additional Details' || $form->getTitle() == 'Contact Details' || $form->getTitle() == 'Ticket Details'){
		echo '<div class="form">';
	} else {
		echo '<div class="form-inline">';
	}
	
    // Form fields, each with corresponding errors follows. Fields marked
    // 'private' are not included in the output for clients
    global $thisclient;
    foreach ($form->getFields() as $field) {

        if (isset($options['mode']) && $options['mode'] == 'create') {
            if (!$field->isVisibleToUsers() && !$field->isRequiredForUsers())
                continue;
        }
        elseif (!$field->isVisibleToUsers() && !$field->isEditableToUsers()) {
            continue;
        }
        ?>

        <div class="form-group">
            <?php if (!$field->isBlockLevel()) { ?>
                <div class="field-label">
                    <label data-toggle="popover" title="<?php if ($field->get('hint')) echo Format::htmlchars($field->getLocal('label')); ?>" data-content="<?php echo ($field->getLocal('hint'))?>" for="<?php echo $field->getFormName(); ?>">
                        <span class="<?php if ($field->isRequiredForUsers()) echo 'required'; ?>"></span>
                        <?php echo Format::htmlchars($field->getLocal('label')); ?>
                        <?php if ($field->isRequiredForUsers()) { ?>
                            <span class="error">*</span>
                        <?php } ?>
			        </label>
                </div>
            <?php
            }
            ?>
            <div class="field-form"><?php
                $field->render(array('client'=>true));?>
			</div><?php
            ?></label><?php
            foreach ($field->errors() as $e) { ?>
                <div class="alert-danger"><?php echo $e; ?></div>
            <?php }
            $field->renderExtras(array('client'=>true));
            ?>
            
        </div>
        <?php
    }
?>
        </div>
    </div>
</div>
