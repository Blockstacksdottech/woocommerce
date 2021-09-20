function handleAccountsChanged(accounts) {
    
    
    if (accounts.length === 0) {
        console.log('Please connect to MetaMask.');
        jQuery('#enableMetamask').html('Connect with Metamask')
    } else if (accounts[0] !== currentAccount) {
        currentAccount = accounts[0];
        jQuery('#enableMetamask').html(currentAccount)
        jQuery('#status').html('')
        jQuery('#connect-btn').css("display","none");
        
        jQuery('#nftauction_type_product_options').css("display","block");
        
        if( jQuery('#approve').length > 0 ){
          jQuery('#approve').css("display","inline-block");  
          let btn = jQuery('#auction-create')[0];
          let btn2 = jQuery('#approve')[0];
          btn2.addEventListener('click',Approve);
          btn.addEventListener('click',createAuctionHandler);
        }

        if (jQuery("#auction-cancel").length > 0){
          let btn = jQuery("#auction-cancel")[0];
          btn.addEventListener('click',cancelZoraAuction);
        }
        
        w3 = new Web3(window.ethereum);
        if(currentAccount != null) {
            // Set the button label
            jQuery('#enableMetamask').html(currentAccount);
            document.getElementById('_owner').value = currentAccount;
        }
    }
    
}

// using this



async function cancelZoraAuction(){ // the id is the auction Id how do yoou access it ?
  let id = document.getElementById('_auction_id').value;
  let c = loadContract(CustomHouseAbi,TestNetContract);
  let resp = await c.methods.cancelAuction(id).send({from: currentAccount});
  if (  resp){
    console.log('auction canceled');
    console.log(resp);
    document.getElementById('_auction_status').value = 'Cancelled';
  }else{
    console.log('failed');
  }
}


function convertDate(d){
    let date2 = new Date()
    let date = new Date(d)
    return ((date.getTime() - date2.getTime()) /1000)
}

    function loadContract(abi,add){
        let c = new w3.eth.Contract(abi,add);
        return c;
    }

  function createAuctionHandler() {
    if( create_validations() ){
      alert('starting');
      let tokId = jQuery('#_token_id')[0].value;
      let tokC = jQuery('#_token_contract')[0].value;
      let duration = jQuery('#_auction_duration')[0].value;
      let reserve = jQuery('#_reserve_price')[0].value;
      let curator = jQuery('#_curator')[0].value;
      let fee = jQuery('#_curator_fee_percent')[0].value;
      let curr = jQuery('#_auction_currency')[0].value;

      curator = curator === "" ? defaultCurrator : curator;
      curr = curr === "" ? EthAdd : curr;


      console.log(curator);
      console.log(curr);
      console.log(String(parseInt(convertDate(duration))))

      let c = loadContract(CustomHouseAbi,TestNetContract);


      c.methods.createAuction(
          Number(tokId),
          tokC,
          String(parseInt(convertDate(duration))),
          Web3.utils.toWei(String(reserve),'ether'),
          curator,
          Number(fee),
          curr

      ).send({'from':currentAccount}).then(res => {
          console.log("result here");
          console.log(res);
          alert("Auction Created with Id : " + res.events.AuctionCreated.returnValues.auctionId);
          jQuery('#auction-create').hide();
          // Save value for auction ID
          document.getElementById('_auction_id').value = res.events.AuctionCreated.returnValues.auctionId;
          document.getElementById('_block_number').value = res.blockNumber;
      });

      /* c.AuctionCreated(function(error,result){
          alert('even triggered');
          console.log(result);
      }) */

      /* c.getPastEvents('AuctionCreated', {}).then(res => console.log(res)); */
    }
  }


  async function Approve(){
    // Validation for approve button
    if( approve_validations() ){
      let tokId = jQuery('#_token_id')[0].value;
      let tokC = jQuery('#_token_contract')[0].value;
      let c = loadContract(NftAbi,tokC);
      let CurrentApproved = await c.methods.getApproved(Number(tokId)).call();
      
      if (CurrentApproved == TestNetContract){
        alert('already approved');
        jQuery('#approve').css('display','none');
        jQuery('#auction-create').css('display','inline-block');
        
      }else{
        c.methods.approve(TestNetContract,tokId).send(
          {from:currentAccount}
        ).then(
          res => {
            alert('nft approved');
            jQuery('#approve').css('display','none');
            jQuery('#auction-create').css('display','inline-block'); // here is success  login
          }
        ).catch(
          res => alert('failed')
        )
      }
    }

  }

  function approve_validations(){
    
    if( jQuery('#_token_id').val() == '' || jQuery('#_token_id').val() == null ){
      jQuery('#_token_id').css({ 'border': 'solid 1px #ff0000' });
      jQuery('html, body').animate({
        scrollTop: jQuery("#nft_auction_options").offset().top
      }, 2000);
      return false;
    }
    if( jQuery('#_token_contract').val() == '' || jQuery('#_token_contract').val() == null ){
      jQuery('#_token_contract').css({ 'border': 'solid 1px #ff0000' });
      jQuery('html, body').animate({
        scrollTop: jQuery("#nft_auction_options").offset().top
      }, 2000);
      return false;
    }

    return true;
  }

  function create_validations(){
    if( jQuery('#_auction_duration').val() == '' || jQuery('#_auction_duration').val() == null ){
      jQuery('#_auction_duration').css({ 'border': 'solid 1px #ff0000' });
      jQuery('html, body').animate({
        scrollTop: jQuery("#nft_auction_options").offset().top
      }, 2000);
      return false;
    }
    if( jQuery('#_reserve_price').val() == '' || jQuery('#_reserve_price').val() == null ){
      jQuery('#_reserve_price').css({ 'border': 'solid 1px #ff0000' });
      jQuery('html, body').animate({
        scrollTop: jQuery("#nft_auction_options").offset().top
      }, 2000);
      return false;
    }
    if( jQuery('#_curator_fee_percent').val() == '' || jQuery('#_curator_fee_percent').val() == null ){
      jQuery('#_curator_fee_percent').css({ 'border': 'solid 1px #ff0000' });
      jQuery('html, body').animate({
        scrollTop: jQuery("#nft_auction_options").offset().top
      }, 2000);
      return false;
    }

    return true;
  }

  function connectHandler() {
    connect();
    setInterval(connect, 1000);
  }

  function connect() {
    
    ethereum
      .request({ method: "eth_requestAccounts" })
      .then(handleAccountsChanged)
      .catch((err) => {
        if (err.code === 4001) {
          console.log("Please connect to MetaMask.");
        } else {
          console.error(err);
        }
      });
  }

window.addEventListener("load", function () {

  try{

    if( document.getElementById("connect-btn") ){
        const btn = document.getElementById("connect-btn");
        currentAccount = null;
        btn.addEventListener("click", connectHandler);    
    }

    if( defaultCurrator ){
      if( document.getElementById('_curator') ){
        // in case already have a value - don't change
        if( document.getElementById('_curator').value !== '' ){
            // nothing to do here
        }else{
            document.getElementById('_curator').value = defaultCurrator;    
        }
        
      }
    }

    let _inputElements = [ '#_token_id', '#_token_contract', '#_auction_duration', '#_reserve_price', '#_curator_fee_percent', '#_auction_id', '#_block_number' ];

    let _return = false;
    
    _inputElements.forEach( function( item, index ) {
      jQuery(item).on('change', function(){
        jQuery(this).css({ 'border-color': '#8c8f94' });
      })
    });

  }catch(e){
    console.log(e);
  }
  
});

function test(event){
  event.preventDefault();
  console.log( this.getAttribute('data-token') );
}

async function GetAuctionDetails(auctionId){
    let  c = loadContract(CustomHouseAbi,TestNetContract);
    let resp = await c.methods.auctions(auctionId).call();
    return resp;
}

function loadContract3(abi,add){
  let w = new Web3(nftData.rpc);
  let c = new w.eth.Contract(abi,add);
  return c;
}

async function GetNftUri(NftId, NftC){
    let c;
    if (currentAccount){
      c = loadContract2(NftAbi,NftC);
    }else{
      c = loadContract3(NftAbi,NftC)
    }
    
    let resp = await  c.methods.tokenURI(NftId).call();
    return resp;
}