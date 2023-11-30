<?php
//Copyright (c) 2022-2023 Carpe Diem Software Developing by Alex Versetty.
//http://carpediem.0fees.us/
//version 1.0

//Установка: 
//создать пустой плагин, вставить этот код, в разделе Системные события поставить галку напротив OnWebPagePrerender
//Описание:
//Ищет на страницах строки вида {camera*<название>*<id камеры>} 
//и преобразует в превью+ссылка на камеру.
//Пример: {camera*Ворота*0cb2cd3d-cba3-483b-86fa-646809ffedc0} 

/////////////////////////////// Конфигурация /////////////////////////////////////////
$key = "КЛЮЧ ИЗ КОНФИГУРАТОРА";		//пример: gF6edf45he6fg
$server = "СЕРВЕР:ПОРТ";			//пример: myserver.ru:8000
$img_width = 360;					//ширина снимка/видео
$img_height = 202;					//высота снимка/видео
$title_font_size = '70%';			//размер шрифта для названий
//////////////////////////////////////////////////////////////////////////////////////

$output = &$modx->resource->_output;

$regex = '/\{camera\*([^*\{]+)\*([^*\}]+)\}/';
$matches = array();
preg_match_all($regex, $output, $matches);

if (count($matches) > 0) {
    for($i = 0; $i < count($matches[0]); $i++) {
		$name = $matches[1][$i];
        $camID = $matches[2][$i];
        $jpeg_url = "https://{$server}/image/?key={$key}&cam={$camID}";
		$click_url = "https://{$server}/player/?key={$key}&cam={$camID}";

		$html = "\n<div style='padding: 5px; display: inline-block;'><center>";
        $html .= "\n\t<a class='cameraLink' href='{$click_url}'>";
        $html .= "\n\t\t<img width='{$img_width}' height='{$img_height}' border='1' src='{$jpeg_url}' "; 
        $html .= "style='background: url(\"rtsp2hls/modx_plugin/cam-loading.png\") no-repeat; background-size: 100% 100%;' onerror=\"this.src='rtsp2hls/modx_plugin/cam-error.png'\" />";
        $html .= "\n\t</a>\n\t<br /><span style='vertical-align: top; font-size: {$title_font_size};'>{$name}</span>";
        $html .= "\n</center></div>";

        if ($i == (count($matches[0]) - 1)) {
            $html .= "\n\n<br /><br /><a href='javascript:;' id='playAll'>&#9654; Воспроизвести все</a>";
            $html .= "\n<script>";
            $html .= "\n\t$('#playAll').click(function() {";
            $html .= "\n\t\t$('.cameraLink').each(function() {";
            $html .= "\n\t\t\tvar url = $(this).attr('href');";
            $html .= "\n\t\t\t$(this).html(\"<iframe allowfullscreen='true' webkitallowfullscreen='true' mozallowfullscreen='true'";
            $html .= "width='{$img_width}' height='{$img_height}' scrolling='no' style='border: 1px solid #0f7096;' src='\" + url + \"'></iframe>\");";
            $html .= "\n\t\t}); $('#playAll').hide();";
            $html .= "\n\t});";
            $html .= "\n</script>\n";
        }

		$output = str_replace($matches[0][$i], $html, $output);
    }
}