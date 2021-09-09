<?php

namespace Drupal\urls\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;


/**
 * Provides a 'urlsBlock' block.
 *
 * @Block(
 *   id = "urls_block_invited",
 *   admin_label = @Translation("TIE - Empresas (Invitaciones)"),
 *   category = @Translation("Plataforma tie")
 * )
 */
class urlsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $gid = buscargrupo();
    $gid_str = strval($gid);
    $uri = 'internal:/group/'.$gid_str.'/content/add/group_invitation?destination=/group/'.$gid_str.'/members';
    $url = Url::fromUri($uri);
    return [
      '#type' => 'link',
      '#url' => $url,
      '#title' => $this->t('Invitar Colaborador'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }


  /*
   *
   */

}
