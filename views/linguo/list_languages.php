<div id="page-wrapper" >
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h2>LANGUAGES</h2>
            </div>
        </div>
        <!-- /. ROW  -->
        <hr />
        
        <div class="row">
            <div class="col-lg-12">
                <?php
                    //CHECK WRITING PERMISSIONS.
                    if(!$can_write){
                    ?>
                    <div class="alert alert-danger" role="alert">
                        <strong>Cannot Write Files</strong>
                        <br />
                        Please check <i>language</i> folder and give writing permissions to webserver user, otherwise Linguo cannot write or update language files.
                    </div>
                    <?php
                    }
                ?>
                <!-- DISCLAIMER -->
                <div class="alert alert-warning" role="alert">
                    <strong>Readme</strong>
                    <br />
                    Hi, first of all, thanks for downloading and install LINGUO. I hope you find it as useful like it was to me.
                    <br />
                    Before you start using it, a few advices:
                    <br /><br />
                    <ol>
                        <li>Please, <strong>backup your language files</strong>!!! I've tested LINGUO on my own apps, and it works fine, but make a backup of your language files just in case something go wrong.</li>
                        <li>If you have a master|default language, set it on your first access, it will allow you to clone/syncronize other language files/strings ad save you a lot of time.</li>
                        <li>If you like LINGUO or you have some useful idea to improve it, just let me know ;) </li>
                    </ol>

                    Thanks !!!
                </div>
            </div>
        </div>
    </div>
</div>