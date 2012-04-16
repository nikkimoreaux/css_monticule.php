# Let's merge some .css files

Tired of all these css files? 

**css_monticule.php** is a little php script that aggregate all your .css 
files in one, with full automatic versioning and LESS CSS parsing.

## Setup

1. Drag **css_monticule.php** and **lessc.inc.php** on your web server

2. Include and call **css_monticule.php** within your page *via*: 

		<?php
		include_once("css_monticule.php");
		css_monticule("file1.css","file2.css","file3.less");
		?>
it will automatically generate this stylesheet snippet: 

		<link rel="stylesheet" type="text/css" href="css_monticule_cache/1d6ef0c6c917ae65308954239f2a5653_4003591211_5070_monticule.css" />
		
3. That's all. **css_monticule.php** take care of the rest.

## MIT License

**css_monticule.php**

Copyright (c) 2012 Nikki Moreaux, http://diplodoc.us

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.