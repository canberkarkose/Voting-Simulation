<?php
include_once("auth.php");
include_once("storage.php");
include_once("userstorage.php");
include_once("helper.php");

function validate($post, &$data, &$errors)
{
    $data = $post;
    return count($errors) === 0;
}


session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$errors = [];
$data = [];
if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
        $auth_user = $auth->authenticate($data["username"], $data["password"]);
        if (!$auth_user) {
            $errors['global'] = "Login failed";
        } else {
            $auth->login($auth_user);
            redirect("index.php");
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
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="login.css">
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
            margin-top: 200px;
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
            font-size: 20px;
            font-weight: normal;
        }
    </style>
    <title>Login</title>
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

    <form action="" method="post" novalidate>
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
            <label for="password">Password: </label><br>
            <input type="password" name="password" id="password" value="<?= $_POST['password'] ?? "" ?>">
            <?php if (isset($errors['password'])): ?>
                <span class="error">
                    <?= $errors['password'] ?>
                </span>
            <?php endif; ?>
            <?php if (isset($errors['global'])): ?>
                <p><span class="error">
                        <?= $errors['global'] ?>
                    </span></p>
            <?php endif; ?>
        </div>
        <div>
            <a href="signup.php">Don't have account yet?</a>
        </div>

        <div>
            <button type="submit">Login</button>
        </div>
</body>

</html>