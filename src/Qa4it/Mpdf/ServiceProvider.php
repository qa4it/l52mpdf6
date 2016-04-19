<?php
namespace Qa4it\Mpdf;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Config;

include base_path('vendor/mpdf/mpdf/mpdf.php');


class ServiceProvider extends BaseServiceProvider {

  /**
   * Indicates if loading of the provider is deferred.
   *
   * @var bool
   */
  protected $defer = false;


  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register() {
    $this->app->bind('mpdf.wrapper', function($app,$cfg)  {
      $app['mpdf.pdf'] = $app->share(function($app) use($cfg) {
        if( ! empty($cfg)) {
          foreach($cfg as $key => $value) {
            Config::set('mpdf.'.$key, $value);
          }
        }
        $mpdf=new \mPDF(
            Config::get('mpdf.mode'),
            Config::get('mpdf.defaultFontSize'),
            Config::get('mpdf.defaultFont'),
            Config::get('mpdf.marginLeft'),
            Config::get('mpdf.marginRight'),
            Config::get('mpdf.marginTop'),
            Config::get('mpdf.marginBottom'),
            Config::get('mpdf.marginHeader'),
            Config::get('mpdf.Footer'),
            Config::get('mpdf.orientation')
        );
        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle(Config::get('mpdf.title'));
        $mpdf->SetAuthor(Config::get('mpdf.author'));
        $mpdf->SetWatermarkText(Config::get('mpdf.watermark'));
        $mpdf->showWatermarkText = Config::get('mpdf.showWatermark');
        $mpdf->watermark_font = Config::get('mpdf.watermarkFont');
        $mpdf->watermarkTextAlpha = Config::get('mpdf.watermarkTextAlpha');
        $mpdf->SetDisplayMode(Config::get('mpdf.displayMode'));

        return $mpdf;
      });

      return new PdfWrapper($app['mpdf.pdf']);
    });
  }

  /**
   * Get the services provided by the provider.
   *
   * @return array
   */
  public function provides() {
    return array('mpdf.pdf');
  }

}