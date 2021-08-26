<?php

namespace Drupal\grafico_indicadores\Controller;

use Drupal\charts\Services\ChartsSettingsServiceInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Uuid\UuidInterface;

/**
 * Grafico Indicadores.
 */
class GraficoIndicadores extends ControllerBase {

  /**
   * Configuraciones del grafico.
   *
   * @var \Drupal\charts\Services\ChartsSettingsServiceInterface
   */
  protected $chartSettings;

  /**
   * El servicio de mensajeria.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * El servicio UUID.
   *
   * @var \Drupal\Component\Uuid\UuidInterface
   */
  protected $uuidService;

  /**
   * Construct.
   *
   * @param \Drupal\charts\Services\ChartsSettingsServiceInterface $chartSettings
   *   Configuraciones del grafico.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   El servicio de mensajeria.
   * @param \Drupal\Component\Uuid\UuidInterface $uuidService
   *   El servicio UUID.
   */
  public function __construct(ChartsSettingsServiceInterface $chartSettings, MessengerInterface $messenger, UuidInterface $uuidService) {
    $this->chartSettings = $chartSettings->getChartsSettings();
    $this->messenger = $messenger;
    $this->uuidService = $uuidService;
  }

  /**
   * Pintar grafico.
   *
   * @return array
   *   Array a renderizar.
   */
  public function ejemplo1() {

    $library = $this->chartSettings['library'];
    if (empty($library)) {
      $this->messenger->addError($this->t('Por favor configurar primero el grafico.'));
      return [];
    }

    // If you want to include raw options, you can do so like this.
    // $options = [
    // 'chart' => [
    // 'backgroundColor' => '#000000'
    // ]
    // ];.
    $build = [
      '#type' => 'chart',
      '#chart_type' => $this->chartSettings['type'],
      '#title' => $this->t('Grafico Inicadores'),
      '#title_position' => 'out',
      '#tooltips' => $this->chartSettings['display']['tooltips'],
      '#data_labels' => $this->chartSettings['data_labels'] ?? '',
      '#colors' => $this->chartSettings['display']['colors'],
      '#background' => $this->chartSettings['display']['background'] ? $this->chartSettings['display']['background'] : 'transparent',
      '#legend' => !empty($this->chartSettings['display']['legend_position']),
      '#legend_position' => $this->chartSettings['display']['legend_position'] ? $this->chartSettings['display']['legend_position'] : '',
      '#width' => $this->chartSettings['display']['dimensions']['width'],
      '#height' => $this->chartSettings['display']['dimensions']['height'],
      '#width_units' => $this->chartSettings['display']['dimensions']['width_units'],
      '#height_units' => $this->chartSettings['display']['dimensions']['height_units'],
     // '#raw_options' => $options,
      '#chart_id' => 'foobar',
    ];

    $categories = ['Valor 1', 'Valor 2', 'Valor 3', 'Valor 4'];

    $build['xaxis'] = [
      '#type' => 'chart_xaxis',
      '#labels' => $categories,
      '#title' => $this->chartSettings['xaxis']['title'] ? $this->chartSettings['xaxis']['title'] : FALSE,
      '#labels_rotation' => $this->chartSettings['xaxis']['labels_rotation'],
      '#axis_type' => $this->chartSettings['type'],
    ];

    $build['yaxis'] = [
      '#type' => 'chart_yaxis',
      '#title' => $this->chartSettings['yaxis']['title'] ? $this->chartSettings['yaxis']['title'] : '',
      '#labels_rotation' => $this->chartSettings['yaxis']['labels_rotation'],
      '#max' => $this->chartSettings['yaxis']['max'],
      '#min' => $this->chartSettings['yaxis']['min'],
    ];

    $i = 0;
    $build[$i] = [
      '#type' => 'chart_data',
      '#data' => [250, 350, 400, 200],
      '#title' => 'Dato 1',
    ];

    // Sample data format.
    $seriesData[] = [
      'name' => 'Dato 1',
      'color' => '#0d233a',
      'type' => 'area',
      'data' => [250, 350, 400, 200],
    ];
    switch ($this->chartSettings['type']) {
      default:
        $seriesData[] = [
          'name' => 'Datos 2',
          'color' => '#8bbc21',
          'type' => 'line',
          'data' => [150, 450, 500, 300],
        ];
        $seriesData[] = [
          'name' => 'Datos 3',
          'color' => '#ff0000',
          'type' => 'area',
          'data' => [[0, 50], [50, 50], [50, 0], [0, 0], [0, 50]],
        ];
      case 'pie':
      case 'donut':

    }

    foreach ($seriesData as $index => $data) {
      $build[$index] = [
        '#type' => 'chart_data',
        '#data' => $data['data'],
        '#title' => $data['name'],
        '#color' => $data['color'],
        '#chart_type' => $data['type'],
      ];
    }

    // Creates a UUID for the chart ID.
    $chartId = 'chart-' . $this->uuidService->generate();

    $build['#id'] = $chartId;

    return $build;
  }

  /**
   * Display Two.
   *
   * @return array
   *   Array a renderizar.
   */
  public function ejemplo2() {

    $options = [
            'chart' => [
            'fillOpacity' => '0.05'
                      ]
              ];

    $chart = [];
    $chart['ejemplo_2'] = [
      '#type' => 'chart',
      '#chart_type' => 'scatter',
      '#width' => $this->chartSettings['display']['dimensions']['width'],
      '#height' => $this->chartSettings['display']['dimensions']['height']
    ];
    $chart['ejemplo_2']['xaxis'] = [
      '#type' => 'chart_xaxis',
      '#title' => 'Grado de Adopción (X)',
      '#min' => '0',
      '#max' => '100',
      //'#labels' => [$this->t('Jan'), $this->t('Feb'), $this->t('Mar')],
    ];
    $chart['ejemplo_2']['yaxis'] = [
      '#type' => 'chart_yaxis',
      '#title' => 'Grado de Apropiación (Y)',
      '#min' => '0',
      '#max' => '100',
      //'#labels' => [$this->t('Jan'), $this->t('Feb'), $this->t('Mar')],
    ];

    $chart['ejemplo_2']['cuadrante1'] = [
      '#type' => 'chart_data',
      '#chart_type' => 'area',
      '#title' => 'Empresa Analoga',
      '#color' => '#FCEDED',
      '#raw_options' => $options,
      '#data' => [[0, 50], [50, 50], [50, 0], [0, 0], [0, 50]]
    ];
    $chart['ejemplo_2']['cuadrante2'] = [
      '#type' => 'chart_data',
      '#chart_type' => 'area',
      '#title' => 'Empresa con Adopción Tecnológica',
      '#color' => '#D0F5DA',
      '#raw_options' => $options,
      '#data' => [[50, 0], [50,50], [100, 50], [100, 0], [50, 0]]
    ];
    $chart['ejemplo_2']['cuadrante3'] = [
      '#type' => 'chart_data',
      '#chart_type' => 'area',
      '#title' => 'Empresa Digital',
      '#color' => '#D6EAFF',
      '#raw_options' => $options,
      '#data' => [[50, 50], [50, 100], [100, 100], [100, 50], [50, 50]]
    ];
    $chart['ejemplo_2']['cuadrante4'] = [
      '#type' => 'chart_data',
      '#chart_type' => 'area',
      '#title' => 'Empresa con Apropiación Tecnológica',
      '#color' => '#FFFDD6',
      '#raw_options' => $options,
      '#data' => [[0, 100], [50, 100], [50, 50], [0, 50], [0, 100]]
    ];
    //  AZUL CUADRANTE PEQUEÑO
    $chart['ejemplo_2']['cuadrante5'] = [
      '#type' => 'chart_data',
      '#chart_type' => 'area',
      '#title' => 'En proceso',
      '#color' => '#7FC1FF',
      '#raw_options' => $options,
      '#data' => [[50, 50], [50, 60], [60, 60], [60, 50], [50, 50]]
    ];
    //  VERDE
    $chart['ejemplo_2']['cuadrante6'] = [
      '#type' => 'chart_data',
      '#chart_type' => 'area',
      '#title' => 'Clase Mundial',
      '#color' => '#7FFFA3',
      '#raw_options' => $options,
      '#data' => [[90, 100], [100, 100], [100, 90], [90, 90], [90, 100]]
    ];
    //  POLIGONOE
    $chart['ejemplo_2']['cuadrante7'] = [
      '#type' => 'chart_data',
      '#chart_type' => 'area',
      '#title' => 'En transicion',
      '#color' => '#F2F2F2',
      '#raw_options' => $options,
      '#data' => [[0, 60], [60, 60], [60, 0], [40, 0], [40, 40], [0, 40], [0, 60]]
    ];

    // PUNTOS
    $chart['ejemplo_2']['puntos'] = [
      '#type' => 'chart_data',
      '#title' => 'Yolima Gordillo',
      '#color' => '#313131',
      '#data' => [[40, 30]]
    ];

    return $chart;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('charts.settings'),
      $container->get('messenger'),
      $container->get('uuid')
    );
  }

}
