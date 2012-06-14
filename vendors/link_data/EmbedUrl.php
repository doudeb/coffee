<?php


class Embed_url {

	var $url;
	var $hostname;
	var $title;
	var $html;
	var $description;
	var $tags;
	var $images;
	var $sortedImage;
	var $dom;

	function __construct($args = array()) {

		if ($args['url']) {
        	$this->url = $this->checkValues($args['url']);
		}
       	$this->hostname = $this->getDomainName($args['url']);
       	$this->dom		= new DOMDocument();
	}

	public function embed () {
		$this->fetchRecord();
		$this->getTags();
		$this->getTitle();
		$this->getDescription();
		$this->getImages();
		$this->sortImages();
	}

	public function get_page_title () {
		$this->fetchRecord();
		$this->getTags();
		$this->getTitle();
	}

	private function checkValues($value) {
		$value = trim($value);
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		$value = strtr($value, array_flip(get_html_translation_table(HTML_ENTITIES)));
		$value = strip_tags($value);
		$value = htmlspecialchars($value);
		return $value;
	}

	private function getDomainName ($url) {
		preg_match('@^(?:http://)?([^/]+)@i', $url, $url_host);
		$host = $url_host[1];
		// get last two segments of host name
		preg_match('/[^.]+\.[^.]+$/', $host, $url_host);
		return $url_host[0];
	}


	private function fetchRecord () {
		$ch = curl_init($this->url);
        $this->setCurlOptions($ch, array(
                sprintf('Host: %s', $this->hostname),
                sprintf('User-Agent: %s', 'Mozilla/5.0 (compatible;')));
        $data = $this->curlExec($ch);
		$this->html =  $data;
	}

	private function getTitle () {
		if (isset($this->tags['title'])) {
			$this->title = _convert($this->tags['title']);
		} else {
			$title_regex = '/<title>(.*?)<\/title>/is';
			preg_match_all($title_regex, $this->html, $title, PREG_PATTERN_ORDER);
            //$titre = eregi("<title>(.*)</title>",$this->html,$title);
			//$this->title = $title[1];
			$this->title = _convert($title[1][0]);
		}
	}

	private function getTags () {
		$this->tags = get_meta_tags($this->url);
	}

	private function getDescription () {
		$this->description = _convert($this->tags['description']);
	}

	private function getImages () {
		$this->dom->loadHTML($this->html);
		$elements = $this->dom->getElementsByTagName('meta');
		if (!is_null($elements)) {
			foreach ($elements as $element) {
				if($element->getAttribute('property') == 'og:image') {
					$this->images['image'] = $element->getAttribute('content');
					return;
				}
			}
		 }
		$image_regex = '/<img[^>]*'.'src=[\"|\'](.*)[\"|\']/Ui';
		preg_match_all($image_regex, $this->html, $img, PREG_PATTERN_ORDER);
		$this->images = $img[1];
	}

	private function sortImages () {
		if (isset($this->images['image'])) {
			$this->sortedImage[] =$this->images['image'];
			return;
		}
		$c=sizeof($this->images);
		for ($i=0;$i<=$c;$i++) {
			$this->images[$i] = $this->chroot($this->images[$i]);
			if(@$this->images[$i]
					&& $this->isImageId($this->images[$i])
					&& $this->isRelatedImage($this->images[$i]) > 40) {
				$image_data = @getimagesize(@$this->images[$i]);
				if(@$image_data) {
					list($width, $height, $type, $attr) = $image_data;
					if($width >= 300){
						$this->sortedImage[] = $this->images[$i];
					}
				}
			}
		}
        if (count($this->sortedImage) == 0) {
            $this->sortedImage[0] = null;
        }
	}

	private function isImageId ($image) {
		return preg_match('/\d{4,10}/i',$image);
	}

	private function isRelatedImage ($image) {
		similar_text($this->hostname,$this->getDomainName($image), $is_image_related);
		return $is_image_related;
	}

	private function chroot($image) {
		if (substr($image,0,1) == '/') {
			 $parsed_url = parse_url($this->url);
			 //var_dump($parsed_url);
			 return $parsed_url['scheme'] . '://' . $parsed_url['host'] . $image;
		}
		return $image;
	}

	private function setCurlOptions(&$ch, $headers = array())
    {
    	curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 4096);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    }

    private function curlExec(&$ch) {
        $res = curl_exec($ch);
        if (false === $res) {
            var_dump(curl_error($ch));
            var_dump(curl_errno($ch));
        }
        curl_close($ch);
        return $res;
    }
}
?>