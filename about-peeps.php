<?php
	$pageTitle = "About Peeps";
	include_once('includes/header.php');

	echo <<<DOCUMENT
		<h1 class="text-center">SteamLUG Admins</h1>
		<section id="peeps">

			<article class="panel panel-default person">
				<header class="panel-heading">
					<h3 class="panel-title"><a href="http://twitter.com/johndrinkwater">johndrinkwater</a></h3>
				</header>
				<div class="panel-body">
					<img src="/avatars/johndrinkwater.png" />
					<p>Hey! I’m John.</p>
				</div>
			</article>

			<article class="panel panel-default person">
				<header class="panel-heading">
					<h3 class="panel-title">meklu</h3>
				</header>
				<div class="panel-body">
					<img src="/avatars/mnarikka.png" />
					<p>A consumer of copious quantities of ammonium chloride and a lazy perfectionist. All my projects are eternal.</p>
				</div>
			</article>

			<article class="panel panel-default person">
				<header class="panel-heading">
					<h3 class="panel-title"><a href="http://twitter.com/beansmyname">bean{,s}</a></h3>
				</header>
				<div class="panel-body">
					<img src="/avatars/beansmyname.png" />
					<p>I'm bean or beans or both at the same time. I'm the pink one. Best traits: looking cute, tasting sweet and having a creamy inside.</p>
				</div>
			</article>
			
			<article class="panel panel-default person">
				<header class="panel-heading">
					<h3 class="panel-title">Tele42</h3>
				</header>
				<div class="panel-body">
					<img src="/avatars/Tele42.png" />
					<p>"Here's an interesting little notion. Did you realize that most people's lives are governed by telephone numbers?"</p>
				</div>
			</article>

		<section>
DOCUMENT;

	include_once('includes/footer.php');


