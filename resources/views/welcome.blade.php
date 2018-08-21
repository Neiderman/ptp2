@extends('master')

@section('style')
<style>

</style>
@endsection

@section('contenido')
<div class="jumbotron">
	<div class="container">
		<h1>Bienvenido ala prueba de pagos!</h1>
		<p>Se crea aplicativo de prueba para realizar 'Pagos' con PSE utilizando los WebServices de Place To Pay.</p>
		<p>
			<center>
				<div class="col-md-4"></div>
				<div class="col-md-4">
					<a id="b_pse" class="btn btn-default btn-xs" role="button"><img src="http://www.msnaturals.co/images/pse.png" style="height: 50px"></a>
					<a id="b_cancelar" class="btn btn-danger btn-xm" role="button" style="display: none;">Cancelar</a>
				</div>
				<div class="col-md-4"></div>
			</center>
		</p>
	</div>
</div>
<hr>

<div id="form_continue" style="display: none;">


</div>

@endsection

@section('javascripts')
<script>
	
	$(document).ready(function() {
		$('#b_pse').on('click', function(event) {
			event.preventDefault();
			$('#progress_bar').css('display', 'block');

			
			$.ajax({
				url: "{{ action('HomeController@inPago') }}",
				type: 'POST',
				dataType: 'html',
				data: {
					_token: $('meta[name=_token]').attr('content')
				},
			})
			.done(function(res) {

				setTimeout(function() {
					$('#progress_bar').css('display', 'none');
					$('[name="submit_button"]').on('click', function(event) {
						iniciarPasarela();
					});
				}, 1000);

				$('#b_pse').css('display','none');
				$('#b_cancelar').css('display','block');
				$('#form_continue').css('display','block');
				$('#form_continue').html(res);
			})
			.fail(function(res) {
				$('#form_continue').html('<div class="alert alert-danger" role="alert"><center>No se pudo obtener la lista de Entidades Financieras, por favor intente más tarde.</center></div>');
			});


		});

		$('#b_cancelar').on('click', function(event) {
			event.preventDefault();

			$('#form_continue').html();
			$('#b_pse').css('display','block');
			$('#b_cancelar').css('display','none');
			$('#form_continue').css('display','none');
			
			// alert('se cancela');
		});
	});

	function iniciarPasarela()
	{
		$('#espacio_errores').html('');
		$('#form_pasarela').find('.form-control').removeAttr('style');
		$('#progress_bar').css('display', 'block');
		$.ajax({
			url: "{{ action('HomeController@pasarelaStart') }}",
			type: 'POST',
			// dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
			data: $('#form_pasarela').serialize()
		})
		.done(function(res) {
			swal('Perfecto!','Parece que todo ha salido bien hasta ahora, seras redireccionado a PSE para continuar tu transacción.','success');
			setTimeout(function() {
				location.href = res.url;
			}, 2000);
		})
		.fail(function(res) {
			var html_error = "";
			$('#progress_bar').css('display', 'none');
			$.each(res.responseJSON.errores, function(index, val) {
				$('#'+index).css('border', 'solid red 1px');
			});

			$('#espacio_errores').html('<center><div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Ops!</strong> Revisa los campos marcados de color rojo. <br> Recuerda que si contiene \'*\' en su nombre es obligatorio.</div></center>');

			
		});
		
	}
</script>
@endsection