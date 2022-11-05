<?php

define('PAGINATION_COUNT' , 10); // Constant of Pagination

function errorMessage($message, $statusCode)
{
    return response([
        'status'=>'error',
        'message'=>$message
    	],
        $statusCode);
}


/* GET FOLDER FOR STYLE BASED ON LANGUAGES */
if(!function_exists('get_folder')){
	function get_folder(){
		return app()->getLocale() == 'ar' ? 'css-rtl' : 'css';
	}
}


if(!function_exists('entityNotFound')){
	function entityNotFound()
	{
	    return response([
	        'status'=>'error',
	        'message'=>'Entity Not Found'],
	        404);
	}
}

if(!function_exists('noDataFound')){
	function noDataFound($msg)
	{
	    return response([
	        'status'=>'error',
	        'message'=>$msg],
	        404);
	}
}

/* GET LANGUAGES FOR DATATABLE*/
if(!function_exists('datatableLang')){
	function datatableLang(){
		return [
                'sProcessing'     => __('datatable.sProcessing'),
                'sLengthMenu'     => __('datatable.sLengthMenu'),
                'sZeroRecords'    => __('datatable.sZeroRecords'),
                'sEmptyTable'     => __('datatable.sEmptyTable'),
                'sInfo'           => __('datatable.sInfo'),
                'sInfoEmpty'      => __('datatable.sInfoEmpty'),
                'sInfoFiltered'   => __('datatable.sInfoFiltered'),
                'sInfoPostFix'    => __('datatable.sInfoPostFix'),
                'sSearch'         => __('datatable.sSearch'),
                'sUrl'            => __('datatable.sUrl'),
                'sInfoThousands'  => __('datatable.sInfoThousands'),
                'sLoadingRecords' => __('datatable.sLoadingRecords'),
                'oPaginate'       => [
                    'sFirst'         => __('datatable.sFirst'),
                    'sLast'          => __('datatable.sLast'),
                    'sNext'          => __('datatable.sNext'),
                    'sPrevious'      => __('datatable.sPrevious'),
                ],
                'oAria'            => [
                    'sSortAscending'  => __('datatable.sSortAscending'),
                    'sSortDescending' => __('datatable.sSortDescending'),
                ],
            ];
	}
}


/* Function to return Extension Validate for image */
if(!function_exists('validate_img')){
	function validate_img($ext = null){
		if ($ext === null) {
			return 'required|image|mimes:jpg,jpeg,bmp,png';
			// return 'image|mimes:jpg,jpeg,bmp,png,gif|max:5000';
		}else{
			return 'required|image|mimes:'.$ext;
		}
	}
}

/* GET ALL ACTIVE LANGUAGES */
if(!function_exists('get_languages')){
	function get_languages(){
		return \App\Models\Language::active()->selection()->get();
	}
}

if(!function_exists('lang')){
	function lang(){
		return app()->getLocale();
	}
}


/* GET DEFAULT LANGUAGE */
if(!function_exists('getLang')){
	function getLang(){
		return Config::get('app.locale');
	}
}


/* UPLOAD IMAGE FUNCTION */
if(!function_exists('uploadImage')){
	function uploadImage($folder, $image)
	{
	    $image->store('/', $folder);
	    $filename = $image->hashName();
	    $path = 'images/' . $folder . '/' . $filename;
	    return $path;
	}
}


/* UNLINK IMAGE FUNCTION */
if(!function_exists('UnlinkImage')){
	function UnlinkImage($image){
	    $image 		= Str::after($image, 'assets/');
        $img_path 	= public_path('assets/' . $image);
        if (file_exists($img_path)) {
	        return unlink($img_path); //delete from folder
	    }
	}
}


/* UPLOAD VEDIO FUNCTION */
if(!function_exists('uploadVideo')){
	function uploadVideo($folder, $video){
	    $video->store('/', $folder);
	    $filename = $video->hashName();
	    $path = 'video/' . $folder . '/' . $filename;
	    return $path;
	}
}

if(!function_exists('getPaymentInfo')){
	function getPaymentInfo(){
		return \App\Models\PaymentInfo::findOrFail(1);
	}
}

if(!function_exists('getSettingsForShipping')){
	function getSettingsForShipping($user_id){
		return \App\Models\Setting::where('user_id' , $user_id)->get();
	}
}

