<?php $head_title="Cart || erepair || erepair PHP Template"?>
<?php require_once('parts/layout/top-layout.php'); ?>
<!-- header -->
<?php require_once('parts/header/header2.php'); ?>
<?php
$page_title = "Cart";
require_once('parts/page-title.php');
?>

        <!--Cart Page One Start-->
        <section class="cart-page-one">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="cart-page-one-table">
                            <div class="cart-page-one-info">
                                <div class="left"><span>Your Cart:</span> 3 Items</div>
                                <div class="right">Total:<span> 15,797</span></div>
                            </div>

                            <div class="table-outer">

                                <table class="cart-table">
                                    <thead class="cart-header clearfix">
                                        <tr>
                                            <th class="prod-column">Product</th>
                                            <th class="hide-me"></th>
                                            <th>Quantity</th>
                                            <th class="price">Price</th>
                                            <th>Total</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="2" class="prod-column">
                                                <div class="column-box">
                                                    <div class="prod-thumb">
                                                        <a href="#">
                                                            <img src="assets/images/shop/product-v1-sidebar-1.png"
                                                                alt="image">
                                                        </a>
                                                    </div>
                                                    <div class="title">
                                                        <h3 class="prod-title">
                                                            Digital multimeter
                                                        </h3>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="qty">
                                                <div class="input-box">
                                                    <input class="quantity-spinner" type="text" value="2"
                                                        name="quantity">
                                                </div>
                                            </td>
                                            <td class="price">$23.00</td>
                                            <td class="sub-total">$30.00</td>
                                            <td>
                                                <div class="remove">
                                                    <i class="icon-plus"></i>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="2" class="prod-column">
                                                <div class="column-box">
                                                    <div class="prod-thumb">
                                                        <a href="#">
                                                            <img src="assets/images/shop/product-v1-sidebar-2.png"
                                                                alt="image">
                                                        </a>
                                                    </div>
                                                    <div class="title">
                                                        <h3 class="prod-title">
                                                            Air conditioners Repair
                                                        </h3>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="qty">
                                                <div class="input-box">
                                                    <input class="quantity-spinner" type="text" value="3"
                                                        name="quantity">
                                                </div>
                                            </td>
                                            <td class="price">$39.00</td>
                                            <td class="sub-total">$117.00</td>
                                            <td>
                                                <div class="remove">
                                                    <i class="icon-plus"></i>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="2" class="prod-column">
                                                <div class="column-box">
                                                    <div class="prod-thumb">
                                                        <a href="#">
                                                            <img src="assets/images/shop/product-v1-sidebar-3.png"
                                                                alt="image">
                                                        </a>
                                                    </div>
                                                    <div class="title">
                                                        <h3 class="prod-title">
                                                            Instruments with toolbox
                                                        </h3>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="qty">
                                                <div class="input-box">
                                                    <input class="quantity-spinner" type="text" value="4"
                                                        name="quantity">
                                                </div>
                                            </td>
                                            <td class="price">$42.00</td>
                                            <td class="sub-total">$120.00</td>
                                            <td>
                                                <div class="remove">
                                                    <i class="icon-plus"></i>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-12">
                        <div class="cart-page-one-bottom">
                            <div class="row">
                                <div class="col-xl-8 col-lg-6 col-md-6">
                                    <form action="#" class="default-form cart-cupon__form">
                                        <input type="text" placeholder="Enter Coupon Code......"
                                            class="cart-cupon__input">
                                        <button class="thm-btn" type="submit"><span></span>Apply
                                            Coupon</button>
                                    </form>
                                </div>
                                <div class="col-xl-4 col-lg-6 col-md-6">
                                    <ul class="cart-total list-unstyled">
                                        <li>
                                            <span>Subtotal</span>
                                            <span>$20.98 USD </span>
                                        </li>
                                        <li>
                                            <span>Shipping Cost</span>
                                            <span>$0.00 USD</span>
                                        </li>
                                        <li>
                                            <span>Total</span>
                                            <span class="cart-total-amount">$20.98 USD</span>
                                        </li>
                                    </ul>
                                    <div class="cart-page__buttons">
                                        <div class="cart-page__buttons-1">
                                            <a href="cart.php" class="thm-btn cart-page__btn-1"><span></span>
                                                Update cart</a>
                                        </div>
                                        <div class="cart-page__buttons-2">
                                            <a href="checkout.php" class="thm-btn cart-page__btn-2"> <span></span>
                                                Checkout</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>
        <!--Cart Page One End-->

        <?php require_once('parts/footer/footer4.php'); ?>
        <?php require_once('parts/layout/bottom-layout.php'); ?> 