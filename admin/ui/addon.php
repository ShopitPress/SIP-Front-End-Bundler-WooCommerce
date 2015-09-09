<?php

	$src_image = SIP_FEBWC_URL . 'admin/assets/images/';
?>
<div class="sip-tab-content">
<style>
    .section{
        margin: -20px;
        padding-bottom: 70px;
        padding-top: 70px;
        font-family: "OpenSansRegular";
    }
    .section h1{
        text-align: center;
        color: #ed1c24;
        font-size: 70px;
        font-weight: 700;
        line-height: normal;
        display: inline-block;
        width: 100%;
        margin: 50px 0;
				text-transform: uppercase;
    }
    .section:nth-child(even){
        background-color: #fff;
    }
    .section:nth-child(odd){
        background-color: #ededed;
    }
    .section .section-title{
        display: table;
    }
    .section .section-title img{
        display: table-cell;
        float: left;
        vertical-align: middle;
        width: auto;
        margin-right: 15px;
    }
    .section .section-title h2{
        text-align: center;
        display: table-cell;
        vertical-align: middle;
        padding: 0;
        font-size: 25px;
        font-weight: 700;
        color: #ed1c24;
    }
    .section p{
        color: #888;
        font-size: 16px;
        margin: 25px 0;
    }
    .section ul li{
        margin-bottom: 4px;
    }
    .pro-wrap{
        max-width: 750px;
        margin-left: auto;
        margin-right: auto;
        padding: 50px 0 30px;
    }
    .pro-wrap:after{
        display: block;
        clear: both;
        content: '';
    }
    .pro-wrap .col-1,
    .pro-wrap .col-2{
        float: left;
        box-sizing: border-box;
        padding: 0 15px;
    }
    .pro-wrap .col-1 img{
        width: 100%;
    }
    .pro-wrap .col-1{
        width: 55%;
    }
    .pro-wrap .col-2{
        width: 45%;
    }
    .sip-bundler-call{
        background-color: #444;
        color: #fff;
        padding: 30px 30px;
    }
    .sip-bundler-call p{
        color:#fff;
        font-size: 14px;
        font-weight: 500;
        display: inline-block;
    }
    .sip-bundler-call a.button{
			    font-size: 20px;
			    margin-bottom: 30px;
			    background-color: #ed1c24;
			    border-color: #ad1c24;
			    border-radius: 0px;
			    color: #fff;
			    float: right;
			    border: none;
			    margin: 12px 16px;
			    padding: 12px 29px;
			    text-shadow: none;
			    font-weight: bold;
			    text-decoration: none;
			    height: 50px;
			    text-align: center;
			    box-shadow: none;    }
    .sip-bundler-call a.button:hover,
    .sip-bundler-call a.button:active,
    .sip-bundler-call a.button:focus{
			    background-color: #ad1c24;
			    border-color: #ad1c24;
			    color: #fff;    }
    .sip-bundler-call .hl{
        text-transform: uppercase;
        background: none;
        font-weight: 800;
        color: #fff;
    }

</style>
<div class="landing">
    <div class="section section-call section-odd">
        <div class="pro-wrap">
            <div class="sip-bundler-call">
                <p>
                    Upgrade to <span class="hl">PRO version</span> to benefit from all these awesome features!
                </p>
                <a href="<?php echo SIP_FEBWC_PLUGIN_URL . '?utm_source=wordpress.org&utm_medium=SIP-panel&utm_content=v'. SIP_FEBWC_VERSION .'&utm_campaign=' .SIP_FEBWC_UTM_CAMPAIGN; ?>" target="_blank" class="sip-bundler-call-button button btn">UPGRADE</a>
            </div>
        </div>
    </div>
    <div class="section section-even clear">
        <h1>Pro Features</h1>
        <div class="pro-wrap">
            <div class="col-1">
                <img src=<?php echo $src_image . 'styles.png';?> alt="Multiple sip-bundler" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <h2>2 more awesome styles</h2>
                </div>
                <p>With these two beautifully crafted designs you have more options to display your products in an engaging way.</p>
                <p>Style 2 is perfect for those who want a wider display of products, while keeping the text at the bottom. Style 3 is suited for stores with few products or those who want less columns of products.</p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear">
        <div class="pro-wrap">
            <div class="col-2">
                <div class="section-title">
                    <h2>Quantity of products</h2>
                </div>
                <p>By enabling the quantity selection users will have the possibility to add quantities of products to their bundles. Quantity selection is aware of your stock level so you don't have to worry about users making an order you can't fulfill. In addition you have the following options:</p>
                <ul>
                    <li><strong>Minimum and Maximum quantity of products per bundle</strong></li>
                    <li><strong>Ability to set quantity for the whole bundle</strong></li>
                    <li><strong>Ability to set quantity for individual products</strong></li>
                </ul>
            </div>
            <div class="col-1">
                <img src=<?php echo $src_image . 'qty.png';?> alt="Quantity of products" />
            </div>
        </div>
    </div>
    <div class="section section-even clear">
        <div class="pro-wrap">
            <div class="col-1">
                <img src=<?php echo $src_image . 'banners.png';?> alt="Offer Banners" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <h2>Offer banners</h2>
                </div>
                <p>Keep your visitors enganed with live offer banners. You can use this space to explain what they are getting or entice them to add more products to the cart.</p>
                <p>Thanks to this feature you can display: "Great, you just got free shipping on your order!" or "Add one more item for a whooping 30% off your order". And the best part is that you have total control of the style with custom CSS styles.</p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear">
        <div class="pro-wrap">
            <div class="col-2">
                <div class="section-title">
                    <h2>Offer rules per quantities</h2>
                </div>
                <p>With this feature you have the option to offer discounts based on how many total products or how many products of an specific SKU are in the cart. Forget about making complicated calculations on total price when you have different offers, set them based on quantities and let the bundler do the math for you.</p>
            </div>
            <div class="col-1">
                <img src=<?php echo $src_image . 'offer-qty.png';?> alt="Admin panel" />
            </div>
        </div>
    </div>
    <div class="section section-even clear">
        <div class="pro-wrap">
            <div class="col-1">
                <img src=<?php echo $src_image . 'variable.png';?> alt="Search sip-bundler" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <h2>Variable products</h2>
                </div>
                <p>Got variations? No problem. With the PRO version you can use variable products in your bundles and display variations as an overlay in the product selection. Easy as pie.
                </p>
            </div>
        </div>
    </div>
    <div class="section section-call section-odd">
			<div class="pro-wrap">
				<div class="sip-bundler-call">
						<p>
								Upgrade to <span class="hl">PRO version</span> to benefit from all these awesome features!
						</p>
						<a href="<?php echo SIP_FEBWC_PLUGIN_URL . '?utm_source=wordpress.org&utm_medium=SIP-panel&utm_content=v'. SIP_FEBWC_VERSION .'&utm_campaign=' .SIP_FEBWC_UTM_CAMPAIGN; ?>" target="_blank" class="sip-bundler-call-button button btn">UPGRADE</a>
				</div>
			</div>
    </div>
</div>
</div>
