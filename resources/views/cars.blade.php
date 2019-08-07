<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Car Data</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>

    <body>
    	<div class="row">
    		<div class="col-md-12">
    			<table class="table table-hover table-stripped">
    				<thead>
    					<tr>
    						<th></th>
							<th>Name</th>
							<th>Delay</th>
							<th>Price</th>
							<th>Color</th>
							<th>Tapicería</th>
							<th>Combustible</th>
							<th>Consumo mixto (l/100 km)</th>
							<th>Emisiones de CO2 (g/km)</th>
							<th>Reserved</th>
							<th>Options</th>
    					</tr>
    				</thead>

    				<tbody>
    					@foreach($cars as $car)
    						<tr>
    							<td>
    								@if (strpos($car->image_url, 'http') !== false)
    									<img src="{{ $car->image_url }}" height="50px" width="50px">
									@else
										<img src="http://www.carstore.citroen.es{{ $car->image_url }}" height="50px" width="50px">
    								@endif
    								
    							</td>
    							<td>{{ $car->name }}</td>
    							<td>{{ $car->delay }}</td>
    							<td>{{ $car->price }}</td>
    							<td>{{ $car->color }}</td>
    							<td>{{ $car->upholstery }}</td>
    							<td>{{ $car->combustible }}</td>
    							<td>{{ $car->consumo_mixto }}</td>
    							<td>{{ $car->emisiones_de }}</td>
    							<td>{{ $car->reserved }}</td>

    							<td>
    								<?php
    									$options = json_decode($car->options);

    									foreach($options as $option)
    										echo $option, '<br>';
    								?>
    							</td>
    						</tr>
						@endforeach
    				</tbody>
    			</table>

    			{{ $cars->links() }}
    		</div>
    	</div>
    </body>
</html>
