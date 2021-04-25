<?php include "include/header.php" ?>

<div class="col-md-6">
	<div class="form-group">
	    <label for="name">Invoice ID</label>
	    <input type="text" class="form-control" id="invoiceid" placeholder="Enter Invoice Number" style="height: 35px;border-radius: 1px;font-size: 13px;">
	    <br>
	    <button type="button" class="btn btn-success btn-block" id="checkstat">Check Status</button>
	</div>
</div>

<div class="col-md-6">
	<label for="response">Response</label>
	<pre id="responsecode">
	</pre>
</div>


<?php include "include/footer.php" ?>

<script type="text/javascript">
	$("#checkstat").click(function(){
		$.post("remote/status.php",
		{
			invid: $("#invoiceid").val()
		},
		function(data, status){
			var responsedata = JSON.parse(data);
			var bfdata = JSON.stringify(responsedata, undefined, 4);
			$("#responsecode").html(bfdata);
		});		
	});

</script>