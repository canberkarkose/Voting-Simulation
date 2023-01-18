<?php
include_once("storage.php");
include_once("userstorage.php");
include_once("auth.php");
include_once("helper.php");
include_once("pollstorage.php");


session_start();
$auth = new Auth(new UserStorage());
$user_storage = new UserStorage();

if (!$auth->is_authenticated()) {
    redirect_alert("login.php", "You must first login!");
}

$poll_storage = new PollStorage();
$polls = $poll_storage->findAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="navbar.css">
    <title>Voting</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #poll-select {
            width: 30%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            padding: 5px;
            border: 1px solid #dddddd;
            border-radius: 4px;
        }

        #poll-options {
            width: 80%;
            margin: 0 auto;
            text-align: center;
            font-size: 20px;
            padding: 10px;
        }

        #submit-button {
            margin: 0 auto;
            display: block;
            width: 30%;
            font-size: 16px;
            padding: 10px;
            border: none;
            border-radius: 4px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        #poll-creation,
        #poll-deadline {
            margin: 10px auto;
            text-align: center;
            font-size: 18px;
        }

        #prompt {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        label {
            margin-right: 10px;
        }

        input[type=radio],
        input[type=checkbox] {
            margin-right: 10px;
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

    <div id="prompt">Please Select Which Poll You Want To Vote To:</div>
    <select id="poll-select">
        <option value="">Select a poll</option>
        <?php foreach ($polls as $poll): ?>
            <option value="<?= $poll["id"] ?>">
                <?= $poll["question"] ?>
            </option>
        <?php endforeach; ?>
    </select>
    <div id="poll-options"></div>
    <script>
        fetch("polls.json")
            .then(response => response.json())
            .then(data => {
                var polls = [];
                for (var key in data) {
                    if (data.hasOwnProperty(key)) {
                        var poll = data[key];
                        poll.id = key;
                        polls.push(poll);
                    }
                }
                $polls = polls;
            });


        $("#poll-select").change(function () {
            var pollId = $(this).val();
            var poll = findPollById(pollId);
            if (poll) {
                var optionsHtml = "";
                for (var i = 0; i < poll.options.length; i++) {
                    var option = poll.options[i];
                    optionsHtml += '<label for="option">' + option + '</label>';
                    if (poll.multiple == false) {
                        optionsHtml += '<input type="radio" name="option_' + pollId + '" value="' + option + '">';
                    } else if (poll.multiple == true) {
                        optionsHtml += '<input type="checkbox" name="option_' + pollId + '" value="' + option + '">';
                    }
                    optionsHtml += '<br>';
                }
                optionsHtml += '<div id="poll-creation">Poll created at: ' + poll.creation + '</div>';
                optionsHtml += '<div id="poll-deadline">Voting deadline: ' + poll.deadline + '</div>';
                optionsHtml += '<button id="submit-button">Submit vote</button>';
                $("#poll-options").html(optionsHtml);
            } else {
                $("#poll-options").html("");
            }
        });

        function findPollById(pollId) {
            for (var i = 0; i < $polls.length; i++) {
                var poll = $polls[i];
                if (poll.id == pollId) {
                    return poll;
                }
            }
            return null;
        }

        $("#poll-options").on("click", "#submit-button", function () {
            var pollId = $("#poll-select").val();
            var poll = findPollById(pollId);

            var selectedOptions = $('input[name="option_' + pollId + '"]:checked');
            if (selectedOptions.length === 0) {
                alert("You should at least select 1 option!");
                return;
            }
            for (var i = 0; i < selectedOptions.length; i++) {
                var option = selectedOptions[i].value;
                poll.answers.push(option);
            }
            if (poll.voted.indexOf("<?= $auth->get_username() ?>") == -1) {
                poll.voted.push("<?= $auth->get_username() ?>");
            }

            var currentDate = new Date();
            var deadline = new Date(poll.deadline);

            if (currentDate > deadline) {
                alert("Sorry, the voting deadline has passed for this poll.");
                return;
            }

            console.log(poll.answers);
            console.log(poll.voted);

            $.ajax({
                url: "polls.php",
                type: "POST",
                data: {
                    id: poll.id,
                    answers: poll.answers,
                    voted: poll.voted
                },
                success: function (response) {
                    console.log(response);
                    alert("Your vote has been submitted!");
                    window.location.href = "index.php";
                },
                error: function (response) {
                    console.log(response);
                    alert("Something went wrong!");
                }
            });

        });
    </script>

</body>

</html>