<?php
namespace App\Util;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class CaptchaService
{
    private $session;

    public function __construct(KernelInterface $kernel, SessionInterface $session)
    {
        $this->kernel  = $kernel;
        $this->session = $session;
    }

    /**
     * Permet de générer un captcha à 4 chiffres
     *
     * @return string
     */
    public function createCaptcha()
    {
        // Enregistrement du captcha en session
        $captchaCode = mt_rand(1000, 9999);
        $this->session->set('captcha', $captchaCode);

        // Création de l'image
        $img = imagecreate(120, 50);

        // Lien vers le fichier font
        $font = $this->kernel->getProjectDir() . '/public/assets/fonts/destroy.ttf';

        // RGB colors
        $bg        = imagecolorallocate($img, 20, 30, 40); // automatiquement la couleur de fond car 1ère couleur déclarée
        $textcolor = imagecolorallocate($img, 255, 255, 255);

        $this->imagettftext_cr($img, 20, 0, 60, 20, $textcolor, $font, $captchaCode);

        ob_start();
        imagejpeg($img, null, 100);
        imagedestroy($img);
        $output = ob_get_clean();

        return base64_encode($output);
    }

    /**
     * Put center-rotated ttf-text into image
     * Same signature as imagettftext();
     *
     * @link https://www.php.net/manual/fr/function.imagettftext.php#48938
     */
    private function imagettftext_cr($img, $size, $angle, $x, $y, $color, $fontfile, $text)
    {
        // retrieve boundingbox
        $bbox = imagettfbbox($size, $angle, $fontfile, $text);
        
        // calculate deviation
        $dx = ($bbox[2] - $bbox[0]) / 2.0 - ($bbox[2] - $bbox[4]) / 2.0;         // deviation left-right
        $dy = ($bbox[3] - $bbox[1]) / 2.0 + ($bbox[7] - $bbox[1]) / 2.0;        // deviation top-bottom
        
        // new pivotpoint
        $px = $x - $dx;
        $py = $y - $dy;
        
        return imagettftext($img, $size, $angle, $px, $py, $color, $fontfile, $text);
    }
}
