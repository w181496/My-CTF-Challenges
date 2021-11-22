<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.8.1/css/bulma.css">
<script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
<script src="asset/main.js"></script>
</head>



<body>

<nav class="navbar" role="navigation" aria-label="main navigation">
  <div class="navbar-brand">
    <a class="navbar-item" href="index.php">
      <h1><strong>MeowWorld</strong></h1>
    </a>

    <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </a>
  </div>

  <div id="navbarBasicExample" class="navbar-menu">
    <div class="navbar-start">
      <a class="navbar-item" href="index.php">
        Home
      </a>

      <a class="navbar-item" href="index.php?f=cat">
        Cats
      </a>

      <a class="navbar-item" href="index.php?f=about">
        About
      </a>

    </div>

    <div class="navbar-end">
      <div class="navbar-item">
        <div class="buttons">
          <a class="button is-primary" onclick="alert('under construction :(')">
            <strong>Sign up</strong>
          </a>
          <a class="button is-light" onclick="alert('under construction :(')">
            Log in
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>

  <section class="section">
    <div class="container">
        <?php 
            $f = $_GET['f'] ?? "home";
            include("{$f}.php"); 
        ?>
    </div>
  </section>
</body>


</body>
</html>
