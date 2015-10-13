<html>
<head>
    <link href='https://fonts.googleapis.com/css?family=PT+Sans&subset=latin,cyrillic,cyrillic-ext' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="/public/css/style.css">
    <title><?php echo controllerManager::$title;?></title><?php echo controllerManager::getResources();?>
    <meta charset="utf-8">
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Online store of goods</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <?php if (controllerManager::$isAuthorized) : ?>
                <button onclick="document.location.assign('/profile'); return false;" type="button" class="ua3 userActionButton btn btn-success">My profile</button>
                <button onclick="document.location.assign('/sign_out'); return false;" type="button" class="ua1 userActionButton btn btn-success">Sign out</button>
            <?php else : ?>
                <button onclick="document.location.assign('/sign_in'); return false;" type="button" class="ua1 userActionButton btn btn-success">Sign in</button>
                <button onclick="document.location.assign('/registration'); return false;" type="button" class="ua2 userActionButton btn btn-success">Create account</button>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php renderPartial('renderGeneralMenu');?>
<div class="container">
    <?php renderPartial('quickExplore');?>
    <?php eval("?>{$content}<?");?>
</div>
</body>
</html>