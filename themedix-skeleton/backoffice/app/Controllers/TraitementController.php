<?php
class TraitementController extends Controller
{
    protected $page = 'traitements';
    protected $title = 'Traitements | The Medix Admin';
    public function index() { $this->render('traitements'); }
}