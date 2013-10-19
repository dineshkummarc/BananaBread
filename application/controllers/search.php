<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends MY_Controller {

	public function index()
	{
		$queries = array_filter(explode(' ', $this->input->get('query')));
		$query_terms = implode(' ', $queries);

		$r = array();
		foreach ($queries as $term) {
			foreach (array('description', 'name') as $field) {
				$this->db->or_where("LOWER($field) LIKE", "%$term%");
			}
		}
		$res = $this->db->get('band');

		$this->config->load('oauth2');
		$ch = curl_init("http://tinysong.com/s/$query_terms?" . http_build_query(array(
			'format' => 'json',
			'key'    => $this->config->item('tinysong')
		)));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data = json_decode(curl_exec($ch));

		$this->load_view('search', array(
			'our_items' => $res->result(),
			'tiny_items' => $data,
			'query_terms'  => $query_terms
		));
	}

}
