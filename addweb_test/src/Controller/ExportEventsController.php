<?php

namespace Drupal\addweb_test\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller for export json.
 */
class ExportEventsController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function data($siteapikey, $nid) {
    $json_array = [
      'data' => [],
    ];
    $config = \Drupal::config('system.site');
    $siteApiKey = $config->get('siteapikey');

    if ($siteApiKey !== $siteapikey) {
      // Site API Key is mismatched.
      throw new AccessDeniedHttpException();
    }
    $nids = \Drupal::entityQuery('node')->condition('type', 'page')->condition('nid', $nid)->execute();

    if (empty($nids)) {
      // When node id is invalid.
      throw new AccessDeniedHttpException();
    }
    $nodes = Node::loadMultiple($nids);
    foreach ($nodes as $node) {
      $json_array['data'][] = [
        'type' => $node->get('type')->target_id,
        'id' => $node->get('nid')->value,
        'attributes' => [
          'title' => $node->get('title')->value,
          'content' => $node->get('body')->value,
        ],
      ];
    }
    return new JsonResponse($json_array);
  }

}
