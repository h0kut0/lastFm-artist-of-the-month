<?php

//Chrome Logger
// include '../ChromePhp.php';

//アーティストイメージ
$jsonUrl = "http://ws.audioscrobbler.com/2.0/?method=user.gettopartists&user=hokuto&api_key=6844510cae2459a0f3b85856f753c316&format=json&period=1month&limit=1";

$json = file_get_contents($jsonUrl,true);
$array = json_decode($json,true);

$artistImg = $array['topartists']['artist']['image'][4]['#text'];
$ext = getimagesize($artistImg)['mime'];
$artistImgWidth = getimagesize($artistImg)['0'];
$artistImgHeight = getimagesize($artistImg)['1'];

//アーティストイメージは拡張子によって場合分けして生成
if($ext === 'image/jpeg')
  $aImg = imagecreatefromjpeg($artistImg);
elseif($ext === 'image/png')
  $aImg = imagecreatefrompng($artistImg);
elseif($ext === 'image/gif')
  $aImg = imagecreatefromgif($artistImg);

//幅を300に比率を保って縮小 元画像の比率を計算し、高さを設定
$width = 300;
$propotion = $artistImgWidth / $artistImgHeight;
$height = $width / $propotion;

$aImgBg = imagecreatetruecolor($width, $height);
imagecopyresampled($aImgBg, $aImg, 0, 0, 0, 0, $width, $height, $artistImgWidth, $artistImgHeight);
imagedestroy($aImg);

//ロゴ
$logoImg = 'http://lastfmlogos.info/image.php?user=hokuto&nb=1&type=1month&color=trans&layout=TwoCols&blackbg=';
$logoImgWidth = getimagesize($logoImg)['0'];
$logoImgHeight = getimagesize($logoImg)['1'];

//ロゴはpng固定で生成
$lImg = imagecreatefrompng($logoImg);
$logoBgColor = imagecolorallocate($lImg,255,255,255); //背景色セット
imagefill($lImg, 0, 0, $logoBgColor); //背景を白く塗る
imagecolortransparent($lImg,$logoBgColor); //透明化

//合成
$background = imagecreatetruecolor($width, $height+$logoImgHeight); //背景イメージを生成
$bgColor = imagecolorallocate($background, 255, 255, 255); //背景色をセット
imagefill($background, 0, 0, $bgColor);//背景を白く塗る
imagecopy($background, $lImg, 150-($logoImgWidth/4), 0, 0, 0, $logoImgWidth, $logoImgHeight);//ロゴを貼る
imagedestroy($lImg);

imagecopy($background, $aImgBg, 0, $logoImgHeight, 0, 0, $artistImgWidth, $artistImgHeight);//アーティストイメージを貼る
imagedestroy($aImgBg);

//ヘッダー
header('Content-type: image/png');

//画像をPNGとして出力
imagepng($background);
//JPEGの圧縮率を変更したい場合は以下のように設定（例：80）
//imagejpeg($img, null, 80);
//メモリ解放
imagedestroy($background);

?>
