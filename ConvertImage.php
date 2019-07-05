<?
namespace frontend\components;

use Yii;


class ConvertImage {
    
  static function toWebp($imageSrc)/*в аргументsrc изображения*/
  {
    $src=$imageSrc;
    $infoImage=pathinfo($src);/*атрибуты файла*/
    $nameImage=$infoImage['filename'];/*имя файла без расширения*/
    $absoluteDirForImage=$infoImage['dirname'].'/';/*абсолютная ссылка до файла*/
    $srcWebpImage=$absoluteDirForImage.$nameImage.'.webp';/*создаем ссылку на будующее изображение с расширением webp*/
    //$im = new \Imagine\Gd\Imagine();
    $im = new \Imagine\Imagick\Imagine();
    //$im = new \Imagine\Gmagick\Imagine();
    $im->open($src)/*открываем изображение для работы*/
       ->save($srcWebpImage,['jpeg_quality' => 100, 'png_compression_level' => 0, 'webp_quality'=>80]);/* сохраняем изображение по указанной ссылке с указанными параметрами.*/
  }

  static function removeSrcToWebp($a,$flag=false)/*В аргумент src изображения, в аргумент flag ставим true, если нам нужно получить чистый путь к картинке*/
  {
    $imageSrc=$a;
    $formats=$_SERVER['HTTP_ACCEPT'];/*проверяем поддержку webp на стороне клиента*/
    if (stripos($formats, 'image/webp')) {/*если поддерживает*/
      $format=substr($imageSrc,strripos($imageSrc,'.')+1);/*убираем расширение файла*/
      $webpSrc = substr($imageSrc,0,-(strlen($format))).'webp';/*и заменяем на расширение webp*/
      $absolurtDir=$_SERVER['DOCUMENT_ROOT'].$webpSrc;/*Получаем абсолютный путь к файлу*/
      $src=file_exists($absolurtDir) ? $webpSrc : $imageSrc;/*если файл webp есть, то подставляем его, иначе родной*/   
    }else{
    $src=$imageSrc; /*иначе возвращаем родной src*/
    }
    $attrSrc="src='".$src."'";
    $imgAttrSrc= $flag ? $src :  $attrSrc;

    return $imgAttrSrc;/*Возвращается готовая строчка src*/
  }

  static function ConvertToWebp($dir) /*В аргумент $dir передаем абсолютный путь каталога*/
  {
    ini_set ( 'max_execution_time', 1200); 
    $absolutDir=$dir;
    $results = scandir($absolutDir);/*Возвращает массив с содержанием каталога*/

    foreach ($results as $value) {
      if ($value=='.' || $value=='..') {/*игнорируем навигационные символы, которые возвращает scandir*/
          continue;
      }
      if (is_file($absolutDir.'/'.$value)) {
        $image=$absolutDir.'/'.$value;/*Получаем абсолютную ссылку на файл*/
        $infoImage=pathinfo($image);
        $nameImage=$infoImage['filename'];
        $absoluteDirForImage=$infoImage['dirname'].'/';
        $arg=mime_content_type($image);/*получаем расширение файла в нижнем регистре*/
        if ($arg=='image/webp') {
          //continue;
          unlink($image); 
        }
        if ($arg=='image/jpeg' || $arg=='image/png') {
          continue;
          $im = new \Imagine\Gd\Imagine();
          $srcWebpImage=$absoluteDirForImage.$nameImage.'.webp';
          $im->open($image)
             ->save($srcWebpImage,['jpeg_quality' => 100, 'png_compression_level' => 0, 'webp_quality'=>100]);
        }
        else{
          continue; 
        }
      }
      if (is_dir($absolutDir.'/'.$value)) {
        $dir=pathinfo($absolutDir.'/'.$value);
        ConvertImage::ConvertToWebp($dir['dirname'].'/'.$dir['filename'].'/');
      }   
    }
  }    
}
?>
