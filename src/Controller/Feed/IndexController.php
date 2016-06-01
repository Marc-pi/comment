<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt BSD 3-Clause License
 */

namespace Module\Comment\Controller\Feed;

use Pi;
use Pi\Mvc\Controller\FeedController;

class IndexController extends FeedController
{
    public function indexAction()
    {
        $feed = $this->getDataModel(array(
            'title'         => __('Comment feed'),
            'description'   => __('Latest comments.'),
            'date_created'  => time(),
        ));

        $where = array('active' => 1);
        $posts = Pi::api('api', 'comment')->getList(
            $where,
            10
        );
        $renderOptions = array();
        $posts = Pi::api('api', 'comment')->renderList($posts, $renderOptions);

        foreach ($posts as $post) {
            $entry = array(
                'title'         => $post['target']['title'],
                'description'   => $post['content'],
                'date_modified' => (int) $post['time'],
                'link'          => Pi::url($post['url'], true)
            );
            $feed->entry = $entry;
        }

        return $feed;
    }
}