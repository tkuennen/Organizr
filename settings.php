<?php 

date_default_timezone_set('America/Los_Angeles');

$data = false;

ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL | E_STRICT);

function registration_callback($username, $email, $userdir)
{
    global $data;
    $data = array($username, $email, $userdir);
}

require_once("user.php");
$USER = new User("registration_callback");

if(!$USER->authenticated) :

    die("Why you trying to access this without logging in?!?!");

endif;

$dbfile = constant('User::DATABASE_LOCATION')  . constant('User::DATABASE_NAME') . ".db";
$file_db = new PDO("sqlite:" . $dbfile);
$file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$dbTab = $file_db->query('SELECT name FROM sqlite_master WHERE type="table" AND name="tabs"');
$dbOptions = $file_db->query('SELECT name FROM sqlite_master WHERE type="table" AND name="options"');

$tabSetup = "Yes";
$hasOptions = "No";

foreach($dbTab as $row) :

    if (in_array("tabs", $row)) :
    
        $tabSetup = "No";
    
    endif;

endforeach;

foreach($dbOptions as $row) :

    if (in_array("options", $row)) :
    
        $hasOptions = "Yes";
    
    endif;

endforeach;

if($hasOptions == "No") :

    $title = "Organizr";
    $topbar = "#eb6363"; 
    $topbartext = "#FFFFFF";
    $bottombar = "#eb6363";
    $sidebar = "#000000";
    $hoverbg = "#eb6363";
    $activetabBG = "#eb6363";
    $activetabicon = "#FFFFFF";
    $activetabtext = "#FFFFFF";
    $inactiveicon = "#FFFFFF";
    $inactivetext = "#FFFFFF";

endif;


if($tabSetup == "No") :

    $result = $file_db->query('SELECT * FROM tabs');
    
endif;

if($hasOptions == "Yes") :

    $resulto = $file_db->query('SELECT * FROM options');
    
endif;


$action = "";
                
if(isset($_POST['action'])) :

    $action = $_POST['action'];
    
endif;    

if($action == "addTabz") :
    
    if($tabSetup == "No") :

        $file_db->exec("DELETE FROM tabs");
        
    endif;
    
    if($tabSetup == "Yes") :
    
        $file_db->exec("CREATE TABLE tabs (name TEXT UNIQUE, url TEXT, defaultz TEXT, active TEXT, user TEXT, guest TEXT, icon TEXT)");
        
    endif;

    $addTabName = array();
    $addTabUrl = array();
    $addTabIcon = array();
    $addTabDefault = array();
    $addTabActive = array();
    $addTabUser = array();
    $addTabGuest = array();
    $buildArray = array();

    foreach ($_POST as $key => $value) :
    
        $trueKey = explode('-', $key);
        
        if ($value == "on") :
        
            $value = "true";
            
        endif;
        
        if($trueKey[0] == "name"):
            
            array_push($addTabName, $value);
            
        endif;
        
        if($trueKey[0] == "url"):
            
            array_push($addTabUrl, $value);
            
        endif;
        
        if($trueKey[0] == "icon"):
            
            array_push($addTabIcon, $value);
            
        endif;
        
        if($trueKey[0] == "default"):
            
            array_push($addTabDefault, $value);
            
        endif;
        
        if($trueKey[0] == "active"):
            
            array_push($addTabActive, $value);
            
        endif;
        
        if($trueKey[0] == "user"):
            
            array_push($addTabUser, $value);
            
        endif;
        
        if($trueKey[0] == "guest"):
            
            array_push($addTabGuest, $value);
            
        endif;         
        
    endforeach;
    /*
    echo "NAME: "; print_r($addTabName);
    echo "<br/><br/>URL: "; print_r($addTabUrl);
    echo "<br/><br/>ICON: "; print_r($addTabIcon);
    echo "<br/><br/>DEFAULT: "; print_r($addTabDefault);
    echo "<br/><br/>ACTIVE: "; print_r($addTabActive);
    echo "<br/><br/>USER: "; print_r($addTabUser);
    echo "<br/><br/>GUEST: "; print_r($addTabGuest);
    */
    $tabArray = 0;
    
    if(count($addTabName) > 0) : 
        
        foreach(range(1,count($addTabName)) as $index) :
        
            if(!isset($addTabDefault[$tabArray])) :
                
                $tabDefault = "false";
            
            else :
                
                $tabDefault = $addTabDefault[$tabArray];
            
            endif;
            
            $buildArray[] = array('name' => $addTabName[$tabArray],
                  'url' => $addTabUrl[$tabArray],
                  'defaultz' => $tabDefault,
                  'active' => $addTabActive[$tabArray],
                  'user' => $addTabUser[$tabArray],
                  'guest' => $addTabGuest[$tabArray],
                  'icon' => $addTabIcon[$tabArray]);

            $tabArray++;
        
        endforeach;
        
    endif; 
    
    $insert = "INSERT INTO tabs (name, url, defaultz, active, user, guest, icon) 
                VALUES (:name, :url, :defaultz, :active, :user, :guest, :icon)";
                
    $stmt = $file_db->prepare($insert);
    
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':url', $url);
    $stmt->bindParam(':defaultz', $defaultz);
    $stmt->bindParam(':active', $active);
    $stmt->bindParam(':user', $user);
    $stmt->bindParam(':guest', $guest);
    $stmt->bindParam(':icon', $icon);
    
    foreach ($buildArray as $t) :
    
        $name = $t['name'];
        $url = $t['url'];
        $defaultz = $t['defaultz'];
        $active = $t['active'];
        $user = $t['user'];
        $guest = $t['guest'];
        $icon = $t['icon'];

        $stmt->execute();
        
    endforeach;
    
endif;

if($action == "addOptionz") :
    
    if($hasOptions == "Yes") :
    
        $file_db->exec("DELETE FROM options");
        
    endif;
    
    if($hasOptions == "No") :

        $file_db->exec("CREATE TABLE options (title TEXT UNIQUE, topbar TEXT, bottombar TEXT, sidebar TEXT, hoverbg TEXT, topbartext TEXT, activetabBG TEXT, activetabicon TEXT, activetabtext TEXT, inactiveicon TEXT, inactivetext TEXT)");
        
    endif;
            
    $title = $_POST['title'];
    $topbartext = $_POST['topbartext'];
    $topbar = $_POST['topbar'];
    $bottombar = $_POST['bottombar'];
    $sidebar = $_POST['sidebar'];
    $hoverbg = $_POST['hoverbg'];
    $activetabBG = $_POST['activetabBG'];
    $activetabicon = $_POST['activetabicon'];
    $activetabtext = $_POST['activetabtext'];
    $inactiveicon = $_POST['inactiveicon'];
    $inactivetext = $_POST['inactivetext'];

    $insert = "INSERT INTO options (title, topbartext, topbar, bottombar, sidebar, hoverbg, activetabBG, activetabicon, activetabtext, inactiveicon, inactivetext) 
                VALUES (:title, :topbartext, :topbar, :bottombar, :sidebar, :hoverbg, :activetabBG, :activetabicon , :activetabtext , :inactiveicon, :inactivetext)";
                
    $stmt = $file_db->prepare($insert);
    
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':topbartext', $topbartext);
    $stmt->bindParam(':topbar', $topbar);
    $stmt->bindParam(':bottombar', $bottombar);
    $stmt->bindParam(':sidebar', $sidebar);
    $stmt->bindParam(':hoverbg', $hoverbg);
    $stmt->bindParam(':activetabBG', $activetabBG);
    $stmt->bindParam(':activetabicon', $activetabicon);
    $stmt->bindParam(':activetabtext', $activetabtext);
    $stmt->bindParam(':inactiveicon', $inactiveicon);
    $stmt->bindParam(':inactivetext', $inactivetext);

    $stmt->execute();

    

    
endif;
?>

<!DOCTYPE html>

<html lang="en" class="no-js">

    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="msapplication-tap-highlight" content="no" />

        <title>Settings</title>

        <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="bower_components/mdi/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="bower_components/metisMenu/dist/metisMenu.min.css">
        <link rel="stylesheet" href="bower_components/Waves/dist/waves.min.css"> 
        <link rel="stylesheet" href="bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css"> 

        <link rel="stylesheet" href="js/selects/cs-select.css">
        <link rel="stylesheet" href="js/selects/cs-skin-elastic.css">
        <link href="bower_components/iconpick/dist/css/fontawesome-iconpicker.min.css" rel="stylesheet">
        
        <link rel="stylesheet" href="bower_components/sweetalert/dist/sweetalert.css">
        <link rel="stylesheet" href="bower_components/smoke/dist/css/smoke.min.css">

        <script src="js/menu/modernizr.custom.js"></script>
        <script type="text/javascript" src="js/sha1.js"></script>
		    <script type="text/javascript" src="js/user.js"></script>

        <link rel="stylesheet" href="css/style.css">

        <link rel="icon" href="img/favicon.ico" type="image/x-icon" />
        <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
        
        <!--[if lt IE 9]>
        <script src="bower_components/html5shiv/dist/html5shiv.min.js"></script>
        <script src="bower_components/respondJs/dest/respond.min.js"></script>
        <![endif]-->
        
    </head>

    <body style="padding: 0;">
        
        <style>
        
            input.form-control.material.icp-auto.iconpicker-element.iconpicker-input {
    display: none;
}
        
        </style>
       
        <div id="main-wrapper" class="main-wrapper">

            <!--Content-->
            <div id="content"  style="margin:0 20px; overflow:hidden">
 
                <br/>
                
                <div id="versionCheck"></div>       
            
                <div class="row">
                
                    <div class="col-lg-12">
                  
                        <div class="tabbable tabs-with-bg" id="eighth-tabs">
                    
                            <ul class="nav nav-tabs">
                      
                                <li class="active">
                        
                                    <a href="#tab-tabs" data-toggle="tab"><i class="fa fa-list gray"></i>Tabs</a>
                      
                                </li>
                      
                                <li>
                        
                                    <a href="#tab-83" data-toggle="tab"><i class="fa fa-paint-brush green"></i>Customize</a>
                      
                                </li>
                      
                                <li>
                        
                                    <a href="#about" data-toggle="tab"><i class="fa fa-gear blue"></i>Info</a>
                     
                                </li>
                    
                            </ul>
                    
                            <div class="tab-content">
                      
                                <div class="content-box box-shadow big-box todo-list tab-pane big-box  fade in active" id="tab-tabs">

                                    <div class="sort-todo">

                                        <a class="total-tabs" href="#">Total Tabs <span class="badge green-bg"></span></a>
                                        
                                        <?php if($action) : ?>
                                        
                                        <button id="apply" class="btn btn-success waves text-uppercase pull-right waves-effect waves-float" type="submit">Apply Changes</button>
                                        
                                        <?php endif; ?>

                                    </div>

                                    <form id="add_tab" method="post">

                                        <div class="form-group add-tab">

                                            <div class="input-group">

                                                <div class="input-group-addon">

                                                    <i class="fa fa-pencil green"></i>

                                                </div>

                                                <input type="text" class="form-control name-of-todo" placeholder="Type In New Tab Name And Hit Enter">

                                            </div>

                                        </div>

                                    </form>

                                    <div class="panel">

                                        <form id="submitTabs" method="post">
                                        
                                            <div class="panel-body todo">

                                                <input type="hidden" name="action" value="addTabz" />

                                                <ul class="list-group ui-sortable">

                                                    <?php if($tabSetup == "No") : $tabNum = 1; 

                                                    foreach($result as $row) : 

                                                    if($row['defaultz'] == "true") : $default = "checked"; else : $default = ""; endif;
                                                    if($row['active'] == "true") : $activez = "checked"; else : $activez = ""; endif;
                                                    if($row['guest'] == "true") : $guestz = "checked"; else : $guestz = ""; endif;
                                                    if($row['user'] == "true") : $userz = "checked"; else : $userz = ""; endif;

                                                    ?>
                                                    <li id="item-<?=$tabNum;?>" class="list-group-item" style="position: relative; left: 0px; top: 0px;">

                                                        <tab class="content-form form-inline">

                                                            <div class="form-group">

                                                                <div class="action-btns" style="width:calc(100%)">

                                                                    <a class="" style="margin-left: 0px"><span class="fa fa-hand-paper-o"></span></a>

                                                                </div>

                                                            </div>

                                                            <div class="form-group">

                                                                <input type="text" class="form-control material" id="name-<?=$tabNum;?>" name="name-<?=$tabNum;?>" placeholder="New Tab Name" value="<?=$row['name'];?>">

                                                            </div>

                                                            <div class="form-group">

                                                                <input type="text" class="form-control material" id="url-<?=$tabNum;?>" name="url-<?=$tabNum;?>" placeholder="Tab URL" value="<?=$row['url']?>">

                                                            </div>

                                                            <div class="form-group">

                                                                <div class="input-group">
                                                                    <input data-placement="bottomRight" class="form-control material icp-auto" name="icon-<?=$tabNum;?>" value="<?=$row['icon'];?>" type="text" />
                                                                    <span class="input-group-addon"></span>
                                                                </div>

                                                            </div>

                                                            <div class="form-group">

                                                                <div class="radio radio-danger">


                                                                    <input type="radio" id="default[<?=$tabNum;?>]" value="true" name="default" <?=$default;?>>
                                                                    <label for="default[<?=$tabNum;?>]">Default</label>

                                                                </div>

                                                            </div>

                                                            <div class="form-group">

                                                                <div class="">

                                                                    <input id="" class="switcher switcher-success" value="false" name="active-<?=$tabNum;?>" type="hidden">
                                                                    <input id="active[<?=$tabNum;?>]" class="switcher switcher-success" name="active-<?=$tabNum;?>" type="checkbox" <?=$activez;?>>

                                                                    <label for="active[<?=$tabNum;?>]"></label>

                                                                </div>
                                                                Active
                                                            </div>

                                                            <div class="form-group">

                                                                <div class="">

                                                                    <input id="" class="switcher switcher-primary" value="false" name="user-<?=$tabNum;?>" type="hidden">
                                                                    <input id="user[<?=$tabNum;?>]" class="switcher switcher-primary" name="user-<?=$tabNum;?>" type="checkbox" <?=$userz;?>>
                                                                    <label for="user[<?=$tabNum;?>]"></label>

                                                                </div>
                                                                User
                                                            </div>

                                                            <div class="form-group">

                                                                <div class="">

                                                                    <input id="" class="switcher switcher-primary" value="false" name="guest-<?=$tabNum;?>" type="hidden">
                                                                    <input id="guest[<?=$tabNum;?>]" class="switcher switcher-warning" name="guest-<?=$tabNum;?>" type="checkbox" <?=$guestz;?>>
                                                                    <label for="guest[<?=$tabNum;?>]"></label>

                                                                </div>
                                                                Guest
                                                            </div>

                                                            <div class="pull-right action-btns" style="padding-top: 8px;">

                                                                <a class="trash"><span class="fa fa-close"></span></a>

                                                            </div>


                                                        </tab>

                                                    </li>
                                                    <?php $tabNum ++; endforeach; endif;?>

                                                </ul>

                                            </div>

                                            <div class="checkbox clear-todo pull-left"></div>

                                            <input class="btn btn-warning waves text-uppercase pull-right waves-effect waves-float" type="submit" value="Save Tabs">
                                            
                                        </form>
                                        
                                    </div>
 
                                </div>

                                <div class="tab-pane big-box  fade in" id="about">
                        
                                    <h4><strong>About Organizr</strong></h4>
                        
                                    <p id="version"></p>
                                    
                                    <p id="whatsnew"></p>
                                    
                                    <p id="downloadnow"></p>
                      
                                </div>
                                
                                <div class="tab-pane big-box  fade in" id="tab-83">
                                    
                                    <?php if($hasOptions == "Yes") : 
                                    
                                        foreach($resulto as $row) : 

                                            $title = $row['title'];
                                            $topbartext = $row['topbartext'];
                                            $topbar = $row['topbar'];
                                            $bottombar = $row['bottombar'];
                                            $sidebar = $row['sidebar'];
                                            $hoverbg = $row['hoverbg'];
                                            $activetabBG = $row['activetabBG'];
                                            $activetabicon = $row['activetabicon'];
                                            $activetabtext = $row['activetabtext'];
                                            $inactiveicon = $row['inactiveicon'];
                                            $inactivetext = $row['inactivetext'];

                                        endforeach;
                                    
                                    endif;
                                    
                                    ?>
                        
                                    <form id="add_optionz" method="post">
                                        
                                        <input type="hidden" name="action" value="addOptionz" />
                                        
                                        <h4><strong>Let's Bidazzle</strong></h4>

                                        <p>Change the colors of the following elements:</p>

                                        <div class="content-box box-shadow big-box grids">

                                            <div class="row show-grids">

                                                <h4><strong>Title</strong></h4>

                                                <div class="col-md-2">

                                                    <center>Title</center>

                                                    <input name="title" class="form-control" value="<?=$title;?>" placeholder="Organizr">

                                                </div>

                                                <div class="col-md-2">

                                                    <center>Title Text</center>

                                                    <input name="topbartext" class="form-control jscolor {hash:true}" value="<?=$topbartext;?>">

                                                </div>

                                            </div>

                                            <div class="row show-grids">

                                                <h4><strong>Navigation Bars</strong></h4>

                                                <div class="col-md-2">

                                                    <center>Top Bar</center>

                                                    <input name="topbar" class="form-control jscolor {hash:true}" value="<?=$topbar;?>">

                                                </div>

                                                <div class="col-md-2">

                                                    <center>Bottom Bar</center>

                                                    <input name="bottombar" class="form-control jscolor {hash:true}" value="<?=$bottombar;?>">

                                                </div>

                                                <div class="clearfix visible-xs-block"></div>

                                                <div class="col-md-2">

                                                    <center>Side Bar</center>

                                                    <input name="sidebar" class="form-control jscolor {hash:true}" value="<?=$sidebar;?>">

                                                </div>

                                                <div class="col-md-2">

                                                    <center>Hover BG</center>

                                                    <input name="hoverbg" class="form-control jscolor {hash:true}" value="<?=$hoverbg;?>">

                                                </div>

                                            </div>

                                            <div class="row show-grids">

                                                <h4><strong>Active Tab</strong></h4>

                                                <div class="col-md-2">

                                                    <center>Active Tab BG</center>

                                                    <input name="activetabBG" class="form-control jscolor {hash:true}" value=<?=$activetabBG;?>"">

                                                </div>

                                                <div class="col-md-2">

                                                    <center>Active Tab Icon</center>

                                                    <input name="activetabicon" class="form-control jscolor {hash:true}" value="<?=$activetabicon;?>">

                                                </div>

                                                <div class="col-md-2">

                                                    <center>Active Tab Text</center>

                                                    <input name="activetabtext" class="form-control jscolor {hash:true}" value="<?=$activetabtext;?>">

                                                </div>

                                            </div>

                                            <div class="row show-grids">

                                                <h4><strong>Inactive Tab</strong></h4>

                                                <div class="col-md-2">

                                                    <center>Inactive Icon</center>

                                                    <input name="inactiveicon" class="form-control jscolor {hash:true}" value="<?=$inactiveicon;?>">

                                                </div>

                                                <div class="col-md-2">

                                                    <center>Inactive Text</center>

                                                    <input name="inactivetext" class="form-control jscolor {hash:true}" value="<?=$inactivetext;?>">

                                                </div>

                                            </div>
                                            
                                            <div class="checkbox clear-todo pull-left"></div>

                                        <input class="btn btn-warning waves text-uppercase pull-right waves-effect waves-float" type="submit" value="Save Options">

                                        </div>
                                        
                                    </form>
                      
                                </div>
                                
                            </div>
                              
                        </div>
                            
                    </div>
                          
                </div>
            
            </div>
            <!--End Content-->

            <!--Welcome notification-->
            <div id="welcome"></div>

        </div>
        <?php if(!$USER->authenticated) : ?>

        <?php endif;?>
        <?php if($USER->authenticated) : ?>

        <?php endif;?>

        <!--Scripts-->
        <script src="bower_components/jquery/dist/jquery.min.js"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="bower_components/metisMenu/dist/metisMenu.min.js"></script>
        <script src="bower_components/Waves/dist/waves.min.js"></script>
        <script src="bower_components/moment/min/moment.min.js"></script>
        <script src="bower_components/jquery.nicescroll/jquery.nicescroll.min.js"></script>
        <script src="bower_components/slimScroll/jquery.slimscroll.min.js"></script>
        <script src="bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js"></script>
        <script src="bower_components/cta/dist/cta.min.js"></script>

        <!--Menu-->
        <script src="js/menu/classie.js"></script>
        <script src="bower_components/iconpick/dist/js/fontawesome-iconpicker.js"></script>


        <!--Selects-->
        <script src="js/selects/selectFx.js"></script>
        <script src="js/jscolor.js"></script>
        
        <script src="bower_components/sweetalert/dist/sweetalert.min.js"></script>

        <script src="bower_components/smoke/dist/js/smoke.min.js"></script>

        <!--Notification-->
        <script src="js/notifications/notificationFx.js"></script>

        <script src="js/jqueri_ui_custom/jquery-ui.min.js"></script>

        <?php if($action == "addTabz") : ?>
        <script>

            swal("Tabs Saved!", "Apply Changes To Reload The Page!", "success");
            
        </script>
        <?php endif; ?>

        <script>

            $(function () {

                $(".todo ul").sortable();

                $("#add_tab").on('submit', function (e) {
                    e.preventDefault();

                    var $toDo = $(this).find('.name-of-todo');
                    toDo_name = $toDo.val();

                    if (toDo_name.length >= 3) {

                        var newid = $('.list-group-item').length + 1;

                        $(".todo ul").append(
                        '<li id="item-' + newid + '" class="list-group-item" style="position: relative; left: 0px; top: 0px;"><tab class="content-form form-inline"> <div class="form-group"><div class="action-btns" style="width:calc(100%)"><a class="" style="margin-left: 0px"><span class="fa fa-hand-paper-o"></span></a></div></div> <div class="form-group"><input type="text" class="form-control material" name="name-' + newid + '" id="name[' + newid + ']" placeholder="New Tab Name" value="' + toDo_name + '"></div> <div class="form-group"><input type="text" class="form-control material" name="url-' + newid + '" id="url[' + newid + ']" placeholder="Tab URL"></div> <div class="form-group"><div class="input-group"><input name="icon-' + newid + '" data-placement="bottomRight" class="form-control material icp-auto" value="fa-diamond" type="text" /><span class="input-group-addon"></span></div></div>  <div class="form-group"> <div class="radio radio-danger"> <input type="radio" name="default" id="default[' + newid + ']" name="default"> <label for="default[' + newid + ']">Default</label></div></div> <div class="form-group"><div class=""><input id="" class="switcher switcher-success" value="false" name="active-' + newid + '" type="hidden"><input name="active-' + newid + '" id="active[' + newid + ']" class="switcher switcher-success" type="checkbox" checked=""><label for="active[' + newid + ']"></label></div> Active</div> <div class="form-group"><div class=""><input id="" class="switcher switcher-primary" value="false" name="user-' + newid + '" type="hidden"><input id="user[' + newid + ']" name="user-' + newid + '" class="switcher switcher-primary" type="checkbox" checked=""><label for="user[' + newid + ']"></label></div> User</div><div class="form-group"><div class=""><input id="" class="switcher switcher-primary" value="false" name="guest-' + newid + '" type="hidden"><input name="guest-' + newid + '" id="guest[' + newid + ']" class="switcher switcher-warning" type="checkbox" checked=""><label for="guest[' + newid + ']"></label></div> Guest</div><div class="pull-right action-btns" style="padding-top: 8px;"><a class="trash"><span class="fa fa-close"></span></a></div></tab></li>'
                        );

                        $('.icp-auto').iconpicker({placement: 'left', hideOnSelect: false, collision: true});

                        var eventObject = {

                            title: $.trim($("#" + newid).text()),
                            className: $("#" + newid).attr("data-bg"),
                            stick: true

                        };

                        $("#" + newid).data('eventObject', eventObject);

                        $toDo.val('').focus();

                    } else {

                        $toDo.focus();
                    }

                });

                count();

                $(".list-group-item").addClass("list-item");

                //Remove one completed item
                $(document).on('click', '.trash', function (e) {

                    var clearedCompItem = $(this).closest(".list-group-item").remove();
                    e.preventDefault();
                    count();

                });

                //Count items
                function count() {

                    var active = $('.list-group-item').length;

                    $('.total-tabs span').text(active);

                };

                $("#submitTabs").on('submit', function (e) {

                    console.log("submitted");

                    $("div.radio").each(function(i) {

                        $(this).find('input').attr('name', 'default-' + i);

                        console.log(i);

                    });

                    $('form input[type="radio"]').not(':checked').each(function() {

                        $(this).prop('checked', true);
                        $(this).prop('value', "false");
                        console.log("found unchecked");

                    });

                });

                $('#apply').on('click touchstart', function(){

                window.parent.location.reload();

                });

            });

        </script>

        <script>
            
            $('.icp-auto').iconpicker({placement: 'left', hideOnSelect: false, collision: true});
            
            $( "span[class^='fa fa-hand-paper-o']" )
                .mouseup(function() {
                 $(this).attr("class", "fa fa-hand-paper-o");
                })
                .mousedown(function() {
                    $(this).attr("class", "fa fa-hand-grab-o");
                });
         
        </script>
        
        <script>
        
        $( document ).ready(function() {
        		
        	$.ajax({
        				
        		type: "GET",
                url: "https://api.github.com/repos/causefx/Organizr/releases/latest",
                dataType: "json",
                success: function(github) {
                   
                    var currentVersion = "0.931";
                    var githubVersion = github.tag_name;
                    var githubDescription = github.body;
                    var githubName = github.name;
                    infoTabVersion = $('#about').find('#version');
                    infoTabNew = $('#about').find('#whatsnew');
                    infoTabDownload = $('#about').find('#downloadnow');
        
        			if(currentVersion < githubVersion){
                    
                    	console.log("You Need To Upgrade");

                        $.smkAlert({
                            text: '<strong>New Version Available</strong> Click Info Tab',
                            type: 'warning',
                            permanent: true
                        });
                        
                        $(infoTabNew).html("<br/><h4><strong>What's New in " + githubVersion + "</strong></h4><strong>Title: </strong>" + githubName + " <br/><strong>Changes: </strong>" + githubDescription);
                        
                        $(infoTabDownload).html("<br/><h4><strong><a href='https://github.com/causefx/Organizr'>Visit Github</a></strong></h4><strong>Download Now: </strong> <br/><strong><a href='https://github.com/causefx/Organizr/archive/master.zip'>Organizr v." + githubVersion + "</a></strong>");
                    
                    }else if(currentVersion === githubVersion){
                    
                    	console.log("You Are on Current Version");
                        
                        $.smkAlert({
                            text: 'Software is <strong>Up-To-Date!</strong>',
                            type: 'success'
                        });
                    
                    }else{
                    
                    	console.log("something went wrong");

                        $.smkAlert({
                            text: '<strong>WTF!? </strong>Can\'t check version.',
                            type: 'danger',
                            time: 10
                        });
                    
                    }

                    $(infoTabVersion).html("<strong>Installed Version: </strong>" + currentVersion + " <strong>Current Version: </strong>" + githubVersion);
                    
                }
                
            });
            
        });
        
        </script>

    </body>

</html>