<?php
defined( 'ABSPATH' ) or die( 'No direct file access allowed!' );

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class GF_Field_Org extends GF_Field {
    public $type = 'org_field';

    public function get_form_editor_field_title() {
        custom_log('get_form_editor_field_title');

        return esc_attr__( 'Organization', 'gravityforms' );
    }

    public function get_form_editor_button() {
        custom_log('get_form_editor_button');
        return array(
            'group' => 'advanced_fields',
            'text'  => $this->get_form_editor_field_title(),
        );
    }
    
    public function get_form_editor_field_description() {
        custom_log('get_form_editor_field_description');

        return esc_attr__( 'Allows users to enter a zip code and select their school using the Agile API.', 'gravityforms' );
    }

    public function get_form_editor_field_icon() {
        custom_log('get_form_editor_field_icon');

        return 'gform-icon--place';
    }

	public function is_conditional_logic_supported() {
        custom_log('is_conditional_logic_supported');

		return true;
	}

    public function get_required_inputs_ids() {
		return array( '4', '5' );
	}

    function validate( $value, $form ) {
        custom_log('validate');
        custom_log($form);
        custom_log($value);

        if ( $this->isRequired ) {
			$this->set_required_error( $value, true );
		}
	}

    function get_form_editor_field_settings() {
        custom_log('get_form_editor_field_settings');

        return array(
            'admin_label_setting',
            'conditional_logic_field_setting',
            'css_class_setting',
            'description_setting',
            'error_message_setting',
            'input_placeholders_setting',
            // 'label_placement_setting',
            'label_setting',
            'prepopulate_field_setting',
            'rules_setting',
            // 'sub_label_placement_setting',
            // 'visibility_setting',
        );
    }

    public function get_field_input( $form, $value = '', $entry = null ) {
        custom_log('get_field_input');
        custom_log($form);
        custom_log($value);
        custom_log($entry);

        // get info
        $is_entry_detail = $this->is_entry_detail();
        $is_form_editor  = $this->is_form_editor();
		$is_admin = $is_entry_detail || $is_form_editor;

        // prep variables
        $form_id  = $form['id'];
		$id       = intval( $this->id );
		$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
		$form_id  = ( $is_entry_detail || $is_form_editor ) && empty( $form_id ) ? rgget( 'id' ) : $form_id;


        $size         = $this->size;
		$class_suffix = rgget('view') == 'entry' ? '_admin' : '';
		$class        = $size . $class_suffix;
		$class        = esc_attr( $class );

        $disabled_text = $is_form_editor ? "disabled='disabled'" : '';
        $class_suffix  = $is_entry_detail ? '_admin' : '';


        // set defaults
        $zipcode = $schools = $school_id = $school_name = $school_is_custom = '';

        if ( is_array( $value ) ) {
            $zipcode            = esc_attr( GFForms::get( $this->id . '.1', $value ) );
			$schools            = esc_attr( GFForms::get( $this->id . '.2', $value ) );
			$school_id          = esc_attr( GFForms::get( $this->id . '.3', $value ) );
			$school_name        = esc_attr( GFForms::get( $this->id . '.4', $value ) );
			$school_is_custom   = esc_attr( GFForms::get( $this->id . '.5', $value ) );
        }


        $zipcode_input          = GFFormsModel::get_input( $this, $this->id . '.1' );
		$schools_input          = GFFormsModel::get_input( $this, $this->id . '.2' );
		$school_id_input        = GFFormsModel::get_input( $this, $this->id . '.3' );
		$school_name_input      = GFFormsModel::get_input( $this, $this->id . '.4' );
		$school_is_custom_input = GFFormsModel::get_input( $this, $this->id . '.5' );


        $zipcode_placeholder_attribute  = GFCommon::get_input_placeholder_attribute( $zipcode_input );
        $schools_placeholder_attribute  = GFCommon::get_input_placeholder_attribute( $schools_input );
		$school_id_placeholder_attribute = GFCommon::get_input_placeholder_attribute( $school_id_input );
		$school_name_placeholder_attribute   = GFCommon::get_input_placeholder_attribute( $school_name_input );
		$school_is_custom_placeholder_attribute = GFCommon::get_input_placeholder_attribute( $school_is_custom_input );

        // ARIA labels.
        $required_attribute     = $this->isRequired ? 'aria-required="true"' : '';
        $invalid_attribute      = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';
        $describedby_attribute  = $this->get_aria_describedby();
        $input_aria_describedby = '';


        // specific aria attributes for each individual input.
        $zipcode_aria_attributes            = $this->get_aria_attributes( $value, '1');
        $schools_aria_attributes            = $this->get_aria_attributes( $value, '2');
        $school_id_aria_attributes          = $this->get_aria_attributes( $value, '3');
        $school_name_aria_attributes        = $this->get_aria_attributes( $value, '4');
        $school_is_custom_aria_attributes   = $this->get_aria_attributes( $value, '5');


        $zipcode_autocomplete           = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $zipcode_input ) : '';
		$schools_autocomplete           = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $schools_input ) : '';
		$school_id_autocomplete         = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $school_id_input ) : '';
		$school_name_autocomplete       = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $school_name_input ) : '';
		$school_is_custom_autocomplete  = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $school_is_custom_input ) : '';



        $zipcode_tabindex           = GFCommon::get_tabindex();
        $schools_tabindex           = GFCommon::get_tabindex();
        $school_id_tabindex         = GFCommon::get_tabindex();
        $school_name_tabindex       = GFCommon::get_tabindex();
        $school_is_custom_tabindex  = GFCommon::get_tabindex();


        $zipcode_markup             = '';
        $schools_markup             = '';
        $school_id_markup           = '';
        $school_name_markup         = '';
        $school_is_custom_markup    = '';


        $zip_limiter = 'min="0" maxlength="5" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"';                            



        $zipcode_markup = "<span id='{$field_id}_1_container' class='org_field_zipcode'>
                                <input type='number' {$zip_limiter} name='input_{$id}.1' id='{$field_id}_1' value='{$zipcode}' {$zipcode_tabindex} {$disabled_text} {$zipcode_aria_attributes} {$zipcode_placeholder_attribute} {$zipcode_autocomplete} {$this->maybe_add_aria_describedby( $zipcode_input, $field_id, $this['formId'] )}/>
                                <label for='{$field_id}_1'>Zipcode</label>
                            </span>";

        $schools_select_class = isset( $schools_input['choices'] ) && is_array( $schools_input['choices'] ) ? 'org_field_schools_select' : '';
        $schools_markup       = self::get_schools_field( $schools_input, $id, $field_id, $schools, $disabled_text, $schools_tabindex );
        $schools_markup       = "<span id='{$field_id}_2_container' class='org_field_schools {$schools_select_class}'>
                                    {$schools_markup}
                                    <label for='{$field_id}_2'>Schools</label>
                                </span>";

        $school_id_markup = "<span id='{$field_id}_3_container' class='org_field_school_id'>
                                <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$school_id}' {$school_id_tabindex} {$disabled_text} {$school_id_aria_attributes} {$school_id_placeholder_attribute} {$school_id_autocomplete} {$this->maybe_add_aria_describedby( $school_id_input, $field_id, $this['formId'] )}/>
                                <label for='{$field_id}_1'>School ID (will be hidden)</label>
                            </span>";


        $school_name_markup = "<span id='{$field_id}_4_container' class='org_field_school_name'>
                                <input type='text' name='input_{$id}.4' id='{$field_id}_4' value='{$school_name}' {$school_name_tabindex} {$disabled_text} {$school_name_aria_attributes} {$school_name_placeholder_attribute} {$school_name_autocomplete} {$this->maybe_add_aria_describedby( $school_name_input, $field_id, $this['formId'] )}/>
                                <label for='{$field_id}_4'>School Name (will be visible if other)</label>
                            </span>";


        $school_is_custom_markup = "<span id='{$field_id}_5_container' class='org_field_school_is_custom'>
                                <input type='text' name='input_{$id}.5' id='{$field_id}_5' value='{$school_is_custom}' {$school_is_custom_tabindex} {$disabled_text} {$school_is_custom_aria_attributes} {$school_is_custom_placeholder_attribute} {$school_is_custom_autocomplete} {$this->maybe_add_aria_describedby( $school_is_custom_input, $field_id, $this['formId'] )}/>
                                <label for='{$field_id}_5'>School Is Custom (will be hidden)</label>
                            </span>";


        $css_class = $this->get_css_class();


        return "<div class='ginput_complex{$class_suffix} ginput_container ginput_container--name {$css_class} gform-grid-row' id='{$field_id}'>
                    {$zipcode_markup}
                    {$schools_markup}
                    {$school_id_markup}
                    {$school_name_markup}
                    {$school_is_custom_markup}
                </div>";
    }

    public function get_css_class() {
        custom_log('get_css_class');

        $css_class = 'ginput_container_org';

        return trim( $css_class );
    }

	public function get_schools_field( $input, $id, $field_id, $value, $disabled_text, $tabindex ) {

        $autocomplete          = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $input ) : '';
		$aria_attributes       = $this->get_aria_attributes( array( $input['id'] => $value ), '2' );
		$describedby_attribute = $this->get_aria_describedby();

        $options           = "<option value='0'>Please Select</option>";
        // maybe remove - start
        // $value_enabled     = rgar( $input, 'enableChoiceValue' );
        // foreach ( $input['choices'] as $choice ) {
        //     $choice_value            = $value_enabled ? $choice['value'] : $choice['text'];
        //     $is_selected_by_default  = rgar( $choice, 'isSelected' );
        //     $is_this_choice_selected = empty( $value ) ? $is_selected_by_default : strtolower( $choice_value ) == strtolower( $value );
        //     $selected                = $is_this_choice_selected ? "selected='selected'" : '';
        //     $options .= "<option value='{$choice_value}' {$selected}>{$choice['text']}</option>";
        // }
        // maybe remove - end
        $options .= "<option value='other'>Other</option>";

        $markup = "<select name='input_{$id}.2' id='{$field_id}_2' {$tabindex} {$disabled_text} {$autocomplete} {$aria_attributes} {$this->maybe_add_aria_describedby( $input, $field_id, $this['formId'] )}>
                        {$options}
                    </select>";
		return $markup;

    }

    public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		if ( is_array( $value ) ) {
			$zipcode            = trim( rgget( $this->id . '.1', $value ) );
			$schools            = trim( rgget( $this->id . '.2', $value ) );
			$school_id          = trim( rgget( $this->id . '.3', $value ) );
			$school_name        = trim( rgget( $this->id . '.4', $value ) );
			$school_is_custom   = trim( rgget( $this->id . '.5', $value ) );

			$org = $zipcode;
			$org .= ! empty( $org ) && ! empty( $schools ) ? " $schools" : $schools;
			$org .= ! empty( $org ) && ! empty( $school_id ) ? " $school_id" : $school_id;
			$org .= ! empty( $org ) && ! empty( $school_name ) ? " $school_name" : $school_name;
			$org .= ! empty( $org ) && ! empty( $school_is_custom ) ? " $school_is_custom" : $school_is_custom;

			$return = $org;
		} else {
			$return = $value;
		}

		if ( $format === 'html' ) {
			$return = esc_html( $return );
		}
		return $return;
	}

    public function sanitize_settings() {
		parent::sanitize_settings();
		if ( is_array( $this->inputs ) ) {
			foreach ( $this->inputs as &$input ) {
				if ( isset ( $input['choices'] ) && is_array( $input['choices'] ) ) {
					$input['choices'] = $this->sanitize_settings_choices( $input['choices'] );
				}
			}
		}
	}

	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		if ( empty( $input_id ) ) {
			$input_id = $this->id;
		}

		if ( absint( $input_id ) == $input_id ) {
			// If field is simple (one input), simply return full content.
			$org = rgar( $entry, $input_id );
			if ( ! empty( $org ) ) {
				return $org;
			}

			// Complex field (multiple inputs). Join all pieces and create name.
			$zipcode            = trim( rgar( $entry, $input_id . '.1' ) );
			$schools            = trim( rgar( $entry, $input_id . '.2' ) );
			$school_id          = trim( rgar( $entry, $input_id . '.3' ) );
			$school_name        = trim( rgar( $entry, $input_id . '.4' ) );
			$school_is_custom   = trim( rgar( $entry, $input_id . '.5' ) );

			$org = $zipcode;
			$org .= ! empty( $org ) && ! empty( $schools ) ? ' ' . $schools : $schools;
			$org .= ! empty( $org ) && ! empty( $school_id ) ? ' ' . $school_id : $school_id;
			$org .= ! empty( $org ) && ! empty( $school_name ) ? ' ' . $school_name : $school_name;
			$org .= ! empty( $org ) && ! empty( $school_is_custom ) ? ' ' . $school_is_custom : $school_is_custom;

			return $org;
		} else {

			return rgar( $entry, $input_id );
		}
	}

    public function get_first_input_id( $form ) {
		return '';
	}

    public function get_form_inline_script_on_page_render( $form ) {
        custom_log('get_form_inline_script_on_page_render');

        $plugin_dir = WP_PLUGIN_DIR . '/gravity-forms-agile-api-field';
        return file_get_contents($plugin_dir . '/js/org_field.js');
    }

}

