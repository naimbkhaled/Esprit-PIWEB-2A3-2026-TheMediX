<?php
class DashboardController extends Controller
{
    protected $page = 'dashboard';
    protected $title = 'Dashboard | The Medix Admin';
    public function index() { $this->render('dashboard'); }
}