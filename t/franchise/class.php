<?php
$class = $params[1];
?>
<script type="text/javascript">
	$(function() {
		$( "#input_startdate" ).datepicker({
			onSelect: function( selectedDate ) {
				$( "#input_enddate" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		$( "#input_enddate" ).datepicker();
	});
	
	$(document).ready(function() {
	    $(".meeting_place").change(function() {
	        if ($(this).val() == "Add New Meeting Place...") {
	        	$('#addMeetingPlace').modal('show');
	        }
	    });
	    $("#addMeeting").click(function() {
	        $('#addMeetingModal').modal('show');
	    });
	    
	    $("#input_price").change(function() {
	    	$("#input_payments_price").val($(this).val());
	    });
	    
	    $("#addNewMeetingPlace").submit(function() {
	    	$.ajax({
		      url: "/api/meetingplace",
		      data: {
		        name: $("#meetingPlaceName").val(),
		        address: $("#meetingPlaceAddress").val()
		      },
		      success: function(data){
		      	$("#refresh").val('1');
		        $("#classForm").submit();
		      }});
		      return false;
	    });
	});
</script>
<div class="row">
	<div class="span12">
		
<?php if ($class == "new") { ?>
	<h3>New Class</h3>
	
	<form class="bs-docs-example form-horizontal" method="POST" id="classForm">
			<input type="hidden" name="action" value="doAddClass" />
			<input type="hidden" name="refresh" id="refresh" value="0" />
            <legend>Class Information</legend>
            
            <?php $fieldName = "name"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Class Name</label>
              <div class="controls">
                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "startdate"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Start Date</label>
              <div class="controls">
                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "enddate"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">End Date</label>
              <div class="controls">
                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $_POST[$fieldName] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "active"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Active</label>
              <div class="controls">
                <select name="<?php echo $fieldName ?>">
					<option value="1">Yes</option>
					<option value="0">No</option>
				</select>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
                <span class="help-block">Customers will only be able to view, search and register for active classes</span>
              </div>
            </div>
            
            <?php $fieldName = "meeting_day"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Meeting Days</label>
              <div class="controls">
                    <label class="checkbox inline">
				    	<input type="checkbox" name="meeting_day[]" value="1"<?php echo (in_array("1", $_POST['meeting_day']) ? 'checked="checked"' : "")?>> Sun
				    </label>
				    <label class="checkbox inline">
				    	<input type="checkbox" name="meeting_day[]" value="2"<?php echo (in_array("2", $_POST['meeting_day']) ? 'checked="checked"' : "")?>> Mon
				    </label>
				    <label class="checkbox inline">
				    	<input type="checkbox" name="meeting_day[]" value="3"<?php echo (in_array("3", $_POST['meeting_day']) ? 'checked="checked"' : "")?>> Tue
				    </label>
				    <label class="checkbox inline">
				    	<input type="checkbox" name="meeting_day[]" value="4"<?php echo (in_array("4", $_POST['meeting_day']) ? 'checked="checked"' : "")?>> Wed
				    </label>
				    <label class="checkbox inline">
				    	<input type="checkbox" name="meeting_day[]" value="5"<?php echo (in_array("5", $_POST['meeting_day']) ? 'checked="checked"' : "")?>> Thu
				    </label>
				    <label class="checkbox inline">
				    	<input type="checkbox" name="meeting_day[]" value="6"<?php echo (in_array("6", $_POST['meeting_day']) ? 'checked="checked"' : "")?>> Fri
				    </label>
				    <label class="checkbox inline">
				    	<input type="checkbox" name="meeting_day[]" value="7"<?php echo (in_array("7", $_POST['meeting_day']) ? 'checked="checked"' : "")?>> Sat
				    </label>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-block"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <div class="control-group">
              <label for="inputEmail" class="control-label">Location</label>
              <div class="controls">
              	<select name="meeting_place" class="meeting_place">
              		<?php
					$ps = $pdo->prepare("SELECT * FROM meeting_places WHERE franchise = ? ORDER BY name");
					$ps->execute(array($user['id']));
					$meeting_places = $ps->fetchAll();
					foreach ($meeting_places as $meeting_place) {
					?>
					<option value="<?php echo $meeting_place['id'] ?>"><?php echo $meeting_place['name'] ?></option>
					<?php } ?>
					<?php if (!$meeting_places) { ?>
					<option>No Meeting Places Found</option>
					<?php } ?>
					<optgroup label="----------"></optgroup>
					<option>Add New Meeting Place...</option>
				</select>
                <span class="help-block">You'll be able to add a new meeting place on the next screen</span>
              </div>
            </div>
            
            <div class="control-group">
              <label for="inputEmail" class="control-label">Meeting Time</label>
              <div class="controls">
              	<select name="hour" class="input-mini">
					<option>12</option>
					<option>1</option>
					<option>2</option>
					<option selected="selected">3</option>
					<option>4</option>
					<option>5</option>
					<option>6</option>
					<option>7</option>
					<option>8</option>
					<option>9</option>
					<option>10</option>
					<option>11</option>
				</select>
				:
				<select name="minute" class="input-mini">
					<option selected="selected">00</option>
					<option>01</option>
					<option>02</option>
					<option>03</option>
					<option>04</option>
					<option>05</option>
					<option>06</option>
					<option>07</option>
					<option>08</option>
					<option>09</option>
					<option>10</option>
					<option>11</option>
					<option>12</option>
					<option>13</option>
					<option>14</option>
					<option>15</option>
					<option>16</option>
					<option>17</option>
					<option>18</option>
					<option>19</option>
					<option>20</option>
					<option>21</option>
					<option>22</option>
					<option>23</option>
					<option>24</option>
					<option>25</option>
					<option>26</option>
					<option>27</option>
					<option>28</option>
					<option>29</option>
					<option>30</option>
					<option>31</option>
					<option>32</option>
					<option>33</option>
					<option>34</option>
					<option>35</option>
					<option>36</option>
					<option>37</option>
					<option>38</option>
					<option>39</option>
					<option>40</option>
					<option>41</option>
					<option>42</option>
					<option>43</option>
					<option>45</option>
					<option>46</option>
					<option>47</option>
					<option>48</option>
					<option>49</option>
					<option>50</option>
					<option>51</option>
					<option>52</option>
					<option>53</option>
					<option>54</option>
					<option>55</option>
					<option>56</option>
					<option>57</option>
					<option>58</option>
					<option>59</option>
				</select>
				<select name="ampm" class="input-mini">
					<option>AM</option>
					<option selected="selected">PM</option>
				</select>
              </div>
            </div>
            
            <div class="form-actions">
			    <button type="submit" class="btn btn-primary">Next <i class="icon-arrow-right icon-white"></i></button>
    		</div>
	</form>
	
<?php } else { ?>
	<?php
	$ps = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
	$ps->execute(array($class));
	$class = $ps->fetch(PDO::FETCH_ASSOC);
	?>
	<?php if ($class) { ?>
	<h3><?php echo $class['name'] ?></h3>
	<script type="text/javascript">
		$(function() {
			$( "#input_startdate" ).datepicker();
			$( "#input_enddate" ).datepicker();
		});
	</script>
	<form class="bs-docs-example form-horizontal" method="POST" id="classForm" enctype="multipart/form-data">
			<input type="hidden" name="action" value="doEditClass" />
			<input type="hidden" name="id" value="<?php echo $class['id'] ?>" />
            <legend>Class Information <span class="pull-right"><a href="/franchise/" class="btn"><i class="icon-chevron-left"></i>Go Back</a></span></legend>
            
            <?php $fieldName = "name"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Class Name</label>
              <div class="controls">
                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $class['name'] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "startdate"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Start Date</label>
              <div class="controls">
                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo date("m/d/Y", $class[$fieldName]) ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "enddate"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">End Date</label>
              <div class="controls">
                <input type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo date("m/d/Y", $class[$fieldName]) ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "active"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Active</label>
              <div class="controls">
                <select name="<?php echo $fieldName ?>">
					<option <?php echo ($class[$fieldName] == 1 ? 'selected' : ''); ?> value="1">Yes</option>
					<option <?php echo ($class[$fieldName] == 0 ? 'selected' : ''); ?> value="0">No</option>
				</select>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
                <span class="help-block">Customers will only be able to view, search and register for active classes</span>
              </div>
            </div>
            
            <?php $fieldName = "price"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Price</label>
              <div class="controls">
              	<div class="input-prepend">
	              	<span class="add-on">$</span><input class="input-small" type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $class['price'] ?>"/>
              	</div>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
                <span class="help-block">Price customers will be charged at registration when paying for the entire class up-front</span>
              </div>
            </div>
            
            <?php $fieldName = "payments_price"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Payments Price</label>
              <div class="controls">
              	<div class="input-prepend">
	              	<span class="add-on">$</span><input class="input-small" type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $class['payments_price'] ?>"/>
              	</div>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
                <span class="help-block">Total price customers will be charged when making equal monthly payments; leave blank to disable monthly payments for this class</span>
              </div>
            </div>
            
            <?php $fieldName = "description"; ?>
            <div class="control-group">
              <label for="inputEmail" class="control-label">Description</label>
              <div class="controls">
              	<textarea name="description"><?php echo $class['description'] ?></textarea>
              </div>
            </div>
            
            <?php $fieldName = "size_limit"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Class Size Limit</label>
              <div class="controls">
              	<input class="input-small" type="text" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>" value="<?php echo $class['size_limit'] ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <?php $fieldName = "image"; ?>
            <div class="control-group <?php echo (isset($badFields[$fieldName]) ? 'warning' : ''); ?>">
              <label for="inputEmail" class="control-label">Class Image</label>
              <div class="controls">
              	<?php if ($class['img'] != "") { ?>
              		<img src="/img/uploads/<?php echo $class['img'] ?>" class="img-polaroid" style="height: 100px;"><br><br>
              	<?php } ?>
              	<input class="input-small" type="file" id="input_<?php echo $fieldName ?>" name="<?php echo $fieldName ?>"/>
                <?php if (isset($badFields[$fieldName])) { ?>
                <span class="help-inline"><?php echo $badFields[$fieldName] ?></span>
                <?php } ?>
              </div>
            </div>
            
            <legend>Meeting Information</legend>
            <?php
			$ps = $pdo->prepare("SELECT * FROM meetings WHERE class = ? ORDER BY day");
			$ps->execute(array($class['id']));
			$meetings = $ps->fetchAll();
			$days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
			?>
			<?php foreach ($meetings as $meeting) { ?>
			<input type="hidden" name="meeting[]" value="<?php echo $meeting['id'] ?>" />
            <div class="control-group">
              <label for="inputEmail" class="control-label"><?php echo $days[$meeting['day']-1] ?></label>
              <div class="controls inline">
              	Meeting at
              	<select name="meeting_place[]" class="meeting_place">
              		<?php
					$ps = $pdo->prepare("SELECT * FROM meeting_places WHERE franchise = ? ORDER BY name");
					$ps->execute(array($user['id']));
					$meeting_places = $ps->fetchAll();
					foreach ($meeting_places as $meeting_place) {
					?>
					<option value="<?php echo $meeting_place['id'] ?>" <?php echo ($meeting_place['id'] == $meeting['location'] ? 'selected="selected"' : ''); ?>><?php echo $meeting_place['name'] ?></option>
					<?php } ?>
					<?php if (!$meeting_places) { ?>
					<option>No Meeting Places Found</option>
					<?php } ?>
					<optgroup label="----------"></optgroup>
					<option>Add New Meeting Place...</option>
				</select>
				at
				<select name="hour[]" class="input-mini">
					<?php echo "<option>".date("g", $meeting['time'])."</option>" ?>
					<option>12</option>
					<option>1</option>
					<option>2</option>
					<option>3</option>
					<option>4</option>
					<option>5</option>
					<option>6</option>
					<option>7</option>
					<option>8</option>
					<option>9</option>
					<option>10</option>
					<option>11</option>
				</select>
				:
				<select name="minute[]" class="input-mini">
					<?php echo "<option>".date("i", $meeting['time'])."</option>" ?>
					<option>00</option>
					<option>15</option>
					<option>30</option>
					<option>45</option>
				</select>
				<select name="ampm[]" class="input-mini">
					<?php echo "<option>".date("A", $meeting['time'])."</option>" ?>
					<option>AM</option>
					<option>PM</option>
				</select>
				
				<a href="/franchise/meeting?action=doDeleteMeeting&id=<?php echo $meeting['id'] ?>&class=<?php echo $class['id'] ?>" class="btn btn-danger"><i class="icon-trash icon-white"></i> Remove</a>
              </div>
            </div>
            <?php } ?>
            
            <div class="control-group">
            	<div class="controls">
            		<button type="button" id="addMeeting" class="btn"><i class="icon-plus-sign"></i> Add Meeting</button>
            	</div>
            </div>
            
            <div class="form-actions">
			    <button type="submit" class="btn btn-success"><i class="icon-ok icon-white"></i> Save</button>
			    <a href="/franchise/" class="btn btn-warning"><i class="icon-remove icon-white"></i>Cancel</a>
    		</div>
	</form>
	<?php } else { ?>
		<h2>Class not found</h2>
		<a href="/franchise" class="btn">Go Back</a>
	<?php } ?>
<?php } ?>
	</div>
</div>
<div class="modal hide fade" id="addMeetingPlace">
	<form action="/franchise/meetingplace/new" method="post" id="addNewMeetingPlace" style="margin:0;">
		<input type="hidden" name="action" value="doAddMeetingPlace" />
		<input type="hidden" name="return" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
	    <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		    <h3>New Meeting Place</h3>
	    </div>
	    <div class="modal-body">
            <div class="control-group">
              <label for="inputEmail" class="control-label">Name</label>
              <div class="controls">
                <input type="text" name="name" id="meetingPlaceName"/>
              </div>
            </div>
            
            <div class="control-group">
              <label for="inputEmail" class="control-label">Address</label>
              <div class="controls">
                <input type="text" name="address" id="meetingPlaceAddress"/>
              </div>
            </div>
	    </div>
	    <div class="modal-footer">
		    <a href="#" class="btn" data-dismiss="modal">Close</a>
		    <button type="submit" class="btn btn-primary">Save changes</button>
	    </div>
	</form>
</div>

<div class="modal hide fade" id="addMeetingModal">
	<form action="/franchise/meetingplace/new" method="post" style="margin:0;">
		<input type="hidden" name="action" value="doAddMeeting" />
		<input type="hidden" name="return" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
		<input type="hidden" name="class" value="<?php echo $class['id'] ?>" />
	    <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		    <h3>New Meeting</h3>
	    </div>
	    <div class="modal-body">
            <div class="control-group">
              <label for="inputEmail" class="control-label">Day</label>
              <div class="controls">
                <select name="day">
					<option value="1">Sunday</option>
					<option value="2">Monday</option>
					<option value="3">Tuesday</option>
					<option value="4">Wednesday</option>
					<option value="5">Thursday</option>
					<option value="6">Friday</option>
					<option value="7">Saturday</option>
				</select>
              </div>
            </div>
            
            <div class="control-group">
              <label for="inputEmail" class="control-label">Place / Time</label>
              <div class="controls">
              	<select name="meeting_place" class="meeting_place">
              		<?php
					$ps = $pdo->prepare("SELECT * FROM meeting_places WHERE franchise = ? ORDER BY name");
					$ps->execute(array($user['id']));
					$meeting_places = $ps->fetchAll();
					foreach ($meeting_places as $meeting_place) {
					?>
					<option value="<?php echo $meeting_place['id'] ?>" <?php echo ($meeting_place['id'] == $meeting['location'] ? 'selected="selected"' : ''); ?>><?php echo $meeting_place['name'] ?></option>
					<?php } ?>
				</select>
				at
				<select name="hour" class="input-mini">
					<?php echo "<option>".date("g", $meeting['time'])."</option>" ?>
					<option>12</option>
					<option>1</option>
					<option>2</option>
					<option>3</option>
					<option>4</option>
					<option>5</option>
					<option>6</option>
					<option>7</option>
					<option>8</option>
					<option>9</option>
					<option>10</option>
					<option>11</option>
				</select>
				:
				<select name="minute" class="input-mini">
					<?php echo "<option>".date("i", $meeting['time'])."</option>" ?>
					<option>00</option>
					<option>15</option>
					<option>30</option>
					<option>45</option>
				</select>
				<select name="ampm" class="input-mini">
					<?php echo "<option>".date("A", $meeting['time'])."</option>" ?>
					<option>AM</option>
					<option>PM</option>
				</select>
              </div>
            </div>
	    </div>
	    <div class="modal-footer">
		    <a href="#" class="btn" data-dismiss="modal">Close</a>
		    <button type="submit" class="btn btn-primary">Save changes</button>
	    </div>
	</form>
</div>