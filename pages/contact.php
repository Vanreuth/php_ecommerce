<?php
require_once './admin/controllers/PageController.php';
require_once './admin/config/database.php';

// Instantiate the PageController
$pageController = new PageController($pdo);

// Fetch the Contact page content from the database
$pageData = $pageController->getPage('Contact'); // Get content by title

$title = $pageData['title'];
$address = $pageData['address'];
$phone = $pageData['phone'];
$email = $pageData['email'];
?>

<!-- Title page -->
<section class="bg-img1 txt-center p-lr-15 p-tb-92 herder-v4" 
         style="background-image: url('./admin/views/pages/uploads/<?= htmlspecialchars($pageData['banner_image']) ?>');">
    <h2 class="ltext-105 cl0 txt-center"><?= htmlspecialchars($title) ?></h2>
</section>


<!-- Content page -->
<section class="bg0 p-t-104 p-b-116">
    <div class="container">
        <div class="flex-w flex-tr">
            <div class="size-210 bor10 p-lr-70 p-t-55 p-b-70 p-lr-15-lg w-full-md">
                <form action="contact_submit.php" method="POST">  <!-- Added action and method -->
                    <h4 class="mtext-105 cl2 txt-center p-b-30">
                        Send Us A Message
                    </h4>

                    <div class="bor8 m-b-20 how-pos4-parent">
                        <input class="stext-111 cl2 plh3 size-116 p-l-62 p-r-30" type="email" name="email" placeholder="Your Email Address" required>
                    </div>

                    <div class="bor8 m-b-30">
                        <textarea class="stext-111 cl2 plh3 size-120 p-lr-28 p-tb-25" name="msg" placeholder="How Can We Help?" required></textarea>
                    </div>

                    <button class="flex-c-m stext-101 cl0 size-121 bg3 bor1 hov-btn3 p-lr-15 trans-04 pointer">
                        Submit
                    </button>
                </form>
            </div>

            <div class="size-210 bor10 flex-w flex-col-m p-lr-93 p-tb-30 p-lr-15-lg w-full-md">
                <div class="flex-w w-full p-b-42">
                    <span class="fs-18 cl5 txt-center size-211">
                        <span class="lnr lnr-map-marker"></span>
                    </span>
                    <div class="size-212 p-t-2">
                        <span class="mtext-110 cl2">Address</span>
                        <p class="stext-115 cl6 size-213 p-t-18">
                            <?= htmlspecialchars($address) ?>  <!-- Dynamic Address -->
                        </p>
                    </div>
                </div>

                <div class="flex-w w-full p-b-42">
                    <span class="fs-18 cl5 txt-center size-211">
                        <span class="lnr lnr-phone-handset"></span>
                    </span>
                    <div class="size-212 p-t-2">
                        <span class="mtext-110 cl2">Let's Talk</span>
                        <p class="stext-115 cl1 size-213 p-t-18">
                            <?= htmlspecialchars($phone) ?>  <!-- Dynamic Phone -->
                        </p>
                    </div>
                </div>

                <div class="flex-w w-full">
                    <span class="fs-18 cl5 txt-center size-211">
                        <span class="lnr lnr-envelope"></span>
                    </span>
                    <div class="size-212 p-t-2">
                        <span class="mtext-110 cl2">Sale Support</span>
                        <p class="stext-115 cl1 size-213 p-t-18">
                            <?= htmlspecialchars($email) ?>  <!-- Dynamic Email -->
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map -->
<div class="map">
    <div class="size-303" id="google_map" data-map-x="40.691446" data-map-y="-73.886787" data-pin="./assets/images/icons/pin.png" data-scrollwhell="0" data-draggable="1" data-zoom="11"></div>
</div>
