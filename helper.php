<?php 

    function redirect($page) {
        header("Location: ${page}");
        exit();
    }

    function redirect_alert($page, $message) {
        echo "<script>alert('${message}'); window.location.href='${page}';</script>";
        exit();
    }

?>