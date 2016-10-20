  	<div class="modal fade" id="newLanguageModal" tabindex="-1" role="dialog" aria-labelledby="newLanguageModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="newLanguageModalLabel">New Language</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="string-key" class="control-label">Name:</label>
                            <input type="text" class="form-control" id="language-name">
                        </div>
                        <?php
                            if($master_language_id !== false){
                            ?>
                            <div class="form-group">
                                <label for="string-key" class="control-label">Clone from master language:</label>
                                <br />
                                <input type="radio" class="form-check-input" value="1" name="language-clone_from_master"> Si
                                &nbsp;&nbsp;
                                <input type="radio" class="form-check-input" value="0" name="language-clone_from_master" checked=""> No
                            </div>
                            <?php
                            }
                        ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" id="btn-new_language" data-dismiss="modal" class="btn btn-primary">Create Language</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="newFileModal" tabindex="-1" role="dialog" aria-labelledby="newFileModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="newFileModalLabel">New Language File</h4>
                </div>
                <div class="modal-body">
                	<div class="col-md-12">
                		<p>
                			If you want to store the file inside a folder, please, type the folder name before the filename:
                			<br/>
                			<code>
                				<small>my_folder/my_filename_lang.php</small>
                			</code>
                		</p>
                	</div>
                    <form>
                        <div class="form-group">
                            <label for="string-key" class="control-label">Filename (please, include .php extension):</label>
                            <input type="text" class="form-control" id="file-name">
                        </div>
                        <?php
                            if($master_language_id !== false){
                            ?>
                            <div class="form-group">
                                <label for="string-key" class="control-label">Clone from master language:</label>
                                <br />
                                <input type="radio" class="form-check-input" value="1" name="file-clone_from_master"> Si
                                &nbsp;&nbsp;
                                <input type="radio" class="form-check-input" value="0" name="file-clone_from_master" checked=""> No
                            </div>
                            <?php
                            }
                        ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" id="btn-new_file" data-dismiss="modal" class="btn btn-primary">Create File</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="newStringModal" tabindex="-1" role="dialog" aria-labelledby="newStringModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="newStringModalLabel">New String</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="string-key" class="control-label">Key:</label>
                            <input type="text" class="form-control" id="string-key">
                        </div>
                        <div class="form-group">
                            <label for="string-value" class="control-label">Value:</label>
                            <textarea class="form-control" id="string-value"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" id="btn-new_string" data-dismiss="modal" class="btn btn-primary">Create String</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delStringModal" tabindex="-1" role="dialog" aria-labelledby="delStringModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="delStringModalLabel">Delete String</h4>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete the selected string? This cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" id="btn-del_string" data-dismiss="modal" class="btn btn-danger">Delete String</button>
                </div>
                <input type="hidden" id="ds-language_id" value=""/>
                <input type="hidden" id="ds-file_id" value=""/>
                <input type="hidden" id="ds-string_id" value=""/>
            </div>
        </div>
    </div>
    <div class="modal fade" id="delFileModal" tabindex="-1" role="dialog" aria-labelledby="delFileModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="delFileModalLabel">Delete File</h4>
                </div>
                <div class="modal-body">
                    <p>Do you really want to delete the selected file (all language strings inside the file will be deleted too)? This cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" id="btn-del_file" data-dismiss="modal" class="btn btn-danger">Delete File</button>
                </div>
                <input type="hidden" id="df-language_id" value=""/>
                <input type="hidden" id="df-file_id" value=""/>
            </div>
        </div>
    </div>
  </div>
  <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
  <!-- JQUERY SCRIPTS -->
  <input type="hidden" id="language_id" value="<?php echo $language_id;?>">
  <input type="hidden" id="file_id" value="<?php echo $file_id;?>">
  <input type="hidden" id="linguo_url" value="<?php echo $linguo_url;?>">
  <script type="text/javascript">
    <?php echo $js_data;?>
  </script>
  </body>
</html>