<?php
/**
 * Plugin Name: Barbershop Core
 * Description: Site-specific functionality for the barbershop site.
 * Version: 0.1.0
 */

defined("ABSPATH") || exit;

function barbershop_register_post_types() {

    register_post_type( 'bs_barber', array(
        'labels' => array(
            'name'          => 'Barbers',
            'singular_name' => 'Barber',
            'add_new_item'  => 'Add New Barber',
            'edit_item'     => 'Edit Barber',
        ),
        'public'       => true,
        'has_archive'  => 'barbers',
        'rewrite'      => array( 'slug' => 'barber' ),
        'menu_icon'    => 'dashicons-businessperson',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest' => true,
    ) );

    register_post_type( 'bs_service', array(
        'labels' => array(
            'name'          => 'Services',
            'singular_name' => 'Service',
            'add_new_item'  => 'Add New Service',
            'edit_item'     => 'Edit Service',
        ),
        'public'       => true,
        'has_archive'  => 'services',
        'rewrite'      => array( 'slug' => 'service' ),
        'menu_icon'    => 'dashicons-list-view',
        'supports'     => array( 'title', 'editor', 'excerpt', 'custom-fields' ),
        'show_in_rest' => true,
    ) );
}
add_action( 'init', 'barbershop_register_post_types' );

function barbershop_meta_fields() {
    return array(
        'bs_service' => array(
            'bs_price'    => 'Price (e.g. £25)',
            'bs_duration' => 'Duration (e.g. 30 min)',
        ),
        'bs_barber'  => array(
            'bs_specialty' => 'Specialty (e.g. Fades & tapers)',
        ),
    );
}

function barbershop_register_meta() {
    foreach ( barbershop_meta_fields() as $post_type => $fields ) {
        foreach ( array_keys( $fields ) as $key ) {
            register_post_meta( $post_type, $key, array(
                'type'              => 'string',
                'single'            => true,
                'show_in_rest'      => true,
                'sanitize_callback' => 'sanitize_text_field',
                'auth_callback'     => function () {
                    return current_user_can( 'edit_posts' );
                },
            ) );
        }
    }
}
add_action( 'init', 'barbershop_register_meta' );


function barbershop_add_meta_boxes() {
    foreach ( array_keys( barbershop_meta_fields() ) as $post_type ) {
        add_meta_box( 'barbershop_details', 'Details', 'barbershop_render_details_box', $post_type );
    }
}
add_action( 'add_meta_boxes', 'barbershop_add_meta_boxes' );

function barbershop_render_details_box( $post ) {
    wp_nonce_field( 'barbershop_save_details', 'barbershop_details_nonce' );
    $fields = barbershop_meta_fields()[ $post->post_type ];
    foreach ( $fields as $key => $label ) {
        $value = get_post_meta( $post->ID, $key, true );
        printf(
            '<p><label for="%1$s"><strong>%2$s</strong></label><br><input type="text" class="widefat" id="%1$s" name="%1$s" value="%3$s"></p>',
            esc_attr( $key ),
            esc_html( $label ),
            esc_attr( $value )
        );
    }
}

function barbershop_save_details( $post_id ) {
    if ( ! isset( $_POST['barbershop_details_nonce'] )
        || ! wp_verify_nonce( $_POST['barbershop_details_nonce'], 'barbershop_save_details' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    $fields = barbershop_meta_fields()[ get_post_type( $post_id ) ] ?? array();
    foreach ( $fields as $key => $label ) {
        if ( isset( $_POST[ $key ] ) ) {
            update_post_meta( $post_id, $key, sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) );
        }
    }
}
add_action( 'save_post', 'barbershop_save_details' );