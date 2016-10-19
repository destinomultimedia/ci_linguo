<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Linguo - Simple CodeIgniter Language Editor</title>
    <style>
        <?php echo $css_data;?>
    </style>
   <link href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css' rel='stylesheet' type='text/css' /> 
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
	<div id="wrapper">
    <div class="navbar navbar-inverse navbar-fixed-top text-center">
    	<h2 style="color: White;">LINGUO</h2>
    </div>
    <!-- /. NAV TOP  -->
    <nav class="navbar-default navbar-side" role="navigation">
        <br />
        <div class="sidebar-collapse">
            <h3>LANGUAGES</h3>
            <ul class="nav" id="main-menu">   
            <?php
                foreach($languages AS $language_id => $language){
                ?>
                <li <?php if($current_language==$language_id){echo("class='active-link'");}?>>
                    <a href="<?php echo $linguo_url;?>/<?php echo $language_id;?>">
                        <i class="fa fa-globe"></i>
                        <?php echo $language['name'];?> 
                        <?php
                            if($language['is_master']=='1'){
                            ?>
                                <span class="badge">Master</span>
                            <?php
                            }
                        ?>                        
                    </a>
                </li>
                <?php
                }
            ?>                              
            </ul>
            <hr/>
            <?php
                if($can_write){
                ?>
                <div class="col-md-12 text-center">
                    <button class="btn btn-info" data-toggle="modal" data-target="#newLanguageModal">Add Language</button>
                </div>
                <?php
                }
            ?>
        </div>
    </nav>