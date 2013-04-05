<script type="text/javascript">
    function postUpload(files) {
        for (var key in files) {
           var obj = files[key];
           $.post("http://registration.kidzart.com/api/curriccenterupload.php?url=" + obj.url + "&type=" + obj.data.type + "&name=" + obj.data.filename);
           $('#uploadButton').toggle();
        }
    }
</script>
<div class="row">
    <div class="span2">
        <?php include("t/admin/sidenav.php"); ?>
    </div>
    
	<?php
		$page = ($_GET['p'] == "" ? 1 : $_GET['p']);
		$resultsPerPage = 40;
		$limit = ($page-1) * $resultsPerPage;
		$q = "%".$_GET['q']."%";
		
		if ($_GET['q'] == "") {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM curriccenter_files WHERE name LIKE ? OR tags LIKE ? ORDER BY name LIMIT $limit,$resultsPerPage");
			$ps->execute(array($q, $q));
			$files = $ps->fetchAll();
			
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		} else {
			$ps = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM curriccenter_files WHERE name LIKE ? OR tags LIKE ? ORDER BY name LIMIT $limit,$resultsPerPage");
			$ps->execute(array($q, $q));
			$files = $ps->fetchAll();
		
			$ps = $pdo->prepare("SELECT FOUND_ROWS()");
			$ps->execute();
			$rows = $ps->fetchColumn();
		}

		$pages = ceil($rows / $resultsPerPage);
	?>
    
    <div class="span10">
        <h2>Curriculum Center <small>Showing <?php echo $limit+1 ?>-<?php echo min($limit+$resultsPerPage, $rows) ?> of <?php echo $rows ?></small></h2>
        
        <hr>
         <!-- Adds the Filepicker.io javascript library to the page -->
            <script src="https://api.filepicker.io/v0/filepicker.js"></script>
            <script type="text/javascript">
                //Seting up Filepicker.io with your api key
                filepicker.setKey('AAa5YrHpScaVURvNVu0bIz');
				        filepicker.stat(file, {filename: true}, function(metadata){str += JSON.stringify(metadata);});
					<!--	filepicker.pickMultiple(function(metadata){str += JSON.stringify(metadata);}); -->
            </script>
            <form action="/admin/curriccenter" method="get" style="margin:0;">
              <input type="filepicker-dragdrop" data-fp-multiple="true" data-fp-option-services="COMPUTER, URL" 
              data-fp-button-class="btn btn-primary" onchange="postUpload(event.files)" data-fp-button-text="Upload" data-fp-openTo="COMPUTER"/>
              <button type="submit" class="btn btn-success hide" id="uploadButton">Finish Upload</button>
            </form>

            <form class="form-search pull-right" style="margin-top: -31px;" action="/admin/curriccenter/" method="get">
                <div class="input-append">
                  <input type="text" class="span3 search-query" name="q" placeholder="Search by name or tags">
                  <button type="submit" class="btn">Search</button>
                </div>
            </form>
        <hr>

  <?php foreach ($files as $file) { ?>

    <?php if ($col == 0) { ?>
        <div class="row-fluid">
          <ul class="thumbnails">
    <?php } ?>
                  <li class="span3">
                  <a class="thumbnail" href="#<?php echo $file['id'] ?>" data-toggle="modal" class="nounderline">
                      <?php if ($file['thumbnail'] == "") { ?>
                        <?php if (strpos($file['type'], "image") === FALSE) { ?>
                          <img alt="" src="http://placehold.it/173x173&text=No Thumbnail"/>
                        <?php } else { ?>
                          <img alt="" src="<?php echo $file['url'] ?>/convert?w=173"/>
                        <?php } ?>
                      <?php } else { ?>
                        <img alt="" src="<?php echo $file['thumbnail'] ?>/convert?w=173"/>
                      <?php } ?>
                      <div class="caption" style="padding: 0px 2px;">
                        <h5>
                          <?php echo $file['name'] ?>
                          <?php if ($file['tags'] != "") { ?>
                            <?php 
                            $tags = explode(" ", $file['tags']);
                            foreach ($tags as $tag) {
                            ?>
                              <span class="label label-info" style="margin-bottom: 10px;"><?php echo strtolower($tag) ?></span>
                            <?php } ?>
                          <?php } ?>
                        </h5>
                      </div>
                  </a>
                </li>
                  <?php $col++; ?>
    <?php if ($col > 3) { ?>
                </ul>
            </div>
            <?php $col = 0; ?>
    <?php } ?>
  <?php } ?>

  <?php if ($col != 0) { ?>
    <?php while ($col < 4) { ?>
      <li class="span3">&nbsp;</li>
      <?php $col++; ?>
    <?php } ?>

    <?php if ($col > 3) { ?>
              </ul>
          </div>
          
        <?php $col = 0; ?>
    <?php } ?>
  <?php } ?>

<?php
  $ps = $pdo->prepare("SELECT * FROM curriccenter_files ORDER BY id");
  $ps->execute();
  $files = $ps->fetchAll();
?>

<div class="pagination">
	<ul>
		<?php if ($page > 1) { ?>
        <li><a href="?q=<?php echo $_GET['q'] ?>&p=<?php echo $page-1 ?>">« Prev</a></li>
		<?php } else { ?>
        <li class="disabled"><a href="#">« Prev</a></li>
		<?php } ?>
		
		<?php $p = 1; while ($p <= $pages) { ?>
        <li <?php echo ($p == $page ? 'class="active"' : "") ?>><a href="?q=<?php echo $_GET['q'] ?>&p=<?php echo $p ?>"><?php echo $p ?></a></li>
		<?php $p++; ?>
		<?php } ?>
		
		<?php if ($page < $pages) { ?>
        <li><a href="?q=<?php echo $_GET['q'] ?>&p=<?php echo $page+1 ?>">Next »</a></li>
		<?php } else { ?>
        <li class="disabled"><a href="#">Next »</a></li>
		<?php } ?>
	</ul>
</div>

<?php foreach ($files as $file) { ?>
<div class="modal hide fade" id="<?php echo $file['id'] ?>">
  <form action="/admin/curriccenter" method="post" style="margin:0;">
      <input type="hidden" name="action" value="doEditCurricCenterFile">
      <input type="hidden" name="id" value="<?php echo $file['id'] ?>">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Edit File</h3>
      </div>
      <div class="modal-body">
            <?php if ($file['thumbnail'] == "") { ?>
              <img src="<?php echo $file['url'] ?>/convert?h=100" class="img-polaroid pull-right">
            <?php } else { ?>
              <img src="<?php echo $file['thumbnail'] ?>/convert?h=100" class="img-polaroid pull-right">
            <?php } ?>

            <div class="control-group">
              <label class="control-label">Name</label>
              <div class="controls">
                <input type="text" name="name" value="<?php echo $file['name'] ?>"/>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label">Tags <small class="muted">(Separated by spaces)</small></label>
              <div class="controls">
                <input type="text" name="tags" value="<?php echo $file['tags'] ?>"/>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label">Change Thumbnail</label>
              <div class="controls">
                <input type="filepicker" name="thumbnail" data-fp-option-services="COMPUTER,URL" data-fp-openTo="COMPUTER" data-fp-button-class="btn"/>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label">Download File</label>
              <div class="controls">
                <a href="<?php echo $file['url'] ?>?dl=true" class="btn btn-primary"><i class="icon-download-alt icon-white"></i> Download File</a>
              </div>
            </div>
      </div>
      <div class="modal-footer">
        <a href="/admin/curriccenter?action=doDeleteCurricCenterFile&id=<?php echo $file['id'] ?>" class="btn btn-danger pull-left">Delete File</a>
        <a href="#" class="btn" data-dismiss="modal">Close</a>
        <button type="submit" class="btn btn-success">Save</button>
      </div>
  </form>
</div>

<?php } ?>