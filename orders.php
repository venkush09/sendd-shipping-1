<?php

require __DIR__.'/vendor/autoload.php';
use phpish\shopify;
$access_token=$_REQUEST['access_token'];
$order_count=$_REQUEST['order_count'];
$shopify = shopify\client($_REQUEST['shop'], SHOPIFY_APP_API_KEY, $access_token );
try
{
	      
			$orders = $shopify('GET /admin/orders.json', array('limit'=>$_REQUEST['limit'],'page'=>$_REQUEST['page_id'],'fulfillment_status'=>'any','order'=>'created_at desc'));
			if($orders){
			require __DIR__.'/popupcontent.php'; //popup content
			echo '<table class="table-hover expanded sennd-order-table">';
			 echo "<thead><tr>";
			 echo '<th>&nbsp;</th><th><span>Order</span></th>
								  <th class="is-sortable">
									<span>Date</span>
								  </th>
								  <th class="is-sortable">
									<span>Customer</span>
								  </th>
								  <th class="is-sortable ">
									<span>Payment status</span>
								  </th>
								  <th class="is-sortable sorted-desc">
									<span>Fulfillment status</span>
								  </th>
								  <th class="type--right is-sortable ">
									<span>Total</span>
								  </th>
								   <!--th class="type--right is-sortable ">
									<span>Tracking Code </span>
								  </th-->
								</tr>
							  </thead>';
							  echo '<tbody>';
							  
			foreach($orders as $singleorder)
			{
				$quantity_total=0;
				$product_titles='';
				$product_quantity_total ='';
				$product_ids ='';
				$tax_title ='';
				$tax_price ='';
				$tax_rate ='';
				$sku_no ='';
				 $id =$singleorder['id'];
				 $name =$singleorder['name'];
				 $created_at =$singleorder['created_at'];
				$total_weight =$singleorder['total_weight'];
				 $gateway =$singleorder['gateway'];
				 $financial_status=$singleorder['financial_status'];
				 $total_price=$singleorder['total_price']; 
				 $subtotal_price=$singleorder['subtotal_price']; 
				 $extra_price = $total_price - $subtotal_price;
				 $email=$singleorder['email'];
				 $address=$singleorder['shipping_address']['address1'];
				 $address2=$singleorder['shipping_address']['address2'];
				 $city=$singleorder['shipping_address']['city'];
				 $zip=$singleorder['shipping_address']['zip'];
				 $province=$singleorder['shipping_address']['province'];
				 $country=$singleorder['shipping_address']['country'];
				 $customer_name=$singleorder['shipping_address']['name'];
				$customer_phone=$singleorder['shipping_address']['phone'];
				$note_attributes=$singleorder['note_attributes'];
				$note_name=$note_attributes[0]['value'];
				$note_value=$note_attributes[1]['value'];
				$full_address = $address ." ". $address2 .",city:".$city .",province:".$province.",country:".$country."-zip:".$zip;
				if($singleorder['fulfillment_status'] == '')
				{
					$fulfillment_status = 'Unfulfilled';
				}
				else 
				{
					$fulfillment_status = $singleorder['fulfillment_status'];
				}
				$disabled1='';
				if($note_value!=''){
					$disabled1="";
				}
				$line_items=$singleorder['line_items'];
				$count=0;
				foreach($line_items as $line_items123){
					$count=$count+1;
				}
				$extra_per_product = $extra_price / $count;
				foreach($line_items as $line_items){
					//Get product names
					if($line_items['fulfillment_status']!= 'fulfilled'){
							$item_total= $line_items['price'] * $line_items['quantity'];
							 $item_total = $item_total + $extra_per_product;
							if($product_quantity_total ==''){
								$product_quantity_total = $item_total;
							}
							else{
							    $product_quantity_total = $product_quantity_total.','.$item_total;
							}
							if($product_titles ==''){
								$product_titles = $line_items['name'];
							}
							else{
								$product_titles = $product_titles.','.$line_items['name'];
							}
						
							if($product_ids ==''){
								$product_ids = $line_items['product_id'];
							}
							else{
								$product_ids = $product_ids.','.$line_items['product_id'];
							}
							if($sku_no ==''){
								$sku_no = $line_items['sku'];
							}
							else{
								$sku_no = $sku_no.','.$line_items['sku'];
							}
							foreach($line_items['tax_lines'] as $tax_lines){
								if($tax_title =='' || $tax_rate == '' || $tax_price == ''){
								$tax_title = $tax_lines['title'];
								$tax_rate =$tax_lines['rate'];
								$tax_price =$tax_lines['price'];
							}
							else{
								$tax_title = $tax_title.','.$tax_lines['title'];
								$tax_rate = $tax_rate.','.$tax_lines['rate'];
								$tax_price = $tax_price.','.$tax_lines['price'];
							}
								
							}
					}
					
				}
				
				if($fulfillment_status == 'partial' || $fulfillment_status == 'Unfulfilled' ){
					echo "<tr>";
					echo '<td><input  type="checkbox" $disabled1 class="select_box" name="order_ids_'.$id.'"  value="'.$id.'"  data-financial_status="'.$financial_status.'" data-total_weight="'.$total_weight.'" data-quantity_total="'.$quantity_total.'" data-customer_total-price="'.$total_price.'" data-customer_email="'.$email.'" data-customer_name="'.$customer_name.'" data-fulladdress="'.$full_address.'" data-gateway="'.$gateway.'" data-customer_phone="'.$customer_phone.'"  data-products_name="'.$product_titles.'" data-products_ids="'.$product_ids.'" data-product_quantity_total="'.$product_quantity_total.'" data-tax-title="'.$tax_title.'" data-tax-price="'.$tax_price.'" data-tax-rate="'.$tax_rate.'" data-sku="'.$sku_no.'"></td>';
					echo "<td>".$name."</td>";
					echo "<td>".$created_at."</td>";
					echo "<td>".$customer_name."</td>";
					echo "<td>".$financial_status."</td>";
					echo "<td>".$fulfillment_status."</td>";
					echo "<td>".$total_price."</td>";
					//echo "<td>".$note_value."<a href='javascript:void(0);' data-id='$id' data-tracking_code='$note_value' class='put_track'>Apply Tracking Code</a>"."</td>";
					echo "</tr>";
				}	
			}
			 echo '</tbody>';
			  echo '</table>';
			}
	else{
	echo "<div class='no-result'>No Order</div>";
	}
}
catch (shopify\ApiException $e)
{
	# HTTP status code was >= 400 or response contained the key 'errors'
	echo $e;
	print_r($e->getRequest());
	print_r($e->getResponse());
}


echo "<a href='#popup_content' class='fancybox_btn'>Submit</a>";


?>
