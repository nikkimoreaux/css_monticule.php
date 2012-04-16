<?php

// css_monticule.php 
// a little php script to aggregate all your .css files 
// Copyright (c) 2012 Nikki Moreaux, http://diplodoc.us
// 
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included in all
// copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.

/*
	TODO <base> support
*/
/*
	TODO .htaccess auto optimization
*/
/*
	TODO perms checking, argument checking...
*/


function css_monticule(){
	
	
	/**
	 * SETUP css_monticule
	 */
	
	clearstatcache();
	
	$css_monticule_version = "0.2";
	
	
	$css_monticule_args = func_get_args();
	
	// work relatively to the calling file 
	// (if css_monticule.php isn't in the same folder as the calling page)
	$debug = debug_backtrace();
	$calling_folder = dirname($debug[0]["file"]);
	
	// css_monticule cache folder
	$cache_folder_name = "css_monticule_cache";
	
	// num files to keep in the cache
	$cache_folder_size = 20;
	
	// all valid .css paths...
	// as asked (./css/reset.css ...)
	$allowed_paths = array();
	
	// as realpath() solved
	$real_paths = array();
	
	// rejected .css as asked (./css/reset.css ...)
	$rejected_paths = array();
	
	// hashs
	$real_paths_hash = "";
	$time_hash = 0;
	$size_hash = 0;
	
	// LESS CSS parser needed?
	$less = false;
	
	
	/**
	 * PARSE Arguments
	 */
	
	foreach($css_monticule_args as $arg){
		$path = realpath($calling_folder ."/". $arg);
		
		/*
			TODO curious bug
		*/
		if(!$path){
			$path = realpath($calling_folder ."/". $arg);
		}
		
		if($path !== false){
			// basic security: check if the file ends with .css
			if(stripos(strrev(strtolower($arg)),"ssc.") === 0){
				$real_paths[] = $path;
				$allowed_paths[] = $arg;
				$time_hash += filemtime($path);
				$size_hash += filesize($path);
			}elseif(stripos(strrev(strtolower($arg)),"ssel.") === 0){
				$less = true;
				$real_paths[] = $path;
				$allowed_paths[] = $arg;
				$time_hash += filemtime($path);
				$size_hash += filesize($path);
				
			}else{
				//not ending in .css or .less
				$rejected_paths[] = $arg . " (bad extention)";
			}
		}else{
			//don't exist
			$rejected_paths[] = $arg . " (don't exist)";
		}
	}
	$real_paths = array_unique($real_paths);
	$allowed_paths = array_unique($allowed_paths);
	$rejected_paths = array_unique($rejected_paths);
	
	
	/**
	 * FIND Cache filename thru hash
	 */
	
	$real_paths_hash = md5(implode("",$real_paths));
	
	// files (real)paths_hash + time_hash + size_hash + htaccess marker
	$css_monticule_package_name = $real_paths_hash . "_".$time_hash . "_".$size_hash . "_monticule.css";
	
	$cache_folder = $calling_folder ."/". $cache_folder_name;
	$css_monticule_package_path = $calling_folder ."/". $cache_folder_name . "/" . $css_monticule_package_name;
	
	
	/**
	 * CREATE the css_monticule_package file if it don't exist
	 */
	
	$package = "";
	
	if(!file_exists($css_monticule_package_path)){
		
		
		$package = "/* Generated by css_monticule.php v".$css_monticule_version." at ". date('H:i:s d-M-Y  O')." \n * Copyright (c) 2012 Nikki Moreaux, http://diplodoc.us \n * \n";
		
		
		if(count($rejected_paths)>0){
			$package .= " * [NOTIFICATION] Unfortunately some files have been rejected: \n * ";
			$package .= implode(" \n * ",$rejected_paths);
			$package .= " \n *\n";
		}

		$package .= " * Here are all your .css files: */ \n\n";
		
		
		foreach($real_paths as $key => $path){
			$package .= "/* ". $allowed_paths[$key] . ": */\n". file_get_contents($path) . "\n\n\n";
		}
		
		
		
		if(!is_dir($cache_folder)){
			if(file_exists($cache_folder)){
				unlink($cache_folder);
			}
				
			if(!mkdir($cache_folder)){
				trigger_error("come on, let me write " . $cache_folder);
			}
		}
		
		//remove oldest files
		$cache_files = array();
		if($handle = opendir($cache_folder)){
			while(false !== ($file = readdir($handle))){
				if(stripos(strrev(strtolower($file)),"ssc.elucitnom_") === 0){
					$file_path = $cache_folder."/".$file;
					array_push($cache_files,array("path" => $file_path,"time" => filemtime($file_path)));
				}
			}
		}
		function css_monticule_cmpBySort($b,$a){
		    return $a["time"] - $b["time"];
		}
		usort($cache_files,"css_monticule_cmpBySort");
		if(count($cache_files) > $cache_folder_size){
			$old_cache_files = array_splice($cache_files,$cache_folder_size);
			foreach($old_cache_files as $old_file){
				
				unlink($old_file["path"]);
			}
		}
		
		if($less){
			include_once("lessc.inc.php");
			$less = new lessc();
			$package = $less->parse($package);
		}
		
		file_put_contents($css_monticule_package_path,$package,LOCK_EX);
		
	}
	
	
	
	/**
	 * OUTPUT Script tag
	 */
	
	
	$output = "<";
	if($size_hash < 10000){
		if($package === ""){
			$package = file_get_contents($css_monticule_package_path);
		}
		$output .= "style type=\"text/css\">\n". $package ."\n</style>";
	}else{
		
		$css_monticule_package_url = $cache_folder_name . "/" . $css_monticule_package_name;
		$output .= 'link rel="stylesheet" type="text/css" href="' . $css_monticule_package_url . '" />';
	}
	
	
	
	print($output . "\n");
	
	
	
}




