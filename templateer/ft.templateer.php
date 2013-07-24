<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine templateer Class
 *
 * @package		Templateer
 * @category	Fieldtypes
 * @author		Nuno Albuquerque
 * @license		http://creativecommons.org/licenses/by/3.0/
 * @link		http://nainteractive.com
 */

class  templateer_ft extends EE_Fieldtype {

	var $info = array(
			'name'		=>	'Templateer',
			'version'	=>	'0.1'
			);


	// --------------------------------------------------------------------

	function install()
	{
		return array(
			'templates'	=> ''
		);
	}


	// --------------------------------------------------------------------
	// Displays the field in the publish form.
	function display_field($data)
	{
		// Load Language
		$this->EE->lang->loadfile('templateer');

		// Load up required includes
		$this->_field_includes();

		// Create field HTML
		$html = form_dropdown($this->field_name ,$this->get_templates($this->get_settings_prop('templates')), $data);

		$this->EE->cp->add_to_foot('
			<script>
				$(document).ready(function() {
					var $elm = $("#sub_hold_field_'. $this->field_id . ' select");
					var $template_select = $("#pages__pages_template_id");

					$elm.change(function (){
						$template_select.val($elm.val());
					});

				});
			</script>');

		return $html;
	}


	// ----------------------------------------------------------------

	function _field_includes()
	{
		if (! isset($this->cache['included_configs']))
		{
			$this->EE->load->library('javascript');
/* 			$this->EE->cp->load_package_js('templateer'); */

			$this->cache['included_configs'] = array();
		}
	}


	// --------------------------------------------------------------------
	// Display fieldtype settings
	function display_settings($data)
	{
		$this->EE->lang->loadfile('templateer');

		$this->settings = array_merge($this->settings, $data);

		$template_ary = $this->get_templates();

		$this->EE->table->add_row(
			'Templates to include in list:', form_multiselect('templates[]', $template_ary, $this->settings['templates'], "style='height:200px;width:200px;'")
		);

	}


	// ----------------------------------------------------------------
	// returns array
	function get_templates($template_ids = '')
	{
		$where = (is_array($template_ids)) ? ' and t.template_id in (' . implode(',', $template_ids).')' :'';

		$query = $this->EE->db->query("select template_id, template_name , t.group_id, t_groups.group_name
										from exp_templates t
										left join exp_template_groups t_groups on t.group_id = t_groups.group_id
										where t.template_type = 'webpage'
										and t.site_id = 1" . $where . " order by group_name");

		$ary = array();

		if($query->num_rows() > 0)
		{
			$results = $query->result();

			foreach($results as $row)
			{
				$ary[$row->template_id] = $row->group_name.'/'.$row->template_name;
			}

		}

		return $ary;

	}


	// ----------------------------------------------------------------

	function save_global_settings()
	{
		return array_merge($this->settings, $_POST);
	}

	// ----------------------------------------------------------------

	function save($field_data)
	{
		return $field_data;
	}


	// --------------------------------------------------------------------

	function save_settings ($settings)
	{
		$settings['field_fmt'] = 'none';
		$settings['field_show_fmt'] = 'n';
		$settings['field_type'] = 'templateer';
		return array_merge($settings, $_POST);
	}


	/**
	 * Displays the field data in a template tag.
	 *
	 * @access	public
	 * @param	array 		$params				The template tag parameters (key / value pairs).
	 * @param	string		$tagdata			The content between the opening and closing tags, if it's a tag pair.
	 * @param 	string		$field_data			The field data.
	 * @param 	array 		$field_settings		The field settings.
	 * @return	string
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		// TODO
		// return selected categories in this group for current entry

		return '';//$this->settings['category_group_id'];
	}

	// --------------------------------------------------------------------

	function get_settings_prop($key, $default = '')
	{
		if(array_key_exists($key, $this->settings))
		{
			return $this->settings[$key];
		}
		return $default;
	}

}
// END  templateer_ft class

/* End of file ft.templateer.php */
/* Location: ./system/expressionengine/third_party/templateer/*/
