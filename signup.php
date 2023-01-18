<?php
include_once("storage.php");
include_once("userstorage.php");
include_once("auth.php");
include_once("helper.php");

session_start();
$auth = new Auth(new UserStorage());
$user_storage = new UserStorage();



function validate($post, &$data, &$errors)
{
  // $data = $post;
  if (!isset($post["username"])) {
    $errors["username"] = "Username does not exist";
  } else
    if (trim($post["username"]) === "") {
      $errors["username"] = "Username is required";
    } else {
      $data["username"] = $post["username"];
    }

  if (!isset($post["email"])) {
    $errors["email"] = "Email does not exist";
  } else if (trim($post["email"]) === "") {
    $errors["email"] = "Email is required";
  } else if (!filter_var($post["email"], FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Email is invalid";
  } else {
    $data["email"] = $post["email"];
  }

  if (!isset($post["password"])) {
    $errors["password"] = "Password does not exist";
  } else if (trim($post["password"]) === "") {
    $errors["password"] = "Password is required";
  } else {
    $data["password"] = $post["password"];
  }

  if (!isset($post["password2"])) {
    $errors["password2"] = "Confirm password does not exist";
  } else if (trim($post["password2"]) === "") {
    $errors["password2"] = "Confirm password is required";
  } else if ($post["password2"] !== $post["password"]) {
    $errors["password2"] = "Passwords do not match";
  } else {
    $data["password2"] = $post["password2"];
  }

  return count($errors) === 0;
}


$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$errors = [];
$data = [];
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    if ($auth->user_exists($data['username'])) {
      $errors['global'] = "User already exists";
    } else {
      $auth->register($data);
      redirect('login.php');
    }
  }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign-Up</title>
  <link rel="stylesheet" href="navbar.css">
  <style>
    form {
      width: 50%;
      margin: 0 auto;
      text-align: left;
      padding: 20px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-shadow: 0 0 8px #ddd;
      background-color: aliceblue;
      margin-top: 150px;
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-size: 16px;
      font-weight: bold;
    }

    input[type="text"],
    input[type="password"],
    input[type="email"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
      font-size: 16px;
    }

    button[type="submit"] {
      width: 100%;
      background-color: green;
      color: white;
      padding: 14px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
    }

    .error {
      color: red;
      font-size: 14px;
      font-weight: normal;
    }
  </style>
</head>

<body>
  <div class="navbar">
    <a href="index.php">Home</a>
    <a href="signup.php">Sign-Up</a>
    <a href="login.php">Login</a>
    <a href="logout.php" style="display: <?= $auth->is_Authenticated() ? "block" : "none" ?>">Logout</a>
    <div class="dropdown">
      <button class="dropbtn">Options
        <i class="fa fa-caret-down"></i>
      </button>
      <div class="dropdown-content">
        <a href="voting.php">Voting</a>
        <a href="poll.php">Poll Creation</a>
      </div>
    </div>
  </div>

  <?php if (isset($errors['global'])): ?>
    <p><span class="error"><?= $errors['global'] ?></span></p>
    <?php endif; ?>
  <form action="" method="post">
    <div>
      <label for="username">Username: </label><br>
      <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>">
      <?php if (isset($errors["username"])): ?>
        <span class="error">
          <?= $errors["username"] ?>
        </span>
        <?php endif; ?>
    </div>
    <div>
      <label for="email">Email: </label><br>
      <input type="email" name="email" id="email" value="<?= $_POST['email'] ?? "" ?>">
      <?php if (isset($errors['email'])): ?>
        <span class="error">
          <?= $errors['email'] ?>
        </span>
        <?php endif; ?>
    </div>
    <div>
      <label for="password">Password: </label><br>
      <input type="password" name="password" id="password" value="<?= $_POST['password'] ?? "" ?>">
      <?php if (isset($errors['password'])): ?>
        <span class="error">
          <?= $errors['password'] ?>
        </span>
        <?php endif; ?>
    </div>
    <div>
      <label for="password2">Confirm Password: </label><br>
      <input type="password" name="password2" id="password2">
      <?php if (isset($errors['password2'])): ?>
        <span class="error"><?= $errors['password2'] ?></span>
        <?php endif; ?>
    </div>
    <div>
      <button type="submit">Register</button>
    </div>
  </form>

</body>

</html>