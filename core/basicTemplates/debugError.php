<html>
<head>
    <title>Error during debug</title>
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:300' rel='stylesheet' type='text/css'>
    <style>
        body {
            background-color: #ffffff;
            color: #333232;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 16px;
            line-height: 20px;
            padding-top: 90px;
        }
        table, tr, td{
            font-size: 16px;
        }
        #container {
            width: 850px;
            margin: 0 auto;
            border: 1px #00bfbf solid;
            background-color: #edffff;
            padding: 15px;
        }
        .number {
            padding-right: 8px;
        }
        .secondRow {
            padding-bottom: 10px;
        }
        #head{
            font-size: 20px;
            margin-bottom: 27px;
            line-height: 24px;
        }
    </style>
</head>
<body>
    <div id="container">
        <?php echo $bResult;?>
    </div>
</body>
</html>