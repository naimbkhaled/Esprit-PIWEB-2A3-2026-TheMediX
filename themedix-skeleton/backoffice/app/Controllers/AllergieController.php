<?php
class AllergieController extends Controller
{
    protected $page = 'allergies';
    protected $title = 'Allergies | The Medix Admin';
    public function index() { $this->render('allergies'); }
}