<div id="page-wrapper" >
    <div id="page-inner">

        <div class="row">
            <h3>
                <div class="col-md-3">
                    LANGUAGE FILES
                </div>
                <div class="col-md-9 text-right">
                    <button class="btn btn-info" data-toggle="modal" data-target="#newFileModal">Add File</button>
                    <?php
                        if($language['is_master']=='0'){
                        ?>
                        <button class="btn btn-warning">Set Master Language</button>
                        <?php
                        }
                    ?>
                </div>
            </h3>
        </div>
        <!-- /. ROW  -->
        <hr />
        
        <div class="row">
            <div class="col-lg-12">
                <ul class="nav" id="main-menu">   
                <?php
                    foreach($files AS $file_id => $file){
                    ?>
                    <li>
                        <i class="fa fa-folder"></i>
                        <?php echo $file['folder'];?> 

                        <a href="<?php echo $linguo_url;?>/<?php echo $language_id;?>/<?php echo $file_id;?>">
                            <?php echo $file['name'];?>     
                        </a>
                    </li>
                    <?php
                    }
                ?>                              
                </ul>
            </div>
        </div>
    </div>
</div>