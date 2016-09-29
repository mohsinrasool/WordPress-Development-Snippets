<?php

/**
 * WordPress shortcode to display Twitch feed of a provided query
 * Usage: [twitch-feed q="game name" limit="10" offset="0"]
 * Version 1.0
 */
class TwitchFeedShortcode {

    public function __construct() {
        add_shortcode( 'twitch-feed', array( $this, 'twitch_feed' ) );
    }

    public function twitch_feed($atts) {

			$atts = shortcode_atts( array(
					'q' => '',
					'limit'=>12,
					'offset'=>0
				), $atts );

				$r = $this->get_url_contents('https://api.twitch.tv/kraken/search/streams?q='.$atts['q'].'&limit='.$atts['limit'].'&offset='.$atts['offset']);
				$r = json_decode($r);


				ob_start();
				echo '<div class="twitch_streams" class="container">';
				// print_r($r);
				$i=1;
				echo '<div class="row">';
				foreach ($r->streams as $stream) {
				?>
					<div class="col-md-6">
						<a target="foobox"  class="foobox custom-caption fbx-instance fbx-link" style="outline: invert;" href="/twitch-page?u=<?=$stream->channel->name?>"><img src="<?=$stream->preview->medium?>" /></a>
						<h2><?=$stream->channel->status?></h2>
						<p><?=$stream->viewers?> viewers on <?=$stream->channel->display_name?></p>
					</div>
				<?php
					if($i++%2 === 0 && $i!=0)
						echo '</div><div class="row">';
				}
					if($i++%2 === 0)
						echo '</div>';
				echo '</div>';
        return ob_get_clean();
    }

    function get_url_contents($url){
		$crl = curl_init();
		$timeout = 5;

		$client_id = 'md2u0biw9xppnrjmyyn13hlgq1bhe0c';
		$token = 'qd2uimzzhblla9a9hmsjaltszdep22';

		curl_setopt ($crl, CURLOPT_URL,$url);
		curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt ($crl, CURLOPT_SSL_VERIFYPEER, 0);

		curl_setopt ($crl, CURLOPT_HTTPHEADER, array('Accept: application/vnd.twitchtv.v3+json', 'Client-ID: '.$client_id,'x-api-version: 3'));

		$ret = curl_exec($crl);
		echo(curl_error($crl));

		curl_close($crl);
		return $ret;
	}

}

new TwitchFeedShortcode();
