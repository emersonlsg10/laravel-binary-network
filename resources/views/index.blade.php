@extends('template')

@section('conteudo')
	<div class="container" >
	<!--    Mensagens após a tentativa de salvar no BD-->
	@if(session('success'))
	<div class="alert alert-success" style="text-align:center">{{ session('success') }}</div>
	@endif

	@if(session('error'))
	<div class="alert alert-danger" style="text-align:center">{{ session('error') }}</div>
	@endif
		<form action="{{url('/adicionar')}}" method="POST" >
		@csrf
		  <div class="row">
			<div class="col">
				<label for="inputState">Nome do novo vendedor</label>
				<input type="text" id="vendedor" required name="vendedor_nome" class="form-control" placeholder="Novo vendedor">
			</div>
			<div class="col">
				<label for="inputState">Patrocinadores disponíveis</label>
				  <select id="patrocionador" class="form-control" name="patrocinador_id">
					@forelse($vendedores as $vendedor)
						@if($vendedor->filhoesquerda == null || $vendedor->filhodireita == null)
						<option value="{{$vendedor->id}}">{{$vendedor->nome}}</option>
						@endif
					@empty
						<option value="0">Primeiro a ser cadastrado!</option>   
					@endforelse
				  </select>
			</div>
		  </div>
		  <div class="form-group" style="margin-top:10px">
			<button type="submit" class="btn btn-primary">Adicionar</button>
		  </div>
		</form>
		<hr/>
		
		<div>
		<h1 style="text-align:center">Relatório<h1/>
		<form action="{{url('/gerar-relatorio')}}" method="POST">
			@csrf
			<button type="submit" class="btn btn-success">Atualizar relatório</button>
		</form>		
		</div>
		<hr/>
		
		<table class="table table-sm table-hover table-bordered">
		  <thead>
			<tr>
			  <th scope="col">#</th>
			  <th scope="col">Vendedor</th>
			  <th scope="col">Pontos Perna Menor</th>
			  <th scope="col">Nível</th>
			</tr>
		  </thead>
		  <tbody>
			<!-- relatório -->
			@forelse($vendedores as $vendedor)
			<tr>
				<th scope="row">{{$vendedor->id}}</th>				
				<td>{{$vendedor->nome}}</td>
				<td>{{$vendedor->pontos}}</td>
				<td>{{$vendedor->plano}}</td>
			</tr>	
			@empty
			<tr>
				<th scope="row"></th>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			@endforelse
		  </tbody>
		</table>	
	</div>
@endsection