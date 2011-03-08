<?php

class views_pdf_plugin_row_fields extends views_plugin_row 
{
	function render($records) {
		$options = $this->option_definition();
		
		foreach ($records as $row_index => $row) {
			$this->view->row_index = $row_index;

			// Header of a record
			$path = $this->view->pdf->getTemplatePath($this->options['leading_template']);
			$this->view->pdf->addPdfDocument($path);
			
			// Set row page template
			$path = $this->view->pdf->getTemplatePath($this->options['template'], $row, $this->view);
			$this->view->pdf->setDefaultPageTemplate($path, 'row', 'row');
			
			// Due of limitations of field renderer, we invoke them
			// here and not in the field render function.
			foreach ($this->view->field as $id => $field) {

				$options = $this->options['formats'][$id];			
				$this->view->pdf->drawContent($row, $options, $this->view, $id);
				if($options['position']['object'] == 'header_footer') {
					// Set or update header / footer options
					$this->view->pdf->setHeaderFooter($row, $this->options, $this->view);
				}
			}
			
			// Footer of a record
			$path = $this->view->pdf->getTemplatePath($this->options['succeed_template']);
			$this->view->pdf->addPdfDocument($path);
						
			
			// Reset the row page number
			$this->view->pdf->resetRowPageNumber();
		}
		
	}

	function option_definition() {
		$options = parent::option_definition();

		$options['formats'] = array('default' => array());
		$options['leading_template'] = array('default' => '');
		$options['template'] = array('default' => '');
		$options['succeed_template'] = array('default' => '');
		
		
		return $options;
	}

	/**
	* Provide a form for setting options.
	*/
	function options_form(&$form, &$form_state) {
	

		$options = $this->display->handler->get_field_labels();
		$fields = $this->display->handler->get_option('fields');
		
		
		$fonts = array_merge(array('default' => t(' -- Default -- ')), views_pdf_get_font_list());
		$font_styles = array(
			'b' => t('Bold'),
			'i' => t('Italic'),
			'u' => t('Underline'),
			'd' => t('Line through'),
			'o' => t('Overline')
		);
		$templates = array_merge(array(t(' -- None -- ')), views_pdf_get_pdf_templates());
		
		$file_fields = array();
		foreach ($this->display->handler->get_handlers('field') as $id => $handler) {
			if($handler->content_field['type'] == 'filefield')
			{
				$file_fields[$id] = $options[$id];
			}
		}		
		$row_templates = array_merge($templates, $file_fields);
		
		$relativeElements = array(
			'page' => t('Page'),
			'header_footer' => t('In header / footer'),
			'last_position' => t('Last wrting position'),
			'self' => t('Field: Self'),
		);
		
		$align = array(
			'L' => t('Left'),
			'C' => t('Center'),
			'R' => t('Right'),
			'J' => t('Justify'),
		);			
			
		

		if (empty($this->options['inline'])) {
			$this->options['inline'] = array();
		}
		$form['formats'] = array(
			'#prefix' => '<div class="description form-item">',
			'#suffix' => '</div>',
			'#value' => t('Here you can define some style settings for each field.'),
		);
		
		foreach($options as $field => $option) {
			
			if($fields[$field]['exclude'] == 1)
			{
				continue;
			}


			$form['formats'][$field] = array(
				'#type' => 'fieldset',
				'#title' => $option,
				'#collapsed' => TRUE,
				'#collapsable' => TRUE,
			);
			
			
			$form['formats'][$field]['position'] = array(
				'#type' => 'fieldset',
				'#title' => t('Position Settings'),
				'#collapsed' => FALSE,
				'#collapsable' => TRUE,
			);
			
			$form['formats'][$field]['position']['object'] = array(
				'#type' => 'select',
				'#title' => t('Position relative to'),
				'#required' => FALSE,
				'#options' => $relativeElements,
				'#default_value' => !empty($this->options['formats'][$field]['position']['object']) ? $this->options['formats'][$field]['position']['object'] : 'page',
			);
			
			$form['formats'][$field]['position']['corner'] = array(
				'#type' => 'radios',
				'#title' => t('Position relative to corner'),
				'#required' => FALSE,
				'#options' => array(
					'top_left' => t('Top Left'),
					'top_right' => t('Top Right'),
					'bottom_left' => t('Bottom Left'), 
					'bottom_right' => t('Bottom Right'),
				),
				'#default_value' => !empty($this->options['formats'][$field]['position']['corner']) ? $this->options['formats'][$field]['position']['corner'] : 'top_left',
			);
			
			$relativeElements['field_'.$field] = t('Field: !field', array('!field' => $option));
			
			
			$form['formats'][$field]['position']['x'] = array(
				'#type' => 'textfield',
				'#title' => t('Position X'),
				'#required' => FALSE,
				'#default_value' => !empty($this->options['formats'][$field]['position']['x']) ? $this->options['formats'][$field]['position']['x'] : '',
			);
			
			$form['formats'][$field]['position']['y'] = array(
				'#type' => 'textfield',
				'#title' => t('Position Y'),
				'#required' => FALSE,
				'#default_value' => !empty($this->options['formats'][$field]['position']['y']) ? $this->options['formats'][$field]['position']['y'] : '',
			);
			
			$form['formats'][$field]['position']['width'] = array(
				'#type' => 'textfield',
				'#title' => t('Width'),
				'#required' => FALSE,
				'#default_value' => !empty($this->options['formats'][$field]['position']['width']) ? $this->options['formats'][$field]['position']['width'] : '',
			);
			
			$form['formats'][$field]['position']['height'] = array(
				'#type' => 'textfield',
				'#title' => t('Height'),
				'#required' => FALSE,
				'#default_value' => !empty($this->options['formats'][$field]['position']['height']) ? $this->options['formats'][$field]['position']['height'] : '',
			);
			
			
			
			$form['formats'][$field]['text'] = array(
				'#type' => 'fieldset',
				'#title' => t('Text Settings'),
				'#collapsed' => FALSE,
				'#collapsable' => TRUE,
			);
			
			$form['formats'][$field]['text']['font_size'] = array(
				'#type' => 'textfield',
				'#title' => t('Font Size'),
				'#size' => 10,
				'#default_value' => isset($this->options['formats'][$field]['text']['font_size']) ? $this->options['formats'][$field]['text']['font_size'] : '',
			);
			$form['formats'][$field]['text']['font_family'] = array(
				'#type' => 'select',
				'#title' => t('Font Family'),
				'#required' => TRUE,
				'#options' => $fonts,
				'#size' => 5,
				'#default_value' => !empty($this->options['formats'][$field]['text']['font_family']) ? $this->options['formats'][$field]['text']['font_family'] : 'default',
			);
			$form['formats'][$field]['text']['font_style'] = array(
				'#type' => 'checkboxes',
				'#title' => t('Font Style'),
				'#options' => $font_styles,
				'#size' => 10,
				'#default_value' => $this->options['formats'][$field]['text']['font_style'] == NULL ? $this->display->handler->get_option('default_font_style') : $this->options['formats'][$field]['text']['font_style'],
			);
			$form['formats'][$field]['text']['align'] = array(
				'#type' => 'radios',
				'#title' => t('Alignment'),
				'#options' => $align,
				'#default_value' => $this->options['formats'][$field]['text']['align'] == NULL ? $this->display->handler->get_option('default_text_align') : $this->options['formats'][$field]['text']['align'],
			);
			$form['formats'][$field]['text']['color'] = array(
				'#type' => 'textfield',
				'#title' => t('Text Color (Hexadecimal)'),
				'#size' => 10,
				'#default_value' => $this->options['formats'][$field]['text']['color'] == NULL ? $this->display->handler->get_option('default_text_color') : $this->options['formats'][$field]['text']['color'],
			);
			$form['formats'][$field]['render'] = array(
				'#type' => 'fieldset',
				'#title' => t('Render Settings'),
				'#collapsed' => FALSE,
				'#collapsable' => TRUE,
			);
			$form['formats'][$field]['render']['is_html'] = array(
				'#type' => 'checkbox',
				'#title' => t('Render As HTML'),
				'#default_value' => isset($this->options['formats'][$field]['render']['is_html']) ? $this->options['formats'][$field]['render']['is_html'] : 1,
			);
			
			$form['formats'][$field]['render']['eval_before'] = array(
				'#type' => 'textarea',
				'#title' => t('PHP Code Before Output'),
				'#default_value' => isset($this->options['formats'][$field]['render']['eval_before']) ? $this->options['formats'][$field]['render']['eval_before'] : '',
			);
			
			$form['formats'][$field]['render']['eval_after'] = array(
				'#type' => 'textarea',
				'#title' => t('PHP Code After Output'),
				'#default_value' => isset($this->options['formats'][$field]['render']['eval_after']) ? $this->options['formats'][$field]['render']['eval_after'] : '',
			);
			
		}
		
		$form['leading_template'] = array(
			'#type' => 'select',
			'#options' => $templates,
			'#title' => t('Leading PDF Template'),
			'#required' => FALSE,
			'#description' => t('Here you specify a PDF file to be printed in front of every row.'),
			'#default_value' => $this->options['leading_template'],
		);
		
		
		$form['template'] = array(
			'#type' => 'select',
			'#options' => $row_templates,
			'#title' => t('Template PDF'),
			'#description' => t('Here you specify a PDF file on which the content is printed. The first page of this document is used for the first page, in the target document. The second page is used for the second page in the target document and so on. If the target document has more that this template file, the last page of the template will be repeated. The leading document has no effect on the order of the pages. This option does not override the same option for the whole document. This template will be applyed addtionaly. The page format is defined by the document template, if it is defined.'),
			'#default_value' => $this->options['template'],
		);
		
		
		$form['succeed_template'] = array(
			'#type' => 'select',
			'#options' => $templates,
			'#title' => t('Succeed PDF Template'),
			'#required' => FALSE,
			'#description' => t('Here you specify a PDF file to be printed after the main content.'),
			'#default_value' => $this->options['succeed_template'],
		);
		
		
		$form['template_file'] = array(
			'#type' => 'file',
			'#title' => t('Upload New Template File'),
		);


	}
	
	function options_submit(&$form, &$form_state) {
		$default = $this->display->handler->get_option('default_font_style');
		foreach($form_state['values']['row_options']['formats'] as $id => $field) {
			
			// Reset to default, if the elements are equal to the default settings
			if(count(array_diff($default, $field['text']['font_style'])) == 0 &&  count(array_diff($field['text']['font_style'], $default)) == 0){
				$form_state['values']['row_options']['formats'][$id]['text']['font_style'] = NULL;
			}
			
			if($field['text']['align'] == $this->display->handler->get_option('default_text_align')){
				$form_state['values']['row_options']['formats'][$id]['text']['align'] = NULL;
			}	
		}

		$files_path = file_directory_path();
		$template_dir = variable_get('views_pdf_template_path','views_pdf_templates');
		$dir = $files_path.'/'.$template_dir;
		if(!is_dir($dir)) {
			@mkdir($dir);
			@chmod($dir, 0775);
		}
		
		if(is_writable($dir)) {			
			if($file) {
				$form_state['values']['row_options']['template'] = $file->destination;
			}
		}
		
		if($form_state['values']['row_options']['leading_template'] == t(' -- None -- ')) {
			$form_state['values']['row_options']['leading_template'] = '';
		}
		
		if($form_state['values']['row_options']['template'] == t(' -- None -- ')) {
			$form_state['values']['row_options']['template'] = '';
		}
		
		if($form_state['values']['row_options']['succeed_template'] == t(' -- None -- ')) {
			$form_state['values']['row_options']['succeed_template'] = '';
		}

	}



}