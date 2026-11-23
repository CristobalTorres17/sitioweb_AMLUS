<?php $head_title="Checkout || erepair || erepair PHP Template"?>
<?php require_once('parts/layout/top-layout.php'); ?>
<!-- header -->
<?php require_once('parts/header/header2.php'); ?>
<?php
$page_title = "Checkout";
require_once('parts/page-title.php');
?>


        <!--Start Checkout Page One-->
        <section class="checkout-page-one">
            <div class="container">
                <div class="row">

                    <div class="col-xl-6 col-lg-6 col-md-6">
                        <div class="checkout-page-one__details">
                            <div class="checkout-page-one-title">
                                <h3>Billing details</h3>
                            </div>

                            <form class="billing_details_form">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="billing_input_box">
                                            <div class="select-box">
                                                <select class="wide">
                                                    <option data-display="Select a country">Select a country</option>
                                                    <option value="1">Canada</option>
                                                    <option value="2">England</option>
                                                    <option value="3">Australia</option>
                                                    <option value="3">USA</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="text" name="first_name" value="" placeholder="First name"
                                                required="">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="text" name="last_name" value="" placeholder="Last name"
                                                required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="billing_input_box">
                                            <input type="text" name="company_name" value="" placeholder="Company">
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="billing_input_box">
                                            <input type="text" name="Address" value="" placeholder="Address">
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="billing_input_box">
                                            <input type="text" name="company_name" value=""
                                                placeholder="Appartment, unit, etc. (optional)">
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="billing_input_box">
                                            <input type="text" name="Town/City" value="" placeholder="Town / City"
                                                required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="text" name="State" value="" placeholder="State" required="">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input name="form_zip" type="text" pattern="[0-9]*" placeholder="Zip code">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input name="email" type="email" placeholder="Email address">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="tel" name="form_phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}"
                                                required="" placeholder="Phone">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="checked-box">
                                            <input type="checkbox" name="skipper1" id="skipper" checked="">
                                            <label for="skipper"><span></span>Create an account?</label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-6">
                        <div class="checkout-page-one__details checkout-page-one__details--2">
                            <div class="checkout-page-one-title">
                                <h3>Ship to a different address</h3>
                            </div>

                            <form class="billing_details_form">
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="billing_input_box">
                                            <div class="select-box">
                                                <select class="wide">
                                                    <option data-display="Select a country">Select a country</option>
                                                    <option value="1">Canada</option>
                                                    <option value="2">England</option>
                                                    <option value="3">Australia</option>
                                                    <option value="3">USA</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="text" name="first_name" value="" placeholder="First name"
                                                required="">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="text" name="last_name" value="" placeholder="Last name"
                                                required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="billing_input_box">
                                            <input type="text" name="company_name" value="" placeholder="Company">
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="billing_input_box">
                                            <input type="text" name="Address" value="" placeholder="Address">
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="billing_input_box">
                                            <input type="text" name="company_name" value=""
                                                placeholder="Appartment, unit, etc. (optional)">
                                        </div>
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="billing_input_box">
                                            <input type="text" name="Town/City" value="" placeholder="Town / City"
                                                required="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="text" name="State" value="" placeholder="State" required="">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input name="form_zip" type="text" pattern="[0-9]*" placeholder="Zip code">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input name="email" type="email" placeholder="Email address">
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="tel" name="form_phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}"
                                                required="" placeholder="Phone">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="ship_different_input">
                                            <textarea placeholder="Notes about order"
                                                name="form_order_notes"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

                <div class="your_order">
                    <div class="title">
                        <h3>Your order</h3>
                    </div>
                    <div class="row">

                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="order_table_box">
                                <table class="order_table_detail">
                                    <thead class="order_table_head">
                                        <tr>
                                            <th>Product</th>
                                            <th class="right">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="pro__title">Product Name</td>
                                            <td class="pro__price">$20.00 USD</td>
                                        </tr>
                                        <tr>
                                            <td class="pro__title">Subtotal</td>
                                            <td class="pro__price">$30.00 USD</td>
                                        </tr>
                                        <tr>
                                            <td class="pro__title">Shipping</td>
                                            <td class="pro__price">$0.00 USD</td>
                                        </tr>
                                        <tr>
                                            <td class="pro__title">Total</td>
                                            <td class="pro__price">$50.00 USD</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-xl-6 col-lg-6 col-md-6">
                            <div class="checkout__payment">
                                <ul class="accordion-box-style1">
                                    <!--Start single accordion box-->
                                    <li class="accordion accordion-block">
                                        <div class="accord-btn active">
                                            <h4>Direct bank transfer</h4>
                                        </div>
                                        <div class="accord-content collapsed">
                                            <div class="text">
                                                <p>
                                                    Make your payment directly into our bank account. Please
                                                    use your Order ID as the payment reference. Your order
                                                    wont be shipped until the funds have cleared.
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <!--End single accordion box-->
                                    <!--Start single accordion box-->
                                    <li class="accordion">
                                        <div class="accord-btn">
                                            <h4>Paypal payment</h4>
                                        </div>
                                        <div class="accord-content">
                                            <div class="text">
                                                <p>
                                                    Make your payment directly into our bank account. Please
                                                    use your Order ID as the payment reference. Your order
                                                    wont be shipped until the funds have cleared.
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <!--End single accordion box-->
                                </ul>

                                <div class="text-right">
                                    <a href="checkout.php" class="thm-btn"> <span></span> Place your order</a>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--End Checkout Page One-->
        <?php require_once('parts/footer/footer4.php'); ?>
        <?php require_once('parts/layout/bottom-layout.php'); ?> 
       