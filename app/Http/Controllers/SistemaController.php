<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendedor;

class SistemaController extends Controller
{
	
	private $vendedor;

    function __construct(Vendedor $vendedor) {
        $this->vendedor = $vendedor;
    }
	
	public function index(){
		
		$vendedores = $this->vendedor->all();
		
		return  view('index', compact('vendedores'));
	}
}
