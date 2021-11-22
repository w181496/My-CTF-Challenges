<?php include("header.php"); ?>

<body translate="no" >
<div class="banner">
<?php
$url = "http://127.0.0.1/get.php?vid=1cmcA48MXoU";
if(isset($_POST['url']))
    $url = $_POST['url'];
echo curl($url);
?>
</div>

<?php include("footer.php"); ?>
