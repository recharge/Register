<div class="row">

<?php
$ps = $pdo->prepare("SELECT * FROM templates WHERE id = ?");
$ps->execute(array($id));
$template = $ps->fetch(PDO::FETCH_ASSOC);
?>
<?php if ($template) { ?>



	<div class="span12">
		<h3><?php echo $template['name'] ?></h3>

		<?php if ($template['owner'] != $uid) { ?>
			<div class="alert">
				    <button type="button" class="close" data-dismiss="alert">×</button>
				    <strong>This template is owned by someone else.</strong> Edit below and we'll save a copy to your account.
				    </div>
		<?php } ?>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/email/<?php echo $template['id'] ?>" method="POST">
			<input type="hidden" name="action" value="doUpdateFranchiseTemplate" />
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $template['name'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "subject"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Subject</label>
	              <div class="controls">
	                <input class="input-xxlarge" type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $template['subject'] ?>" />
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "body"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Body</label>
	              <div class="controls">
	                <textarea class="input-xxlarge ckeditor" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/><?php echo $template['body'] ?></textarea>
	              </div>
	            </div>
	            
	            <div class="form-actions">
	            	<button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
	            	<?php if ($template['owner'] != $uid) { ?>
	            		<a href="/franchise/email" class="btn"><i class="icon-chevron-left"></i> Back</a>
	            	<?php } ?>
	    		</div>
		</form>

	</div>
<?php } else if ($id == "new") { ?>
	<div class="span12">
		<h3>New Template</h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/email/new" method="POST">
				<input type="hidden" name="action" value="doAddFranchiseTemplate" />
	            
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Name</label>
	              <div class="controls">
	                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $template['name'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "subject"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Subject</label>
	              <div class="controls">
	                <input class="input-xxlarge" type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $template['subject'] ?>"/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "body"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Body</label>
	              <div class="controls">
	                <textarea class="input-xxlarge ckeditor" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/><?php echo $template['body'] ?></textarea>
	              </div>
	            </div>
	            
	            <div class="form-actions">
				    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
	    		</div>
		</form>

	</div>
	<?php } else if ($id == "compose") { ?>

	<script type="text/javascript">
	$(document).ready(function() { 
		$('#template').change(function() {
			var id = $(this).val();

			$.getJSON('/api/templates?id=' + id, function(data) {
				$('#input_subject').val(data.subject);
				var editor = CKEDITOR.instances.input_body;
		   		editor.setData(data.body);
			});
		});
	});
	</script>

	<?php
	if (is_array($_POST['child'])) {
		$_POST['child'] = array_unique($_POST['child']);
		foreach ($_POST['child'] as $key => $value) {
			$_POST['child'][$key] = "'$value'";
		}
		$children = implode(",", $_POST['child']); //echo $ids;
	}
	if (is_array($_POST['parent'])) {
		$_POST['parent'] = array_unique($_POST['parent']);
		foreach ($_POST['parent'] as $key => $value) {
			$_POST['parent'][$key] = "'$value'";
		}
		$parents = implode(",", $_POST['parent']); //echo $ids;
	}

	if (strlen($children) == 0) { $children = "''"; }
	if (strlen($parents) == 0) { $parents = "''"; }

	$ps = $pdo->prepare("SELECT id, name, email FROM users WHERE id IN (SELECT parent FROM children WHERE id IN ($children)) OR id IN (SELECT id FROM users WHERE id IN ($parents))");
    $ps->execute();
    $parents = $ps->fetchAll();
	
	?>

	<div class="span12">
		<h3>Compose</h3>
	
	<hr>
		
		<form class="bs-docs-example form-horizontal" action="/franchise/email/compose" method="POST">
				<input type="hidden" name="action" value="doSendEmail" />
				<?php foreach ($parents as $parent) { ?>
				<input type="hidden" name="to[]" value="<?php echo $parent['id'] ?>" />
				<?php } ?>
	            
	            <?php $fieldName = "name"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">To</label>
	              <div class="controls" style="padding-top: 5px">
	                <?php foreach ($parents as $parent) { ?>
	                	<span class="toblock"><?php echo $parent['name'] ?></span>
	                <?php } ?>
	                
	              </div>
	            </div>

	            <?php
	            	$ps = $pdo->prepare("SELECT * FROM templates WHERE owner = 'admin' OR owner = ? ORDER BY owner");
	            	$ps->execute(array($uid));
	            	$templates = $ps->fetchAll();
	            ?>

	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Template</label>
	              <div class="controls">
	                <select id="template">
	                	<option value="">None</option>
	                	<?php foreach ($templates as $template) { ?>
	                	<option value="<?php echo $template['id'] ?>"><?php echo $template['name'] ?></option>
	                	<?php } ?>
	                </select>
	              </div>
	            </div>
	            
	            <?php $fieldName = "subject"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Subject</label>
	              <div class="controls">
	                <input style="width: 743px;" type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value=""/>
	                <?php if (isset($badFields[$fieldName])) { ?>
	                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
	                <?php } ?>
	              </div>
	            </div>
	            
	            <?php $fieldName = "body"; ?>
	            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
	              <label for="inputEmail" class="control-label">Body</label>
	              <div class="controls">
	                <textarea class="ckeditor" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/></textarea>
	              </div>
	            </div>
	            
	            <div class="form-actions">
				    <button type="submit" class="btn btn-success"><i class="icon-thumbs-up icon-white"></i> Send</button>
	    		</div>
		</form>

	</div>
	<?php } else { ?>
	<div class="span2">
		<?php include("sidenav.php"); ?>
	</div>

	<?php
		$ps = $pdo->prepare("SELECT * FROM templates WHERE owner = 'admin' OR owner = ? ORDER BY owner");
		$ps->execute(array($uid));
		$templates = $ps->fetchAll();
	?>

	<div class="span10">
		<h2>Email Templates</h2>
		
		<hr>
		
		<table class="table table-striped">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Subject</th>
                  <th>Owned By</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
              	<?php foreach ($templates as $template) { ?>
                <tr>
                  <td><a href="/franchise/email/<?php echo $template['id'] ?>"><?php echo $template['name'] ?></a></td>
                  <td><?php echo $template['subject'] ?></td>
                  <td>
                  	<?php if ($template['owner'] == $uid) { ?>
                  		<span class="label label-success">Me</span>
                  	<?php } else { ?>
                  		<span class="label">Everyone</span>
                  	<?php } ?>
                  </td>
                  <td>    
                  	<?php if ($template['owner'] == $uid) { ?>
                  	<div class="btn-group">
				    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    Action
				    <span class="caret"></span>
				    </a>
				    <ul class="dropdown-menu">
				    	<li><a href="/franchise/email/<?php echo $template['id'] ?>"><i class="icon-pencil"></i> Edit</a></li>
				    	<li><a href="?action=doCopyEmail&id=<?php echo $template['id'] ?>"><i class="icon-retweet"></i> Copy</a></li>
				    	<li class="divider"/>
				    	<li><a href="?action=doDeleteFranchiseEmail&id=<?php echo $template['id'] ?>"><i class="icon-trash"></i> Delete</a></li>
				    </ul>
				    </div>
				    <?php } else { ?>
				    <div class="btn-group">
				    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
				    Action
				    <span class="caret"></span>
				    </a>
				    <ul class="dropdown-menu">
				    	<li><a href="/franchise/email/<?php echo $template['id'] ?>"><i class="icon-pencil"></i> Edit</a></li>
				    	<li><a href="?action=doCopyEmail&id=<?php echo $template['id'] ?>"><i class="icon-retweet"></i> Copy</a></li>
				    </ul>
				    </div>
				    <?php } ?>
				  </td>
                </tr>
                <?php } ?>
                <?php if (!$template) { ?>
                <tr>
                  <td colspan="3">No Email Templates Found</td>
                </tr>
                <?php } ?>
              </tbody>
            </table>  

            <p><a href="/franchise/email/new" class="btn btn-primary"><i class="icon-plus icon-white"></i> Add Template</a></p>   
	</div>
<?php } ?>
</div>

<script type="text/javascript" src="//api.filepicker.io/v1/filepicker.js"></script>
<script type="text/javascript">
	//Seting up Filepicker.io with your api key
	filepicker.setKey('AmbykCv5aQmWKNnYERsRnz');
</script>
<script type="text/javascript" src="/ckeditor/ckeditor.js"></script>
<script type="text/javascript">

	CKEDITOR.replace( 'input_body', {
	toolbar: [

	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
	{ name: 'styles', items: [  'Font', 'FontSize', 'Styles', 'Format' ] },
	{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
	'/',
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo' ] },
	{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-', 'Templates' ] },
	{ name: 'links', items: [ 'Link', 'Unlink'] },
	{ name: 'insert', items: [ 'Image', 'upload','Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'NumberedList', 'BulletedList'] },
	{ name: 'hey', items: [ 'Maximize', 'ShowBlocks' ] }
	],
	extraPlugins: 'autogrow,upload',
	autoGrow_maxHeight: 800,

	// Remove the Resize plugin as it does not make sense to use it in conjunction with the AutoGrow plugin.
	removePlugins: 'resize'
});

function sayhey() {
		filepicker.pick({
		    container: 'modal',
		    services:['COMPUTER'],
		  },
		  function(FPFile){
		    console.log(JSON.stringify(FPFile));
		    var editor = CKEDITOR.instances.input_body;
		    editor.insertHtml("<img src='" + FPFile.url + "'>");
		  },
		  function(FPError){
		    console.log(FPError.toString());
		  }
		);
	}
</script>
