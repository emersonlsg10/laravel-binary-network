<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Vendedor;

class VendedoresController extends Controller
{
	private $contadorEsquerda;
	private $contadorDireita;
		
	public function adicionar(Request $request){
				
		$vendedor = DB::table('vendedors')->exists();		
		
		$data = $request->except('_token');
		
		$request->validate([
            'vendedor_nome' => 'required|string|min:3|max:100'
        ]);
		
		//verifica se é o  primeiro a ser cadastrado
		if(!$vendedor){						
			$cadastrar  = DB::table('vendedors')->insert(
				['nome' => $data['vendedor_nome'], 'pai' => $data['patrocinador_id']]
			);
		}else{
			$filhoDisponivel = $this->verificaFilhosDoPatrocinador( $data['patrocinador_id']);			
			
			//verifica se este patrocinador pode cadastrar mais um
			if($filhoDisponivel){
								
				$cadastrar  = DB::table('vendedors')->insert(
					['nome' => $data['vendedor_nome'], 'pai' => $data['patrocinador_id']]
				);
				
				$ultimo = DB::table('vendedors')->orderBy('id', 'desc')->first();
								
				//atualiza o pai adicionando o respectivo id do filho
				$update = DB::table('vendedors')
					->where('id', $data['patrocinador_id'])
					->update([$filhoDisponivel => $ultimo->id]);				
			}
		}

		 //se tudo ocorrer bem, cadastra e volta para a HOME
        if ($cadastrar || $update)
            return redirect()
                            ->route('sistema.index')
                            ->with('success', 'Sucesso ao cadastrar!');

        //se der alguma falha, volta para a home com msg de falha
        return redirect()
                        ->route('sistema.index')
                        ->with('error', 'Falha ao cadastrar!');	
		
		return redirect()->back();
	}
	
	public function verificaFilhosDoPatrocinador($patrocinador){
		$patrocinador = DB::table('vendedors')->where('id', $patrocinador)->get();
		//dd($patrocinador[0]);
		if($patrocinador[0]->filhoesquerda == null )
			return "filhoesquerda";
		else if($patrocinador[0]->filhodireita == null)
			return "filhodireita";
		
		return false;
	}
	
	public function relatorio(){
		
		$vendedores = DB::table('vendedors')->select('nome','id','pai','filhodireita','filhoesquerda')->get();
		//$vendedor->filhoesquerda;
		
		$vendedoresNiveis = [];
		
		foreach($vendedores as $vendedor){
			
			//reinicia os contadores
			$this->contadorEsquerda = [];
			$this->contadorDireita = [];			
			
			if(isset($vendedor->filhoesquerda)){
				$this->contarFilhosEsquerda($vendedor->filhoesquerda);				
			}

			if(isset($vendedor->filhodireita)){
				$this->contarFilhosDireita($vendedor->filhodireita);				
			}	
			if(sizeof($this->contadorEsquerda) > sizeof($this->contadorDireita)){	
				DB::table('vendedors')
					->where('id', $vendedor->id)
					->update(['plano' => $this->retornaNivel(sizeof($this->contadorDireita)),
					'pontos' => sizeof($this->contadorDireita) * 500]);
			}else if(sizeof($this->contadorEsquerda) == sizeof($this->contadorDireita)){
				DB::table('vendedors')
					->where('id', $vendedor->id)
					->update(['plano' => $this->retornaNivel(sizeof($this->contadorDireita)),
					'pontos' => sizeof($this->contadorDireita) * 500]);
			}else{
				DB::table('vendedors')
					->where('id', $vendedor->id)
					->update(['plano' => $this->retornaNivel(sizeof($this->contadorEsquerda)),
					'pontos' => sizeof($this->contadorDireita) * 500]);
			}						
		}
		
		return redirect()->back();
	}

	public function contarFilhosEsquerda($vendedor){
		
		array_push($this->contadorEsquerda, $vendedor);
				
		$patrocinador = DB::table('vendedors')->where('id', '=', $vendedor)->get();
		
		if(isset($patrocinador[0]->filhoesquerda)){	
			$this->contarFilhosEsquerda($patrocinador[0]->filhoesquerda);			
		}
		if(isset($patrocinador[0]->filhodireita)){			
			$this->contarFilhosEsquerda($patrocinador[0]->filhodireita);
		}
	}

	public function contarFilhosDireita($vendedor){
		
		array_push($this->contadorDireita, $vendedor);
				
		$patrocinador = DB::table('vendedors')->where('id', '=', $vendedor)->get();
		
		if(isset($patrocinador[0]->filhoesquerda)){	
			$this->contarFilhosDireita($patrocinador[0]->filhoesquerda);			
		}
		if(isset($patrocinador[0]->filhodireita)){			
			$this->contarFilhosDireita($patrocinador[0]->filhodireita);
		}
	}
	
	public function retornaNivel($nivel){
		
		$nivel = sizeof($this->contadorDireita) * 500;
		
		if($nivel == 0){
			$nivel = "Vendedor";
		}else if($nivel > 0 && $nivel <= 500){
			$nivel = "Bronze";
		}else if($nivel > 500 && $nivel <= 1000){
			$nivel = "Prata";
		}else if($nivel > 1000 && $nivel <= 2000){
			$nivel = "Ouro";
		}else{
			$nivel = "Diamante";
		}
		return $nivel;
	}
}





















