<?php
include_once("storage.php");
include_once("userstorage.php");
include_once("auth.php");
include_once("helper.php");
include_once("pollstorage.php");

session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);

if (!$auth->is_authenticated()) {
    redirect_alert("login.php", "You must first login!");
}

if (!$auth->authorize(["admin"])) {
    redirect_alert("index.php", "You are not authorized to view this page!");
}

function validate($post, &$data, &$errors)
{
    if (!isset($post["question"])) {
        $errors["question"] = "Question does not exist";
    } else if (trim($post["question"]) === "") {
        $errors["question"] = "Question is required";
    } else {
        $data["question"] = $post["question"];
    }

    if (!isset($post["options"])) {
        $errors["options"] = "Options does not exist";
    } else if (trim($post["options"]) === "") {
        $errors["options"] = "Options are required";
    } else {
        $data["options"] = explode("\r\n", $post["options"]);
    }

    if (!isset($post["multiple"])) {
        $errors["multiple"] = "Multiple option does not exist";
    } else if (trim($post["multiple"]) === "") {
        $errors["multiple"] = "Multiple option is required";
    } else {
        if ($post["multiple"] === "yes") {
            $data["multiple"] = true;
        } else {
            $data["multiple"] = false;
        }
    }

    if (!isset($post["deadline"])) {
        $errors["deadline"] = "Deadline does not exist";
    } else if (trim($post["deadline"]) === "") {
        $errors["deadline"] = "Deadline is required";
    } else {
        $data["deadline"] = $post["deadline"];
    }

    $data["creation"] = date("Y-m-d H:i:s");

    $data["voted"] = array();
    $data["answers"] = array();

    return count($errors) === 0;
}

$errors = [];
$data = [];

if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
        $poll_storage = new PollStorage();
        $data["poll_id"] = "poll" . (count($poll_storage->findAll()) + 1);
        $poll_storage->add($data);
        redirect_alert("index.php", "Poll created successfully!");
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
    <title>Poll</title>
    <style>
        form {
            width: 600px;
            margin: 0 auto;
            text-align: left;
            padding: 60px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-top: 100px;
            background-color: aliceblue;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 16px;
            resize: vertical;
        }

        input[type="radio"] {
            margin-right: 8px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            padding: 15px 272px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
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
            <button class="dropbtn">Options <i class="fa fa-caret-down"></i></button>
            <div class="dropdown-content">
                <a href="voting.php">Voting</a>
                <a href="poll.php">Poll Creation</a>
            </div>
        </div>
    </div>

    <form method="post" action="" novalidate>
        <label for="question">Write your question here:</label>
        <input type="text" name="question" id="question" required><br>
        <?php if (isset($errors["question"])): ?>
            <span style="color: red"><?= $errors["question"] ?></span>
        <?php endif; ?>
        <label for="options">Write your options here:</label>
        <textarea name="options" id="options" cols="30" rows="10" required></textarea><br>
        <?php if (isset($errors["options"])): ?>
            <span style="color: red"><?= $errors["options"] ?></span>
        <?php endif; ?>
        <label for="multiple">Multiple options can be selected:</label>
        <input type="radio" name="multiple" id="multiple" value="yes" required>Yes
        <input type="radio" name="multiple" id="multiple" value="no" required>No<br>
        <?php if (isset($errors["multiple"])): ?>
            <span style="color: red"><?= $errors["multiple"] ?></span>
        <?php endif; ?>
        <label for="deadline">Voting deadline:</label>
        <input type="date" name="deadline" id="deadline" required><br>
        <?php if (isset($errors["deadline"])): ?>
            <span style="color: red"><?= $errors["deadline"] ?></span>
        <?php endif; ?>
        <label for="creation">Time of creation:</label>
        <input type="date" name="creation" id="creation"><br>
        <input type="submit" value="Create Poll">
    </form>