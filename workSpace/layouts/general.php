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
            <a class="navbar-brand" href="#">Online store of goods</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
                <button onclick="document.location.assign('/login'); return false;" type="button" class="ua1 userActionButton btn btn-success">Sign in</button>
                <button onclick="document.location.assign('/registration'); return false;" type="button" class="ua2 userActionButton btn btn-success">Create account</button>
        </div><!--/.navbar-collapse -->
    </div>
</nav>
<!-- menu -->
<div class="col-sm-3 col-md-2 sidebar h100P">
    <ul class="nav nav-sidebar">
        <?php renderPartial('renderGeneralMenu');?>
    </ul>
</div>
<div class="container">
    <?php renderPartial('quickExplore');?>
    <?php eval("?>{$content}<?");?>
</div>
</body>
</html>