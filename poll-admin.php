<?php
$pageTitle = 'Polls';
include_once( 'includes/header.php' );
include_once( 'includes/functions_poll.php' );

// are we logged in? no → leave
if ( ! login_check() ) {
	header( 'Location: /' );
	exit();
} else {
	$me = $_SESSION['u'];
}

?>
<h1 class="text-center">Poll Admin</h1>
		<article class="panel panel-default">
			<header class="panel-heading">
				<h3 class="panel-title">Polls</h3>
			</header>
			<div class="panel-body">
				<form method="get" class="form-horizontal" action="<?=$_SERVER['PHP_SELF'] ?>">
					<?php
						// TODO: the following save & delete should be in the header
						if (isset($_POST['poll_title'])) {
							savePoll();
						}
						//TODO: We should probably sort out $_GET and $_POST stuff so that it's handled more consistently/nicely
						// Would be nice to have the site send everything via POST, but still allow for navigation to an admin page via GET parameters
						if (isset($_GET['poll']) && isset($_GET['deletePoll'])) {
							deletePoll($_GET['poll']);
						}
						showPollSelector('poll', (isset($_GET['poll']) ? $_GET['poll'] : -1), True, 20);
						echo <<<FORMGROUP
							<div class="form-group">
								<label for="deletePoll" class="col-lg-2 control-label">Delete</label>
								<div class="col-lg-10">
									<input type="checkbox" id="deletePoll" name="deletePoll">
								</div>
							</div>
							<div class="form-group">
								<div class="col-lg-12">
									<button type="submit" class="btn btn-default col-xs-offset-2">Go</button>
								</div>
							</div>
						</form>
FORMGROUP;

						showPollAdmin();
					?>
				</div>
			</article>
<?php
include_once( 'includes/footer.php' );
