<?php

/**
 * Implements hook_help().
 */
function curso_module_help($route_name, \Drupal\Core\Routing\RouteMatchInterface $route_match) {
  switch ($route_name) {

    // Main module help for the block module.
    case 'help.page.curso_module':
      return '<p> Este es el hook_help de nuestro modulo del curso. </p>';
  }
}
