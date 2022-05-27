<?php
/*
 * @copyright Copyright (c) 2021 AltumCode (https://altumcode.com/)
 *
 * This software is exclusively sold through https://altumcode.com/ by the AltumCode author.
 * Downloading this product from any other sources and running it without a proper license is illegal,
 *  except the official ones linked from https://altumcode.com/.
 */

namespace Altum\Controllers;

use Altum\Title;

class ApiDocumentation extends Controller {

    public function index() {

        /* Prepare the View */
        $view = new \Altum\Views\View('api-documentation/index', (array) $this);

        $this->add_view_content('content', $view->run());

    }

    public function user() {

        Title::set(l('api_documentation.user.title'));

        /* Prepare the View */
        $view = new \Altum\Views\View('api-documentation/user', (array) $this);

        $this->add_view_content('content', $view->run());

    }

    public function links() {

        Title::set(l('api_documentation.links.title'));

        /* Prepare the View */
        $view = new \Altum\Views\View('api-documentation/links', (array) $this);

        $this->add_view_content('content', $view->run());

    }

    public function statistics() {

        Title::set(l('api_documentation.statistics.title'));

        /* Prepare the View */
        $view = new \Altum\Views\View('api-documentation/statistics', (array) $this);

        $this->add_view_content('content', $view->run());

    }

    public function projects() {

        Title::set(l('api_documentation.projects.title'));

        /* Prepare the View */
        $view = new \Altum\Views\View('api-documentation/projects', (array) $this);

        $this->add_view_content('content', $view->run());

    }

    public function pixels() {

        Title::set(l('api_documentation.pixels.title'));

        /* Prepare the View */
        $view = new \Altum\Views\View('api-documentation/pixels', (array) $this);

        $this->add_view_content('content', $view->run());

    }

    public function qr_codes() {

        Title::set(l('api_documentation.qr_codes.title'));

        /* Prepare the View */
        $view = new \Altum\Views\View('api-documentation/qr_codes', (array) $this);

        $this->add_view_content('content', $view->run());

    }

    public function data() {

        Title::set(l('api_documentation.data.title'));

        /* Prepare the View */
        $view = new \Altum\Views\View('api-documentation/data', (array) $this);

        $this->add_view_content('content', $view->run());

    }

    public function domains() {

        Title::set(l('api_documentation.domains.title'));

        /* Prepare the View */
        $view = new \Altum\Views\View('api-documentation/domains', (array) $this);

        $this->add_view_content('content', $view->run());

    }

    public function payments() {

        Title::set(l('api_documentation.payments.title'));

        /* Prepare the View */
        $view = new \Altum\Views\View('api-documentation/payments', (array) $this);

        $this->add_view_content('content', $view->run());

    }

    public function users_logs() {

        Title::set(l('api_documentation.users_logs.title'));

        /* Prepare the View */
        $view = new \Altum\Views\View('api-documentation/users_logs', (array) $this);

        $this->add_view_content('content', $view->run());

    }
}


