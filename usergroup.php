<?php

/*
 * QuickCMS v5.0
 * Класс страницы admin_parts

 * @author Alexsandr Kondrashov 
 * http://wemake.org.ua
 * Данная программа НЕ является свободным программным обеспечением. Вы
 * НЕ вправе распространять ее и/или модифицировать.
 * 
 */

class admin_system_usergroup extends Page {

    //текстовые переменные
    // public static $text_login = '';
    public static $text_warning = '';
    public static $text_success = '';
    public static $text_entry_group_name = '';
    public static $text_entry_functions = '';
    public static $text_select_all = '';
    public static $text_unselect_all = '';
    public static $text_edit = '';
    public static $heading_title = '';
    public static $column_groups = '';
    public static $column_action = '';
    public static $button_add = '';
    public static $button_delete = '';
    public static $button_cancel = '';
    public static $text_no_results = '';
    //сортировка, сраницы
    public static $sort;
    public static $order;

    /**
     * урл сортировки по группе
     * @var type 
     */
    public static $sort_group;

    /**
     * страницы
     * @var type 
     */
    public static $pagination;

    /**
     * урл для загрузки списка
     * @var type 
     */
    public static $action_list;

    /**
     * Общий список групп
     * @var array 
     */
    public static $list;

    /**
     * id группы (для вормы редактирования)
     * @var int 
     */
    public static $id_group;
    public static $group_name;

    /**
     * список функций в группе
     * @var array 
     */
    public static $group_functions;
    public static $functions;

    /**
     * урл форм
     * @var atring 
     */
    public static $action_edit = '';

    /**
     * урл формы для удаления
     * @var string 
     */
    public static $action_delete = '';

    /**
     * урл для добавления
     * @var string 
     */
    public static $insert = '';

    function __construct(Application $app) {
        $this->app = $app;
        $this->masterPageClass = admin_adminMasterPage; //Указать MasterPage
        self::$Template = $_SERVER['DOCUMENT_ROOT'] . '/view/' . $this->toPath() . '.html.php';
        self::$check_auth = true;
        self::$description = '';
        self::$keyWords = '';
        self::$title = $this->app->language->get('heading_title');
        self::$redirect = $this->app->url->link('admin/auth');
        self::$breadcrumbs = '';
        self::$pagination = null;
        self::$currentPage = 0;

        Breadcrumbs::set(false, $this->app->url->link('admin/main'), $this->app->language->get('text_home'));

        parent::__construct($app);
    }

    function Render() {
        self::$breadcrumbs = Breadcrumbs::get();

        parent::Render();

        /* для ajax
          self::$Template=$_SERVER['DOCUMENT_ROOT'] . '/view/mainMasterPage.html.php';
          $this->app->output(json_encode($this->AjaxRender()));
         * */
    }

    /**
     * основная функция, вызывается по умолчанию если не вызываются другие функции
     * @param mixed $args любой набор аргументов в соответствии с требованиями страницы
     */
    function Init($args = null) {
        $url = '';
        if (isset($this->app->query->get['sort'])) {
            $url.='&sort=' . $this->app->query->get['sort'];
        } else {
            $url.='&sort=name';
        }

        if (isset($this->app->query->get['order'])) {
            $url.='&order=' . $this->app->query->get['order'];
        } else {
            $url.='&order=ASC';
        }
        if (isset($this->app->query->get['order'])) {
            $url.='&page=' . $this->app->query->get['page'];
        }

       

        self::$action_list = $this->app->url->link('admin/system/usergroup/getList' . $url);
        self::$text_warning = Error::getErrors('text_warning');
        self::$text_success = Error::getErrors('text_success');
        Breadcrumbs::set(Config::BC_SEPARATOR, '', $this->app->language->get('text_groups'));
       
        //$this->app->output($this->AjaxRender());//для ajax ответа
    }

    /**
     * грузит список
     */
    public function getList() {

        self::$heading_title = $this->app->language->get('heading_title');
        self::$button_add = $this->app->language->get('button_add');
        self::$button_delete = $this->app->language->get('button_delete');

        self::$column_groups = $this->app->language->get('column_groups');
        self::$column_action = $this->app->language->get('column_action');
        self::$text_no_results = $this->app->language->get('text_no_results');
        self::$text_edit = $this->app->language->get('text_edit');

        $url = '';

        if (isset($this->app->query->get['sort'])) {
            self::$sort = $this->app->query->get['sort'];
        } else {
            self::$sort = 'name';
        }

        if (isset($this->app->query->get['order'])) {
            self::$order = $this->app->query->get['order'];
        } else {
            self::$order = 'ASC';
        }

        if (isset($this->app->query->get['page'])) {
            self::$currentPage = $this->app->query->get['page'];
            $url.='&page=' . self::$currentPage;
        } else {
            self::$currentPage = 1;
        }


        if (self::$order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }


        self::$sort_group = $this->app->url->link('admin/system/usergroup', '&sort=name' . $url);

        self::$insert = $this->app->url->link('admin/system/usergroup/update');
        self::$action_delete = $this->app->url->link('admin/system/usergroup/delete' . $url);
        //создаем экземпляр модели
        $Group = new admin_Group($this->app);
        $data = array(
            'sort' => self::$sort,
            'order' => self::$order,
            'start' => (self::$currentPage - 1) * 10,
            'limit' => 10
        );

        $groups = $Group->getList($data);
        //формируем список

        foreach ($groups as $group) {
            self::$list[] = array(
                'id' => $group->id,
                'name' => $group->name,
                'action' => $this->app->url->link('admin/system/usergroup/update;' . $group->id)
            );
        }

        if (isset($this->app->query->get['order'])) {
            $url = '&order=' . $this->app->query->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $Group->total;
        $pagination->page = self::$currentPage;
        $pagination->limit = 10;
        $pagination->text = ">";

        $pagination->url = $this->app->url->link('admin/system/usergroup', $url . '&page={page}');

        self::$pagination = $pagination->render();

        self::$Template = $_SERVER['DOCUMENT_ROOT'] . '/view/admin/system/groupList.html.php';
        $this->app->output($this->AjaxRender());
    }

    /**
     * форма редактирования групп
     * @param int $id
     */
    function update($id) {
        //echo $part_id;
        //   $this->app->language->setLanguage($this->app->languageCode,$this->toString());
        self::$text_entry_group_name = $this->app->language->get('text_entry_group_name');
        self::$text_entry_functions = $this->app->language->get('text_entry_functions');
        self::$text_select_all = $this->app->language->get('text_select_all');
        self::$text_unselect_all = $this->app->language->get('text_unselect_all');

        $Group = new admin_Group($this->app);
        $Group->getGroupInfo($id);

        self::$id_group = $Group->group_id;
        self::$group_name = $Group->name;
        self::$group_functions = $Group->group_functions;
        self::$functions = $Group->all_functions;


        self::$action_edit = $this->app->url->link('admin/system/usergroup/set;' . self::$id_group);


        self::$Template = $_SERVER['DOCUMENT_ROOT'] . '/view/' . $this->toPath() . '.form.html.php';
        $this->app->output($this->AjaxRender());
    }

    /**
     * сохранение групп
     * @param int $id
     */
    function set($id = 0) {
        $json = array();
        $json['event'] = 'null';
        $json['object'] = 'dialog';
        $json['object_update'] = '';
        $json['message'] = '';

        $Group = new admin_Group($this->app);

        if ($Group->editGroup($id, $this->app->query->post)) {
            if (isset($this->app->query->post['postaction'])) {
                $json['event'] = $this->app->query->post['postaction'];
            }
        } else {
            $json['event'] = 'alert';
            $json['message'] = $this->app->language->get('error_egit_group');
        }

     

        if ($this->app->query->responceType == 'json') {
            $this->app->output(json_encode($json));
        } else {
            $this->app->output($this->AjaxRender());
        }
    }

    /**
     * удаление групп
     */
    function delete() {
        if (isset($this->app->query->post['selected'])) {

            $Group = new admin_Group($this->app);
            $aff = $Group->delete($this->app->query->post['selected']);
            if ($aff) {
                $err = Error::set('text_success', sprintf($this->app->language->get('success_delete'), $aff));
            } else {
                $err = Error::set('text_warning', $this->app->language->get('error_delete_groups'));
            }
        } else {
            $err = Error::set('text_warning', $this->app->language->get('error_select_groups'));
        }
        $this->app->redirect($this->app->url->link('admin/system/usergroup'));
    }

}

?>
