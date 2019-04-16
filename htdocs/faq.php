<?php
require 'shared/nav-menu.php';

$title = "Terms & Conditions - CashifySkins";
$css[] = "/css/faq.css";  
output_header(); 

?>

<div id="basic_content_wrapper">
<h1>Frequently Asked Questions</h1>
For item issues please submit a <a href='/support/ticket-inbox.php'>support ticket</a>
<dl id="faq">
	
    <h2>General</h2>
    <div class='qa_wrapper'>
	<dt>What are security tokens<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
    	<p>
         	Security token are tokens that is issued to you when you trade us. <br>It is used to ensure the legitimacy of the trade offer sent by our bot by comparing the token you recieved with the token in the trade offer. <br>If there is a mismatch, <b>DO NOT</b> trade with the bot as the bot you are trading with is a scammer.
    	</p>  
    </dd>
    </div>
    
	<h2>Account</h2>
    <div class='qa_wrapper'>
	<dt>What are game preferences<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
    	<p>
         	Game preferences is a settings that sets the buy and sell filter to your prefered game type.
    	</p>  
    </dd>
    </div>
   
    <div class='qa_wrapper'>
	<dt>What is trust score<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
    	<p>
         	Trust scores is an indicator that we use to determine the possibility an account being fraudulent.<br>If your trust score within 'Unverified' or 'Poor' catorgery, restrictions will be placed on your account. 
    	</p>  
    </dd>
    </div>
    <div class='qa_wrapper'>
	<dt>How do I improve my trust score<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
    	<p>
         	We look into a number of factors listed below.
            <ul>
              <li>Total playtime*</li>
              <li>Account age</li>
              <li>Steam account level</li>
              <li>Number of games owned</li>
              <li>Number of month old friends</li>
            </ul>
          <p>For account under the 'Poor' catorgery please improve the above factors to receive a better score.</p>
          <p>For account under the 'Unverified' catorgery please set your steam account profile to public and relogin so that we can recalibrate your score. You may set your profile back to private once your account trust score has been calibated to 'good'.</p>
           
    	</p>  
    </dd>
    </div>
    <div class='qa_wrapper'>
	<dt>How do I setup my trade url<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
         <ol>
           <li>Hover over to your name and click account.</li>
           <li>Click on the orange url 'Get Trade Url' label and you will be redirected to steam.</li>
           <li>Scroll to the bottom and copy trade url from the third-party site section.</li>
           <li>Head back to the account page and paste the link into input beside the get trade url.</li>
           <li>Click on the submit button</li>
    	</ol> 
    </dd>
    </div>
     <div class='qa_wrapper'>
    <dt>How do I set my profile and inventory to public<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
         <ol>
            <li>Login to steam</li>
           	<li>Click your name top right then go to view profile</li>
           	<li>Click Edit Profile</li>
          	<li>Click My Privacy Settings</li>
           	<li>Set inventory and profile to Public</li>
           	<li>Click on the submit button</li>
    	</ol> 
    </dd>
    </div>

    <h2>Buying</h2>
    <div class='qa_wrapper'>
	<dt>What is the process to purchase an item<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
    	<ol>
    	  <li>Deposit money.</li>
    	  <li>Set a buy order for your desired item.</li>
          <li>When your price matches with a seller that is lower or equal to your price, the item will be desposted into your stash where you can collect anytime.</li>
   		</ol>  
    </div>
    <div class='qa_wrapper'>
	<dt>What is the process to purchase a specfic item<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
    	<ol>
    	  <li>Deposit money.</li>
    	  <li>Find & buy your desired item.</li>
    	  <li>The item will be send via trade offer for you to review.</li>
          <li>Once accepted, funds will then be deducted from your account.</li>
   		</ol>  
    </div>
	<div class='qa_wrapper'>
	<dt>How to retrive my purchased items from my stash<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
    	<ol>
           <li>Hover over to your name and click manage orders.</li>
           <li>Click on the drop down list and change it to purchases.</li>
           <li>Click on collect purchase items button.</li>
           <li>Our bot will send you your item via trade offer.</li>
    	</ol> 
    </dd>
    </div>

    <h2>Selling</h2>
    <div class='qa_wrapper'>
	<dt>What is the process to buy an item<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
    	<ol>
          <li>Place your item you want to sell.</li>
          <li>Our bot will send you a trade offer. Once accepted your item will be listed on the marketplace.</li>
          <li>When someone purchase your item, funds will be deposted to your account.</li>
    	</ol>  
    </dd>
    </div>
    <div class='qa_wrapper'>
	<dt>How do I remove my listing and retrieve my items<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
    	<ol>
           <li>Hover over to your name and go to 'Manage Orders'.</li>
           <li>Click on the drop down list and change it to 'Sales'.</li>
           <li>Select your items to return and click on 'Confirm Returns'.</li>
           <li>Our bot will send you your item via trade offer.</li>
    	</ol> 
    </dd>
    </div>
    <div class='qa_wrapper'>
	<dt>Any selling fees?<span class="expand_toggle_label">Expand</span></dt>
	<dd>
    	<p>
    		Yes. We impose a 5% fee on items sold.
   		</p>
    </dd>
    </div>
    
    <h2>Withdrawals</h2>
    <div class='qa_wrapper'>
	<dt>How do I withdraw funds<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
         <ol>
            <li>Hover over to your name and click 'Withdraw Funds'.</li>
            <li>Set up your paypal email.</li>
           <li>Input your desired amount.</li>
           <li>Payment will be made to the paypal receipent during the payout period.</li>
    	</ol> 
    </dd>
    </div>
    <div class='qa_wrapper'>
	<dt>Any withdrawal fees?<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
    	<p>            
            Yes. We impose a 5% fee on fund withdrawal.
    	</p>  
    </dd>
    </div>
    <div class='qa_wrapper'>
    <dt>When is your payout period<span class='expand_toggle_label'>Expand</span></dt>
	<dd>
    	<p>            
            We process payout once every 1-2 days
    	</p>  
    </dd>
    </div>
    
   
</dl>
</div>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.1/jquery.min.js"></script>
<script> 
	$(document).ready(function() {
		$('#faq').find('dd').hide().end().find('dt').click(function() {
			$(this).next().slideToggle('fast');
			var expand_toggle_label = this.getElementsByTagName("span")[0].innerHTML;
			if(expand_toggle_label == "Expand"){
				this.getElementsByTagName("span")[0].innerHTML = "Collapse";
			}else{
				this.getElementsByTagName("span")[0].innerHTML = "Expand";
			}
			
		});
	});
</script>
<?php footer(); ?>
</body>
</html>