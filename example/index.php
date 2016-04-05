<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Text File Stats > Example</title>
    <meta name="description" content="An example of the use of the PHP class TextFileStats, used to extract word and letter statistics from a text file." />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/fontawesome/4.2.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/formValidation.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="icon" href="./favicon.ico" />
    <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
</head>
<body>
<div class="container-fluid text-center">
    <div class="row bg-primary">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
            <h1 id="logo"><img src="./img/logo.png" alt="Text File Stats" /></h1>
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
    <div class="row text-left">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
            <h3>Please note</h3>
            <p>I have set up specific criteria for this example, they are:</p>
            <ul>
                <li>Files must be <b>under 2MB</b></li>
                <li>File extensions must end in <b>.txt</b></li>
                <li>The content type must be <b>text/plain</b></li>
                <li>The language supported in <b>English</b>, as the only characters I'm looking for letter wise are <b>a to z</b></li>
                <li>Numbers will be counted during word count but discounted during mean, mode and median tests</li>
            </ul>
            <p>Need more support? Well, I'd be pretty rubbish if I couldn't add more! Get in touch and let me know. (I assume, if you're reading this, you have my information)</p>
        </div>
    </div>
</div>
<a href="https://github.com/alexanderholman/text-file-stats" rel="external" target="_blank"><img style="position: absolute; top: 0; left: 0; border: 0;" src="https://camo.githubusercontent.com/82b228a3648bf44fc1163ef44c62fcc60081495e/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f6c6566745f7265645f6161303030302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_left_red_aa0000.png"></a>
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
</body>
</html>