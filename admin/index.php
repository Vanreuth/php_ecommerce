<?php
ob_start(); // Start output buffering
session_start();
require_once './config/database.php';
require_once './models/Page.php';
require_once './controllers/PageController.php';

$pdo = Database::connect(); 

if (!isset($_SESSION['user'])) {
    header('Location: views/login.php');
    exit();
}

$user = $_SESSION['user'];
$page = "./views/dashboard.php"; 

if (isset($_GET['p'])) {
    $p = $_GET['p'];

    switch ($p) {
        case "usermagement":
            $page = "./views/user/list.php";
            break;
        case "product":
            $page = "./views/product/list.php";
            break;
        case "category":
            $page = "./views/category/list.php";
            break;
        case "brand":
            $page = "./views/brand/list.php";
            break;
        case "order":
            $page = "./views/order/list.php";
            break;
        case "sliders":
            $page = "./views/sliders/list.php";
            break;
        case "pages": // Case for managing About & Contact pages
            if (isset($_GET['action']) && isset($_GET['title'])) {
                $pageController = new PageController($pdo);
        
                $action = $_GET['action'];
                $title = $_GET['title'];
        
                if ($action === 'edit') {
                    if ($title === "About") {
                        $page = "./views/pages/edit_about.php";  // Load About Edit Page
                    } elseif ($title === "Contact") {
                        $page = "./views/pages/edit_contact.php";  // Load Contact Edit Page
                    } else {
                        $page = "./views/pages/view.php";  // Default View Page
                    }
                } else {
                    $page = "./views/pages/view.php";  // Default View Page
                }
            }
            break;
            
        default:
            $page = "./views/dashboard.php";
            break;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<?php include "includes/head.php" ?>
  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <?php include "includes/sidebar.php" ?>
      <!-- End Sidebar -->

      <div class="main-panel">
        <?php include "includes/nav.php" ?>
        
        <div class="container">
          <?php include $page ?>
        </div>

        <?php include "includes/footer.php" ?>
      </div>

    </div>

    <!-- Core JS Files -->
    <?php include "includes/script.php" ?>
  </body>
</html>
