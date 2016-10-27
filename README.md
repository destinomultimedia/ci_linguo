# CI_LINGUO

LINGUO is a language frontend editor for CodeIgniter. With LINGUO you can:

  - Add/Edit/Delete language files.
  - Create new languages cloning existing one.
  - Syncronize language strings from one language to another.

### Installation:

Just clone/download this repo. The repo has 2 folders, 

* _libraries_. has one fille called linguo.php. Is a CodeIgniter library.
* _views_. It contains one folder called LINGUO where the UI files are stored.

### How to use:

You can call LINGUO from any controller inside your app, just create a method like this one:

```sh
public function linguo($language='', $file='', $action=''){
    //Load library
    $this->load->library('linguo');
    $this->linguo->render($language, $file, $action);
    return;
}
```

and open the web browser and point it to your url. Done !

### Screenshots

You can check some screenshots here:

![linguo_1](https://cloud.githubusercontent.com/assets/1169328/19765958/83fee0fe-9c4b-11e6-889e-3c5139cb88b8.JPG)
_Main View_

![linguo_2](https://cloud.githubusercontent.com/assets/1169328/19765960/84003670-9c4b-11e6-972a-2f8170545008.JPG)
_Master language files view_

![linguo_3](https://cloud.githubusercontent.com/assets/1169328/19765961/8402ace8-9c4b-11e6-86c9-2d5dbe63ab62.JPG)
_New language modal_

![linguo_4](https://cloud.githubusercontent.com/assets/1169328/19765956/83fcebaa-9c4b-11e6-8f0d-f00d24e97ee6.JPG)
_Secondary language files view_

![linguo_5](https://cloud.githubusercontent.com/assets/1169328/19765957/83feb872-9c4b-11e6-884a-42b945db1972.JPG)
_Language file strings view_

![linguo_6](https://cloud.githubusercontent.com/assets/1169328/19765959/840004e8-9c4b-11e6-915a-5fd52c1f78c6.JPG)
_New string modal_
