<?php
namespace GraphQL\REST\Controller;

use GraphQL\Parser\HTML;

class Attachments extends \WP_REST_Attachments_Controller
{
    public function __construct( $post_type ) {
        parent::__construct($post_type);

        $this->namespace = 'graphql/v1';
    }

    // @codingStandardsIgnoreLine
    public function prepare_item_for_response( $post, $request ) {
        $response = parent::prepare_item_for_response($post, $request);
        $data = $response->get_data();

        $response->remove_link('self');
        $response->remove_link('collection');
        $response->remove_link('about');
        $response->remove_link('author');
        $response->remove_link('replies');
        $response->remove_link('version-history');
        $response->remove_link('https://api.w.org/attachment');
        $response->remove_link('https://api.w.org/term');
        $response->remove_link('https://api.w.org/featuredmedia');

        if (wp_attachment_is('audio', $post) || wp_attachment_is('video', $post)) {
            $data['featured_media'] = (int) get_post_thumbnail_id($post->ID);
        }

        $response->set_data($data);
        return $response;
    }
}
