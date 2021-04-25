<?php include "include/header.php" ?>

<div class="col-md-6">
	<div class="form-group">
	    <label for="name">Name</label>
	    <input type="email" class="form-control" id="name" value="John Doe">
	</div>
	<div class="form-group">
	    <label for="phone">Phone</label>
	    <input type="text" class="form-control" id="phone" value="01111111111">
	</div>
	<div class="form-group">
	    <label for="email">Email</label>
	    <input type="email" class="form-control" id="email" value="johndoe@example.com">
	</div>
	<div class="form-group">
	    <label for="amount">Amount</label>
	    <input type="number" class="form-control" id="amount" value="2000">
	</div>
</div>

<div class="col-md-6">
	<table class="table">
	    <thead>
	    	<tr rowspan="4">
	    		<th>Cart</th>
	    	</tr>
	        <tr>
	            <th>SL</th>
	            <th>Product Name</th>
	            <th>Quantity</th>
	            <th>Unit Price</th>
	        </tr>
	    </thead>
	    <tbody>
	        <tr>
	            <td>1</td>
	            <td>T-Shirt</td>
	            <td>1</td>
	            <td>1000</td>
	        </tr>
	        <tr>
	            <td>2</td>
	            <td>Pant</td>
	            <td>1</td>
	            <td>1000</td>
	        </tr>
	        <tr style="background: lavenderblush;font-weight: bold;">
	            <td colspan="2">Total</td>
	            <td>2</td>
	            <td>2000 Tk</td>
	        </tr>
	    </tbody>
	</table>
	<br><br>
	<button type="button" class="btn btn-primary btn-block" id="placeorder">Place Order</button>
</div>


<?php include "include/footer.php" ?>

<script type="text/javascript">
	$("#placeorder").click(function(){
  		$.post("remote/request.php",
		{
		    name: $("#name").val(),
		    phone: $("#phone").val(),
		    email: $("#email").val(),
		    amount: $("#amount").val()
		},
		function(data, status){
			var invoice = JSON.parse(data);
			Swal.fire({
				title: '<u>INVOICE ID</u><br><br>'+invoice.invoice_id,
				html: "<a href='"+invoice.qr_image_pay_url+"' target='_blank'>QR Link</a>",
				imageUrl: invoice.qr_image_pay_url,
				imageWidth: 300,
				imageHeight: 200,
				imageAlt: invoice.invoice_id,
				showDenyButton: true,
			  	denyButtonText: 'Pay Invoice',
			  	denyButtonColor: 'green',
				confirmButtonColor: '#3085d6',
  				confirmButtonText: 'Cancel Invoice',
  				showCancelButton: true,
  				cancelButtonColor: '#d33',
  				cancelButtonText: 'Close'
			})
			.then((result) => {
				if (result.isConfirmed) {
					$.post("remote/cancel.php",
					{
						invid: invoice.invoice_id
					},
					function(data, status){
						var canceldata = JSON.parse(data);
						var bfdata = JSON.stringify(canceldata, undefined, 4);
						Swal.fire(
				  		'Cancelled!',
				  		'<h4>Your order has been Cancelled.</h4> <br><h5><b>Response:</b><br> <pre>'+ bfdata +'</pre></h5>',
				  		'success')
					});
				}
				else if (result.isDenied) {
				    Swal.fire({
						icon: 'success',
						title: 'Redirecting...',
						showConfirmButton: false,
						timer: 1500
					});
					setTimeout(function(){ window.location.href = invoice.pay_url; }, 1500);
				}
			});
		});
	});

</script>