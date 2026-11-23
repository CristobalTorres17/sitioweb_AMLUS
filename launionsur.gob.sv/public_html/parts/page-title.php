
        <!--Page Header Start-->
        <section class="page-header">
            <div class="page-header__shape-1">
                <img src="assets/images/shapes/page-header-shape-1.png" alt="">
            </div>
            <div class="container">
                <div class="page-header__inner">
                
                    <h3><?php if(isset($page_title)&&!empty($page_title)) { echo $page_title; } ?></h3>
                    <div class="thm-breadcrumb__inner">
                        <ul class="thm-breadcrumb list-unstyled">
                            <li><a href="index.php">Inicio</a></li>
                            <li><span class="icon-angle-right"></span></li>
                            <li><?php if(isset($page_title)&&!empty($page_title)) { echo $page_title; } ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!--Page Header End-->