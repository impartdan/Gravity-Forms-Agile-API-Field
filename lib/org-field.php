<?php
defined( 'ABSPATH' ) or die( 'No direct file access allowed!' );

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class GF_Field_Org extends GF_Field {
    public $type = 'org_field';

    public function get_form_editor_field_title() {
        // custom_log('get_form_editor_field_title');

        return esc_attr__( 'Organization', 'gravityforms' );
    }

    public function get_form_editor_button() {
        // custom_log('get_form_editor_button');
        return array(
            'group' => 'advanced_fields',
            'text'  => $this->get_form_editor_field_title(),
        );
    }
    
    public function get_form_editor_field_description() {
        // custom_log('get_form_editor_field_description');

        return esc_attr__( 'Allows users to enter a zip code and select their school using the Agile API.', 'gravityforms' );
    }

    public function get_required_inputs_ids() {
        // custom_log('get_required_inputs_ids');

		return array( '1', '2', '3', '4' );
	}

    function validate( $value, $form ) {
        // custom_log('validate');
        // custom_log($value);
        // custom_log($form);
        // custom_log($this);

        $school_id = rgar( $value, $this->id . '.3' );
        $school_name = rgar( $value, $this->id . '.4' );
        $school_is_custom = rgar( $value, $this->id . '.5' );
		
        if ( $this->isRequired ) {

            if ( empty( $school_id ) && empty( $school_is_custom ) && $school_is_custom == '0' ) {
                $this->failed_validation  = true;
                $this->validation_message = empty( $this->errorMessage ) ? esc_html__( 'This field is required.', 'gravityforms' ) : $this->errorMessage;
            }

            if ( empty( $school_name ) && empty( $school_is_custom ) && $school_is_custom == '1' ) {
                $this->failed_validation  = true;
                $this->validation_message = empty( $this->errorMessage ) ? esc_html__( 'This field is required.', 'gravityforms' ) : $this->errorMessage;
            }
        }
	}

    public function get_form_editor_field_icon() {
        // custom_log('get_form_editor_field_icon');

        return 'gform-icon--place';
    }

    function get_form_editor_field_settings() {
        // custom_log('get_form_editor_field_settings');

        return array(
            'admin_label_setting',
            'conditional_logic_field_setting',
            'css_class_setting',
            'description_setting',
            'error_message_setting',
            // 'input_placeholders_setting',
            // 'label_placement_setting',
            'label_setting',
            'prepopulate_field_setting',
            'rules_setting',
            // 'sub_label_placement_setting',
            // 'visibility_setting',
        );
    }

	public function is_conditional_logic_supported() {
        // custom_log('is_conditional_logic_supported');

		return true;
	}

    public function get_field_input( $form, $value = '', $entry = null ) {
        // custom_log('get_field_input form');
        // custom_log($form);
        // custom_log('get_field_input value');
        // custom_log($value);
        // custom_log('get_field_input entry');
        // custom_log($entry);

        $is_entry_detail = $this->is_entry_detail();
        $is_form_editor  = $this->is_form_editor();
		$is_admin = $is_entry_detail || $is_form_editor;

        $form_id  = $form['id'];
		$id       = intval( $this->id );
		// $field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
		$field_id = "input_$id";
		$form_id  = ( $is_entry_detail || $is_form_editor ) && empty( $form_id ) ? rgget( 'id' ) : $form_id;

        $zipcode = $schools = $school_id = $school_name = $school_is_custom = '';

        if ( is_array( $value ) ) {
            
            $zipcode = esc_attr( GFForms::get( $this->id . '.1', $value ) );
			$schools  = esc_attr( GFForms::get( $this->id . '.2', $value ) );
			$school_id = esc_attr( GFForms::get( $this->id . '.3', $value ) );
			$school_name   = esc_attr( GFForms::get( $this->id . '.4', $value ) );
			$school_is_custom = esc_attr( GFForms::get( $this->id . '.5', $value ) );
        }

        $disabled_text = $is_form_editor ? "disabled='disabled'" : '';
        $class_suffix  = $is_entry_detail ? '_admin' : '';

        $zipcode_tabindex = GFCommon::get_tabindex();
        $schools_tabindex  = GFCommon::get_tabindex();
        $school_id_tabindex = GFCommon::get_tabindex();
        $school_name_tabindex = GFCommon::get_tabindex();
        $school_is_custom_tabindex = GFCommon::get_tabindex();

        $required_attribute = $this->isRequired ? 'aria-required="true"' : '';
        $invalid_attribute  = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';

        $zip_limiter = 'min="0" maxlength="5" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"';                            

        $zipcode_markup = '<span id="'.$field_id.'_1_container" class="org_field_zipcode">';
        $zipcode_markup .= '<input type="number" '.$zip_limiter.' name="'.$field_id.'.1" id="'.$field_id.'_1" value="'.$zipcode.'" aria-label="Zip Code" ' . $zipcode_tabindex . ' ' . $disabled_text . ' ' . $required_attribute . ' ' . $invalid_attribute . '>';
        $zipcode_markup .= '<label for="'.$field_id.'_1">Zipcode</label>';
        $zipcode_markup .= '</span>';

		$schools_input  = GFFormsModel::get_input( $this, $this->id . '.2' );
        $schools_markup = '<span id="'.$field_id.'_2_container" class="org_field_schools">';
        $schools_markup .= self::get_schools_field( $schools_input, $id, $field_id, $schools, $disabled_text, $schools_tabindex );
        $schools_markup .= '<label for="'.$field_id.'_2">Schools</label>';
        $schools_markup .= '</span>';

        $school_id_markup = '<span id="'.$field_id.'_3_container" class="org_field_school_id">';
        $school_id_markup .= '<input type="text" name="'.$field_id.'.3" id="'.$field_id.'_3" value="'.$school_id.'" aria-label="Email" ' . $school_id_tabindex . ' ' . $disabled_text . ' ' . $required_attribute . ' ' . $invalid_attribute . '>';
        $school_id_markup .= '<label for="'.$field_id.'_3">School ID (will be hidden)</label>';
        $school_id_markup .= '</span>';

        $school_name_markup = '<span id="'.$field_id.'_4_container" class="org_field_school_name">';
        $school_name_markup .= '<input type="text" name="'.$field_id.'.4" id="'.$field_id.'_4" value="'.$school_name.'" aria-label="School Name" ' . $school_name_tabindex . ' ' . $disabled_text . ' ' . $required_attribute . ' ' . $invalid_attribute . '>';
        $school_name_markup .= '<label for="'.$field_id.'_4">School Name (will be visible if other)</label>';
        $school_name_markup .= '</span>';

        $school_is_custom_markup = '<span id="'.$field_id.'_5_container" class="org_field_school_is_custom">';
        $school_is_custom_markup .= '<input type="text" name="'.$field_id.'.5" id="'.$field_id.'_5" value="'.$school_is_custom.'" aria-label="School Is Custom" ' . $school_is_custom_tabindex . ' ' . $disabled_text . ' ' . $required_attribute . ' ' . $invalid_attribute . '>';
        $school_is_custom_markup .= '<label for="'.$field_id.'_5">School Is Custom (will be hidden)</label>';
        $school_is_custom_markup .= '</span>';

        $css_class = $this->get_css_class();

        return "<div class='ginput_complex{$class_suffix} ginput_container {$css_class} gfield_trigger_change' id='{$field_id}'>
                    {$zipcode_markup}
                    {$schools_markup}
                    {$school_id_markup}
                    {$school_name_markup}
                    {$school_is_custom_markup}
                    <div class='gf_clear gf_clear_complex'></div>
                </div>";
    }

    public function get_css_class() {
        // custom_log('get_css_class');

        $css_class = 'ginput_container_org';

        return trim( $css_class );
    }

	public function get_schools_field( $input, $id, $field_id, $value, $disabled_text, $tabindex ) {
        // custom_log('get_schools_field');
        $options = "<option value='0'>Please Select</option>";
        $options .= "<option value='other'>Other</option>";
        $markup = "<select name='{$field_id}.2' id='{$field_id}_2' {$tabindex} {$disabled_text} {$aria_attributes} {$this->maybe_add_aria_describedby( $input, $field_id, $this['formId'] )}>{$options}</select>";

		return $markup;
	}

   

    public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
        // custom_log('get_value_entry_detail');
        // custom_log($value);
        
        
        $return = '';

        if ( is_array( $value ) ) {
            $zipcode = trim( rgget(  $this->id . '.1', $value  ) );
            $schools  = trim( rgget(  $this->id . '.2', $value  ) );
            $school_id = trim( rgget(  $this->id . '.3', $value  ) );
            $school_name = trim( rgget(  $this->id . '.4', $value  ) );
            $school_is_custom = trim( rgget(  $this->id . '.5', $value  ) );

            $return = $zipcode;
            $return .= $return != '' && ! empty( $schools ) ? " $schools" : $schools;
            $return .= $return != '' && ! empty( $school_id ) ? " $school_id" : $school_id;
            $return .= $return != '' && ! empty( $school_name ) ? " $school_name" : $school_name;
            $return .= $return != '' && ! empty( $school_is_custom ) ? " $school_is_custom" : $school_is_custom;
        }

        if ( $format === 'html' ) {
            $return = esc_html( $return );
        }

        return $return;
    }


    public function get_form_inline_script_on_page_render( $form ) {
        // custom_log('get_form_inline_script_on_page_render');

        $plugin_dir = WP_PLUGIN_DIR . '/gravity-forms-agile-api-field';
        return file_get_contents($plugin_dir . '/js/org_field.js');
    }

}

