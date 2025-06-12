<?php
require_once 'controllers/PageController.php';

// Instantiate the PageController
$pageController = new PageController();

// Fetch the About page content from the database
$pageData = $pageController->getPage('About');
?>

<section class="bg-img1 txt-center p-lr-15 p-tb-92 herder-v4 m-t-80" 
         style="background-image: url('./admin/views/pages/uploads/<?= htmlspecialchars($pageData['banner_image']) ?>'); height: 350px; background-size: cover; background-position: center;">
    <h2 class="ltext-105 cl0 txt-center"><?= htmlspecialchars($pageData['title']) ?></h2>
</section>

<!-- Content page -->
<section class="bg0 p-t-75 p-b-120">
    <div class="container">
        <div class="row p-b-148">
            <div class="col-md-7 col-lg-8">
                <div class="p-t-7 p-r-85 p-r-15-lg p-r-0-md">
                    <h3 class="mtext-111 cl2 p-b-16">
                        <?= htmlspecialchars($pageData['subtitle1']) ?>
                    </h3>
                    <p class="stext-113 cl6 p-b-26">
                        <?= htmlspecialchars($pageData['description1'])?>
                    </p>
                </div>
            </div>
            <div class="col-11 col-md-5 col-lg-4 m-lr-auto">
                <div class="how-bor1">
                    <div class="hov-img0">
                        <img src="./admin/views/pages/uploads/<?= htmlspecialchars($pageData['image1']) ?>" alt="Image 1">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="order-md-2 col-md-7 col-lg-8 p-b-30">
                <div class="p-t-7 p-l-85 p-l-15-lg p-l-0-md">
                    <h3 class="mtext-111 cl2 p-b-16">
                        <?= htmlspecialchars($pageData['subtitle2']) ?>
                    </h3>
                    <p class="stext-113 cl6 p-b-26">
                        <?= nl2br(htmlspecialchars($pageData['description2'])) ?>
                    </p>
                </div>
            </div>
            <div class="order-md-1 col-11 col-md-5 col-lg-4 m-lr-auto p-b-30">
                <div class="how-bor2">
                    <div class="hov-img0">
                        <img src="./admin/views/pages/uploads/<?= htmlspecialchars($pageData['image2']) ?>" alt="Image 2">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
