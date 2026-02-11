<?php
class ControllerInformationContactSuccess extends Controller {
    public function index() {
        $this->load->language('information/contact');

        $this->document->setTitle('Thank You - Enquiry Received');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/contact')
        );

        $data['breadcrumbs'][] = array(
            'text' => 'Thank You',
            'href' => $this->url->link('information/contact/success')
        );

        $data['heading_title'] = 'Thank You!';
        $data['message'] = 'Your enquiry has been successfully submitted. We will get back to you within 24–48 hours.';

        $data['button_continue'] = $this->language->get('button_continue');
        $data['continue'] = $this->url->link('common/home');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        unset($this->session->data['success']);


        $this->response->setOutput($this->load->view('information/contact_success', $data));
    }
}