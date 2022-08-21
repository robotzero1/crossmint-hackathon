<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>Robot Moda</title>
  <link href="assets/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <div class="header"><img src="images/header.png"</div>
  <div class="grid-container">
      
    <?php 
    $block = 1;
    while ($block <= 54){
        echo '<div id="item_'.$block.'" class="grid-item"><img src="images/image ('.$block.').png"></div>';
        $block++;
    }
    ?>
  </div>


  <template id="modal-preview-template">
    <div class="modal modal-preview">
      <div class="preview-poster">
          <div>POSTER</div>
          <img class="poster" src="images/full_poster.png" style="width:60%">
      </div>
      
      <div class="preview-plus">+</div>
      
      <div class="preview-nft">
          <div>SELECTED NFT</div>
          <img class="image" src="">
          <div><button class="choose-another">ANOTHER</button><button class="choose-me">BUY $30</button></div>
          <p>Buy this NFT and receive a printed poster of the entire artwork</p>
      </div>
    </div>
  </template>
  

  <template id="modal-pay-template">
    <div class="modal modal-pay">
      <div>
          <img class="poster" src="images/full_poster.png" style="width:200px">
          <img class="image" src="images/image%20(14).png" style="width:200px">
      </div>
      <div class="customer-fields">
          Address for the poster
          <label>Name
          <input type="text" name="name" placeholder="name" value="John Smith" required>
          </label>
          <label>Address
          <input type="text" name="address1" placeholder="address1" value="19749 Dearborn St" required disabled>
          <input type="hidden" name="address2" placeholder="address2" value="Second Line" required>
          <input type="text" name="city" placeholder="city" value="Chatsworth" required disabled>
          <input type="hidden" name="state_code" placeholder="state_code" value="CA" required>
          <input type="text" name="state_name" placeholder="state_name" value="California" required disabled>
          <input type="hidden" name="country_code" placeholder="country_code" value="US" required>
          <input type="text" name="country_name" placeholder="country_name" value="United States" required disabled>
          <input type="text" name="zip" placeholder="zip" value="91311" required disabled>
          </label>
          <label>Email Address
          <input type="text" name="email" placeholder="email" value="example@domain.com" required>
          </label>
          <label>Wallet address*
          <input type="text" name="wallet-address" placeholder="Wallet Address" value="">
          </label>
          <p>*optional. Leave empty and a new address will be created.</p>
      </div>
      <div>
          <form id="payment-form">
            <div id="payment-element">
              <!-- Elements will create form elements here -->
            </div>
            <!--button class="choose-another">CHOOSE ANOTHER</button--><button id="submit" class="button purchase-me" style="margin: 10px 0;">SUBMIT ORDER</button>
            <div id="stripe-message"></div>
          </form>
          <div class="status">
              <div class="status-payment">Payment Status
                <div></div>
              </div>
              <div class="status-print">Print Status
                <div></div>
              </div>              
              <div class="status-mint">Mint Status
                <div></div>
              </div>
            </div>
         </div>
      </div>
    </div>
  </template>
  
  
  <template id="modal-background-template">
      <div class="modal-background"></div>
  </template>
  
  

  <script src="https://js.stripe.com/v3/"></script>
  <script src="assets/script.js"></script>


</body>

</html>
