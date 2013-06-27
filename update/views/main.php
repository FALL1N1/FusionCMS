<!DOCTYPE html>
<html>
	<head>
		<title>Update FusionCMS</title>
		<link rel="shortcut icon" type="image/png" href="static/images/favicon.png">
		<link rel="stylesheet" type="text/css" href="static/css/main.css">
		<!--[if lt IE 9] >
			<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>

	<body>
		<section id="wrapper">
			<header>FusionCMS</header>

			<div class="info_bar">
				<?php if(isset($tool)) { ?>
					<a href="?">Updates</a> &rarr;
					<span class="tag"><?php echo $tool->version; ?></span>
					<?php echo $tool->name; ?>
				<?php } elseif(isset($_GET['version']) && isset($_GET['action']) && $_GET['action'] == "import") { ?>
					<a href="?">Updates</a> &rarr;
					<span class="tag"><?php echo $_GET['version'] ?></span>
					Import database changes
				<?php } else { ?>
					You are currently running version
					<span class="tag <?php echo (Update::compareVersions(Update::$currentVersion, Update::$latestVersion)) ? "good" : "" ?>">
						<?php echo Update::$currentVersion; ?>
					</span>
					and the latest version is
					<span class="tag good">
						<?php echo Update::$latestVersion; ?>
					</span>
				<?php } ?>
			</div>
			
			<section id="main">
				<?php if(isset($tool)) { ?>
					<?php echo $tool->run(); ?>
				<?php } elseif(isset($_GET['version']) && isset($_GET['action']) && $_GET['action'] == "import") { ?>
					All changes have been successfully imported
				<?php } else { ?>
					<?php if(!Update::compareVersions(key(Update::$updates), Update::$latestVersion)) { ?>
						<a href="http://fusion.raxezdev.com/update" class="button">Click here to download v<?php echo Update::$latestVersion; ?></a>
					<?php } ?>

					<?php if(!count(Update::$updates)) { ?>
						<div class="divider"></div>
						There are no update packages downloaded.
					<?php } else { ?>
						<?php foreach(Update::$updates as $version => $contents) { ?>
							<div class="divider"></div>
							<article class="update <?php echo (!Update::compareVersions(Update::$currentVersion, $version)) ? "" : "old" ?>">
								<h1><span class="tag <?php echo (!Update::compareVersions(Update::$currentVersion, $version)) ? "good" : "" ?>"><?php echo $version; ?></span></h1>
								<?php if($contents['changelog']) { ?>
									<h2>What's new?</h2>
									<div class="changelog">
										<?php echo $contents['changelog']; ?>
									</div>
								<?php } ?>

								<h2>Installation instructions</h2>

								<?php if($contents['instructions']) { ?>
									<div class="instructions">
										<b>Special instructions:</b> <?php echo $contents['instructions']; ?>
									</div>
								<?php } ?>

								<ol>
									<?php if(count($contents['sql'])) { ?>
										<li><a href="?version=<?php echo $version; ?>&action=import">Click here to import database changes</a></li>
									<?php } ?>

									<?php if(count($contents['zip'])) { ?>
										<?php foreach($contents['zip'] as $zip) { ?>
											<li>Extract <b><?php echo $zip; ?></b> into your web folder where your FusionCMS website is located (usually www or htdocs) and override the existing files.</li>
										<?php } ?>
									<?php } ?>

									<?php if(count($contents['tools'])) { ?>
										<?php foreach($contents['tools'] as $tool) { ?>
											<li><a href="?version=<?php echo $version; ?>&action=<?php echo $tool; ?>">Click here to use tool: <?php echo preg_replace("/_/", " ", $tool);?></a></li>
										<?php } ?>
									<?php } ?>
								</ol>
							</article>
						<?php } ?>
					<?php } ?>
				<?php } ?>
			</section>
		</section>
	</body>
</html>