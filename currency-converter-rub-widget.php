<?php
/**
 * Plugin Name: Currency Converter Rub Widget
 * Plugin URI: http://paha.khspu.ru/blog
 * Text Domain: ccr
 * Domain Path: /languages 
 * Description: This widget displays the Russian ruble, according to central bank (cbr). (Этот виджет отображает Российский рубль, по курсу ЦБ (cbr).
 * Version: 1.5.0
 * Author: PahaW
 * Author URI: http://paha.khspu.ru/blog/
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'ccr_load_widgets' );   


/**
 * Register our widget.
 * 'Currency Converter Rub Widget' is the widget class used below.
 *
 * @since 0.1
 */
 
function ccr_load_widgets() {
	register_widget( 'Currency_Converter_Rub_Widget' );
}

/**
 * Currency Converter Rub Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
 

class Currency_Converter_Rub_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'ccr', 'description' => __('Данный Widget показывает курс рубля согласно ЦБ РФ.', 'ccr') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'ccr-widget' );

		/* Create the widget. */
		//$this->WP_Widget( 'ccr-widget', __('Курс Российского рубля Widget', 'ccr'), $widget_ops, $control_ops );
		parent::__construct( 'ccr-widget', __('Курс Российского рубля Widget', 'ccr'), $widget_ops, $control_ops );
	}


	/* 
	* Output timezone ($this->number = widget_id)
	*/ 
	function get_timezone(){
	 // get timezone
	  if (!empty($timezone)){
	   	$timezone = $get_mass_widget[$this->number]['timezone'];
	  } else { $timezone = 10; }
			return $timezone;
	}

	// Output date + timezone	
	function get_date($format){
	  //$timezone = '10';
	  $timezone = Currency_Converter_Rub_Widget::get_timezone();
		  // Create date
		  $date=gmdate($format, time() + 3600*($timezone+date("I")));
			return $date;
	}

	// Put html text in the file cache
	function put_ht_fc($text, $data = ""){
			$upload_dir = wp_upload_dir();
			$cache_ccr_dir = $upload_dir['basedir'].'/cache_ccr';
			$error = 0;
			// Create folder for cache 
			if (!is_dir($cache_ccr_dir)){
				// Create folder
				mkdir($cache_ccr_dir, 0777, true);	
			} else {
				if ($data!=""){
					$file = $data."_".$this->get_date('Ymd_N').".txt";
					$string_to_search = $data."_";   // String to search day of the week
					if (is_array($text)){
						$text_tmp = "";
						foreach ($text as $tt) {
							$text_tmp .= $tt[1]."|".$tt[3]."\n";
						}
						$text = $text_tmp;
					}
				} else {
					$file = $this->get_date('Ymd_N').".txt";
	  				$tml_day_week = explode($file, "_");        // Day of the week
					$string_to_search = '_'.$tml_day_week[2];   // String to search day of the week
				}

				/* Search and delete file */
				$dir = opendir($cache_ccr_dir);
				$mass_sas[] = "";
				while(($s_file = readdir($dir)) !== false) {
				    $mass_sa = strrchr($s_file,$string_to_search);
				    $mass_sas[] = $s_file;
				    // Delete file the last week
				    if($mass_sa != "") unlink($cache_ccr_dir.'/'.$s_file);
				}
				closedir($dir);

				$newcontent = stripslashes($text);

				// Write or create and write file
				$f = fopen($cache_ccr_dir.'/'.$file, 'w+');
				if ($f !== FALSE) {
					fwrite($f, $newcontent);
					fclose($f);
					//$error = ($data!=""?"date":"no date");
				} else {
					$error = "2";
				}
			}
			// Close folder
			chmod($cache_ccr_dir, 0755);
			return $error;
	}

	// Get xml page (cbr.ru)
	function get_content(){
		// Create date for link
		$link_date=Currency_Converter_Rub_Widget::get_date('d/m/Y');
		// Формируем ссылку
		$link = "http://www.cbr.ru/scripts/XML_daily.asp?date_req=".$link_date;

		$list = array();
		$xml = new DOMDocument();
		if (@$xml->load($link)){
			$root = $xml->documentElement;
			$items = $root->getElementsByTagName('Valute');
			$i = 0;
			foreach ($items as $item){
				$CharCode = $item->getElementsByTagName('CharCode')->item(0)->nodeValue;
				$NumCode = $item->getElementsByTagName('NumCode')->item(0)->nodeValue;
				$Nominal = $item->getElementsByTagName('Nominal')->item(0)->nodeValue;
				$curs = $item->getElementsByTagName('Value')->item(0)->nodeValue;
				$list[$i] = array($NumCode, $CharCode, $Nominal, floatval(str_replace(',', '.', $curs)));
				$i++;
			}
		} else _e('Запрашиваемая страница не найдена', 'ccr');
		return $list;

	}
	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );
		/* Our variables from the widget settings. */
		$title    = apply_filters('widget_title', $instance['title'] );
		$name     = $instance['name'];
		$currency = $instance['currency'];
		$timezone = $instance['timezone'];
		$number   = $instance['number'];
		$for_sale = $instance['for_sale'];
		$status   = $instance['status'];
		$edit_rub = $instance['edit_rub'];
		$images   = $instance['images'];
		$show_us  = isset( $instance['show_us'] ) ? $instance['show_us'] : 0;
		$show_eu  = isset( $instance['show_eu'] ) ? $instance['show_eu'] : 0;
		$show_kr  = isset( $instance['show_kr'] ) ? $instance['show_kr'] : 0;
		$show_jp  = isset( $instance['show_jp'] ) ? $instance['show_jp'] : 0;
		$show_ch  = isset( $instance['show_ch'] ) ? $instance['show_ch'] : 0;
		$show_gb  = isset( $instance['show_gb'] ) ? $instance['show_gb'] : 0;
		$show_chf = isset( $instance['show_chf'] ) ? $instance['show_chf'] : 0;

		/* Get widget_id */
		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = null;

		$dat = explode('-', $args['widget_id']);
		$widget_id = $dat[count($dat)-1];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		// Получаем текущие курсы валют в rss-формате с сайта www.cbr.ru
		$content = $this->get_content();

		$dollar = "";
		$euro = "";
		$iena = "";
		$von = "";
		$ch = "";
		$gb = "";

		$number_dollar = "";
		$number_euro = "";
		$number_iena = "";
		$number_von = "";
		$number_ch = "";
		$number_gb = "";
		$number_chf = "";

		foreach($content as $cur){
			if ($cur[1] == "USD") { // 840
				$dollar = $cur[3];
				$number_dollar = $cur[2];
			}
			if ($cur[1] == "EUR") { // 978
				$euro = $cur[3];
				$number_euro = $cur[2];
			}
			if ($cur[1] == "JPY") { // 392
				$iena = $cur[3];
				$number_iena = $cur[2];
			}
			if ($cur[1] == "KRW") { // 410
				$von = $cur[3];
				$number_von = $cur[2];
			}
		    if ($cur[1] == "CNY") { // 156
				$ch = $cur[3];
				$number_ch = $cur[2];
			}
		    if ($cur[1] == "GBP") { // 826
				$gb = $cur[3];
				$number_gb = $cur[2];
			}
			if ($cur[1] == "CHF") { // 756
				$chf = $cur[3];
				$number_chf = $cur[2];
			}
		}
	    if ($status == 'enabled') {
	    	$html_cache = '';
	    	$error = '';
			/* If show valute was selected, display valute. */
			if ($edit_rub == 'enabled') {
		 	    $html_cache .= "<div>\n".
		 	     "<form name='raschets' action='#' method='post'>".
			     "<input type='checkbox' name='enable_edit-fields' value='0' onclick=\"edit_input(this, 'number');\">".__('Изменить "Кол-во"', 'ccr');
		 	    $number_dollar = "<input type='text' id='number_0' name='number' onkeyup=\"doLoadUp('raschets', {$number_dollar}, 0)\" class='input' value='{$number_dollar}' maxlength='10' disabled='disabled'>";
			    $number_dollar .=  "<input type='hidden' id='result_0' name='result_0' value='{$dollar}' readonly='readonly'>"; 
		 	    $number_euro = "<input type='text' id='number_1' name='number' onkeyup=\"doLoadUp('raschets', {$number_euro}, 1)\" class='input' value='{$number_euro}' maxlength='10' disabled='disabled'>";
		 	    $number_euro .=  "<input type='hidden' id='result_1' name='result_1' value='{$euro}' readonly='readonly'>";
			    $number_iena = "<input type='text' id='number_2' name='number' onkeyup=\"doLoadUp('raschets', {$number_iena}, 2)\" class='input' value='{$number_iena}' maxlength='10' disabled='disabled'>";
		 	    $number_iena .=  "<input type='hidden' id='result_2' name='result_2' value='{$iena}' readonly='readonly'>";
			    $number_von = "<input type='text' id='number_3' name='number' onkeyup=\"doLoadUp('raschets', {$number_von}, 3)\" class='input' value='{$number_von}' maxlength='10' disabled='disabled'>";
		 	    $number_von .=  "<input type='hidden' id='result_3' name='result_3' value='{$von}' readonly='readonly'>";
			    $number_ch = "<input type='text' id='number_4' name='number' onkeyup=\"doLoadUp('raschets', {$number_ch}, 4)\" class='input' value='{$number_ch}' maxlength='10' disabled='disabled'>";
		 	    $number_ch .=  "<input type='hidden' id='result_4' name='result_4' value='{$ch}' readonly='readonly'>";
			    $number_gb = "<input type='text' id='number_5' name='number' onkeyup=\"doLoadUp('raschets', {$number_gb}, 5)\" class='input' value='{$number_gb}' maxlength='10' disabled='disabled'>";
		 	    $number_gb .=  "<input type='hidden' id='result_5' name='result_5' value='{$gb}' readonly='readonly'>";
			    $number_chf = "<input type='text' id='number_6' name='number' onkeyup=\"doLoadUp('raschets', {$number_chf}, 6)\" class='input' value='{$number_chf}' maxlength='10' disabled='disabled'>";
		 	    $number_chf .=  "<input type='hidden' id='result_6' name='result_6' value='{$chf}' readonly='readonly'>";

			    $dollar = "<input type='text' class='input' name='view_0' value='{$dollar}' readonly='readonly'>";
			    $euro = "<input type='text' class='input' name='view_1' value='{$euro}' readonly='readonly'>";
			    $iena = "<input type='text' class='input' name='view_2' value='{$iena}' readonly='readonly'>";
			    $von = "<input type='text' class='input' name='view_3' value='{$von}' readonly='readonly'>";
			    $ch = "<input type='text' class='input' name='view_4' value='{$ch}' readonly='readonly'>";
			    $gb = "<input type='text' class='input' name='view_5' value='{$gb}' readonly='readonly'>";
			    $chf = "<input type='text' class='input' name='view_6' value='{$chf}' readonly='readonly'>";
			}
			$html_cache .= "<table border='1' width='100%' align='center' class='kurs'>\n";
			$date = $this->get_date('d.m.Y');
			/* Display name from widget settings if one was input. */
			if ( $name )
		   		$html_cache .= sprintf( '<caption>' . __('%1$s '.$date, 'ccr') . '</caption>', $name );
		    $html_cache .= "<thead><tr><th>{$currency}</th><th>{$number}</th><th><img src='".WP_PLUGIN_URL ."/currency-converter-rub/images/".$images."/ru.gif' alt='".__('Рубль', 'ccr')."' border='0'>&nbsp;RUB<br/>{$for_sale}</th></tr></thead>\n";
		    $html_cache .= "<tbody>";
		    if ( $show_us )
		       $html_cache .= "<tr><td id='cvet'><img src='".WP_PLUGIN_URL ."/currency-converter-rub/images/".$images."/us.gif' alt='".__('Доллар США', 'ccr')."' border='0'>&nbsp;USD<td>{$number_dollar}</td><td>{$dollar}</td></tr>\n";
		    if ( $show_eu )
		       $html_cache .= "<tr><td id='cvet'><img src='".WP_PLUGIN_URL ."/currency-converter-rub/images/".$images."/eu.gif' alt='".__('Евро', 'ccr')."' border='0'>&nbsp;EUR</td><td>{$number_euro}</td><td>{$euro}</td></tr>\n";
		    if ( $show_kr )
		       $html_cache .= "<tr><td id='cvet'><img src='".WP_PLUGIN_URL ."/currency-converter-rub/images/".$images."/kr.gif' alt='".__('Вон Республики Корея', 'ccr')."' border='0'>&nbsp;KRW</td><td>{$number_von}</td><td>{$von}</td></tr>\n";
		    if ( $show_jp )
		       $html_cache .= "<tr><td id='cvet'><img src='".WP_PLUGIN_URL ."/currency-converter-rub/images/".$images."/jp.gif' alt='".__('Японских иен', 'ccr')."' border='0'>&nbsp;JPY</td><td>{$number_iena}</td><td>{$iena}</td></tr>\n";
		    if ( $show_gb )
		       $html_cache .= "<tr><td id='cvet'><img src='".WP_PLUGIN_URL ."/currency-converter-rub/images/".$images."/uk.gif' alt='".__('Фунт стерлингов Соединенного королевства', 'ccr')."' border='0'>&nbsp;GBP</td><td>{$number_gb}</td><td>{$gb}</td></tr>\n";
		    if ( $show_chf )
		       $html_cache .= "<tr><td id='cvet'><img src='".WP_PLUGIN_URL ."/currency-converter-rub/images/".$images."/ch.gif' alt='".__('Швейцарский франк', 'ccr')."' border='0'>&nbsp;CHF</td><td>{$number_chf}</td><td>{$chf}</td></tr>\n";
		    if ( $show_ch )
		       $html_cache .= "<tr><td id='cvet'><img src='".WP_PLUGIN_URL ."/currency-converter-rub/images/".$images."/cn.gif' alt='".__('Китайских юаней', 'ccr')."' border='0'>&nbsp;CNY</td><td>{$number_ch}</td><td>{$ch}</td></tr>\n";
		    $html_cache .= "</tbody>\n";
		    $html_cache .= "</table>\n";
		    if ( $edit_rub == 'enabled') {
		       $html_cache .= "</form>";
		       $html_cache .= "</div>\n";
		    }
	    } else { $html_cache .= '<p>'.__('Плагин курс валют отключен!', 'ccr').'</p>'; $error = 1; }

		/* After widget (defined by themes). */
		$html_cache .= $after_widget;

	  	// Update cache file and time update
	  	//$get_mass_widget = get_option('widget_ccr-widget');
	  	//$time_update = $get_mass_widget[$widget_id]['time_update'];
		$timeNow =  time();
		$ccr_time_update = 'ccr_time_update'; 
		$time_update =  get_transient($ccr_time_update);
		if (!$time_update){
			set_transient($ccr_time_update, $timeNow, 60*60*12);
			update_option($ccr_time_update, $timeNow);
	    } else {
				$diff = $timeNow - $time_update;
				$days = floor($diff / (3600*24));
				$hours = floor(($diff - ($days * 3600 * 24)) / 3600);
   				if ($hours >= 3) {
					$this->put_ht_fc($html_cache);
					$this->put_ht_fc($content, "shortcode");
  				}
		}

			/* Shows that the plugin is disabled */
			if ($error == 1) { echo $html_cache;
			} else {
				$upload_dir = wp_upload_dir();
	  			$cache_ccr_dir = $upload_dir['basedir'].'/cache_ccr';
				$file_read = $cache_ccr_dir.'/'.$this->get_date('Ymd_N').'.txt';
				if ( !is_file($file_read) ) $error = 1;
				if ( $error!=1 && filesize($file_read) > 0 ) {
				//if ( filesize($file_read) > 0 ) {
					$f = fopen($file_read, 'r');
					$content = fread($f, filesize($file_read));
					echo $content.'<div style="display: none;">Use data from the cache</div>';
				} else {
					echo $html_cache.'<div style="display: none;">Uses data directly from the website</div>';
				}
			}
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title']    = strip_tags( $new_instance['title'] );
		$instance['name']     = strip_tags( $new_instance['name'] );
		$instance['currency'] = strip_tags( $new_instance['currency'] );
		$instance['number']   = strip_tags( $new_instance['number'] );
		$instance['for_sale'] = strip_tags( $new_instance['for_sale'] );

		/* No need to strip tags for sex and show_sex. */
		$instance['status']      = $new_instance['status'];
		$instance['edit_rub']    = $new_instance['edit_rub'];
		$instance['images']      = $new_instance['images'];
		$instance['timezone']    = $new_instance['timezone'];
		$instance['show_us']     = $new_instance['show_us'];
		$instance['show_eu']     = $new_instance['show_eu'];
		$instance['show_kr']     = $new_instance['show_kr'];
		$instance['show_jp']     = $new_instance['show_jp'];
		$instance['show_ch']     = $new_instance['show_ch'];
		$instance['show_gb']     = $new_instance['show_gb'];
		$instance['show_chf']    = $new_instance['show_chf'];

		return $instance;
	}

	/**
	 * [Shortcode] Currency Conversion
	 *
	 */

	function currency_conversion($atts) {
		extract(shortcode_atts(array(
			'from'   => 'USD',
			'to' 	 => 'RUB',
			'amount' => '1',
			'time'   => '21600'
		), $atts));

		$currencytransient = 'ccr_' . $from . $to . $amount;
		$cachedresult =  get_transient($currencytransient);

		if ($cachedresult !== false ) {
			return $cachedresult;
		} else { 
/*			$error = 0;
			$upload_dir = wp_upload_dir();  $curr = "";
			$cache_ccr_dir = $upload_dir['basedir'].'/cache_ccr';
			$file_read = $cache_ccr_dir.'/shortcode_'.Currency_Converter_Rub_Widget::get_date('Ymd_N').'.txt';
			if ( !is_file($file_read) ) $error = 1;
			if ( $error!=1 && filesize($file_read) > 0 ) {
				$f = fopen($file_read, 'r');
				while (!feof($f)){
						$line = fgets($f, 4096);
						$cur = explode("|", $line);
						if ($cur[0] == $from) {
							$c = $cur[1];
							$curr = $amount * $c;
							$curr.= "из кэша";
						}
				}
				fclose($f);
			} else {*/
				$content = Currency_Converter_Rub_Widget::get_content();
				foreach($content as $cur){
						if ($cur[1] == $from) {
							$c = $cur[3];
							$curr = $amount * $c;
						}
				}
/*			}*/

			/*
			  <span curr="2" style="">38914 руб.</span>
			  <span style="display:none;" curr="1">38914 руб.</span>
			  <span style="display:none;" curr="3">942 EURO</span>
			*/

			/* View shortcode text */

			$view_sc_text = '<span curr="2" style="">'.$curr.' RUB.</span> <span curr="3" style="">'.$amount." ".$from.'</span>';
			set_transient($currencytransient, $view_sc_text, 60*60*12);
			update_option($currencytransient, $view_sc_text);
			return $view_sc_text;
		} /* end else */
    }

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {
		#$this->load_textdomain();
		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Курс валют', 'ccr'), 'name' => __('Ежедневный курс иностранной валюты ЦБ РФ на ', 'ccr'), 'currency' => __('Валюта', 'ccr'), 'number' => __('Кол-во', 'ccr'), 'for_sale' => __('Продажа (руб.)', 'ccr'), 'images' => 'big', 'status' => 'enabled', 'edit_rub' => 'enabled', 'show_us' => 1, 'show_eu' => 1, 'show_kr' => 1, 'show_jp' => 1, 'show_gb' => 1, 'show_ch' => 1 );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Заголовок:', 'ccr'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- Текст: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e('Текст:', 'ccr'); ?></label>
			<input id="<?php echo $this->get_field_id( 'name' ); ?>" name="<?php echo $this->get_field_name( 'name' ); ?>" value="<?php echo $instance['name']; ?>" style="width:100%;" />
		</p>

		<!-- Left block: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'currency' ); ?>"><?php _e('Левый блок:', 'ccr'); ?></label>
			<input id="<?php echo $this->get_field_id( 'currency' ); ?>" name="<?php echo $this->get_field_name( 'currency' ); ?>" value="<?php echo $instance['currency']; ?>" style="width:100%;" />
		</p>

		<!-- Middle block: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e('Средний блок:', 'ccr'); ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" value="<?php echo $instance['number']; ?>" style="width:100%;" />
		</p>


		<!-- Right block: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'for_sale' ); ?>"><?php _e('Правый блок:', 'ccr'); ?></label>
			<input id="<?php echo $this->get_field_id( 'for_sale' ); ?>" name="<?php echo $this->get_field_name( 'for_sale' ); ?>" value="<?php echo $instance['for_sale']; ?>" style="width:100%;" />
		</p>


		<!-- Set of images: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'images' ); ?>"><?php _e('Набор картинок:', 'ccr'); ?></label>
			<select id="<?php echo $this->get_field_id( 'images' ); ?>" name="<?php echo $this->get_field_name( 'images' ); ?>" class="widefat" style="width:100%;">
				<option value="big" <?php selected( $instance['images'], 'big' ); ?>><?php _e('Большие', 'ccr'); ?></option>
				<option value="middle" <?php selected( $instance['images'], 'middle' ); ?>><?php _e('Средние', 'ccr'); ?></option>
				<option value="small" <?php selected( $instance['images'], 'small' ); ?>><?php _e('Маленькие', 'ccr'); ?></option>
			</select>
		</p>

		<!-- Status: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'status' ); ?>"><?php _e('Состояние:', 'ccr'); ?></label>
			<select id="<?php echo $this->get_field_id( 'status' ); ?>" name="<?php echo $this->get_field_name( 'status' ); ?>" class="widefat" style="width:100%;">
				<option value="enabled" <?php selected( $instance['status'], 'enabled' ); ?>><?php _e('Включено', 'ccr'); ?></option>
				<option value="disabled" <?php selected( $instance['status'], 'disabled' ); ?>><?php _e('Выключено', 'ccr'); ?></option>
			</select>
		</p>

		<!-- Status: Edit Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'edit_rub' ); ?>"><?php _e('Включить он-лайн расчет:', 'ccr'); ?></label>
			<select id="<?php echo $this->get_field_id( 'edit_rub' ); ?>" name="<?php echo $this->get_field_name( 'edit_rub' ); ?>" class="widefat" style="width:100%;">
				<option value="enabled" <?php selected( $instance['edit_rub'], 'enabled' ); ?>><?php _e('Включено', 'ccr'); ?></option>
				<option value="disabled" <?php selected( $instance['edit_rub'], 'disabled' ); ?>><?php _e('Выключено', 'ccr'); ?></option>
			</select>
		</p>

		<!-- Timezone: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'status' ); ?>"><?php _e('Timezone:', 'ccr'); ?></label> 
			<select id="<?php echo $this->get_field_id( 'status' ); ?>" name="<?php echo $this->get_field_name( 'timezone' ); ?>" class="widefat" style="width:300px;">
			<?php
				echo "<option value='-12.0' ".(selected( $instance['timezone'], '-12.0' )).">(GMT -12:00) Eniwetok, Kwajalein</option>";
			    echo "<option value='-11.0' ".(selected( $instance['timezone'], '-11.0' )).">(GMT -11:00) Midway Island, Samoa</option>";
			    echo "<option value='-10.0' ".(selected( $instance['timezone'], '-10.0' )).">(GMT -10:00) Hawaii</option>";
			    echo "<option value='-9.0' ".(selected( $instance['timezone'], '-9.0' )).">(GMT -9:00) Alaska</option>";
			    echo "<option value='-8.0' ".(selected( $instance['timezone'], '-8.0' )).">(GMT -8:00) Pacific Time (US &amp; Canada)</option>";
			    echo "<option value='-7.0' ".(selected( $instance['timezone'], '-7.0' )).">(GMT -7:00) Mountain Time (US &amp; Canada)</option>";
			    echo "<option value='-6.0' ".(selected( $instance['timezone'], '-6.0' )).">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>";
			    echo "<option value='-5.0' ".(selected( $instance['timezone'], '-5.0' )).">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>";
			    echo "<option value='-4.0' ".(selected( $instance['timezone'], '-4.0' )).">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>";
			    echo "<option value='-3.5' ".(selected( $instance['timezone'], '-3.5' )).">(GMT -3:30) Newfoundland</option>";
			    echo "<option value='-3.0' ".(selected( $instance['timezone'], '-3.0' )).">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>";
			    echo "<option value='-2.0' ".(selected( $instance['timezone'], '-2.0' )).">(GMT -2:00) Mid-Atlantic</option>";
			    echo "<option value='-1.0' ".(selected( $instance['timezone'], '-1.0' )).">(GMT -1:00 hour) Azores, Cape Verde Islands</option>";
			    echo "<option value='0.0' ".(selected( $instance['timezone'], '0.0' )).">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>";
			    echo "<option value='1.0' ".(selected( $instance['timezone'], '1.0' )).">(GMT +1:00) Brussels, Copenhagen, Madrid, Paris</option>";
			    echo "<option value='2.0' ".(selected( $instance['timezone'], '2.0' )).">(GMT +2:00) Kaliningrad, South Africa</option>";
			    echo "<option value='3.0' ".(selected( $instance['timezone'], '3.0' )).">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>";
			    echo "<option value='3.5' ".(selected( $instance['timezone'], '3.5' )).">(GMT +3:30) Tehran</option>";
			    echo "<option value='4.0' ".(selected( $instance['timezone'], '4.0' )).">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>";
			    echo "<option value='4.5' ".(selected( $instance['timezone'], '4.5' )).">(GMT +4:30) Kabul</option>";
			    echo "<option value='5.0' ".(selected( $instance['timezone'], '5.0' )).">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>";
			    echo "<option value='5.5' ".(selected( $instance['timezone'], '5.5' )).">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>";
			    echo "<option value='5.75' ".(selected( $instance['timezone'], '5.75' )).">(GMT +5:45) Kathmandu</option>";
			    echo "<option value='6.0' ".(selected( $instance['timezone'], '6.0' )).">(GMT +6:00) Almaty, Dhaka, Colombo</option>";
			    echo "<option value='7.0' ".(selected( $instance['timezone'], '7.0' )).">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>";
			    echo "<option value='8.0' ".(selected( $instance['timezone'], '8.0' )).">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>";
			    echo "<option value='9.0' ".(selected( $instance['timezone'], '9.0' )).">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>";
			    echo "<option value='9.5' ".(selected( $instance['timezone'], '9.5' )).">(GMT +9:30) Adelaide, Darwin</option>";
			    echo "<option value='10.0' ".(selected( $instance['timezone'], '10.0' )).">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>";
			    echo "<option value='11.0' ".(selected( $instance['timezone'], '11.0' )).">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>";
			    echo "<option value='12.0' ".(selected( $instance['timezone'], '12.0' )).">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option>";
			?>  
			</select>
		</p>

		<!-- Show US? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['show_us'], 1 ); ?> id="<?php echo $this->get_field_id( 'show_us' ); ?>" name="<?php echo $this->get_field_name( 'show_us' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_us' ); ?>"><?php _e('Включить US?', 'ccr'); ?></label>
		</p>

		<!-- Show EU? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['show_eu'], 1 ); ?> id="<?php echo $this->get_field_id( 'show_eu' ); ?>" name="<?php echo $this->get_field_name( 'show_eu' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_eu' ); ?>"><?php _e('Включить EU?', 'ccr'); ?></label>
		</p>

		<!-- Show KR? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['show_kr'], 1 ); ?> id="<?php echo $this->get_field_id( 'show_kr' ); ?>" name="<?php echo $this->get_field_name( 'show_kr' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_kr' ); ?>"><?php _e('Включить KR?', 'ccr'); ?></label>
		</p>
		
		<!-- Show JP? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['show_jp'], 1 ); ?> id="<?php echo $this->get_field_id( 'show_jp' ); ?>" name="<?php echo $this->get_field_name( 'show_jp' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_jp' ); ?>"><?php _e('Включить JP?', 'ccr'); ?></label>
		</p>
		
		<!-- Show CH? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['show_ch'], 1 ); ?> id="<?php echo $this->get_field_id( 'show_ch' ); ?>" name="<?php echo $this->get_field_name( 'show_ch' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_ch' ); ?>"><?php _e('Включить CH?', 'ccr'); ?></label>
		</p>
		
		<!-- Show CHF? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['show_chf'], 1 ); ?> id="<?php echo $this->get_field_id( 'show_chf' ); ?>" name="<?php echo $this->get_field_name( 'show_chf' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_chf' ); ?>"><?php _e('Включить CHF?', 'ccr'); ?></label>
		</p>
		<!-- Show GB? Checkbox -->
		<p>
			<input class="checkbox" type="checkbox" value="1" <?php checked( $instance['show_gb'], 1 ); ?> id="<?php echo $this->get_field_id( 'show_gb' ); ?>" name="<?php echo $this->get_field_name( 'show_gb' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_gb' ); ?>"><?php _e('Включить GB?', 'ccr'); ?></label>
		</p>
	<?php
	}
}
     
	/**
	 * Register shortcode.
	 *  ( 'baztag', array('MyPlugin', 'baztag_func') );
	 */
 
	add_shortcode('ccr', array('Currency_Converter_Rub_Widget', 'currency_conversion') );
     
    /**
     * Register languages.
     *
     */
    define( 'CCR_BASEDIR', dirname( plugin_basename(__FILE__) ) );
    #define( 'CCR_TEXTDOMAIN', 'ccr' );  //future
 
    add_action( 'plugins_loaded', 'ccr_wp_load_textdomain' );
  
    function ccr_wp_load_textdomain() {
	     load_plugin_textdomain( 'ccr', false, CCR_BASEDIR.'/languages');
    }
  
    /*
     * register with hook 'wp_print_styles'
     */
    add_action('wp_print_styles', 'add_ccr_stylesheet');

    /*
     * Enqueue style-file, if it exists.
     */

    function add_ccr_stylesheet() {
        $myStyleUrl = WP_PLUGIN_URL . '/currency-converter-rub/currency-converter-rub.css';
        $myStyleFile = WP_PLUGIN_DIR . '/currency-converter-rub/currency-converter-rub.css';
        if ( file_exists($myStyleFile) ) {
            wp_register_style('CurrencyConverter', $myStyleUrl);
            wp_enqueue_style( 'CurrencyConverter');
        }
    }
    
    /*
     * register with hook 'init'
     */
    add_action('init', 'add_ccr_script');
    
    /*
     * Enqueue script-file, if it exists.
     */

    function add_ccr_script() {
        $myJsUrl = WP_PLUGIN_URL . '/currency-converter-rub/currency-converter-rub.js';
        $myJsFile = WP_PLUGIN_DIR . '/currency-converter-rub/currency-converter-rub.js';
        if ( file_exists($myJsFile) ) {
            wp_register_script('CurrencyConverter', $myJsUrl);
            wp_enqueue_script( 'CurrencyConverter');
        }
    }
    
    
?>