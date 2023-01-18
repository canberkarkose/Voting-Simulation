<?php
include_once("storage.php");
include_once("userstorage.php");
include_once("auth.php");
include_once("helper.php");
include_once("pollstorage.php");


session_start();

$user_storage = new UserStorage();
$users = $user_storage->findAll();
$auth = new Auth($user_storage);

if (!$auth->is_Authenticated()) {
  redirect_alert("login.php", "You must be logged in to access this page, please login or signup!");
}

$poll_storage = new PollStorage();
$polls = $poll_storage->findAll();

$open_polls = array();
$closed_polls = array();

foreach ($polls as $poll) {
  if (time() < strtotime($poll["deadline"])) {
    $open_polls[] = $poll;
  } else {
    $closed_polls[] = $poll;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="navbar.css">
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

  <p class="flash"
    style="display: inline-block; margin: 20px 0 0 20px; font-size: 3em; font-weight: bold; color: #333;">Welcome
    <?= $_SESSION["user"]["username"] ?></p>

  <h1 id="title" class="text-center" align="center">Voting Simulation</h1>
  <textarea class="textarea" readonly>
  Welcome to the voting simulation page, where you can participate in various polls and make your voice heard. On this page, you will find two sections: the left section displays polls that are currently open for voting, while the right section shows polls that have already closed. Each poll displays its unique ID, creation time, voting deadline, and a button to vote. By participating in these polls, you will not only learn about the voting process, but also understand the impact of your vote on the final outcome of an election. Whether you are a first-time voter or an experienced one, this page offers a fun and educational experience for everyone. So, take a look at the polls and make your vote count!    </textarea>

  <div class="quote-container">
    <blockquote cite="https://www.forbes.com/quotes/3844/">
      <p id="quote" class="quote text-center">Democracy is based upon the conviction there are extraordinary
        possibilities
        in ordinary people.</p>
      <cite class="quote text-center">-- Harry Emerson Fosdick</cite>
    </blockquote>
  </div>
</body>

<div class="open-section">
  <div class="poll-section">
    <div class="poll-header">Open Polls</div>
    <?php
    usort($open_polls, function ($a, $b) {
      return strtotime($b['creation']) - strtotime($a['creation']);
    });
    if (!empty($open_polls)) {
      foreach ($open_polls as $poll) {
        ?>
        <div class="poll-container">
          <div class="poll-question">Poll Question: <?= $poll['question'] ?></div>
          <div class="poll-creation-time">Created on: <?= $poll['creation'] ?></div>
          <div class="poll-deadline">Deadline: <?= $poll['deadline'] ?></div>
          <?php echo '<form action="voting.php" method="post">'; ?>
          <input type="submit" value="Vote">
        </div>
      <?php
      }
    } else {
      echo '<div class="poll-info">No open polls available.</div>';
    }
    ?>
  </div>

  <div class="closed-section">
    <div class="poll-section">
      <div class="poll-header">Closed Polls</div>
      <?php
      usort($closed_polls, function ($a, $b) {
        return strtotime($b['creation']) - strtotime($a['creation']);
      });
      if (!empty($closed_polls)) {
        foreach ($closed_polls as $poll) {
          ?>
          <div class="poll-container">
            <div class="poll-question">Poll Question: <?= $poll['question'] ?></div>
            <div class="poll-creation-time">Created on: <?= $poll['creation'] ?></div>
            <div class="poll-deadline-over">Deadline: <?= $poll['deadline'] ?></div>
          </div>
        <?php
        }
      } else {
        echo '<div class="poll-info">No closed polls available.</div>';
      }
      ?>
    </div>
  </div>

</html>