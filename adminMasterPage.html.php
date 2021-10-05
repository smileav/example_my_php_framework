<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="ru-RU">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="description" content="<?php echo Page::$description; ?>" />
        <meta name="keywords" content="<?php Page::$keyWords; ?>" />
        <title><?php echo Page::$title; ?></title>

        <?php echo Page::getCssBlock(); ?>
        <!--[if lt IE # ]>  <![endif]-->
        <link href="style/admin.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="view/javascript/jquery/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
        <link rel="stylesheet" type="text/css" href="style/highlight-css.css" />
        <script type="text/javascript" src="view/javascript/jquery/tabs.js"></script>
        <script type="text/javascript" src="view/javascript/jquery/superfish/js/superfish.js"></script>
        <link rel="stylesheet" type="text/css" href="view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
        <script type="text/javascript" src="view/javascript/jquery/table-highlighting.js"></script>
        <script type="text/javascript" src="view/javascript/jquery/ui/jquery.form.js"></script>
        <script type="text/javascript" src="view/javascript/jquery/ui/jquery.validate.js"></script>
        <script type="text/javascript" src="view/admin/javascript/admin.common.js"></script>
        <?php echo Page::getJsBlock(); ?>
        <script type="text/javascript"><!--
        $(document).ready(function() {
                $('#menu > ul').superfish({
                    hoverClass: 'sfHover',
                    pathClass: 'overideThisToUse',
                    delay: 0,
                    animation: {height: 'show'},
                    speed: 'normal',
                    autoArrows: false,
                    dropShadows: false,
                    disableHI: false, /* set to true to disable hoverIntent detection */
                    onInit: function() {
                    },
                    onBeforeShow: function() {
                    },
                    onShow: function() {
                    },
                    onHide: function() {
                    }
                });

                $('#menu > ul').css('display', 'block');
            });

            function getURLVar(urlVarName) {
                var urlHalves = String(document.location).toLowerCase().split('?');
                var urlVarValue = '';

                if (urlHalves[1]) {
                    var urlVars = urlHalves[1].split('&');

                    for (var i = 0; i <= (urlVars.length); i++) {
                        if (urlVars[i]) {
                            var urlVarPair = urlVars[i].split('=');

                            if (urlVarPair[0] && urlVarPair[0] == urlVarName.toLowerCase()) {
                                urlVarValue = urlVarPair[1];
                            }
                        }
                    }
                }

                return urlVarValue;
            }

            $(document).ready(function() {
                route = getURLVar('route');

                if (!route) {
                    $('#dashboard').addClass('selected');
                } else {
                    part = route.split('/');

                    url = part[0];

                    if (part[1]) {
                        url += '/' + part[1];
                    }

                    $('a[href*=\'' + url + '\']').parents('li[id]').addClass('selected');
                }
            });
            //--></script> 

    </head>
    <body>
        <div id="container">
            <div id="header">
                <div class="div1">
                    <div class="div2"><img src="view/admin/img/logo.png" title="<?php echo Page::$title; ?>" onclick="location = '<?php echo admin_adminMasterPage::$text_home; ?>'" /></div>
                    <?php if (admin_adminMasterPage::$logged) { ?>
                        <div class="div3"><img src="view/admin/img/lock.png" alt="" style="position: relative; top: 3px;" />&nbsp;<?php echo admin_adminMasterPage::$logged; ?></div>
                    <?php } ?>
                </div>
                
                <div id="menu">
                    <ul class="left" style="display: none;">
                        <li id="dashboard"><a href="<?php echo admin_adminMasterPage::$link_home; ?>" class="top"><?php echo admin_adminMasterPage::$text_home; ?></a></li>
                        <li id="catalog"><a class="top"><?php echo admin_adminMasterPage::$text_system; ?></a>
                            <ul>
                                <li><a href="<?php echo admin_adminMasterPage::$link_setting; ?>"><?php echo admin_adminMasterPage::$text_setting; ?></a></li>
                                <li><a href="<?php echo admin_adminMasterPage::$link_parts; ?>"><?php echo admin_adminMasterPage::$text_parts; ?></a></li>
                                <li><a class="parent"><?php echo admin_adminMasterPage::$text_security; ?></a>
                                    <ul>
                                        <li><a href="<?php echo admin_adminMasterPage::$link_user_group; ?>"><?php echo admin_adminMasterPage::$text_user_group; ?></a></li>
                                        <li><a href="<?php echo admin_adminMasterPage::$link_users; ?>"><?php echo admin_adminMasterPage::$text_users; ?></a></li>
                                    </ul>
                                </li>
                                
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
<?php echo Page::getContent(); ?>

        </div>
        <div id="dialog"></div>
        <div id="dialog1"></div>
        <div id="dialog2"></div>
    </body>
</html>
