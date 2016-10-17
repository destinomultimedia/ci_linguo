<?php
    //Prepare views
    $arr_views = array();
    //Header
    $arr_views[] = $views_folder.'header';
    //Content
    $arr_views[] = $views_folder.$view_content;
    //Footer
    $arr_views[] = $views_folder.'footer';

    foreach($arr_views AS $view_item){
        if (file_exists(APPPATH."views/".$view_item.EXT)){
            $this->load->view($view_item);
        }
    }
?>