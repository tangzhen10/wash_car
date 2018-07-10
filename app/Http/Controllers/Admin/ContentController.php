<?php

namespace App\Http\Controllers\Admin;

class ContentController extends BaseController {
	
	const MODULE = 'content';
	
	public function structure() {
		
		return view('admin/content/structure/list', $this->data);
	}
	
	public function structureForm() {
		
		$this->data['detail']        = $this->service->getStructureDetailById();
		$this->data['form_elements'] = $this->service->getFormElements();
		
		return view('admin/content/structure/form', $this->data);
	}
	
}
