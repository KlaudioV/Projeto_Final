<?php
require_once("requires/bd.php");
session_start();

$currentPage = $_GET["p"];
// Set Language variable
if (isset($_GET['lang']) && !empty($_GET['lang'])) {
	$_SESSION['lang'] = $_GET['lang'];

	if (isset($_SESSION['lang']) && $_SESSION['lang'] != $_GET['lang']) {
		echo "<script type='text/javascript'> location.reload(); </script>";
	}
}

// Include Language file
if (isset($_SESSION['lang'])) {
	include "languages/lang_" . strtolower($_SESSION['lang']) . ".php";
} else {
	include "languages/lang_en.php";
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>RAMv2 Tomar - </title>
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/main.css?v=2">
	<script src="http://code.jquery.com/jquery-3.3.1.min.js"></script>
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<script src="js/bootstrap.min.js"></script>
</head>

<body>
	<nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">

		<a href="#" class="navbar-brand col-sm-3 col-md-2 mr-0">Roteiros de Tomar</a>

		<input onkeyup=return(keyTyped(event)) class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search">
		<div class="dropdown">
			<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
				<svg class="bi bi-flag" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" d="M3.5 1a.5.5 0 0 1 .5.5v13a.5.5 0 0 1-1 0v-13a.5.5 0 0 1 .5-.5z" />
					<path fill-rule="evenodd" d="M3.762 2.558C4.735 1.909 5.348 1.5 6.5 1.5c.653 0 1.139.325 1.495.562l.032.022c.391.26.646.416.973.416.168 0 .356-.042.587-.126a8.89 8.89 0 0 0 .593-.25c.058-.027.117-.053.18-.08.57-.255 1.278-.544 2.14-.544a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-.5.5c-.638 0-1.18.21-1.734.457l-.159.07c-.22.1-.453.205-.678.287A2.719 2.719 0 0 1 9 9.5c-.653 0-1.139-.325-1.495-.562l-.032-.022c-.391-.26-.646-.416-.973-.416-.833 0-1.218.246-2.223.916a.5.5 0 1 1-.515-.858C4.735 7.909 5.348 7.5 6.5 7.5c.653 0 1.139.325 1.495.562l.032.022c.391.26.646.416.973.416.168 0 .356-.042.587-.126.187-.068.376-.153.593-.25.058-.027.117-.053.18-.08.456-.204 1-.43 1.64-.512V2.543c-.433.074-.83.234-1.234.414l-.159.07c-.22.1-.453.205-.678.287A2.719 2.719 0 0 1 9 3.5c-.653 0-1.139-.325-1.495-.562l-.032-.022c-.391-.26-.646-.416-.973-.416-.833 0-1.218.246-2.223.916a.5.5 0 0 1-.554-.832l.04-.026z" />
				</svg>
			</button>
			<div class="dropdown-menu">
				<?php
				$check_query = $bd->query("SELECT * FROM Linguagem");

				while ($row = $check_query->fetchArray()) {
				?>
					<a class="dropdown-item <?php if (isset($_SESSION['lang']) && $_SESSION['lang'] == $row["nome"]) {
												echo "active";
											} ?>" href="?p=<?php echo $_GET["p"] ?>&lang=<?php echo $row["Nome"] ?>"><?php echo $row["Nome"] ?></a>


				<?php
				}

				?>

			</div>
		</div>
		<ul class="navbar-nav px-3">
			<li class="nav-item text-nowrap">

				<?php
				if (!isset($_SESSION["username"])) {
					echo '<a class="nav-link" href="#modalLogin" data-toggle="modal">' . _LOGIN . '</a>';
				} else {
					echo '<a class="nav-link" href="logout.php">' . _LOGOUT . '</a>';
				}
				?>
			</li>
		</ul>


	</nav>

	<div class="container-fluid">
		<div class="row">
			<nav class="col-md-2 d-none d-md-block bg-light sidebar">
				<div class="sidebar-sticky">
					<ul class="nav flex-column">
						<li class="nav-item">
							<a class="nav-link active" href="#">
								<?php
								if (isset($_SESSION["username"])) {
									echo _HELLO . $_SESSION["username"] . '!';
								}  ?>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="?p=0">
								<?= _HOME ?>
							</a>
						</li>
						<?php
						if (isset($_SESSION["username"])) {

						?>
							<li class="nav-item">
								<a class="nav-link" href="?p=1">
									+ <?= _ADDBUILDING ?>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="?p=3">
									+ <?= _CREATEROUTE ?>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#modalRegisto" data-toggle="modal">
									+ <?= _REGISTERUSER ?>
								</a>
							<?php
						} ?>
							</li>
					</ul>
			</nav>



			<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

				<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
					<?php 
					
						if ($currentPage  == 0) {


					?>
						<h1 class="h1">Dashboard - <?= _DASHBOARD ?></h1>
				</div>
				<div class="row col-md-12">
					<div class="col-md-3">

						<div class="card">
							<img class="card-img-top" src="https://victortravelblogdotcom.files.wordpress.com/2014/06/templars-convent-christ-tomar-portugal-22.jpg?w=780" alt="Card image cap">
							<div class="card-body">
								<h5 class="card-title"><?= _OVERVIEW ?></h5>
								<p class="card-text"><?= _STATISTICS ?></p>
							</div>
							<ul class="list-group list-group-flush">
								<?php /* Query Edificios */
								$check_query = $bd->query("SELECT * FROM Edificios");
								$checked_query = 0;
								while ($row = $check_query->fetchArray()) {
									$checked_query += 1;
								}
								echo '
<li class="list-group-item">' . _TOTALBUILDINGS . ': ' . $checked_query . '</li> 
';
								/* Query Rotas */
								$check_query = $bd->query("SELECT * FROM Rotas");
								$checked_query = 0;
								while ($row = $check_query->fetchArray()) {
									$checked_query += 1;
								}
								echo '
                    <li class="list-group-item">' . _TOTALROUTES . ': ' . $checked_query . '</li> 
                    ';
								?>
								<?php /* Query Utilizadores */
								$check_query = $bd->query("SELECT * FROM Utilizadores");
								$checked_query = 0;
								while ($row = $check_query->fetchArray()) {
									$checked_query += 1;
								}
								echo '
                    <li class="list-group-item">' . _TOTALUSERS . ': ' . $checked_query . '</li> 
                    ';
								?>
							</ul>
						</div>
					</div>
					<div class="col-md-9">
						<div class="card" >
							<div class="card-body"">
								<h5 class="card-title"><?= _BUILDINGSLIST ?></h5>
							</div>
							<?php
							$check_query = $bd->query("SELECT * FROM DescEdif as a,Edificios as b WHERE a.ling=1 AND a.idEdificio=b.idEdificio");

							$checked_query = 0;
                            echo '<ul style="overflow-y:scroll; height: 400px;" class="list-group list-group-flush searchable">';
							while ($row = $check_query->fetchArray()) {
							echo '	
<li class="list-group-item">' . $row["NomeEdif"] . '<a href="?p=2&id=' . $row["idEdificio"] . '">' . (isset($_SESSION["username"]) && $_SESSION["username"] != "" ? "<span class='badge badge-warning ml-2'>" . _EDIT . "</span>" : "") . '</a></li>
';
                            }
                            echo '</ul>';

							?>

						</div>
						<div class="card mt-2">
                            <div class="card">
                                <div class="card-body" >
                                    <h5 class="card-title"><?= _ROUTESLIST ?></h5>
                                </div>
							<?php
							$check_query = $bd->query("SELECT * FROM Rotas");

							$checked_query = 0;
                            echo '<ul style="overflow-y:scroll; height: 400px" class="list-group list-group-flush searchable">';
							while ($row = $check_query->fetchArray()) {
								echo '
<li class="list-group-item">' . $row["nome"] . '<a href="?p=4&id=' . $row["id"] . '">' . (isset($_SESSION["username"]) && $_SESSION["username"] != "" ? "<span class='badge badge-warning ml-2'>" . _EDIT . "</span>" : "") . '</a></li>
';
							}
echo '</ul>';
							?>
                            </div>
						</div>
						<div class="card mt-2" >
                            <div class="card" >
                                <div class="card-body">
                                    <h5 class="card-title" style="margin-top: -15px";><?= _MISSINGTRANSLATION ?></h5>
                                </div>
							<?php
							$check_query = $bd->query('SELECT * FROM DescEdif as a,Edificios as b WHERE a.idEdificio=b.idEdificio');
							
                            echo '<ul style="overflow-y:scroll; height: 400px" class="list-group list-group-flush searchable">';
							while ($row = $check_query->fetchArray()) {
								if($row["Descricao"]==""){
									$check_query2 = $bd->query("SELECT * FROM Linguagem WHERE id=".$row['ling']."
									");
									$row2 = $check_query2->fetchArray();
								echo '
<li class="list-group-item">' . $row["NomeEdif"] . '<a href="?p=2&id=' . $row["idEdificio"] . '">' . (isset($_SESSION["username"]) && $_SESSION["username"] != "" ? "<span class='badge badge-warning ml-2'>" . _EDIT . "</span>" : "") . '<span class="badge badge-info ml-2">'.$row2["Nome"].'</span></a></li>
';
								}
							}
echo '</ul>';
							?>
                            </div>
						</div>
					</div>
				</div>
			<?php } else if ($_GET["p"] == 1) {
						require_once("addEdif.php");
					} else if ($_GET["p"] == 2) {
						require_once("editEdif.php");
					} else if ($_GET["p"] == 3) {
						require_once("addRota.php");
					}else if ($_GET["p"] == 4) {
						require_once("editRota.php");
					}


			?>
			</main>
		</div>












		<!-- Modal HTML -->
		<div id="modalRegisto" class="modal fade">
			<div class="modal-dialog modal-login">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title"><?= _REGISTERUSER ?></h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">
						<form action="register.php" method="post">
							<div class="form-group">
								<i class="fa fa-user"></i>
								<input type="text" class="form-control" placeholder="Username" name="username" required="required">
							</div>
							<div class="form-group">
								<i class="fa fa-user"></i>
								<input type="text" class="form-control" placeholder="Email" name="email" required="required">
							</div>
							<div class="form-group">
								<i class="fa fa-lock"></i>
								<input type="password" class="form-control" placeholder="Password" name="password" required="required">
							</div>
							<div class="g-recaptcha" data-sitekey="6LfDdKMZAAAAABMfwOGqsm0hTqIuursms5znx8hR"></div>
							<div class="form-group">
								<input type="submit" class="btn btn-primary btn-block btn-lg" value="<?= _REGISTER ?>">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal HTML -->
		<div id="modalLogin" class="modal fade">
			<div class="modal-dialog modal-login">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title"><?= _LOGIN ?></h4>
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					</div>
					<div class="modal-body">
						<form action="login.php" method="post">
							<div class="form-group">
								<i class="fa fa-user"></i>
								<input type="text" class="form-control" placeholder="Username" name="username" required="required">
							</div>
							<div class="form-group">
								<i class="fa fa-lock"></i>
								<input type="password" class="form-control" placeholder="Password" name="password" required="required">
							</div>
							<div class="form-group">
								<input type="submit" class="btn btn-primary btn-block btn-lg" value="<?= _LOGIN ?>">
							</div>
						</form>

					</div>
					<div class="modal-footer">

						<a href="#">Forgot Password?</a>
					</div>
				</div>
			</div>
		</div>
</body>

<script>
    function keyTyped(e) {
        var items = document.querySelectorAll(".searchable > li");
        var matches = [];
        var typed = e.target.value.toLowerCase();
        var text, i;
        for (i = 0; i < items.length; i++) {
            text = items[i].textContent.toLowerCase();
            if (!typed || text.indexOf(typed) != -1) {
                matches.push(items[i]);
            }
        }
        // now hide all li tags and show all matches
        for (i = 0; i < items.length; i++) {
            items[i].style.display = "none";
        }
        // now show all matches
        for (i = 0; i < matches.length; i++) {
            matches[i].style.display = "";
        }
    }
    </script>

</html>