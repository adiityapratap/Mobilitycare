<?php

namespace liveopencart\lib\v0030\traits;
trait html_decode {
	protected function decodeHTML($str) {
		return html_entity_decode($str, ENT_QUOTES, 'UTF-8');
	}
}
