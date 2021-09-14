<?php

namespace Drupal\gamification\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Render\Element;

/**
 * Form handler for the Gamification add and edit forms.
 */
class GamificationForm extends EntityForm {

  /**
   * Gamification widget manager.
   *
   * @var object
   */
  protected $gamificationWidgetManager;

  /**
   * Entity type manager object.
   *
   * @var object
   */
  protected $entityTypeManager;

  /**
   * Entity type bundle info object.
   *
   * @var object
   */
  protected $entityTypeBundleInfo;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = new static();
    $instance->gamificationWidgetManager = $container->get('plugin.manager.gamification.widget');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->entityTypeBundleInfo = $container->get('entity_type.bundle.info');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {

    $form = parent::form($form, $form_state);
    $form['#prefix'] = '<div id="edit-output">';
    $form['#suffix'] = '</div>';
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => 255,
      '#default_value' => $this->entity->getTitle(),
      '#help' => $this->t('Configuration title'),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#title' => $this->t('Machine name'),
      '#default_value' => $this->entity->id(),
      '#machine_name' => [
        'exists' => [$this, 'exists'],
        'source' => ['title'],
      ],
      '#disabled' => !$this->entity->isNew(),
      '#description' => $this->t('A unique machine-readable name for this configuration. It must only contain lowercase letters, numbers, and underscores. This name will be used for constructing the URL of the %gamification-add page, in which underscores will be converted into hyphens.'),
    ];

    $form['widget'] = [
      '#title' => $this->t('Widget options'),
      '#type' => 'fieldset',
    ];

    // Find available gamification widgets.
    $gamification_widgets = [];
    foreach ($this->gamificationWidgetManager->getGamificationWidgets() as $gamification_widget) {
      $gamification_widgets[$gamification_widget->getId()] = $gamification_widget->getLabel();
    }

    $widget_id = $this->entity->getWidgetValue();
    $form['widget']['widget'] = [
      '#type' => 'select',
      '#title' => $this->t('Widget'),
      '#options' => $gamification_widgets,
      '#default_value' => $widget_id,
      '#ajax' => [
        'callback' => '::widgetsAjaxCallback',
        'disable-refocus' => FALSE,
        'event' => 'change',
        'wrapper' => 'edit-output',
        'method' => 'replace',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Verifying...'),
        ],
      ],
      '#description' => $this->t("Widget title"),
      '#required' => FALSE,
    ];

    if (empty($widget_id)) {
      $widget_id = array_key_first($gamification_widgets);
    }

    $actions = $this->gamificationWidgetManager->getActionsFromWidget($widget_id);
    $form['widget']['action'] = [
      '#type' => 'select',
      '#title' => $this->t('Action'),
      '#options' => $actions,
      '#maxlength' => 255,
      '#default_value' => $this->entity->getAction(),
    ];

    $widget_options = $this->gamificationWidgetManager->getWidgetOptions($widget_id);
    $options_value = $this->entity->getWidgetOptionsValue();
    foreach ($widget_options as $option) {
      $form['widget']['widget_options'][$option] = [
        '#type' => 'textfield',
        '#title' => $option,
        '#maxlength' => 255,
        '#default_value' => !empty($options_value) && isset($options_value[$option]) ? $options_value[$option] : '',
      ];
    }

    $all_entity_types = $this->entityTypeManager->getDefinitions();
    foreach ($all_entity_types as $entity_type_id => $entity_type_obj) {
      if ($entity_type_obj instanceof ContentEntityType) {
        $entity_types[$entity_type_id] = $entity_type_obj->getLabel(); 
      }
    }

    $form['source'] = [
      '#title' => $this->t('Gamification source'),
      '#type' => 'fieldset',
    ];

    $form['source']['source_entity_type_id'] = [
      '#type' => 'select',
      '#title' => t('Entity Type'),
      '#description' => t('Entity type'),
      '#required' => FALSE,
      '#options' => $entity_types,
      '#default_value' => $this->entity->getSourceEntityTypeId(),
      '#ajax' => [
        'callback' => [$this, 'getBundles'],  'event' => 'change',
        'method' => 'replace',
        'wrapper' => 'bundle-to-update',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Verifying...'),
        ],
      ],
    ];

    $options = [];
    $entity_id = $this->entity->getSourceEntityTypeId();
    if (empty($entity_id)) {
      $entity_id = array_key_first($entity_types);
    }
    $bundles_obj = $this->entityTypeBundleInfo->getBundleInfo($entity_id);
    foreach ($bundles_obj as $key => $value) {
        $options[$key] = $value['label'];
    }

    $form['source']['source_bundle'] = [
      '#title' => t('Bundle'),
      '#type' => 'select',
      '#description' => t('Select the bundles'),
      '#options' => $options,
      '#default_value' => $this->entity->getSourceBundle(),
      '#attributes' => ["id" => 'bundle-to-update'],
      '#multiple' => TRUE,
      '#validated' => TRUE
    ];

    $events = [
      'entity_insert' => $this->t('On entity creation'),
      'entity_update' => $this->t('On entity update'),
      'entity_delete' => $this->t('On entity delete'),
    ];
    $form['source']['source_events'] = [
      '#title' => t('Events'),
      '#type' => 'checkboxes',
      '#description' => t('Select the events'),
      '#options' => $events,
      '#default_value' => !empty($this->entity->getSourceEvents()) ? $this->entity->getSourceEvents() : [],
      '#multiple' => TRUE,
    ];

    $form['limits'] = [
      '#title' => $this->t('Limits on the attribution of points'),
      '#type' => 'fieldset',
    ];

    $form['limits']['entity_id'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Limit by gamification source entity id'),
      '#description' => $this->t('If checked, the points are only attributed once to a certain user, for each gamification source entity id.'),
      '#maxlength' => 255,
      '#default_value' => !empty($this->entity->getLimits()) && isset($this->entity->getLimits()['entity_id']) ? $this->entity->getLimits()['entity_id'] : '',
    ];

    $form['limits']['bundle'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Limit by gamification source bundle'),
      '#description' => $this->t('If checked, the points are only attributed once to a certain user, for each of available gamification source bundles.'),
      '#maxlength' => 255,
      '#default_value' => !empty($this->entity->getLimits()) && isset($this->entity->getLimits()['bundle']) ? $this->entity->getLimits()['bundle'] : '',
    ];

    $options = [
      -1 => $this->t('Always (no limits)'),
      0 => $this->t('Once'),
      60 => $this->t('One time per minute'),
      300 => $this->t('One time per 5 minutes'),
      600 => $this->t('One time per 10 minutes'),
      1800 => $this->t('One time per 30 minutes'),
      3600 => $this->t('Once an hour'),
      86400 => $this->t('Daily'),
      604800 => $this->t('Weekly'),
      2419200 => $this->t('Monthly'),
    ];
    $form['limits']['time'] = [
      '#type' => 'select',
      '#title' => $this->t('Time interval'),
      '#options' => $options,
      '#description' => $this->t('Option to have limit by time. Points are only attributed to each user according to the time selected on this field.'),
      '#maxlength' => 255,
      '#default_value' => !empty($this->entity->getLimits()) && isset($this->entity->getLimits()['time']) ? $this->entity->getLimits()['time'] : '',
    ];
    
    $form['points'] = [
      '#type' => 'number',
      '#title' => $this->t('Points'),
      '#default_value' => $this->entity->getPoints(),
      '#required' => FALSE,
    ];

    $form = $this->gamificationWidgetManager->hideFieldsFromForm($widget_id, $form);
    return $form;
  }

  /**
   * Form Ajax Helper function.
   */
  public function widgetsAjaxCallback(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('widget');
    if (empty($value)) {
      return $form['widget'];  
    }

    $actions = $this->gamificationWidgetManager->getActionsFromWidget($value);
    $form['widget']['action']['#options'] = $actions;
    $widget_options = $this->gamificationWidgetManager->getWidgetOptions($value);
    if (!empty($widget_options)) {
      foreach ($widget_options as $option) {
        if (!isset($form['widget']['widget_options'][$option])) {
          $form['widget']['widget_options'][$option] = [
            '#type' => 'textfield',
            '#title' => $option,
            '#maxlength' => 255,
            '#default_value' => $this->entity->getWidgetOptionsValue()[$option],
          ];
        }
      }
    }
    $form = $this->gamificationWidgetManager->hideFieldsFromForm($value, $form);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $widget = $this->entity->getWidgetValue();
    if (empty($form['source'])) {
      $source = $this->gamificationWidgetManager->getWidgetSourceValues($widget);
      $this->entity->setSourceEntityId($source['source_entity_type_id']);
      $this->entity->setSourceBundle($source['source_entity_bundle']);
      $this->entity->setSourceEvents($source['source_events']);
    }
    if (empty($form['source']['source_events'])) {
      $source = $this->gamificationWidgetManager->getWidgetSourceValues($widget);
      $this->entity->setSourceEvents($source['source_events']);
    }

    // Set widget options.
    if (isset($form['widget']['widget_options'])) {
      $attributes = [];
      foreach (array_keys($form['widget']['widget_options']) as $key) {
        if (strpos($key, '#') === 0) {
          continue;
        }
        $attributes[$key] = $form_state->getValue($key);
      }
      $this->entity->setWidgetOptionsValue($attributes);
    } else {
      $this->entity->setWidgetOptionsValue([]);
    }

    // Set limits values.
    $attributes = [];
    foreach (array_keys($form['limits']) as $key) {
      if (strpos($key, '#') === 0) {
        continue;
      }
      $attributes[$key] = $form_state->getValue($key);
    }
    $this->entity->setLimits($attributes);

    $status = $this->entity->save();
    $this->entity->statusMessage($status);
    $form_state->setRedirect('entity.gamification.collection');
  }
  
  public function getBundles(array &$element, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $value = $triggering_element['#value'];
    $bundles = $this->entityTypeBundleInfo->getBundleInfo($value);
    foreach ($bundles as $key => $value) {
       $options[$key] = $value['label'] ;
    }
    $wrapper_id = $triggering_element["#ajax"]["wrapper"];
    $rendered_field = '';
    foreach ($options as $key => $value) {
      $rendered_field .= "<option value='". $key . "'>" . $value . "</option>";
    }
    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand("#" . $wrapper_id, $rendered_field));
    return $response;
  }

  /**
   * Checks for an existing gamification entity.
   *
   * @param string|int $entity_id
   *   The entity ID.
   * @param array $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return bool
   *   TRUE if this entity already exists, FALSE otherwise.
   */
  public function exists($entity_id, array $element, FormStateInterface $form_state) {
    $query = $this->entityTypeManager->getStorage('gamification')->getQuery();
    $result = $query->condition('id', $element['#field_prefix'] . $entity_id)
      ->execute();

    return (bool) $result;
  }

}
