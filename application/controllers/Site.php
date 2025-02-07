<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Site extends Core_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $classes = [
            'Lokal' => 'primary',
            'Web' => 'success',
            'Monitoring' => 'danger',
            'MA' => 'warning',
            'Badilag' => 'info',
            'PTA Surabaya' => 'light text-dark',
            'Lain-lain' => 'secondary',
        ];

        $this->load->model('Web_Model', 'web');
        $this->load->vars([
            'main_body' => 'layout_content',
            'view' => 'site/one',
            'classes' => $classes,
            'apps' => $this->_transform_data($this->web->find()),
        ]);

        $this->load->vars($this->vars);

        $this->load->view('layout_no_sidebar');
    }

    function about()
    {
        $this->load->vars([
            'main_body' => 'layout_content',
            'view' => 'site/about',
        ]);

        $this->load->vars($this->vars);

        $this->load->view('layout_no_sidebar');
    }

    function get_ratio()
    {
        $this->load->model('Ratio_Model', 'ratio');

        $this->vars = [
            'main_body' => 'layout_content',
            'view' => 'site/_ratio',
            'ratio_all' => $this->ratio->get_ratio_all(),
            'ratio_summary' => $this->ratio->get_ratio_summary(date('Y'), date('m')),
            'ratio_antrian' => $this->ratio->get_ratio_antrian(),
            'ratio_bas' => $this->ratio->get_ratio_bas(),
            'ratio_dirput' => $this->ratio->get_ratio_dirput(),
            // 'ratio_dirput' => $this->ratio->get_ratio_dirput(date('Y')),
            'ratio' => $this->ratio->get_ratio(),
            'ratio2' => $this->ratio->get_ratio2(),
            'ratio3' => $this->ratio->get_ratio3(),
            'ratio4' => $this->ratio->get_ratio4(),
            'ratio5' => $this->ratio->get_ratio5(),
        ];

        $this->load->vars($this->vars);

        if ($this->input->is_ajax_request()) {
            return $this->viewAjax('site/_ratio');
        }

        $this->load->view('layout_no_sidebar');
    }

    /**
     * @param string     $view
     * @param array|null $data
     * @param bool       $returnhtml
     *
     * @return mixed
     */
    public function _render_page($view, $data = NULL, $returnhtml = FALSE) //I think this makes more sense
    {

        $viewdata = (empty($data)) ? $this->vars : $data;

        $this->load->vars([
            'main_body' => 'layout_content',
            'view' => $view,
        ]);

        $view_html = $this->load->view('layout_no_sidebar', $viewdata, $returnhtml);

        // This will return html on 3rd argument being true
        if ($returnhtml) {
            return $view_html;
        }
    }

    private function _transform_data($data)
    {
        $result = [];
        foreach ($data as $item) {
            $result[$item->category][] = [
                $item->name,
                filter_var($item->url, FILTER_VALIDATE_URL) ? $item->url : base_url($item->url),
                $item->icon ? file_url2('uploads/web/' . $item->icon) : null,
                $item->category,
                $item->icon_width,
                $item->icon_height,
            ];
        }
        return $result;
    }
}
