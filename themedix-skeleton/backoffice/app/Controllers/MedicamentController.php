<?php
class MedicamentController extends Controller
{
    protected $page = 'medicaments';
    protected $title = 'Médicaments | The Medix Admin';
    public function index() { $this->render('medicaments'); }
}