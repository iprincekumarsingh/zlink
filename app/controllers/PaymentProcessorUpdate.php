<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Alerts;
use Altum\Database\Database;
use Altum\Date;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Uploads;

class PaymentProcessorUpdate extends Controller {

    public function index() {

        Authentication::guard();

        /* Team checks */
        if(\Altum\Teams::is_delegated() && !\Altum\Teams::has_access('update')) {
            Alerts::add_info(l('global.info_message.team_no_access'));
            redirect('payment-processors');
        }

        $payment_processor_id = isset($this->params[0]) ? (int) $this->params[0] : null;

        if(!$payment_processor = db()->where('payment_processor_id', $payment_processor_id)->where('user_id', $this->user->user_id)->getOne('payment_processors')) {
            redirect('payment-processors');
        }
        $payment_processor->settings = json_decode($payment_processor->settings);

        if(!empty($_POST)) {
            $settings = [];

            $_POST['name'] = trim(Database::clean_string($_POST['name']));
            $_POST['processor'] = isset($_POST['processor']) && in_array($_POST['processor'], ['stripe', 'paypal']) ? Database::clean_string($_POST['processor']) : 'https://';

            switch($_POST['processor']) {
                case 'paypal':
                    $settings['mode'] = $_POST['mode'] = in_array($_POST['mode'], ['live', 'sandbox']) ? $_POST['mode'] : 'live';
                    $settings['client_id'] = $_POST['client_id'] = filter_var($_POST['client_id'], FILTER_SANITIZE_STRING);
                    $settings['secret'] = $_POST['secret'] = filter_var($_POST['secret'], FILTER_SANITIZE_STRING);
                    break;

                case 'stripe':
                    $settings['publishable_key'] = $_POST['publishable_key'] = filter_var($_POST['publishable_key'], FILTER_SANITIZE_STRING);
                    $settings['secret_key'] = $_POST['secret_key'] = filter_var($_POST['secret_key'], FILTER_SANITIZE_STRING);
                    $settings['webhook_secret'] = $_POST['webhook_secret'] = filter_var($_POST['webhook_secret'], FILTER_SANITIZE_STRING);
                    break;
            }

            //ALTUMCODE:DEMO if(DEMO) if($this->user->user_id == 1) Alerts::add_error('Please create an account on the demo to test out this function.');

            /* Check for any errors */
            $required_fields = ['name'];
            foreach($required_fields as $field) {
                if(!isset($_POST[$field]) || (isset($_POST[$field]) && empty($_POST[$field]) && $_POST[$field] != '0')) {
                    Alerts::add_field_error($field, l('global.error_message.empty_field'));
                }
            }

            if(!Csrf::check()) {
                Alerts::add_error(l('global.error_message.invalid_csrf_token'));
            }

            if(!Alerts::has_field_errors() && !Alerts::has_errors()) {
                $settings = json_encode($settings);

                /* Database query */
                db()->where('payment_processor_id', $payment_processor->payment_processor_id)->update('payment_processors', [
                    'name' => $_POST['name'],
                    'processor' => $_POST['processor'],
                    'settings' => $settings,
                    'last_datetime' => \Altum\Date::$date,
                ]);

                /* Set a nice success message */
                Alerts::add_success(sprintf(l('global.success_message.update1'), '<strong>' . filter_var($_POST['name'], FILTER_SANITIZE_STRING) . '</strong>'));

                /* Clear the cache */
                \Altum\Cache::$adapter->deleteItemsByTag('payment_processors?user_id=' . $this->user->user_id);

                redirect('payment-processor-update/' . $payment_processor_id);
            }
        }

        /* Prepare the View */
        $data = [
            'payment_processor' => $payment_processor,
        ];

        $view = new \Altum\Views\View('payment-processor-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
