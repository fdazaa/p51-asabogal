<?php

namespace Drupal\indicadores_asignados\Plugin\Block;


use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;


/**
 * Provides a 'urlsBlock' block.
 *
 * @Block(
 *   id = "ps_block",
 *   admin_label = @Translation("TIE - Indicadores (Productos y servicios)"),
 *   category = @Translation("Plataforma tie")
 * )
 */


class psBlock extends BlockBase
{


  public function build()
  {
    // TODO: Implement build() method.
    $indicador = [];
    $indicador['title']=[
      '#type'=> 'text',
      '#title'=>'Titulo',
    ];

    return [
      '#type'=> 'text',
      '#title'=>$this->t('asdf')
    ];

  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

}
