<?php

/** QuickCMS v5.0
 * Система управления контентом 
 *
 * Контроллер шаблона страницы mainMasterPage.
 * 
 * 
 * @author Alexsandr Kondrashov 
 * http://wemake.org.ua
 * Данная программа НЕ является свободным программным обеспечением. Вы
 * НЕ вправе распространять ее и/или модифицировать.
 * 
 */
class admin_adminMasterPage extends Page {

    public static $text_home='';
    public static $text_system='';
    public static $text_setting='';
    public static $text_users='';
    public static $text_user_group='';
    public static $text_security='';
    public static $text_parts='';
    
    public static $link_home='';
    public static $link_setting='';
    public static $link_users='';
    public static $link_user_group='';
    public static $link_parts='';
    
    
    
    public static $logged='';
    
    function __construct(Application $app) {
        $this->app=$app;
        
        $this->app->language->setLanguage($this->app->languageCode,$this->toString());
        
        self::$text_home=$this->app->language->get('text_home');
        self::$text_system=$this->app->language->get('text_system');
        self::$text_setting=$this->app->language->get('text_setting');
        self::$text_users=$this->app->language->get('text_users');
        self::$text_user_group=$this->app->language->get('text_user_group');
        self::$text_security=$this->app->language->get('text_security');
        self::$text_parts=$this->app->language->get('text_parts');
    
        
        
        self::$link_home=$this->app->url->link('admin/main');
        self::$link_setting=$this->app->url->link('admin/setting');
        self::$link_users=$this->app->url->link('admin/system/users');
        self::$link_user_group=$this->app->url->link('admin/system/usergroup');
        self::$link_parts=$this->app->url->link('admin/system/parts');
        //parent::__construct($app);

    }

    function __destruct() {
       
    }

    function PreInit() {
        //put your code here
        $this->Init();
        $this->Render();
    }

    /**
     * пользовательские функции
     */
    function Init() {
        self::$logged=$this->app->user->firstName;
        //put your code here
    }

    /**
     * рендер шаблона мастерпейдж и немедленная отправка шаблона в поток вывода, после чего 
     * готовится шаблон контента self::$output, и вызовом функции Page::grtContent() в шаблоне вставляется
     * Можно не заботиться о рендере
     */
    function Render() {
        $this->MasterPageTemplate = $_SERVER['DOCUMENT_ROOT'] . '/view/'. $this->toPath() . '.html.php';
      
        parent::Render();
       
    }

    //put your code here
}

?>
