<?php

namespace WP_Fields\Utility;

class PostTypeFetch {

    // Fetch all public post types
    public function get_post_types() {
        $args = [
            'public' => true,
        ];
        return get_post_types($args, 'objects');
    }

    // Render the select dropdown with post type keys as values
    public function render() {
        $post_types = $this->get_post_types();
        ?>
        <select id="post-type-select">
            <?php foreach ($post_types as $key => $post_type): ?>
                <option value="<?php echo esc_attr($key); ?>">
                    <?php echo esc_html($post_type->labels->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }
}
