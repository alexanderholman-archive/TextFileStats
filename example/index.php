<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Text File Statistics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/fontawesome/4.2.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/formValidation.min.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row bg-primary">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
            <h1>Text File Statistics</h1>
        </div>
    </div>
    <div id="results" class="row collapse" aria-expanded="false">
        <div class="col-xs-12 col-md-8 col-md-offset-2">
            <h2>Results</h2>
            <hr>
            <div id="hasResults" class="table-responsive">
            </div>
            <p id="noResults" class="helper bg-info hide">No currently no statistics available to display.</p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
            <form id="fileForm" role="form" action="./action.php" enctype="multipart/form-data" method="post">
                <fieldset>
                    <div class="form-group text-left has-feedback">
                        <label class="control-label hide" for="fileUpload">File</label>
                        <div id="drop-area-div" class="">
                            <input class="form-control input-lg" type="file" name="fileUpload[]" id="fileUpload" required />
                        </div>
                        <div id="uploadProgress" class="progress collapse">
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                                0%
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <input id="submitInput" class="btn btn-primary btn-lg" type="submit" name="submitInput" value="Submit" />
                        <input id="resetInput" class="btn btn-default btn-lg" type="submit" name="resetInput" value="Reset" formnovalidate />
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
<template id="resultsTable">
    <table class="table table-striped table-bordered table-hover">
        <thead>
        <tr class="bg-primary">
            <th class="text-center" colspan="4"><span id="fileName"></span></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Word count</th>
            <td colspan="3"><span id="WordCount"></span></td>
        </tr>
        <tr>
            <th>Line count</th>
            <td colspan="3"><span id="LineCount"></span></td>
        </tr>
        <tr>
            <th>Letters per word</th>
            <td><span id="Mean"></span> <small><i>Mean</i></small></td>
            <td><span id="Mode"></span> <small><i>Mode</i></small></td>
            <td><span id="Median"></span> <small><i>Median</i></small></td>
        </tr>
        <tr>
            <th>Most common letter</th>
            <td colspan="3"><span id="CommonLetter"></span></td>
        </tr>
        </tbody>
    </table>
</template>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.js"><\/script>')</script>
<script src="js/vendor/bootstrap.min.js"></script>
<script src="js/vendor/formvalidator/formValidation.min.js"></script>
<script src="js/vendor/formvalidator/bootstrap.min.js"></script>
<script src="js/vendor/dmuploader.min.js"></script>
<script src="js/main.js"></script>
<script src="../../src/js/text-file-stats.js"></script>
</body>
</html>