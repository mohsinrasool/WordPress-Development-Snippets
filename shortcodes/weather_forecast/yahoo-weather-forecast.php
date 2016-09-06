<?php

/**
* Adds a shortcode [weather_forecast]  to fetch Weather information from Yahoo API. Its a responsive code based on Twitter bootstrap.
*/

class YahooWeatherForecast
{


	public static function shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'city' => 'Kingston, ON'
		), $atts );

		if ( false === ( $weather_forecast = get_transient( 'weather_forecast' ) ) ) {

			$city = urlencode($atts['city']);
			$weather_forecast_raw = wp_remote_post( 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20weather.forecast%20where%20woeid%20in%20(select%20woeid%20from%20geo.places(1)%20where%20text%3D%22'.$city.'%22)&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys');

			if(is_wp_error($weather_forecast_raw) || $weather_forecast_raw['response']['code'] != 200)
					return '';

		    if($weather_forecast_raw['response']['code'] != 200)
				return '';

			$wf = $weather_forecast_raw['body'];
		    set_transient( 'weather_forecast_raw', $weather_forecast_raw, 24 * HOUR_IN_SECONDS );


			$wf = json_decode($wf);
			$channel  = $wf->query->results->channel;
			$forecast = $wf->query->results->channel->item->forecast;
			$units = $wf->query->results->channel->units;

			ob_start();

		?>
		<div class="row" id="weather_block">
			<div class="col-sm-5 col-sm-offset-6 col-md-4 col-md-offset-7 col-xs-12">
				<div class="today"><?php echo date('l', strtotime($channel->item->condition->date)); ?></div>
				<hr />
				<div class="row">
					<div class="col-xs-6">
						<span class="location"><?php echo $channel->location->city ?>,<?php echo $channel->location->region ?> </span>
						<div class="temperature">
						<?php echo FtoC($channel->item->condition->temp); ?>&deg;
						</div>
					</div>
					<div class="col-xs-6">
						<img src="<?php echo get_template_directory_uri().'/images/weather/'.(self::WeatherImageName($channel->item->condition->text)); ?>" alt="<?php echo $channel->item->condition->text; ?>" class="img-responsive" />
					</div>
				</div>

				<hr />
				<div class="row">
					<div class="col-xs-6">
						<h4>Wind:</h4>
						<span>Speed:</span> <?php echo $channel->wind->speed?> <?php echo $units->speed ?> <br/>
						<span>Feels Like:</span> <?php echo self::FtoC($channel->wind->chill)?>&deg;<br/>
						<span>Direction:</span> <?php echo $channel->wind->direction?>&deg; <br/>

						<h4>Atmosphere:</h4>
						<span>Humidity:</span> <?php echo $channel->atmosphere->humidity?>% <br/>
						<span>Pressure:</span> <?php echo $channel->atmosphere->pressure?> <?php echo $units->pressure ?><br/>
						<!-- <span>Rising:</span> <?php echo $channel->atmosphere->rising?> <br/> -->
						<span>Visibility:</span> <?php echo $channel->atmosphere->visibility?> <?php echo $units->distance ?><br/>
					</div>
					<div class="col-xs-6">
						<br/>
						<img src="<?php echo get_template_directory_uri(); ?>/images/weather/wind.png" alt="<?php echo $channel->wind->speed?> <?php echo $units->speed ?>" class="img-responsive" style="max-width: 100px;" />
					</div>
				</div>
				<div class="row forecast">
					<?php

					$i=0;
					foreach ($forecast as $day) {
					?>
					<div class="col-xs-2">
						<div class="inner">
							<h5><?php echo $day->day; ?></h5>
							<img src="<?php echo get_template_directory_uri().'/images/weather/'.(self::WeatherImageName($day->text)); ?>" alt="<?php echo $day->text; ?>" class="img-responsive" />
							<div class="temp">
								<?php echo self::FtoC($day->high); ?>C
							</div>
						</div>
					</div>
					<?php
						if(++$i >= 6)
							break;
					}
					?>
				</div>
			</div>
		</div>
		<?php
			$weather_forecast = ob_get_clean();
			set_transient( 'weather_forecast', $weather_forecast, 24 * HOUR_IN_SECONDS );
		}
		return $weather_forecast;
	}


	public static function WeatherImageName($str) {
		return strtolower(str_replace(' ', '_', $str)).'.png';
	}

	public static function FtoC($temp) {
		return ceil ( ($temp-32)/1.8 );
	}

}

add_shortcode( 'weather_forecast', array( 'YahooWeatherForecast', 'shortcode' ) );
