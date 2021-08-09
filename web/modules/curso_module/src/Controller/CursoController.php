<?php

namespace Drupal\curso_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\curso_module\Services\Repetir;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CursoController extends ControllerBase
{

  /** @var Repetir */
  private $repetir;

  public function __construct(Repetir $repetir, ConfigFactoryInterface $configFactory)
  {
    $this->repetir = $repetir;
    $this->configFactory = $configFactory;
  }

  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('curso_module.repetir'),
      $container->get('config.factory')
    );
  }

  public function home(NodeInterface $node) {

    $resultado = $this->repetir->repetir('curso ');

    return [
      '#theme' => 'curso_plantilla',
      '#etiqueta' => $node->label(),
      '#tipo' => $resultado,
    ];
  }

  public function formController() {

    $form = $this->formBuilder()->getForm('\Drupal\curso_module\Form\CursoForm');

    $build = [];

    $markup = ['#markup' => 'Esta es la pagina del formulario',];

    $build[] = $markup;
    $build[] = $form;

    return $build;
  }

  public function configCurso() {

    $config = $this->config('system.site');

    dpm($config, 'config');
    dpm($config->get('name'), 'name');

    $configEditable = $this->configFactory->getEditable('system.site');

    dpm($configEditable, 'config editable');

    $configEditable->set('slogan', 'Slogan editado en codigo');
    $configEditable->save();

    return ['#markup' => 'ruta de configuracion'];
  }

}