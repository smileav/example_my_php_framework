<?php

/** QuickCMS v5.0
 * Ядро системы управления контентом 
 *
 * Description of Page v5.0.1
 * Базовый класс контроллеров страниц
 * 
 * @author Alexsandr Kondrashov 
 * http://wemake.org.ua
 * Данная программа НЕ является свободным программным обеспечением. Вы
 * НЕ вправе распространять ее и/или модифицировать.
 * 
 */
class Module {

   

    /**
     *
     * @var string путь к файлу шаблона страницы
     */
    public static $Template;

  

    /**
     *
     * @var Application 
     */
    public $app;

    


   
    /**
     * отправляемый контент в поток вывода
     * @var type 
     */
    public static $output = '';

    function __construct(Application $app) {
        $this->app = $app;

        //$this->PreInit();
    }

    /**
     * первое событие
     * 
     */
    public function PreInit() {

        // $this->app = &$app;
        //self::$apps = $this->app;
        /*   if ($_REQUEST['page'] > 1) {

          self::$currentPage = '?page=' . $_REQUEST['page'];
          } */
        // $this->name=$this->toString();
        $this->permissions();
    }

    /**
     * обработчик события загрузки страницы
     */
    function Render() {
        //определяет какой шаблон загрузить
        if (!$this->app->query->isAjax) {
            try {
                if ($this->masterPageClass) {
                    $mp = Loader::loadOnceClass($this->masterPageClass, $this->app);
                    $mp->PreInit();
                } else {
                    if ($this->MasterPageTemplate) {

                        if (self::$Template) {
                            ob_start();
                            include_once self::$Template;
                            self::$output = ob_get_contents();
                            ob_clean();
                        }
                        include_once $this->MasterPageTemplate;
                    } else if (self::$Template) {
                        ob_start();
                        include_once self::$Template;
                        self::$output = ob_get_contents();
                        ob_clean();
                    }
                }
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        } else {
            // $this->AjaxRender();
        }
    }

    /**
     * render для ajax запросов,  подключает кастомный шаблон $this->Template
     */
    protected function AjaxRender() {
        //if (in_array('text/html', explode(',', $this->app->query->server['HTTP_ACCEPT']))) {
        try {
            if (self::$Template) {
                ob_start();
                include self::$Template;
                self::$output = ob_get_contents();
                ob_clean();
                return self::$output;
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        //}
    }

    /**
     * возвращает шаблон контента для главного шаблона
     */
    static function getContent() {

        // include_once self::$Template;
        return self::$output;
    }

    /**
     *
     * @return string возвращает имя класса 
     */
    function toString() {
        return get_class($this);
    }

    /**
     *
     * @return string возвращает относительный путь к файлу класса 
     */
    function toPath() {
        return str_replace('_', '/', get_class($this));
    }

    /**
     * определяет права пользователя, если нет прав доступа-посылает на указанный редирект или на error_Perm, если неуказан редирект
     * 
     */
    private function permissions() {
        if (self::$check_auth) {
            $this->app->user->permission($this->toString());
            if (empty($this->app->user->permission)) {
                if (self::$redirect) {

                    $this->app->redirect(self::$redirect, HTTP_REDIRECT);
                } else {
                    $this->app->redirect($this->app->url->link('error/Perm', http_build_query($this->app->query->get)), HTTP_REDIRECT);

                    // throw new QDException('Доступ закрыт!!',00);
                }
            }
        }
    }

    /**
     * запуск обработчиков общедоступных событий
     * @param string $eventArray массив событий
     */
    /* public function callEvent($eventArray) {

      //var_dump(self::$perms);
      foreach ($eventArray as $event => $arg) {
      if (is_array($arg)) {//если параметры
      call_user_func_array(array(&$this, $event), $arg);
      } else {
      call_user_func(array(&$this, $event));
      }
      }



      } */

    /**
     * запуск обработчиков защищенных событий
     * @param string $eventArray массив событий
     */
    public function callEvent() {
        $eventArray = $this->app->query->functions;

        if (!self::$check_auth) {//если проверка авторизации необязательна (для всех пользователей), мы не проверяем разрешения на доступ к функциям
            //а сразу начинаем выполнять
            if (!empty($eventArray)) {
                foreach ($eventArray as $event) {
                    if (isset($event['function'])) {
                        if (!$event['args']) {
                            call_user_func(array($this, $event['function']));
                        } else {

                            call_user_func_array(array($this, $event['function']), $event['args']);
                        }
                    } else {
                        call_user_func(array($this, 'Init'), $event['args']);
                    }
                }
            } else {

                call_user_func(array($this, 'Init'));
            }
        } else {//если нужна проверка на доступ к конкретной функции
            if (!empty($eventArray)) {
                foreach ($eventArray as $event) {
                    if (isset($event['function'])) {
                        if (in_array($event['function'], $this->app->user->perm_function)) {
                            if (!$event['args']) {
                                call_user_func(array($this, $event['function']));
                            } else {

                                call_user_func_array(array($this, $event['function']), $event['args']);
                            }
                        } else {//в массиве вызываемой ф-ии не обнаружено - ошибка доступа
                            $this->app->loader = Loader::loadOnceClass('error_Perm', $this->app);
                            $this->app->loader->PreInit();
                            $this->app->loader->callEvent();
                        }
                    } else {
                        if (in_array('Init', $this->app->user->perm_function)) {
                            call_user_func(array($this, 'Init'), $event['args']);
                        }
                    }
                }
            } else {
                if (in_array('Init', $this->app->user->perm_function)) {
                    call_user_func(array($this, 'Init'));
                } else {//в массиве вызываемой ф-ии не обнаружено - ошибка доступа
                    $this->app->loader = Loader::loadOnceClass('error_Perm', $this->app);
                    $this->app->loader->PreInit();
                    $this->app->loader->callEvent();
                }
            }
        }
        /* foreach ($eventArray as $event => $arg) {
          if (is_array($arg)) {//если параметры
          if (self::$neces_auth) {//авторизация нужна
          foreach (self::$perms as $key_ => $value_) {//смотрим по БД события
          $val = explode(",", $value_['func_name']); //если несколько ф-й на событие -разбиваем
          if (is_array($val) && count($val) > 1) {
          if (in_array($event, $val)) {
          self::$errorPerms = null;
          call_user_func_array(array(&$this, $event), $arg['args']);
          break;
          } else {
          self::$errorPerms = 1;
          }
          } else {
          if ($value_['func_name'] == $event) {
          if (isset(self::$errorPerms)) {
          self::$errorPerms = null;
          }

          call_user_func_array(array(&$this, $event), $arg['args']);
          break;
          } else {
          self::$errorPerms = 1;
          }
          }
          }
          } else {
          call_user_func_array(array(&$this, $event), $arg['args']);
          }
          } else {
          if (self::$neces_auth) {
          foreach (self::$perms as $key_ => $value_) {//смотрим по БД события
          $val = explode(",", $value_['func_name']); //если несколько ф-й на событие -разбиваем
          if (is_array($val) && count($val) > 1) {
          if (in_array($event, $val)) {
          self::$errorPerms = null;
          call_user_func(array(&$this, $event));
          break;
          } else {
          self::$errorPerms = 1;
          }
          } else {
          if ($value_['func_name'] == $event) {
          if (isset(self::$errorPerms)) {
          self::$errorPerms = null;
          }

          call_user_func(array(&$this, $event));
          break;
          } else {
          self::$errorPerms = 1;
          }
          }
          }
          } else {
          call_user_func(array(&$this, $event));
          }
          }
          } */
    }

  

}

?>
