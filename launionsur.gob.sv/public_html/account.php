<?php $head_title="Account || erepair || erepair PHP Template"?>
<?php require_once('parts/layout/top-layout.php'); ?>
<!-- header -->
<?php require_once('parts/header/header2.php'); ?>
<?php
$page_title = "Account";
require_once('parts/page-title.php');
?>

        <!--Account Start-->
        <section class="account">
            <div class="container">
                <div class="account__main-tab-box tabs-box">
                    <ul class="tab-buttons clearfix list-unstyled">
                        <li data-tab="#login" class="tab-btn"><span>Login</span></li>
                        <li data-tab="#register" class="tab-btn active-btn"><span>Register</span></li>
                    </ul>

                    <div class="tabs-content">
                        <!--tab-->
                        <div class="tab" id="login">
                            <div class="account__main-tab-inner">
                                <form class="account__form">
                                    <div class="account__form-input-box">
                                        <input type="text" placeholder="Username">
                                    </div>
                                    <div class="account__form-input-box">
                                        <input type="password" placeholder="Password">
                                    </div>
                                    <div class="account__form-btn-box">
                                        <button type="submit" class="thm-btn account__form-btn"> <span></span>Login
                                            now</button>
                                        <div class="checked-box">
                                            <input type="checkbox" name="skipper2" id="skipper2" checked="">
                                            <label for="skipper2"><span></span>Remember me?</label>
                                        </div>
                                    </div>
                                    <div class="account__form-forgot-password">
                                        <a href="#">Forgot your password?</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!--tab-->
                        <!--tab-->
                        <div class="tab active-tab" id="register">
                            <div class="account__main-tab-inner">
                                <form class="account__form">
                                    <div class="account__form-input-box">
                                        <input type="text" placeholder="Username">
                                    </div>
                                    <div class="account__form-input-box">
                                        <input type="password" placeholder="Password">
                                    </div>
                                    <div class="account__form-btn-box">
                                        <button type="submit" class="thm-btn account__form-btn"> <span></span>Login
                                            now</button>
                                        <div class="checked-box">
                                            <input type="checkbox" name="skipper2" id="skipper2" checked="">
                                            <label for="skipper2"><span></span>Remember me?</label>
                                        </div>
                                    </div>
                                    <div class="account__form-forgot-password">
                                        <a href="#">Forgot your password?</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!--tab-->
                    </div>

                </div>
            </div>
        </section>
        <!--Account End-->

        <?php require_once('parts/footer/footer4.php'); ?>
        <?php require_once('parts/layout/bottom-layout.php'); ?>  