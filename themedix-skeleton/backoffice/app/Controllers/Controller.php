<?php
abstract class Controller
{
    protected $page = 'dashboard';
    protected $title = 'The Medix Admin';

    protected function render($view)
    {
        ob_start();
        require __DIR__ . '/../Views/pages/' . $view . '.php';
        $content = ob_get_clean();

        $title = $this->title;
        $currentPage = $this->page;
        require __DIR__ . '/../Views/layout.php';
    }
}