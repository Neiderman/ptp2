@extends('master')

@section('style')
<style>

</style>
@endsection

@section('contenido')
<div class="jumbotron">
	<div class="container">
		<center>
			<h1>Estado de transacción</h1>
			<p>Sección para verificar el estado de tu transacción.</p>
		</center>
		<p>
			<center>
				<div class="row">
					<div class="col-md-3"></div>
					<div class="col-md-6">
						<a class="btn btn-default btn-xs" role="button"><img src="http://www.msnaturals.co/images/pse.png" style="height: 50px"></a>
						<hr>
						<div id="alerta_status" class="alert alert-info">
							<a class="close" id="refresh_transaction_status"><i class="fas fa-sync-alt"></i></a>
							<strong>Estado de transacción: </strong> <span id="status_transaction">Pendiente</span>
						</div>
					</div>
					<div class="col-md-3"></div>
				</div>
				<div class="row">
					<div class="col-md-3"></div>
					<div class="col-md-6">
						<a id="redireccion_btn" href="{{ url('/') }}" class="btn btn-default" role="button" style="display: none;">Regresar a la pagina principal</a>
					</div>
					<div class="col-md-3"></div>
				</div>
			</center>
		</p>
	</div>
</div>
<hr>

@endsection

@section('javascripts')
<script>
	
	$(document).ready(function() {
		$('#refresh_transaction_status').on('click', function(event) {
			$('#progress_bar').css('display', 'block');
			$('#refresh_transaction_status').hide();
			verificarStatusTransaction();
		});
		
		setTimeout(function() {
			$('#refresh_transaction_status').click();
		}, 1000);

	});

	function verificarStatusTransaction()
	{
		$('#status_transaction').html();
		$('#redireccion_btn').css('display', 'none');
		$('#alerta_status').removeClass('alert-info');
		$('#alerta_status').removeClass('alert-danger');
		$('#alerta_status').removeClass('alert-success');
		$('#alerta_status').addClass('alert-warning');

		setTimeout(function() {
			$.ajax({
				url: "{{ action('HomeController@pasarelaStatusTransaction') }}",
				type: 'POST',
			// dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
			data: {
				_token: $('meta[name=_token]').attr('content'),
				transaction_id: "{{ $_transaccion }}",
				session_id: "{{ $_session }}",
				trazabilidad_id: "{{ $_trazabilidad }}",
				url_id: "{{ $_url }}"
			}
		})
			.done(function(res) {
				if(res.status == 'Aprobada'){
					$('#alerta_status').removeClass('alert-info');
					$('#alerta_status').removeClass('alert-danger');
					$('#alerta_status').removeClass('alert-warning');
					$('#alerta_status').addClass('alert-success');
				} else {
					$('#alerta_status').removeClass('alert-warning');
					$('#alerta_status').removeClass('alert-danger');
					$('#alerta_status').removeClass('alert-success');
					$('#alerta_status').addClass('alert-info');

					$('#refresh_transaction_status').show();
				}
				
				$('#redireccion_btn').css('display', 'block');
				$('#progress_bar').css('display', 'none');
				$('#status_transaction').html(res.status);
			})
			.fail(function(res) {
				$('#alerta_status').removeClass('alert-info');
				$('#alerta_status').removeClass('alert-success');
				$('#alerta_status').removeClass('alert-warning');
				$('#alerta_status').addClass('alert-danger');

				$('#progress_bar').css('display', 'none');
				$('#status_transaction').html('Fallida.')
				$('#refresh_transaction_status').show();
			});
		}, 1000);
		
	}
</script>
@endsection