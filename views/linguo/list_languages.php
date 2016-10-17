<div id="wrapper">
    <div class="navbar navbar-inverse navbar-fixed-top">

    </div>
    <!-- /. NAV TOP  -->
    <nav class="navbar-default navbar-side" role="navigation">
        <br />
        <div class="sidebar-collapse">
            <h3>Languages List</h3>
            <ul class="nav" id="main-menu">   
            <?php
                foreach($languages AS $language_id => $language){
                ?>
                <li <?php if($current_language==$language_id){echo("class='active-link'");}?>>
                    <a href="<?php echo $linguo_url;?>/<?php echo $language_id;?>">
                        <i class="fa fa-globe"></i>
                        <?php echo $language['slug'];?> 
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
        </div>
    </nav>

    <!-- /. NAV SIDE  -->
    <div id="page-wrapper" >
        <div id="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <h2>BASIC UI ELEMENTS</h2>
                </div>
            </div>
            <!-- /. ROW  -->
            <hr />
            
            <div class="row">
                <div class="col-lg-12">
                    <h5>Input Examples Set</h5>
                    <div class="input-group">
                        <span class="input-group-addon">@</span>
                        <input type="text" class="form-control" placeholder="Username" />
                    </div>
                    <br />
                    <div class="input-group">
                        <input type="text" class="form-control" />
                        <span class="input-group-addon">.00</span>
                    </div>
                    <br />
                    <div class="input-group">
                        <span class="input-group-addon">$</span>
                        <input type="text" class="form-control" />
                        <span class="input-group-addon">.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /. PAGE WRAPPER  -->
</div>