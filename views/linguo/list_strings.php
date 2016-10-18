<div id="page-wrapper" >
    <div id="page-inner">
        <div class="row">
            <h3>
                <div class="col-md-3">
                    LANGUAGE FILE STRINGS
                </div>
                <div class="col-md-9 text-right">
                    <button class="btn btn-info" data-toggle="modal" data-target="#newStringModal">Add String</button>
                </div>
            </h3>
        </div>
        <!-- /. ROW  -->
        <hr />
        
        <div class="row">
            <div class="col-lg-12">
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" id="search_key" placeholder="Search key..." value=""/>
                </div>
                <hr />
            </div>

            <div class="col-lg-12">
                <ul class="nav" id="ul-strings_list">   
                <?php
                    foreach($strings AS $string_id => $string){
                    ?>
                    <li data-search-term="<?php echo $string['key'];?>">
                        <i class="fa fa-key"></i>
                        <?php echo $string['key'];?> 
                        <input type="text" class="form-control string_content" id="str-<?php echo($language_id);?>-<?php echo($file_id);?>-<?php echo($string['string_id']);?>" placeholder="Insert translation here." value="<?php echo $string['value'];?>"/>
                        <br />
                    </li>                    
                    <?php
                    }
                ?>                              
                </ul>
            </div>
        </div>
    </div>
</div>