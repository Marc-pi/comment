<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt BSD 3-Clause License
 */

namespace Module\Comment\Form;

use Pi;
use Pi\Form\Form as BaseForm;


/**
 * Form of comment post
 *
 * @author Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
 */
class PostForm extends BaseForm
{
    /**
     * Editor type
     *
     * @var string
     */
    protected $markup = 'text';

    /**
     * Constructor
     *
     * @param string|int    $name   Optional name for the element
     * @param string        $markup Page type: text, html, markdown
     */
    public function __construct($name = '', $markup = '')
    {
        $name = $name ?: 'comment-post';
        $this->markup = $markup ?: $this->markup;
        parent::__construct($name);
        $this->setAttribute('action', Pi::service('comment')->getUrl('submit'));
    }

    /**
     * Load filter
     *
     * @return PostFilter
     */
    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new PostFilter;
        }

        return $this->filter;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $set = '';
        switch ($this->markup) {
            case 'html':
                $editor         = 'html';
                break;
            case 'markdown':
                $editor         = 'markitup';
                $set            = 'markdown';
                break;
            case 'text':
            default:
                $editor         = 'textarea';
                $this->markup   = 'text';
                break;
        }

        $userId = Pi::user()->getId();
        $guestApprove = Pi::service('config')->get('guest_approve', 'comment');

        if ($guestApprove === 1 && $userId === 0) {

            $this->add(array(
                'name' => 'identity',
                'options' => array(
                    'label' => __('Identity'),
                ),
                'attributes' => array(
                    'type' => 'text',
                    'required' => true,
                )
            ));

            $this->add(array(
                'name' => 'email',
                'options' => array(
                    'label' => __('Email'),
                ),
                'attributes' => array(
                    'type' => 'text',
                    'required' => true,
                )
            ));

        }
        
        $this->add(array(
            'name'          => 'content',
            'type'          => 'editor',
            'options'       => array(
                'label'     => __('Comment'),
                'editor'    => $editor,
                'set'       => $set,
            ),
            'attributes'    => array(
                'placeholder'   => __('Type your content'),
                'class'         => 'form-control',
                'rows'          => 5,
            ),
        ));

        $this->add(array(
            'name'          => 'submit',
            //'type'          => 'button',
            'attributes'    => array(
                'type'  => 'submit',
                'value' => __('Save'),
                'class' => 'btn btn-primary',
            ),
        ));

        $this->add(array(
            'name'          => 'markup',
            'attributes'    => array(
                'type'  => 'hidden',
                'value' => $this->markup,
            ),
        ));

        foreach (array(
                     'id',
                     'root',
                     'reply',
                     'module',
                     'type',
                     'item',
                     'redirect'
                 ) as $hiddenElement
        ) {
            $this->add(array(
                'name'  => $hiddenElement,
                'type'  => 'hidden',
            ));
        }
    }
}
