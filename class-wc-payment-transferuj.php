<?php
/**
  /**
 * Transferuj.pl Woocommerce payment module
 *
 * @author Transferuj.pl
 *
 * Plugin Name: Transferuj.pl Woocommerce payment module
 * Plugin URI: http://www.transferuj.pl
 * Description: Brama płatności Transferuj.pl do WooCommerce.
 * Author: Transferuj.pl
 * Author URI: http://www.transferuj.pl
 * Version: 1.1.3
 */


add_action('plugins_loaded', 'init_transferuj_gateway');

function init_transferuj_gateway() {

    

    
    
    if (!class_exists('WC_Payment_Gateway')){
        add_action( 'admin_init', 'child_plugin_has_parent_plugin' );
function child_plugin_has_parent_plugin() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        add_action( 'admin_notices', 'child_plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function child_plugin_notice(){
    ?><div class="error"><p>Moduł płatności Transferuj.pl wymaga zainstalowanej wtyczki Woocommerce, którą można pobrać <a target="blank" href="https://wordpress.org/plugins/woocommerce/">tutaj</a></p></div><?php
}
        return;
    }
        
    
    

    class WC_Gateway_Transferuj extends WC_Payment_Gateway {

        /**
         * Constructor for the gateway.
         *
         * @access public
         *
         * 
         * @global type $woocommerce
         */
        
       
        
        
        public function __construct() {
          
    // Visual Attributes is activated
 
            global $woocommerce;
            
            
            $this->id = __('transferuj', 'woocommerce');
            $this->icon = apply_filters('woocommerce_transferuj_icon', plugins_url('images/logo-transferuj-50x25.png', __FILE__));
            $this->has_fields = true;

            $this->method_title = __('Transferuj.pl', 'woocommerce');
            $this->notify_link = str_replace('https:', 'http:', add_query_arg('wc-api', 'WC_Gateway_Transferuj', home_url('/')));

            // Add Transferuj.pl as payment gateway
            add_filter('woocommerce_payment_gateways', array($this, 'add_transferuj_gateway'));

            // Load the settings.
            $this->init_form_fields();
            $this->init_settings();

            


            // Define user set variables
            $this->title = $this->get_option('title');
            $this->kwota_doplaty = $this->get_option('kwota_doplaty');
            $this->description = $this->get_option('description');
            $this->seller_id = $this->get_option('seller_id');
            $this->security_code = $this->get_option('security_code');
            $this->bank_list = $this->get_option('bank_list');
            $this->doplata = $this->get_option('doplata');
            $this->scroll = $this->get_option('scroll');
            $this->status = $this->get_option('status');

            // Actions
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            
            //obliczanie koszyka na nowo jesli jest doplata za Transferuj
            if ($this->doplata != 0) {
                add_action('woocommerce_cart_calculate_fees', array($this, 'add_fee_t'), 99);
                add_action('woocommerce_review_order_after_submit', array($this, 'print_autoload_js'));
            }
            // Payment listener/API hook
            add_action('woocommerce_api_wc_gateway_transferuj', array($this, 'gateway_communication'));

            add_filter('payment_fields', array($this, 'payment_fields'));
         
        }
       
        
        
        function add_fee_t() {
            //dodawanie do zamowienia oplaty za Transferuj
            if ((WC()->session->chosen_payment_method ) == 'transferuj') {

                global $woocommerce;
                if ($this->doplata != 0) {
                    if ($this->doplata == 1) {

                        $doplata = $this->kwota_doplaty;
                    }

                    if ($this->doplata == 2) {

                        $kwota = $woocommerce->cart->cart_contents_total + $woocommerce->cart->shipping_total;
                        $doplata = $kwota * $this->kwota_doplaty / 100;
                    }

                    $woocommerce->cart->add_fee('Opłata za płatność online', $doplata, true, 'standard');
                }
            }
        }

        function print_autoload_js() { //przeladowanie koszyka zamowienia po wybraniu platnosci Transferuj
            ?><script type="text/javascript">
                                jQuery(document).ready(function ($) {
                                    $(document.body).on('change', 'input[name="payment_method"]', function () {
                                        $('body').trigger('update_checkout');
                                        $.ajax($fragment_refresh);
                                    });
                                });
            </script><?php
        }

        /**
         * Generates box with gateway name and description, terms acceptance checkbox and channel list
         */
        function payment_fields() {



            if ($this->get_option('doplata') == 1) {

                echo "<p>Za korzystanie z płatności online sprzedawca dolicza:<b> " . $this->get_option('kwota_doplaty') . " zł</b> </p>";
            }
            
            if ($this->get_option('doplata') == 2) {
                //global $woocommerce;
                $kwota = WC()->cart->cart_contents_total + WC()->cart->shipping_total;
                $kwota = $kwota * ($this->get_option('kwota_doplaty')) / 100;
                $kwota = doubleval($kwota);
                $kwota=  number_format($kwota,2);
                echo "<p>Za korzystanie z płatności online sprzedawca dolicza:<b> " . $kwota . " zł</b> </p>";
            }
            if ($this->get_option('scroll') == 0){
                $jQuery = '$jQuery';
                $scroll="$jQuery('html, body').animate({ scrollTop: n }, 500)";
            }
            else{
                
                $scroll='';
            }

            if ($this->get_option('bank_list') == "0" && $this->get_option('bank_view') == "0") {
                $jQuery = '$jQuery';
                echo <<<FORM
             
              <input type="hidden" id="channel"  name="kanal" value=" ">
            <style type="text/css">                 
            .checked_v {
                box-shadow: 0px 0px 10px 3px #15428F !important;;

            }
            .channel {
                display: inline-block; 
                width: 130px; 
                height:63px; 
                margin: 2px 13px 0px 0; 
                text-align:center;
            }
             </style>   
            
            <script type="text/javascript">
                function ShowChannelsCombo()
                {
                    var $jQuery = jQuery.noConflict();
                    var str = '<div  style="margin:20px 0 15px 0"  id="kanal"><label>Wybierz bank:</label></div>';

                    for (var i = 0; i < tr_channels.length; i++) {
                        str += '<div   class="channel" ><img id="' + tr_channels[i][0] + '" class="check" style="height: 80%" src="' + tr_channels[i][3] + '"></div>';
                    }

                    var container = jQuery("#kanaly_v");
                    container.append(str);
                    
                    
                      jQuery(document).ready(function () {
                        
                        jQuery(".check").click(function () {
                            
                            $jQuery(".check").removeClass("checked_v");
                            $jQuery(this).addClass("checked_v");
                            var n = $jQuery(document).height();
                            $scroll
                            var kanal = 0;
                            kanal = jQuery(this).attr("id");
                             $jQuery('#channel').val(kanal);

                         });
                        });
                     


                }
                 jQuery.getScript("https://secure.transferuj.pl/channels-{$this->seller_id}0.js", function () {
                    ShowChannelsCombo()
                });
            </script>
            <div style="background: white " id="kanaly_v"></div>
            <div id="descriptionBox">{$this->description}</div> <br/>
            <div id="termsCheckboxBox">
                <input type="checkbox" id="termsCheckbox" name="terms_t">
                    <a href="https://secure.transferuj.pl/regulamin.pdf" target="blank">
                                Akceptuję warunki regulaminu korzystania z serwisu Transferuj.pl
                    </a>
                </input> <br/>
            </div>
            

FORM;
                return;
            } else if ($this->get_option('bank_list') == "0" && $this->get_option('bank_view') == "1") {
                // get script with channels from Transferuj.pl
                $channels_url = "https://secure.transferuj.pl/channels-" . $this->seller_id . "0.js";
                $JSON = file_get_contents($channels_url);

                // parse the channel list
                $pattern = "!\['(?<id>\d{1,2})','(?<name>.+)','(.+)','(.+)','!";
                preg_match_all($pattern, $JSON, $matches);

                // create list of channels
                $channels = '<select class="channelSelect" id ="channelSelect" name="kanal">';
                for ($i = 0; $i < count($matches['id']); $i++) {
                    $channels .= '<option value="' . $matches['id'][$i] . '">' .
                            $matches['name'][$i] . "</option>";
                }
                $channels .= '</select>';
            // generate box
            echo <<<FORM
			
            <div id="descriptionBox">{$this->description}</div> <br/>
            <div id="termsCheckboxBox">
                <input type="checkbox" id="termsCheckbox" name="terms_t">
                    <a href="https://secure.transferuj.pl/regulamin.pdf" target="blank">
                                Akceptuję warunki regulaminu korzystania z serwisu Transferuj.pl
                    </a>
                </input> <br/>
            </div>
            <div id="channelSelectBox">{$channels}</div>

FORM;
                           
                }
            
        }

        /**
         * Adds Transferuj.pl payment gateway to the list of installed gateways
         * @param $methods
         * @return array
         */
        function add_transferuj_gateway($methods) {
            $methods[] = 'WC_Gateway_Transferuj';
            return $methods;
        }

        /**
         * Generates admin options
         */
        public function admin_options() {
            ?>
            <h2><?php _e('Transferuj.pl', 'woocommerce'); ?></h2>
            <table class="form-table">
            <?php $this->generate_settings_html(); ?>
            </table> 
            <style type="text/css">                 
                .checked_v {
                    box-shadow: 0px 0px 10px 3px #15428F !important;;

                }
                .channel {
                    display: inline-block; 
                    width: 130px; 
                    height:63px; 
                    margin: 2px 13px 0px 0; 
                    text-align:center;
                }
            </style> 
            <script type="text/javascript">




                jQuery(document).ready(function () {

                    function bank_list() {
                        var a = jQuery("#woocommerce_transferuj_bank_list option:selected").val();

                        if (a == "1") {
                            jQuery('label[for="woocommerce_transferuj_bank_view"]').attr("style", "visibility: hidden ")
                            jQuery("#woocommerce_transferuj_bank_view").attr("style", "visibility: hidden")
                            jQuery('label[for="woocommerce_transferuj_scroll"]').attr("style", "visibility: hidden ")
                            jQuery("#woocommerce_transferuj_scroll").attr("style", "visibility: hidden")

                        }
                        else {
                            jQuery('label[for="woocommerce_transferuj_bank_view"]').attr("style", "visibility: ")
                            jQuery("#woocommerce_transferuj_bank_view").attr("style", "visibility: ")
                             var a = jQuery("#woocommerce_transferuj_bank_view option:selected").val();

                            if (a == "1") {
                            jQuery('label[for="woocommerce_transferuj_scroll"]').attr("style", "visibility: hidden ")
                            jQuery("#woocommerce_transferuj_scroll").attr("style", "visibility: hidden")

                              }
                             else {
                            jQuery('label[for="woocommerce_transferuj_scroll"]').attr("style", "visibility: ")
                            jQuery("#woocommerce_transferuj_scroll").attr("style", "visibility: ")


                        }    

                        }


                    }
                    bank_list();
                    jQuery("#woocommerce_transferuj_bank_list").change(function () {

                        bank_list();
                    });
                    
                     jQuery("#woocommerce_transferuj_bank_view").change(function () {

                        var a = jQuery("#woocommerce_transferuj_bank_view option:selected").val();

                        if (a == "1") {
                            jQuery('label[for="woocommerce_transferuj_scroll"]').attr("style", "visibility: hidden ")
                            jQuery("#woocommerce_transferuj_scroll").attr("style", "visibility: hidden")

                        }
                        else {
                            jQuery('label[for="woocommerce_transferuj_scroll"]').attr("style", "visibility: ")
                            jQuery("#woocommerce_transferuj_scroll").attr("style", "visibility: ")


                        }
                    });


                    jQuery("#woocommerce_transferuj_doplata").change(function () {

                        if (jQuery("#woocommerce_transferuj_doplata").val() == "0") {

                            jQuery("#woocommerce_transferuj_kwota_doplaty").attr("style", "visibility: hidden")

                        }
                        else {
                            if (jQuery("#woocommerce_transferuj_doplata").val() == "2") {
                                alert("Podaj jaki % kwoty zamówienia ma zostać doliczony do zapłaty")
                            }
                            jQuery("#woocommerce_transferuj_kwota_doplaty").attr("style", "visibility: visible ")
                        }
                    });

                });




            </script>



            <?php
        }

        /**
         * Initialise Gateway Settings Form Fields
         *
         * @access public
         * @return void
         */
        function init_form_fields() {

            $ukryj_d = 'visibility: visible';
            $ukryj_k = 'visibility: visible';
            $ukryj_s = 'visibility: visible';

            if ($this->get_option('doplata') == '0') {
                $ukryj_d = 'visibility: hidden';
            }
            if ($this->get_option('bank_list') == '1') {
                $ukryj_k = 'visibility: hidden';
            }
             if ($this->get_option('bank_view') == '1') {
                $ukryj_s = 'visibility: hidden';
            }

            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Włącz/Wyłącz', 'woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Włącz metodę płatności przez Transferuj.pl.', 'woocommerce'),
                    'default' => 'yes',
                    'description' => sprintf( __( ' <a href="%s" TARGET="_blank">Zarejestruj konto w systemie Transferuj.pl</a>.', 'woocommerce' ), 'https://secure.transferuj.pl/panel/rejestracja.html' ),
                ),
               
                'title' => array(
                    'title' => __('Nazwa', 'woocommerce'),
                    'type' => 'text',
                    'default' => __('Transferuj.pl', 'woocommerce'),
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => __('Opis', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('Ustawia opis bramki, który widzi użytkownik przy tworzeniu zamówienia.', 'woocommerce'),
                    'default' => __('Zapłać przez Transferuj.pl.', 'woocommerce')
                ),
                'seller_id' => array(
                    'title' => __('ID sprzedawcy', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Twoje ID sprzedawcy w systemie Transferuj.pl. Liczba co najmniej czterocyfrowa (może być pięciocyfowa), np. 12345', 'woocommerce'),
                    'default' => __('0', 'woocommerce'),
                    'desc_tip' => true,
                ),
                'security_code' => array(
                    'title' => __('Kod bezpieczeństwa', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Kod bezpieczeństwa Twojego konta na Transferuj.pl.', 'woocommerce'),
                    'default' => __('0', 'woocommerce'),
                    'desc_tip' => true,
                ),
                'status' => array(
                    'title' => __('Status zamówienia po opłaceniu w Transferuj.pl', 'woocommerce'),
                    'type' => 'select',
                    'default' => '0',
                    'options' => array(
                        '0' => __('W trakcie realizacji', 'woocommerce'),
                        '1' => __('Zrealizowane', 'woocommerce'),
                    ),
                ),
                'doplata' => array(
                    'title' => __('Dopłata doliczana za korzystanie z Transferuj', 'woocommerce'),
                    'type' => 'select',
                    'default' => '0',
                    'options' => array(
                        '0' => __('NIE', 'woocommerce'),
                        '1' => __('PLN', 'woocommerce'),
                        '2' => __('%', 'woocommerce'),
                    ),
                ),
                'kwota_doplaty' => array(
                    'title' => __('Kwota dopłaty', 'woocommerce'),
                    'type' => "text",
                    'css' => $ukryj_d,
                    'description' => __('Kwota jaka zostanie doliczona do zamówienia. Jako separator liczb należy wykorzystać kropkę', 'woocommerce'),
                    'default' => __('0', 'woocommerce'),
                    'desc_tip' => true,
                ),
                'bank_list' => array(
                    'title' => __('Włącz wybór banku na stronie sklepu', 'woocommerce'),
                    'type' => 'select',
                    'default' => '0',
                    'options' => array(
                        '0' => __('TAK', 'woocommerce'),
                        '1' => __('NIE', 'woocommerce'),
                    ),
                ),
                'bank_view' => array(
                    'title' => __('Widok listy kanałów', 'woocommerce'),
                    'type' => 'select',
                    'default' => '0',
                    'css' => $ukryj_k,
                    'options' => array(
                        '0' => __('Kafelki', 'woocommerce'),
                        '1' => __('Lista', 'woocommerce'),
                    ),
                ),
                'scroll' => array(
                    'title' => __('Automatyczne przewijanie do przycisku płatności po wyborze banku ', 'woocommerce'),
                    'type' => 'select',
                    'default' => '0',
					
                    'css' => $ukryj_s,
                    'options' => array(
                        '0' => __('TAK', 'woocommerce'),
                        '1' => __('NIE', 'woocommerce'),
                    ),
					
                ),
                 'documentation' => array(
                    'title' => __('Dokumentacja techniczna', 'woocommerce'),
                    'type'   => 'title',
		    'description' => sprintf( __( ' <a href="%s" TARGET="_blank">Link do dokumentacji Technicznej systemu Transferuj.pl</a>.', 'woocommerce' ), 'https://transferuj.pl/dokumentacje.html' ),
                ),
            );
        }

        /**
         * Sends and receives data to/from Transferuj.pl server
         */
        function gateway_communication() {
            if (($_SERVER['REMOTE_ADDR'] == '195.149.229.109') && (!empty($_POST))) {
                $this->verify_payment_response();
            } else if (isset($_GET['order_id'])) {
                $this->send_payment_data($_GET['order_id']);
            }
            exit;
        }

        /**
         * Verifies that no errors have occured during transaction
         */
        function verify_payment_response() {
            $data['order_id'] = base64_decode($_POST['tr_crc']);
            $data['seller_id'] = $_POST['id'];
            $data['transaction_status'] = $_POST['tr_status'];
            $data['transaction_id'] = $_POST['tr_id'];
            $data['total'] = $_POST['tr_amount'];
            $data['error'] = $_POST['tr_error'];
            $data['crc'] = $_POST['tr_crc'];
            $data['checksum'] = $_POST['md5sum'];

            $data['local_checksum'] = md5($data['seller_id'] . $data['transaction_id'] . $data['total'] . $data['crc'] . $this->security_code);

            if (strcmp($data['checksum'], $data['local_checksum']) == 0) {
                if (($data['transaction_status'] == 'TRUE')) {
                    if ($data['error'] == 'none') {
                        // transaction successful
                        $this->complete_payment($data['order_id'], 'success', false);
                    } else if ($data['error'] == 'overpay') {
                        // payment was bigger than required
                        $this->complete_payment($data['order_id'], 'success', true);
                    }
                } else {
                    // transaction failed
                    $this->complete_payment($data['order_id'], 'failure', false);
                }
            }

            echo 'TRUE'; // data has been received; response for server
        }

        /**
         * Sets proper transaction status for order based on $status
         * @param $order_id ; id of an order
         * @param $status ; status of a transaction, enum : {success, failure}
         * @param $overpay ; whether there was an overpay during payment
         */
        function complete_payment($order_id, $status, $overpay) {
            $order = new WC_Order($order_id);
            if($this->get_option('status')== 0){
                $order_status='processing';
            }
            else{
                $order_status='completed';
                
            }
            if ($status == 'success') {
                if ($overpay) {
                    $order->update_status($order_status, __('Zapłacono z nadpłatą.'));
                } else {

                    $order->update_status($order_status, __('Zapłacono'));
                }
            } else if ($status == 'failure') {
                $order->update_status('failed', __('Zapłata nie powiodła się.'));
            }
        }

        /**
         * Processes payment for the order - see WC_Payment_Gateway
         * @param $order_id ; id of an order
         * @return array
         */
        function process_payment($order_id) {
            global $woocommerce;
            $order = new WC_Order($order_id);
            // Mark as on-hold (we will be awaiting the Transferuj.pl payment)
            $order->update_status('on-hold', __('Awaiting Transferuj.pl payment', 'woocommerce'));
            // Reduce stock levels
            $order->reduce_order_stock();
            // Clear cart
            $woocommerce->cart->empty_cart();
            // Post data and redirect to Transferuj.pl
            return array(
                'result' => 'success',
                'redirect' => add_query_arg(array('terms_t' => $_POST['terms_t'], 'order_id' => $order_id, 'kanal' => $_POST['kanal']), $this->notify_link)
            );
        }

        function validate_fields() {
            if (get_woocommerce_currency() == "PLN")
                return true;
            else {
                echo("Bramka umożliwia tylko płatność w PLN");
                exit;
            }
        }

        /**
         * Handles sending data to the server via post method
         * @param $order_id ; id of an order
         */
        function send_payment_data($order_id) {
            global $wp;
            // get order data
            $order = new WC_Order($order_id);

            $url = "https://secure.transferuj.pl/";

            // populate data array to be posted
            $data['seller_id'] = $this->seller_id;
            $data['security_code'] = $this->security_code;
            $data['kwota'] = $order->get_total();
            $data['opis'] = "Transakcja " . $order_id;
            ;
            $data['crc'] = base64_encode($order_id);

            $data['email'] = $order->billing_email;
            $data['nazwisko'] = $order->billing_first_name . ' ' . $order->billing_last_name;
            $data['adres'] = $order->billing_address_1 . ' ' . $order->billing_address_2;
            $data['miasto'] = $order->billing_city;
            $data['kraj'] = $order->billing_country;
            $data['kod'] = $order->billing_postcode;
            $data['telefon'] = $order->billing_phone;
            $data['md5sum'] = md5($data['seller_id'] . $data['kwota'] . $data['crc'] . $data['security_code']);
            $data['pow_url'] = esc_url(add_query_arg('utm_nooverride', '1', $this->get_return_url($order)));
            $data['pow_url_blad'] = esc_url($order->get_cancel_order_url());
            $data['wyn_url'] = $this->notify_link;

            if (strcmp(get_locale(), "pl_PL") == 0) {
                $data['jezyk'] = "pl";
            } else if (strcmp(get_locale(), "de_DE") == 0) {
                $data['jezyk'] = "de";
            } else {
                $data['jezyk'] = "en";
            }

            if (isset($_GET['kanal'])) {
                $data['kanal'] = $_GET['kanal'];
            }
            // else {
            ///    $data['kanal'] = 1;
            // }

            $termsInput = "";
            if (isset($_GET['terms_t'])) {
                $data['terms_t'] = $_GET['terms_t'];
                if ($data['terms_t'] == 'on') {
                    $termsInput = '<input type="hidden" name="akceptuje_regulamin" value="' . $data['terms_t'] . '"/>';
                }
            }

            $form = <<<FORM

            <form action="{$url}" method="post" id="tr_payment" name="tr_payment">
                <input type="hidden" name="id" value="{$data['seller_id']}"/>
                <input type="hidden" name="kwota" value="{$data['kwota']}"/>
                <input type="hidden" name="opis" value="{$data['opis']}"/>
                <input type="hidden" name="crc" value="{$data['crc']}"/>
                <input type="hidden" name="email" value="{$data['email']}"/>
                <input type="hidden" name="nazwisko" value="{$data['nazwisko']}"/>
                <input type="hidden" name="adres" value="{$data['adres']}"/>
                <input type="hidden" name="miasto" value="{$data['miasto']}"/>
                <input type="hidden" name="kraj" value="{$data['kraj']}"/>
                <input type="hidden" name="kod" value="{$data['kod']}"/>
                <input type="hidden" name="telefon" value="{$data['telefon']}"/>
                <input type="hidden" name="md5sum" value="{$data['md5sum']}"/>
                <input type="hidden" name="pow_url" value="{$data['pow_url']}"/>
                <input type="hidden" name="pow_url_blad" value="{$data['pow_url_blad']}"/>
                <input type="hidden" name="wyn_url" value="{$data['wyn_url']}"/>
                <input type="hidden" name="kanal" value="{$data['kanal']}"/>
                <input type="hidden" name="jezyk" value="{$data['jezyk']}"/>
                {$termsInput}
            </form>
            <script type="text/javascript">
                // post data to server
                document.getElementById('tr_payment').submit();
            </script>
FORM;
            echo $form;
            die();
        }

    }

    new WC_Gateway_Transferuj();
}
