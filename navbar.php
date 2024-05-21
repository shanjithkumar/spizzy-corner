<?php

ob_start();

include 'partials/_dbconnect.php';

// Check if user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    $loggedin = true;
    $userId = $_SESSION['userId'];
    $username = $_SESSION['username'];
} else {
    $loggedin = false;
    $userId = 0;
}

// Fetch system name from database
$sql = "SELECT * FROM `sitedetail`";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$systemName = $row['systemName'];

// Display header with navigation bar
echo '<header id="site-header" class="fixed-top">
<div class="container">
    <nav class="navbar navbar-expand-lg navbar-light">
    <a class="navbar-brand" href="index.php">' . $systemName . '</a>
        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>
            <span class="navbar-toggler-icon fa icon-close fa-times"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarScroll">
            <ul class="navbar-nav ms-auto my-2 my-lg-0 navbar-nav-scroll">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarScrollingDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Top Categories <i class="fas fa-angle-down"></i>
                    </a>

                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">';
// Fetch categories from database and display in dropdown
$sql = "SELECT categorieName, categorieId FROM `categories`";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    echo '<a class="dropdown-item" href="viewPizzaList.php?catid=' . $row['categorieId'] . '">' . $row['categorieName'] . '</a>';
}
echo '</div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="viewOrder.php">Your Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php">About Us</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact Us</a>
                </li>
                <li class="nav-item">
                    <form method="get" action="search.php" class="input-group">
                        <input type="search" name="search" id="search" class="form-control" placeholder="Search" required>
                        <div class="input-group-append">
                            <button type="submit" class="input-group-text" id="basic-addon2"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </li>';

// Fetch cart item count and display in navbar
$countsql = "SELECT SUM(`itemQuantity`) FROM `viewcart` WHERE `userId`=$userId";
$countresult = mysqli_query($conn, $countsql);
$countrow = mysqli_fetch_assoc($countresult);
$count = $countrow['SUM(`itemQuantity`)'];
if (!$count) {
    $count = 0;
}

// Display cart icon and count
echo '<li class="nav-item">
    <span class="w3l-icon -buy">
        <span class="fa fa-shopping-cart"></span>
    </span>
    <a class="w3l-text" href="viewCart.php"><span>Cart(' . $count . ')</span></a>
</li>';

if ($loggedin) {
    echo '<ul class="navbar-nav mr-2">
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown"> Welcome ' . $username . '</a>
      <div class="dropdown-menu" aria-labelledby="navbarDropdown">
        <a class="dropdown-item" href="partials/_logout.php">Logout</a>
      </div>
    </li>
  </ul>
  <div class="text-center image-size-small position-relative">
    <a href="viewProfile.php"><img src="img/person-' . $userId . '.jpg" class="rounded-circle" onError="this.src = \'img/profilePic.jpg\'" style="width:40px; height:40px"></a>
  </div>';
} else {
    echo '
  <li class="nav-item">
  <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
</li>
<li class="nav-item">
  <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#signupModal">Sign up</a>
</li>';
}

// Display login and signup links
echo '
 
</ul>
</div>
</nav>
</div>
</header>';

// Login Modal
echo '<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="loginModalLabel">Login</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <form action="partials/_handleLogin.php" method="post">
                <div class="mb-3">
                <label for="username">Username</label>
                    <input class="form-control"id="loginusername" name="loginusername" placeholder="Enter Your Username" type="text" required>
                </div>
                <div class="mb-3">
                <label for="password">Password</label>
                    <input type="password" class="form-control"  id="loginpassword" name="loginpassword" placeholder="Enter Your Password" type="password" required data-toggle="password">
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
</div>
</div>';

// SignUp Modal
echo '<!-- Sign up Modal -->
<div class="modal fade" id="signupModal" tabindex="-1" role="dialog" aria-labelledby="signupModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="signupModal">SignUp Here</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="partials/_handleSignup.php" method="post">
          <div class="form-group">
            <label for="username">Username</label>
            <input class="form-control" id="username" name="username" placeholder="Choose a unique Username" type="text" required minlength="3" maxlength="11">
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="firstName">First Name:</label>
              <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name" required>
            </div>
            <div class="form-group col-md-6">
              <label for="lastName">Last name:</label>
              <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last name" required>
            </div>
          </div>
          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter Your Email" required>
          </div>
          <div class="form-group">
            <label for="phone">Phone No:</label>
            <div class="input-group mb-3">
              <div class="input-group-prepend">
                <span class="input-group-text" id="basic-addon">+91</span>
              </div>
              <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter Your Phone Number" required pattern="[0-9]{10}" maxlength="10">
            </div>
          </div>
          <div class="text-left my-2">
            <label for="password">Password:</label>
            <input class="form-control" id="password" name="password" placeholder="Enter Password" type="password" required data-toggle="password" minlength="4" maxlength="21">
          </div>
          <div class="text-left my-2">
            <label for="password1">Renter Password:</label>
            <input class="form-control" id="cpassword" name="cpassword" placeholder="Renter Password" type="password" required data-toggle="password" minlength="4" maxlength="21">
          </div>
          <button type="submit" class="btn btn-success">Submit</button>
        </form>
        <p class="mb-0 mt-1">Already have an account? <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#loginModal">Login here</a>.</p>
      </div>
    </div>
  </div>
</div>
';

if (isset($_GET['signupsuccess']) && $_GET['signupsuccess'] == "true" && !isset($_SESSION['signup_success_alert_shown'])) {
    echo '<script>
          // Trigger SweetAlert popup
          Swal.fire({
              icon: "success",
              title: "Registration Successful!",
              text: "You can now login.",
              showConfirmButton: false,
              timer: 2000 // Close the alert after 2 seconds
          });
        </script>';
    $_SESSION['signup_success_alert_shown'] = true;
}

if (isset($_GET['error']) && $_GET['signupsuccess'] == "false" && !isset($_SESSION['signup_error_alert_shown'])) {
    echo '<script>
          // Trigger SweetAlert popup
          Swal.fire({
              icon: "error",
              title: "Registration Error!",
              text: "' . $_GET['error'] . '",
              showConfirmButton: false,
              timer: 2000 // Close the alert after 2 seconds
          });
        </script>';
    $_SESSION['signup_error_alert_shown'] = true;
}

if (isset($_GET['loginsuccess']) && $_GET['loginsuccess'] == "true" && !isset($_SESSION['login_success_alert_shown'])) {
    echo '<script>
          // Trigger SweetAlert popup
          Swal.fire({
              icon: "success",
              title: "Login Successful!",
              text: "You are now logged in.",
              showConfirmButton: false,
              timer: 2000 // Close the alert after 2 seconds
          });
        </script>';
    $_SESSION['login_success_alert_shown'] = true;
}

if (isset($_GET['loginsuccess']) && $_GET['loginsuccess'] == "false" && !isset($_SESSION['login_failed_alert_shown'])) {
    echo '<script>
          // Trigger SweetAlert popup
          Swal.fire({
              icon: "error",
              title: "Login Failed!",
              text: "Invalid Credentials.",
              showConfirmButton: false,
              timer: 2000 // Close the alert after 2 seconds
          });
        </script>';
    $_SESSION['login_failed_alert_shown'] = true;
}

// Output buffer flushing
ob_end_flush();
?>
